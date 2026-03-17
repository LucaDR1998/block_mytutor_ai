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

namespace block_mytutor_ai\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use block_mytutor_ai\local\rag\index_manager;

/**
 * External API to queue a course reindex.
 *
 * @package    block_mytutor_ai
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reindex_course extends external_api {
    /**
     * Parameters definition.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course identifier'),
        ]);
    }

    /**
     * Queue the reindex job.
     *
     * @param int $courseid Course identifier.
     * @return array
     */
    public static function execute(int $courseid): array {
        ['courseid' => $courseid] = self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid,
        ]);

        if (!get_config('block_mytutor_ai', 'enabled')) {
            throw new \moodle_exception('pluginisdisabled', 'block_mytutor_ai');
        }

        $context = \context_course::instance($courseid);
        self::validate_context($context);
        require_capability('block/mytutor_ai:manage', $context);

        $queueid = (new index_manager())->queue_course_reindex($courseid);

        return [
            'queueid' => $queueid,
            'status' => index_manager::STATUS_QUEUED,
        ];
    }

    /**
     * Return definition.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'queueid' => new external_value(PARAM_INT, 'Queue record identifier'),
            'status' => new external_value(PARAM_ALPHAEXT, 'Queue status'),
        ]);
    }
}
