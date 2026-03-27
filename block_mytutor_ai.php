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
 * Block class for block_mytutor_ai.
 *
 * @package    block_mytutor_ai
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_mytutor_ai\output\chat;

/**
 * MyTutor AI block - provides an AI chat interface inside courses.
 */
class block_mytutor_ai extends block_base
{

    /**
     * Initialise the block.
     */
    public function init(): void
    {
        $this->title = get_string('pluginname', 'block_mytutor_ai');
    }
    /**
     * Define where the block can be added.
     *
     * @return array
     */
    public function applicable_formats(): array
    {
        return ['course-view' => true];
    }
    /**
     * Only one instance per course.
     *
     * @return bool
     */
    public function instance_allow_multiple(): bool
    {
        return false;
    }
    /**
     * This block has global settings.
     *
     * @return bool
     */
    public function has_config(): bool
    {
        return true;
    }

    /**
     * Hide the block title during normal course usage.
     *
     * Keep the header visible in editing mode so block controls remain accessible.
     *
     * @return bool
     */
    public function hide_header(): bool
    {
        return !$this->page->user_is_editing();
    }

    /**
     * Generate the block content.
     *
     * @return stdClass|null
     */
    public function get_content(): ?stdClass
    {
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        if (!has_capability('block/mytutor_ai:use', $this->context)) {
            return $this->content;
        }

        $renderable = new chat($this->page->course->id);
        $renderer = $this->page->get_renderer('block_mytutor_ai');
        $this->content->text = $renderer->render($renderable);

        return $this->content;
    }
}
