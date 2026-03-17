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

/**
 * Split long texts into retrieval-sized chunks.
 *
 * @package    block_mytutor_ai
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class chunk_processor {
    /**
     * Split a text into chunks using simple character windows.
     *
     * @param string $text Text to split.
     * @param int $chunksize Chunk size in characters.
     * @param int $overlap Overlap in characters.
     * @return array
     */
    public function chunk_text(string $text, int $chunksize, int $overlap): array {
        $text = trim((string) preg_replace('/\s+/u', ' ', $text));
        if ($text === '') {
            return [];
        }

        $chunksize = max(200, $chunksize);
        $overlap = max(0, min($overlap, $chunksize - 1));
        $step = max(1, $chunksize - $overlap);

        if (core_text::strlen($text) <= $chunksize) {
            return [$text];
        }

        $chunks = [];
        $length = core_text::strlen($text);
        for ($start = 0; $start < $length; $start += $step) {
            $chunk = trim(core_text::substr($text, $start, $chunksize));
            if ($chunk !== '') {
                $chunks[] = $chunk;
            }

            if ($start + $chunksize >= $length) {
                break;
            }
        }

        return array_values(array_unique($chunks));
    }
}
