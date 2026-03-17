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

/**
 * Build prompts from the retrieved contexts.
 *
 * @package    block_mytutor_ai
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class prompt_builder {
    /**
     * Build a prompt string.
     *
     * @param string $question User question.
     * @param array $contexts Retrieved contexts.
     * @return string
     */
    public function build(string $question, array $contexts): string {
        $lines = [
            'You are the Moodle course assistant.',
            'Answer using only the provided course context. If the context is incomplete, say so clearly.',
            'Course context:',
        ];

        if (empty($contexts)) {
            $lines[] = '- No local chunks were retrieved.';
        } else {
            foreach ($contexts as $index => $context) {
                $number = $index + 1;
                $lines[] = "[Chunk {$number}] " . $context['content'];
            }
        }

        $lines[] = 'Question: ' . trim($question);

        return implode("\n\n", $lines);
    }
}
