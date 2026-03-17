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

/**
 * Web service definitions for block_mytutor_ai.
 *
 * @package    block_mytutor_ai
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'block_mytutor_ai_ask_question' => [
        'classname' => 'block_mytutor_ai\external\ask_question',
        'methodname' => 'execute',
        'description' => 'Ask a course-scoped question to the MyTutor AI assistant.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'block/mytutor_ai:chat',
        'loginrequired' => true,
    ],
    'block_mytutor_ai_reindex_course' => [
        'classname' => 'block_mytutor_ai\external\reindex_course',
        'methodname' => 'execute',
        'description' => 'Queue a course reindex job for the MyTutor AI assistant.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'block/mytutor_ai:manage',
        'loginrequired' => true,
    ],
    'block_mytutor_ai_test_provider_connection' => [
        'classname' => 'block_mytutor_ai\external\test_provider_connection',
        'methodname' => 'execute',
        'description' => 'Run a local provider readiness check for the configured MyTutor AI providers.',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'moodle/site:config',
        'loginrequired' => true,
    ],
];
