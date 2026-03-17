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
 * Live preview module for the chat personalization admin settings.
 *
 * @module     block_mytutor_ai/config_preview
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Attach a native colour swatch next to a text input and sync them bidirectionally.
 *
 * @param {HTMLInputElement} textInput The text input element.
 */
const attachColorSwatch = (textInput) => {
    const swatch = document.createElement('input');
    swatch.type = 'color';
    swatch.className = 'mytutor-ai-color-swatch ml-2';
    swatch.value = textInput.value || '#ffffff';
    swatch.style.cssText = 'width:36px;height:36px;border:1px solid #ccc;'
        + 'border-radius:4px;cursor:pointer;padding:0;vertical-align:middle;';

    // Insert swatch after the text input.
    textInput.parentNode.insertBefore(swatch, textInput.nextSibling);

    // Sync swatch → text.
    swatch.addEventListener('input', () => {
        textInput.value = swatch.value;
        textInput.dispatchEvent(new Event('input', {bubbles: true}));
    });

    // Sync text → swatch.
    textInput.addEventListener('input', () => {
        if (/^#([0-9a-fA-F]{3}){1,2}$/.test(textInput.value)) {
            swatch.value = textInput.value;
        }
    });
};

/**
 * Initialise the config preview panel.
 */
export const init = () => {
    const preview = document.getElementById('mytutor-ai-config-preview');
    if (!preview) {
        return;
    }

    const chatContainer = preview.querySelector('.block-mytutor-ai-chat');
    const userBubble = preview.querySelector('.mytutor-ai-message-user');
    const assistantBubble = preview.querySelector('.mytutor-ai-message-assistant');
    const avatarImg = preview.querySelector('[data-region="preview-avatar"]');
    const assistantLabel = preview.querySelector('[data-region="preview-assistant-label"]');
    const welcomeSpan = preview.querySelector('[data-region="preview-welcome"]');
    const headerEl = preview.querySelector('[data-region="preview-header"]');
    const headerAvatar = preview.querySelector('[data-region="preview-header-avatar"]');
    const headerName = preview.querySelector('[data-region="preview-header-name"]');

    // Colour inputs.
    const chatBgInput = document.getElementById('id_chat_bg_color');
    const userMsgInput = document.getElementById('id_user_msg_color');
    const assistantMsgInput = document.getElementById('id_assistant_msg_color');

    // Attach colour swatches.
    [chatBgInput, userMsgInput, assistantMsgInput].forEach((input) => {
        if (input) {
            attachColorSwatch(input);
        }
    });

    // Update preview colours on input changes.
    const updateColors = () => {
        if (chatContainer && chatBgInput) {
            chatContainer.style.background = chatBgInput.value || 'transparent';
        }
        if (userBubble && userMsgInput) {
            userBubble.style.background = userMsgInput.value || '#0f6cbf';
        }
        if (assistantBubble && assistantMsgInput) {
            assistantBubble.style.background = assistantMsgInput.value || '#f7f9fc';
        }
    };

    [chatBgInput, userMsgInput, assistantMsgInput].forEach((input) => {
        if (input) {
            input.addEventListener('input', updateColors);
        }
    });

    // Apply initial colours.
    updateColors();

    /**
     * Show or hide the avatar across the preview (header + message).
     *
     * @param {String} url Avatar image URL or empty to hide.
     */
    const setAvatarUrl = (url) => {
        if (avatarImg) {
            avatarImg.src = url || '';
            avatarImg.style.display = url ? '' : 'none';
        }
        if (headerEl && headerAvatar) {
            headerAvatar.src = url || '';
            headerEl.style.display = url ? '' : 'none';
        }
    };

    // Filemanager thumbnail scanner — reusable.
    const fmItem = document.getElementById('id_customavatar')
        ?.closest('.fitem');
    const scanFilemanager = () => {
        if (!fmItem) {
            return;
        }
        const img = fmItem.querySelector(
            '.fp-file img, '
            + '.fp-thumbnail img, '
            + '.filemanager .realpreview, '
            + '.filemanager-container .fp-thumbnail img'
        );
        if (img && img.src && !img.src.endsWith('/f1')) {
            setAvatarUrl(img.src);
        }
    };

    // Watch filemanager DOM for custom avatar uploads.
    if (fmItem) {
        const fmObserver = new MutationObserver(scanFilemanager);
        fmObserver.observe(fmItem, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['src'],
        });
    }

    // Avatar selector.
    const avatarSelect = document.getElementById('id_tutoravatar');
    if (avatarSelect) {
        const updateAvatar = () => {
            const val = avatarSelect.value;
            if (val && val !== 'custom') {
                const url = preview.dataset['avatar' + val];
                setAvatarUrl(url || '');
            } else if (val === 'custom') {
                scanFilemanager();
            } else {
                setAvatarUrl('');
            }
        };
        avatarSelect.addEventListener('change', updateAvatar);
        updateAvatar();
    }

    // Tutor name — update both message label and header name.
    const tutornameInput = document.getElementById('id_tutorname');
    const defaultLabel = assistantLabel
        ? assistantLabel.textContent : '';
    const updateTutorName = () => {
        if (!tutornameInput) {
            return;
        }
        const name = tutornameInput.value.trim() || defaultLabel;
        if (assistantLabel) {
            assistantLabel.textContent = name;
        }
        if (headerName) {
            headerName.textContent = name;
        }
    };
    if (tutornameInput) {
        tutornameInput.addEventListener('input', updateTutorName);
        updateTutorName();
    }

    // Welcome message.
    const welcomeInput = document.getElementById('id_welcomemessage');
    const defaultWelcome = welcomeSpan
        ? welcomeSpan.textContent : '';
    const updateWelcome = () => {
        if (!welcomeInput || !welcomeSpan) {
            return;
        }
        welcomeSpan.textContent =
            welcomeInput.value.trim() || defaultWelcome;
    };
    if (welcomeInput && welcomeSpan) {
        welcomeInput.addEventListener('input', updateWelcome);
        updateWelcome();
    }
};
