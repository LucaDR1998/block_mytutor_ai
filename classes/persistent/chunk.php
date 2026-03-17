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

namespace block_mytutor_ai\persistent;

use core\persistent;

/**
 * Persistent representing an indexed chunk.
 *
 * @package    block_mytutor_ai
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class chunk extends persistent {
    /** @var string Table name. */
    public const TABLE = 'block_mytutor_ai_chunk';

    /**
     * Define the persistent properties.
     *
     * @return array
     */
    protected static function define_properties(): array {
        return [
            'documentid' => [
                'type' => PARAM_INT,
            ],
            'courseid' => [
                'type' => PARAM_INT,
            ],
            'contextid' => [
                'type' => PARAM_INT,
            ],
            'sequencenumber' => [
                'type' => PARAM_INT,
            ],
            'content' => [
                'type' => PARAM_RAW,
            ],
            'embeddingref' => [
                'type' => PARAM_RAW_TRIMMED,
                'null' => NULL_ALLOWED,
            ],
            'metadata' => [
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
            ],
            'timemodified' => [
                'type' => PARAM_INT,
            ],
            'timecreated' => [
                'type' => PARAM_INT,
            ],
        ];
    }
}
