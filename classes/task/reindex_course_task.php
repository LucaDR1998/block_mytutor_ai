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

namespace block_mytutor_ai\task;

use core\task\adhoc_task;
use block_mytutor_ai\local\rag\index_manager;

/**
 * Ad-hoc task used to reindex a course.
 *
 * @package    block_mytutor_ai
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reindex_course_task extends adhoc_task {
    /**
     * Execute the indexing task.
     *
     * @return void
     */
    public function execute(): void {
        $data = $this->get_custom_data();

        if (empty($data->queueid) || empty($data->courseid)) {
            mtrace('Skipping block_mytutor_ai reindex task because custom data is incomplete.');
            return;
        }

        (new index_manager())->run_course_reindex((int) $data->courseid, (int) $data->queueid);
    }

    /**
     * Queue an ad-hoc task.
     *
     * @param int $queueid Queue entry identifier.
     * @param int $courseid Course identifier.
     * @return void
     */
    public static function queue(int $queueid, int $courseid): void {
        $task = new self();
        $task->set_custom_data([
            'queueid' => $queueid,
            'courseid' => $courseid,
        ]);

        \core\task\manager::queue_adhoc_task($task);
    }
}
