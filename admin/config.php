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
 * External admin configuration page for block_mytutor_ai.
 *
 * @package    block_mytutor_ai
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('block_mytutor_ai_config');

$pageurl = new moodle_url('/blocks/mytutor_ai/admin/config.php');

$component = 'block_mytutor_ai';

// All config keys managed by this form (excluding file-based fields).
$keys = [
    'enabled',
    'embeddingprovider',
    'vectorstore',
    'apikey',
    'endpoint',
    'embeddingmodel',
    'qdrantendpoint',
    'qdrantcollection',
    'pgvectortablename',
    'chunksize',
    'chunkoverlap',
    'retrievaltopk',
    'chat_bg_color',
    'user_msg_color',
    'assistant_msg_color',
    'tutoravatar',
    'tutorname',
    'welcomemessage',
    'themepreset',
    'accent_color',
    'chat_text_color',
    'tutorcaption',
    'quickreplies',
    'disclaimer',
];

$form = new \block_mytutor_ai\form\config_form($pageurl);

if ($form->is_cancelled()) {
    redirect($pageurl);
}

if ($data = $form->get_data()) {
    foreach ($keys as $key) {
        if (isset($data->{$key})) {
            set_config($key, $data->{$key}, $component);
        }
    }

    // Handle custom avatar file upload.
    $systemcontext = context_system::instance();
    $draftitemid = file_get_submitted_draft_itemid('customavatar');
    if ($draftitemid) {
        file_save_draft_area_files(
            $draftitemid,
            $systemcontext->id,
            'block_mytutor_ai',
            'customavatar',
            0,
            ['subdirs' => 0, 'maxfiles' => 1, 'accepted_types' => ['web_image']]
        );
        $fs = get_file_storage();
        $files = $fs->get_area_files($systemcontext->id, 'block_mytutor_ai', 'customavatar', 0, 'id', false);
        if ($files) {
            $file = reset($files);
            set_config('customavatarrev', $file->get_timemodified(), $component);
        } else {
            set_config('customavatarrev', 0, $component);
        }
    }

    redirect($pageurl, get_string('changessaved'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Load current values into the form.
$current = new stdClass();
foreach ($keys as $key) {
    $value = get_config($component, $key);
    if ($value !== false) {
        $current->{$key} = $value;
    }
}

// Prepare draft area for custom avatar filepicker.
$systemcontext = context_system::instance();
$draftitemid = file_get_submitted_draft_itemid('customavatar');
file_prepare_draft_area(
    $draftitemid,
    $systemcontext->id,
    'block_mytutor_ai',
    'customavatar',
    0,
    ['subdirs' => 0, 'maxfiles' => 1, 'accepted_types' => ['web_image']]
);
$current->customavatar = $draftitemid;

$form->set_data($current);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', $component));

$aiproviderurl = new moodle_url('/admin/settings.php', ['section' => 'aiprovider']);
$chatconfigmessage = get_string('chatmanagedbycoreai', $component, (object) [
    'providerslink' => html_writer::link(
        $aiproviderurl,
        get_string('aiproviders', 'ai'),
        ['class' => 'alert-link']
    ),
]);
echo $OUTPUT->notification($chatconfigmessage, \core\output\notification::NOTIFY_INFO, false);
$form->display();
echo $OUTPUT->footer();
