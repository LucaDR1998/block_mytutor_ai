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
 * Renderable for the MyTutor AI chat block.
 *
 * @package    block_mytutor_ai
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_mytutor_ai\output;
use context_system;
use core_ai\aiactions\generate_text;
use context_course;
use html_writer;
use moodle_url;
use renderable;
use renderer_base;
use templatable;

/**
 * Chat renderable for the chat interface container.
 */
class chat implements renderable, templatable {
    /** @var int Maximum number of characters allowed for the tutor name. */
    private const TUTOR_NAME_MAX_LENGTH = 40;

    /** @var int Maximum number of characters allowed for the tutor caption. */
    private const TUTOR_CAPTION_MAX_LENGTH = 60;

    /** @var int Maximum number of characters allowed for the welcome message. */
    private const WELCOME_MESSAGE_MAX_LENGTH = 160;

    /** @var int Maximum number of characters allowed for the disclaimer. */
    private const DISCLAIMER_MAX_LENGTH = 180;

    /** @var int Maximum number of quick replies. */
    private const QUICK_REPLIES_MAX_COUNT = 5;

    /** @var int Maximum number of characters allowed per quick reply. */
    private const QUICK_REPLY_MAX_LENGTH = 40;

    /** @var int The course ID. */
    private int $courseid;

    /** @var string Unique DOM id. */
    private string $uniqid;

    /**
     * Constructor.
     *
     * @param int $courseid The course ID.
     */
    public function __construct(int $courseid) {
        $this->courseid = $courseid;
        $this->uniqid = uniqid('mytutor-ai-', false);
    }
    /**
     * Export data for the Mustache template.
     *
     * @param renderer_base $output The renderer.
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        global $PAGE;

        $coursecontext = context_course::instance($this->courseid);
        $backendenabled = (bool) get_config('block_mytutor_ai', 'enabled');
        $coreaiready = \core\di::get(\core_ai\manager::class)->is_action_available(generate_text::class);
        $canchat = $backendenabled && $coreaiready && has_capability('block/mytutor_ai:chat', $coursecontext);

        // Resolve personalization settings.
        $component = 'block_mytutor_ai';
        $tutoravatarurl = $this->resolve_avatar_url();
        $chatbgcolor = get_config($component, 'chat_bg_color') ?: '';
        $usermsgcolor = get_config($component, 'user_msg_color') ?: '';
        $assistantmsgcolor = get_config($component, 'assistant_msg_color') ?: '';
        $tutorname = self::limit_text((string) (get_config($component, 'tutorname') ?: ''), self::TUTOR_NAME_MAX_LENGTH);
        $tutorcaption = self::limit_text(
            (string) (get_config($component, 'tutorcaption') ?: ''),
            self::TUTOR_CAPTION_MAX_LENGTH
        );
        $accentcolor = get_config($component, 'accent_color') ?: '';
        $chattextcolor = get_config($component, 'chat_text_color') ?: '';
        $welcomemessage = self::limit_text(
            (string) (get_config($component, 'welcomemessage') ?: ''),
            self::WELCOME_MESSAGE_MAX_LENGTH
        );
        $disclaimer = self::limit_text((string) (get_config($component, 'disclaimer') ?: ''), self::DISCLAIMER_MAX_LENGTH);
        $quickrepliesraw = get_config($component, 'quickreplies') ?: '';
        $quickreplies = [];
        if (!empty($quickrepliesraw)) {
            $lines = array_filter(array_map('trim', explode("\n", $quickrepliesraw)));
            $quickreplies = array_map(function($line) {
                return ['text' => s(self::limit_text($line, self::QUICK_REPLY_MAX_LENGTH))];
            }, array_slice($lines, 0, self::QUICK_REPLIES_MAX_COUNT));
        }

        if ($canchat) {
            $PAGE->requires->js_call_amd('block_mytutor_ai/chat', 'init', [$this->uniqid, $tutoravatarurl]);
        }

        if (!$backendenabled) {
            $unavailablemessage = s(get_string('backenddisabled', 'block_mytutor_ai'));
        } else if (!$coreaiready) {
            $aiproviderurl = new moodle_url('/admin/settings.php', ['section' => 'aiprovider']);
            $unavailablemessage = get_string('coreaichatnotconfiguredwithlinks', 'block_mytutor_ai', (object) [
                'providerslink' => html_writer::link(
                    $aiproviderurl,
                    get_string('aiproviders', 'ai'),
                    ['class' => 'alert-link']
                ),
            ]);
        } else {
            $unavailablemessage = s(get_string('nopermission', 'block_mytutor_ai'));
        }

        $assistantlabel = $tutorname ?: get_string('assistantlabel', $component);

        return [
            'uniqid' => $this->uniqid,
            'courseid' => $this->courseid,
            'isready' => $canchat,
            'chatplaceholder' => get_string('chatplaceholder', $component),
            'sendbutton' => get_string('sendbutton', $component),
            'emptyconversation' => $welcomemessage ?: get_string('emptyconversation', $component),
            'thinkingtext' => get_string('thinkingtext', $component),
            'assistantlabel' => $assistantlabel,
            'userlabel' => get_string('userlabel', $component),
            'errortext' => get_string('errortext', $component),
            'unavailablemessagehtml' => $unavailablemessage,
            'chatbgcolor' => $chatbgcolor,
            'usermsgcolor' => $usermsgcolor,
            'assistantmsgcolor' => $assistantmsgcolor,
            'tutoravatarurl' => $tutoravatarurl,
            'hastutoravatar' => !empty($tutoravatarurl),
            'tutorcaption' => $tutorcaption,
            'hastutorcaption' => !empty($tutorcaption) && !empty($tutoravatarurl),
            'accentcolor' => $accentcolor,
            'chattextcolor' => $chattextcolor,
            'disclaimer' => $disclaimer ? format_text($disclaimer, FORMAT_PLAIN) : '',
            'hasdisclaimer' => !empty($disclaimer),
            'quickreplies' => $quickreplies,
            'hasquickreplies' => !empty($quickreplies),
        ];
    }

    /**
     * Limit a text to the configured maximum length.
     *
     * @param string $text Source text.
     * @param int $maxlength Maximum allowed characters.
     * @return string
     */
    private static function limit_text(string $text, int $maxlength): string {
        $text = trim($text);
        if (\core_text::strlen($text) <= $maxlength) {
            return $text;
        }

        return rtrim(\core_text::substr($text, 0, $maxlength - 1)) . '…';
    }

    /**
     * Resolve the tutor avatar URL from config.
     *
     * Custom upload takes precedence over built-in selection.
     *
     * @return string Avatar URL or empty string.
     */
    private function resolve_avatar_url(): string {
        $component = 'block_mytutor_ai';
        $avatarnum = get_config($component, 'tutoravatar');

        if (empty($avatarnum)) {
            return '';
        }

        // Custom uploaded avatar.
        if ($avatarnum === 'custom') {
            $systemcontext = context_system::instance();
            $fs = get_file_storage();
            $files = $fs->get_area_files($systemcontext->id, $component, 'customavatar', 0, 'id', false);
            if ($files) {
                $file = reset($files);
                $rev = get_config($component, 'customavatarrev') ?: 0;
                return moodle_url::make_pluginfile_url(
                    $systemcontext->id,
                    $component,
                    'customavatar',
                    $rev,
                    $file->get_filepath(),
                    $file->get_filename()
                )->out(false);
            }
            return '';
        }

        // Built-in avatar.
        return (new moodle_url("/blocks/mytutor_ai/pix/avatars/avatar_{$avatarnum}.png"))->out(false);
    }
}
