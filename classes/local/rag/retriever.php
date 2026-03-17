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

use core_text;
use block_mytutor_ai\local\provider\provider_factory;
use block_mytutor_ai\persistent\chunk;

/**
 * Retrieve relevant chunks for a question.
 *
 * @package    block_mytutor_ai
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class retriever {
    /**
     * Retrieve contexts for a course question.
     *
     * @param int $courseid Course identifier.
     * @param string $question User question.
     * @param int $limit Maximum contexts to return.
     * @return array
     */
    public function retrieve_course_context(int $courseid, string $question, int $limit = 5): array {
        $vectorstore = provider_factory::create_vector_store();
        $embeddingprovider = provider_factory::create_embedding_provider();
        $queryembedding = $embeddingprovider->embed_texts([$question])[0] ?? [];
        $vectorresults = $vectorstore->search($courseid, $queryembedding, $limit);

        if (!empty($vectorresults)) {
            return $vectorresults;
        }

        $terms = $this->extract_terms($question);
        $results = [];

        foreach (chunk::get_records(['courseid' => $courseid], 'id', 'ASC', 0, max($limit * 5, 20)) as $record) {
            $content = (string) $record->get('content');
            $results[] = [
                'chunkid' => (int) $record->get('id'),
                'documentid' => (int) $record->get('documentid'),
                'content' => $content,
                'score' => $this->score_chunk($content, $terms),
            ];
        }

        usort($results, static function (array $left, array $right): int {
            if ($left['score'] === $right['score']) {
                return $left['chunkid'] <=> $right['chunkid'];
            }

            return $right['score'] <=> $left['score'];
        });

        return array_slice($results, 0, $limit);
    }

    /**
     * Split the question into simple lexical terms.
     *
     * @param string $question User question.
     * @return array
     */
    private function extract_terms(string $question): array {
        $terms = preg_split('/\s+/u', core_text::strtolower(trim($question))) ?: [];
        $terms = array_filter($terms, static fn(string $term): bool => core_text::strlen($term) > 2);
        return array_values(array_unique($terms));
    }

    /**
     * Score a chunk using naive lexical matching.
     *
     * @param string $content Chunk content.
     * @param array $terms Search terms.
     * @return int
     */
    private function score_chunk(string $content, array $terms): int {
        if (empty($terms)) {
            return 0;
        }

        $normalised = core_text::strtolower($content);
        $score = 0;
        foreach ($terms as $term) {
            $score += substr_count($normalised, $term);
        }

        return $score;
    }
}
