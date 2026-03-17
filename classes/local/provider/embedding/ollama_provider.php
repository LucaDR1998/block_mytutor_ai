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

namespace block_mytutor_ai\local\provider\embedding;
use block_mytutor_ai\local\provider\embedding_provider_interface;
use block_mytutor_ai\local\provider\provider_catalog;

/**
 * Stub Ollama embedding provider.
 *
 * @package    block_mytutor_ai
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ollama_provider implements embedding_provider_interface
{
    // TODO
    /**
     * Return the provider short name.
     *
     * @return string
     */
    public function get_name(): string
    {
        return 'ollama';
    }
    /**
     * Run a local readiness check.
     *
     * @return array
     */
    public function ping(): array
    {
        $label = provider_catalog::get_provider_label('embedding', $this->get_name());
        $endpoint = (string) get_config('block_mytutor_ai', 'endpoint');

        if ($endpoint === '') {
            return [
                'success' => false,
                'message' => get_string('providerrequiresendpoint', 'block_mytutor_ai', $label),
            ];
        }

        return [
            'success' => true,
            'message' => get_string('providerstubready', 'block_mytutor_ai', $label),
        ];
    }
    /**
     * Return deterministic placeholder embeddings.
     *
     * @param array $texts Texts to embed.
     * @return array
     */
    public function embed_texts(array $texts): array
    {
        return array_map(static fn(string $text): array => [round(strlen($text) / 1000, 4)], $texts);
    }
}
