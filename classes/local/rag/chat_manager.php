<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace block_mytutor_ai\local\rag;

use block_mytutor_ai\local\provider\provider_catalog;
use block_mytutor_ai\local\provider\provider_factory;
use core_ai\aiactions\generate_text;

/**
 * Coordinate the retrieval and response generation flow.
 *
 * @package    block_mytutor_ai
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class chat_manager {
    /** @var retriever Retriever instance. */
    private retriever $retriever;

    /** @var prompt_builder Prompt builder instance. */
    private prompt_builder $promptbuilder;

    /**
     * Constructor.
     *
     * @param retriever|null $retriever Optional retriever override.
     * @param prompt_builder|null $promptbuilder Optional prompt builder override.
     */
    public function __construct(?retriever $retriever = null, ?prompt_builder $promptbuilder = null) {
        $this->retriever = $retriever ?? new retriever();
        $this->promptbuilder = $promptbuilder ?? new prompt_builder();
    }

    /**
     * Build an answer for a course question.
     *
     * @param int $courseid Course identifier.
     * @param string $question User question.
     * @return array
     */
    public function answer_course_question(int $courseid, string $question): array {
        $question = trim($question);
        if ($question === '') {
            throw new \invalid_parameter_exception(get_string('questionempty', 'block_mytutor_ai'));
        }

        $limit = max(1, (int) get_config('block_mytutor_ai', 'retrievaltopk'));
        $contexts = $this->retriever->retrieve_course_context($courseid, $question, $limit);
        $prompt = $this->promptbuilder->build($question, $contexts);
        $providerresponse = $this->generate_response_with_core_ai($courseid, $prompt);

        return [
            'answer' => (string) ($providerresponse['answer'] ?? ''),
            'notice' => (string) ($providerresponse['notice'] ?? ''),
            'chatprovider' => (string) ($providerresponse['chatprovider'] ?? get_string('chatprovidermoodleai', 'block_mytutor_ai')),
            'embeddingprovider' => provider_catalog::get_provider_label(
                'embedding',
                provider_factory::get_active_embedding_provider_name()
            ),
            'vectorstore' => provider_catalog::get_provider_label('vector', provider_factory::get_active_vector_store_name()),
            'contexts' => $contexts,
        ];
    }

    /**
     * Generate the final answer through Moodle's AI subsystem.
     *
     * @param int $courseid Course identifier.
     * @param string $prompt Prompt assembled from the retrieved chunks.
     * @return array
     */
    private function generate_response_with_core_ai(int $courseid, string $prompt): array {
        global $USER;

        $aimanager = \core\di::get(\core_ai\manager::class);
        if (!$aimanager->is_action_available(generate_text::class)) {
            throw new \moodle_exception('coreaichatnotconfigured', 'block_mytutor_ai');
        }

        $coursecontext = \context_course::instance($courseid);
        $action = new generate_text(
            contextid: $coursecontext->id,
            userid: $USER->id,
            prompttext: $prompt,
        );
        $response = $aimanager->process_action($action);

        if (!$response->get_success()) {
            $message = $response->get_errormessage() !== '' ? $response->get_errormessage() : $response->get_error();
            throw new \moodle_exception('coreaigenerationfailed', 'block_mytutor_ai', '', $message);
        }

        $responsedata = $response->get_response_data();

        return [
            'answer' => (string) ($responsedata['generatedcontent'] ?? ''),
            'notice' => '',
            'chatprovider' => $this->resolve_core_ai_provider_label($aimanager),
        ];
    }

    /**
     * Resolve the label of the first Moodle AI provider available for text generation.
     *
     * @param \core_ai\manager $aimanager AI manager instance.
     * @return string
     */
    private function resolve_core_ai_provider_label(\core_ai\manager $aimanager): string {
        $providersbyaction = $aimanager->get_providers_for_actions([generate_text::class], true);
        $providers = $providersbyaction[generate_text::class] ?? [];
        $provider = reset($providers);

        if (!$provider) {
            return get_string('chatprovidermoodleai', 'block_mytutor_ai');
        }

        return provider_catalog::get_core_ai_provider_label($provider->provider);
    }
}
