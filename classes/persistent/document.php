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
 * Persistent representing an indexed document source.
 *
 * @package    block_mytutor_ai
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class document extends persistent {
    /** @var string Table name. */
    public const TABLE = 'block_mytutor_ai_doc';

    /**
     * Define the persistent properties.
     *
     * @return array
     */
    protected static function define_properties(): array {
        return [
            'courseid' => [
                'type' => PARAM_INT,
            ],
            'cmid' => [
                'type' => PARAM_INT,
            ],
            'contextid' => [
                'type' => PARAM_INT,
            ],
            'component' => [
                'type' => PARAM_COMPONENT,
            ],
            'itemtype' => [
                'type' => PARAM_ALPHANUMEXT,
            ],
            'itemid' => [
                'type' => PARAM_INT,
            ],
            'title' => [
                'type' => PARAM_TEXT,
            ],
            'sourcehash' => [
                'type' => PARAM_ALPHANUM,
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
