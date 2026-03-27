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

namespace block_mytutor_ai\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use block_mytutor_ai\local\provider\provider_catalog;

/**
 * Configuration form for block_mytutor_ai.
 *
 * @package    block_mytutor_ai
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class config_form extends \moodleform {
    /** @var int Maximum number of characters allowed for the tutor name. */
    private const TUTOR_NAME_MAX_LENGTH = 40;

    /** @var int Maximum number of characters allowed for the tutor caption. */
    private const TUTOR_CAPTION_MAX_LENGTH = 60;

    /** @var int Maximum number of characters allowed for the welcome message. */
    private const WELCOME_MESSAGE_MAX_LENGTH = 160;

    /** @var int Maximum number of characters allowed for the disclaimer. */
    private const DISCLAIMER_MAX_LENGTH = 180;

    /** @var int Maximum number of quick reply suggestions. */
    private const QUICK_REPLIES_MAX_COUNT = 5;

    /** @var int Maximum number of characters allowed for a single quick reply. */
    private const QUICK_REPLY_MAX_LENGTH = 40;

    /**
     * Form definition.
     */
    protected function definition(): void {
        $mform = $this->_form;

        // General.
        $mform->addElement(
            'advcheckbox',
            'enabled',
            get_string('enabled', 'block_mytutor_ai'),
            get_string('enabled_desc', 'block_mytutor_ai')
        );
        $mform->setDefault('enabled', 1);

        // Provider selection.
        $mform->addElement('header', 'settingsproviders', get_string('settingsproviders', 'block_mytutor_ai'));

        $mform->addElement(
            'select',
            'embeddingprovider',
            get_string('embeddingprovider', 'block_mytutor_ai'),
            provider_catalog::get_embedding_provider_options()
        );
        $mform->setDefault('embeddingprovider', 'openai');
        $mform->addHelpButton('embeddingprovider', 'embeddingprovider', 'block_mytutor_ai');

        $mform->addElement(
            'select',
            'vectorstore',
            get_string('vectorstore', 'block_mytutor_ai'),
            provider_catalog::get_vector_store_options()
        );
        $mform->setDefault('vectorstore', 'qdrant');
        $mform->addHelpButton('vectorstore', 'vectorstore', 'block_mytutor_ai');

        // Provider credentials.
        $mform->addElement(
            'header',
            'settingscredentials',
            get_string('settingscredentials', 'block_mytutor_ai')
        );

        $mform->addElement(
            'passwordunmask',
            'apikey',
            get_string('apikey', 'block_mytutor_ai')
        );
        $mform->setType('apikey', PARAM_TEXT);
        $mform->addHelpButton('apikey', 'apikey', 'block_mytutor_ai');

        $mform->addElement('text', 'endpoint', get_string('endpoint', 'block_mytutor_ai'));
        $mform->setType('endpoint', PARAM_URL);
        $mform->addHelpButton('endpoint', 'endpoint', 'block_mytutor_ai');

        $mform->addElement('text', 'embeddingmodel', get_string('embeddingmodel', 'block_mytutor_ai'));
        $mform->setType('embeddingmodel', PARAM_TEXT);
        $mform->addHelpButton('embeddingmodel', 'embeddingmodel', 'block_mytutor_ai');

        // Vector store.
        $mform->addElement('header', 'settingsvectorstore', get_string('settingsvectorstore', 'block_mytutor_ai'));

        $mform->addElement('text', 'qdrantendpoint', get_string('qdrantendpoint', 'block_mytutor_ai'));
        $mform->setType('qdrantendpoint', PARAM_URL);
        $mform->setDefault('qdrantendpoint', 'http://localhost:6333');
        $mform->addHelpButton('qdrantendpoint', 'qdrantendpoint', 'block_mytutor_ai');
        $mform->hideIf('qdrantendpoint', 'vectorstore', 'neq', 'qdrant');

        $mform->addElement('text', 'qdrantcollection', get_string('qdrantcollection', 'block_mytutor_ai'));
        $mform->setType('qdrantcollection', PARAM_ALPHANUMEXT);
        $mform->setDefault('qdrantcollection', 'moodle_course_chunks');
        $mform->addHelpButton('qdrantcollection', 'qdrantcollection', 'block_mytutor_ai');
        $mform->hideIf('qdrantcollection', 'vectorstore', 'neq', 'qdrant');

        $mform->addElement('text', 'pgvectortablename', get_string('pgvectortablename', 'block_mytutor_ai'));
        $mform->setType('pgvectortablename', PARAM_ALPHANUMEXT);
        $mform->setDefault('pgvectortablename', 'block_mytutor_ai_vectors');
        $mform->addHelpButton('pgvectortablename', 'pgvectortablename', 'block_mytutor_ai');
        $mform->hideIf('pgvectortablename', 'vectorstore', 'neq', 'pgvector');

        // Retrieval defaults.
        $mform->addElement('header', 'settingsretrieval', get_string('settingsretrieval', 'block_mytutor_ai'));

        $mform->addElement('text', 'chunksize', get_string('chunksize', 'block_mytutor_ai'));
        $mform->setType('chunksize', PARAM_INT);
        $mform->setDefault('chunksize', 1000);
        $mform->addHelpButton('chunksize', 'chunksize', 'block_mytutor_ai');

        $mform->addElement('text', 'chunkoverlap', get_string('chunkoverlap', 'block_mytutor_ai'));
        $mform->setType('chunkoverlap', PARAM_INT);
        $mform->setDefault('chunkoverlap', 120);
        $mform->addHelpButton('chunkoverlap', 'chunkoverlap', 'block_mytutor_ai');

        $mform->addElement('text', 'retrievaltopk', get_string('retrievaltopk', 'block_mytutor_ai'));
        $mform->setType('retrievaltopk', PARAM_INT);
        $mform->setDefault('retrievaltopk', 5);
        $mform->addHelpButton('retrievaltopk', 'retrievaltopk', 'block_mytutor_ai');

        // Chat personalization.
        $mform->addElement(
            'header',
            'settingspersonalization',
            get_string('settingspersonalization', 'block_mytutor_ai')
        );

        // Theme preset selector.
        $presets = [
            '' => get_string('themepreset_none', 'block_mytutor_ai'),
            'light' => get_string('themepreset_light', 'block_mytutor_ai'),
            'dark' => get_string('themepreset_dark', 'block_mytutor_ai'),
            'ocean' => get_string('themepreset_ocean', 'block_mytutor_ai'),
            'warm' => get_string('themepreset_warm', 'block_mytutor_ai'),
        ];
        $mform->addElement('select', 'themepreset', get_string('themepreset', 'block_mytutor_ai'), $presets);
        $mform->addHelpButton('themepreset', 'themepreset', 'block_mytutor_ai');
        $mform->setType('themepreset', PARAM_ALPHA);

        $mform->addElement('text', 'chat_bg_color', get_string('chat_bg_color', 'block_mytutor_ai'));
        $mform->setType('chat_bg_color', PARAM_TEXT);
        $mform->setDefault('chat_bg_color', '#ffffff');
        $mform->addHelpButton('chat_bg_color', 'chat_bg_color', 'block_mytutor_ai');

        $mform->addElement('text', 'user_msg_color', get_string('user_msg_color', 'block_mytutor_ai'));
        $mform->setType('user_msg_color', PARAM_TEXT);
        $mform->setDefault('user_msg_color', '#0f6cbf');
        $mform->addHelpButton('user_msg_color', 'user_msg_color', 'block_mytutor_ai');

        $mform->addElement('text', 'assistant_msg_color', get_string('assistant_msg_color', 'block_mytutor_ai'));
        $mform->setType('assistant_msg_color', PARAM_TEXT);
        $mform->setDefault('assistant_msg_color', '#f7f9fc');
        $mform->addHelpButton('assistant_msg_color', 'assistant_msg_color', 'block_mytutor_ai');

        // Accent colour.
        $mform->addElement('text', 'accent_color', get_string('accent_color', 'block_mytutor_ai'));
        $mform->setType('accent_color', PARAM_TEXT);
        $mform->setDefault('accent_color', '');
        $mform->addHelpButton('accent_color', 'accent_color', 'block_mytutor_ai');

        // Chat text colour.
        $mform->addElement('text', 'chat_text_color', get_string('chat_text_color', 'block_mytutor_ai'));
        $mform->setType('chat_text_color', PARAM_TEXT);
        $mform->setDefault('chat_text_color', '');
        $mform->addHelpButton('chat_text_color', 'chat_text_color', 'block_mytutor_ai');

        // Built-in avatar selector.
        $avataroptions = ['' => get_string('tutoravatar_none', 'block_mytutor_ai')];
        for ($i = 1; $i <= 6; $i++) {
            $num = str_pad($i, 2, '0', STR_PAD_LEFT);
            $avataroptions[$num] = get_string('tutoravatar_option', 'block_mytutor_ai', $i);
        }
        $avataroptions['custom'] = get_string('tutoravatar_custom', 'block_mytutor_ai');
        $mform->addElement('select', 'tutoravatar', get_string('tutoravatar', 'block_mytutor_ai'), $avataroptions);
        $mform->setDefault('tutoravatar', '');
        $mform->addHelpButton('tutoravatar', 'tutoravatar', 'block_mytutor_ai');

        // Custom avatar upload — only visible when "Custom" is selected.
        $mform->addElement(
            'filemanager',
            'customavatar',
            get_string('customavatar', 'block_mytutor_ai'),
            null,
            [
                'subdirs' => 0,
                'maxfiles' => 1,
                'accepted_types' => ['web_image'],
                'maxbytes' => 524288,
            ]
        );
        $mform->addHelpButton('customavatar', 'customavatar', 'block_mytutor_ai');
        $mform->hideIf('customavatar', 'tutoravatar', 'neq', 'custom');

        // Tutor display name.
        $mform->addElement(
            'text',
            'tutorname',
            get_string('tutorname', 'block_mytutor_ai'),
            ['size' => 40, 'maxlength' => self::TUTOR_NAME_MAX_LENGTH]
        );
        $mform->setType('tutorname', PARAM_TEXT);
        $mform->addHelpButton('tutorname', 'tutorname', 'block_mytutor_ai');
        $mform->addRule(
            'tutorname',
            get_string('maximumchars', '', self::TUTOR_NAME_MAX_LENGTH),
            'maxlength',
            self::TUTOR_NAME_MAX_LENGTH,
            'client'
        );

        // Tutor caption.
        $mform->addElement(
            'text',
            'tutorcaption',
            get_string('tutorcaption', 'block_mytutor_ai'),
            ['size' => 50, 'maxlength' => self::TUTOR_CAPTION_MAX_LENGTH]
        );
        $mform->setType('tutorcaption', PARAM_TEXT);
        $mform->addHelpButton('tutorcaption', 'tutorcaption', 'block_mytutor_ai');
        $mform->addRule(
            'tutorcaption',
            get_string('maximumchars', '', self::TUTOR_CAPTION_MAX_LENGTH),
            'maxlength',
            self::TUTOR_CAPTION_MAX_LENGTH,
            'client'
        );

        // Welcome message.
        $mform->addElement(
            'textarea',
            'welcomemessage',
            get_string('welcomemessage', 'block_mytutor_ai'),
            ['rows' => 3, 'cols' => 50, 'maxlength' => self::WELCOME_MESSAGE_MAX_LENGTH]
        );
        $mform->setType('welcomemessage', PARAM_TEXT);
        $mform->addHelpButton('welcomemessage', 'welcomemessage', 'block_mytutor_ai');
        $mform->addRule(
            'welcomemessage',
            get_string('maximumchars', '', self::WELCOME_MESSAGE_MAX_LENGTH),
            'maxlength',
            self::WELCOME_MESSAGE_MAX_LENGTH,
            'client'
        );

        // Quick replies.
        $mform->addElement(
            'textarea',
            'quickreplies',
            get_string('quickreplies', 'block_mytutor_ai'),
            ['rows' => 5, 'cols' => 50]
        );
        $mform->setType('quickreplies', PARAM_TEXT);
        $mform->addHelpButton('quickreplies', 'quickreplies', 'block_mytutor_ai');

        // Disclaimer.
        $mform->addElement(
            'textarea',
            'disclaimer',
            get_string('disclaimer', 'block_mytutor_ai'),
            ['rows' => 3, 'cols' => 50, 'maxlength' => self::DISCLAIMER_MAX_LENGTH]
        );
        $mform->setType('disclaimer', PARAM_TEXT);
        $mform->addHelpButton('disclaimer', 'disclaimer', 'block_mytutor_ai');
        $mform->addRule(
            'disclaimer',
            get_string('maximumchars', '', self::DISCLAIMER_MAX_LENGTH),
            'maxlength',
            self::DISCLAIMER_MAX_LENGTH,
            'client'
        );

        // Live preview panel.
        $previewhtml = $this->get_preview_html();
        $mform->addElement(
            'html',
            \html_writer::div(
                $previewhtml,
                'block-mytutor-ai-personalization-preview',
                ['id' => 'block-mytutor-ai-personalization-preview']
            )
        );

        $this->add_action_buttons();
    }

    /**
     * Validate the form data.
     *
     * @param array $data Form data.
     * @param array $files Uploaded files.
     * @return array Validation errors.
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);
        $colorfields = ['chat_bg_color', 'user_msg_color', 'assistant_msg_color', 'accent_color', 'chat_text_color'];

        foreach ($colorfields as $field) {
            if (!empty($data[$field]) && !preg_match('/^#([0-9a-fA-F]{3}){1,2}$/', $data[$field])) {
                $errors[$field] = get_string('invalidcolour', 'block_mytutor_ai', $data[$field]);
            }
        }

        $this->validate_text_max_length($errors, $data, 'tutorname', self::TUTOR_NAME_MAX_LENGTH);
        $this->validate_text_max_length($errors, $data, 'tutorcaption', self::TUTOR_CAPTION_MAX_LENGTH);
        $this->validate_text_max_length($errors, $data, 'welcomemessage', self::WELCOME_MESSAGE_MAX_LENGTH);
        $this->validate_text_max_length($errors, $data, 'disclaimer', self::DISCLAIMER_MAX_LENGTH);

        $quickreplies = preg_split('/\R/u', (string) ($data['quickreplies'] ?? ''));
        $quickreplies = array_values(array_filter(array_map('trim', $quickreplies), static function(string $line): bool {
            return $line !== '';
        }));

        if (count($quickreplies) > self::QUICK_REPLIES_MAX_COUNT) {
            $errors['quickreplies'] = get_string(
                'quickrepliesmaxcount',
                'block_mytutor_ai',
                self::QUICK_REPLIES_MAX_COUNT
            );
        } else {
            foreach ($quickreplies as $reply) {
                if (\core_text::strlen($reply) > self::QUICK_REPLY_MAX_LENGTH) {
                    $errors['quickreplies'] = get_string(
                        'quickrepliesmaxlength',
                        'block_mytutor_ai',
                        self::QUICK_REPLY_MAX_LENGTH
                    );
                    break;
                }
            }
        }

        return $errors;
    }

    /**
     * Validate a text field maximum length.
     *
     * @param array $errors Current validation errors.
     * @param array $data Submitted data.
     * @param string $field Field name.
     * @param int $maxlength Maximum allowed characters.
     * @return void
     */
    private function validate_text_max_length(array &$errors, array $data, string $field, int $maxlength): void {
        $value = trim((string) ($data[$field] ?? ''));
        if (\core_text::strlen($value) > $maxlength) {
            $errors[$field] = get_string('maximumchars', '', $maxlength);
        }
    }

    /**
     * Build the static HTML for the live chat preview panel via mustache.
     *
     * @return string
     */
    private function get_preview_html(): string {
        global $PAGE, $OUTPUT;

        $PAGE->requires->js_call_amd('block_mytutor_ai/config_preview', 'init');

        $avatars = [];
        for ($i = 1; $i <= 6; $i++) {
            $num = str_pad($i, 2, '0', STR_PAD_LEFT);
            $avatars[] = [
                'num' => $num,
                'url' => (new \moodle_url("/blocks/mytutor_ai/pix/avatars/avatar_{$num}.png"))->out(false),
            ];
        }

        return $OUTPUT->render_from_template('block_mytutor_ai/config_preview', [
            'avatars' => $avatars,
        ]);
    }
}
