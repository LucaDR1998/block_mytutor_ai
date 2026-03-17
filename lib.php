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
 * Library functions for block_mytutor_ai.
 *
 * @package    block_mytutor_ai
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Serve files for the block_mytutor_ai plugin.
 *
 * @param stdClass $course Course object.
 * @param stdClass $birecord_or_cm Block instance record.
 * @param context $context Context object.
 * @param string $filearea File area.
 * @param array $args Extra arguments.
 * @param bool $forcedownload Whether to force download.
 * @param array $options Additional options.
 * @return bool
 */
function block_mytutor_ai_pluginfile($course, $birecord_or_cm, $context, $filearea, $args, $forcedownload,
        array $options = []) {
    global $CFG;

    if ($filearea !== 'customavatar') {
        send_file_not_found();
    }

    if ($context->contextlevel != CONTEXT_SYSTEM) {
        send_file_not_found();
    }

    if ($CFG->forcelogin) {
        require_login();
    }

    $fs = get_file_storage();

    // First arg is the revision (for cache-busting), rest is filepath + filename.
    $revision = array_shift($args);
    $filename = array_pop($args);
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';

    $file = $fs->get_file($context->id, 'block_mytutor_ai', 'customavatar', 0, $filepath, $filename);
    if (!$file || $file->is_directory()) {
        send_file_not_found();
    }

    \core\session\manager::write_close();
    send_stored_file($file, null, 0, $forcedownload, $options);
}
