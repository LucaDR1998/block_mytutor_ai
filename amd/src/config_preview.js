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

/** @type {Object<string, {chat_bg_color: string, user_msg_color: string, assistant_msg_color: string, accent_color: string}>} */
const THEME_PRESETS = {
    light: {chat_bg_color: '#ffffff', user_msg_color: '#0f6cbf', assistant_msg_color: '#f7f9fc',
        accent_color: '#0f6cbf', chat_text_color: '#162134'},
    dark: {chat_bg_color: '#1e1e2e', user_msg_color: '#6c5ce7', assistant_msg_color: '#2d2d3f',
        accent_color: '#a29bfe', chat_text_color: '#e2e2f0'},
    ocean: {chat_bg_color: '#f0f7ff', user_msg_color: '#0077b6', assistant_msg_color: '#e8f4f8',
        accent_color: '#00b4d8', chat_text_color: '#1a3a4a'},
    warm: {chat_bg_color: '#fdf6ec', user_msg_color: '#e17055', assistant_msg_color: '#ffeaa7',
        accent_color: '#d63031', chat_text_color: '#3d2c1e'},
};

/** @type {Object<string, number>} */
const TEXT_LIMITS = {
    tutorname: 40,
    tutorcaption: 60,
    welcomemessage: 160,
    disclaimer: 180,
    quickreply: 40,
    quickreplycount: 5,
};

/**
 * Apply or clear a CSS custom property on an element.
 *
 * @param {HTMLElement|null} element Target element.
 * @param {String} property CSS custom property name.
 * @param {String} value Property value.
 */
const setCssCustomProperty = (element, property, value) => {
    if (!element) {
        return;
    }

    if (value) {
        element.style.setProperty(property, value);
        return;
    }

    element.style.removeProperty(property);
};

/**
 * Clamp preview text to a safe maximum length.
 *
 * @param {String} value Source text.
 * @param {Number} maxlength Maximum allowed characters.
 * @return {String}
 */
const clampText = (value, maxlength) => {
    const text = (value || '').trim();
    if (text.length <= maxlength) {
        return text;
    }

    return `${text.slice(0, maxlength - 1).trimEnd()}…`;
};

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
    const avatarImg = preview.querySelector('[data-region="preview-avatar"]');
    const assistantLabel = preview.querySelector('[data-region="preview-assistant-label"]');
    const welcomeSpan = preview.querySelector('[data-region="preview-welcome"]');
    const headerEl = preview.querySelector('[data-region="preview-header"]');
    const headerAvatar = preview.querySelector('[data-region="preview-header-avatar"]');
    const headerName = preview.querySelector('[data-region="preview-header-name"]');
    const captionEl = preview.querySelector('[data-region="preview-header-caption"]');
    const quickRepliesContainer = preview.querySelector('[data-region="preview-quick-replies"]');
    const disclaimerEl = preview.querySelector('[data-region="preview-disclaimer"]');

    // Colour inputs.
    const chatBgInput = document.getElementById('id_chat_bg_color');
    const userMsgInput = document.getElementById('id_user_msg_color');
    const assistantMsgInput = document.getElementById('id_assistant_msg_color');
    const accentInput = document.getElementById('id_accent_color');
    const chatTextInput = document.getElementById('id_chat_text_color');

    // Attach colour swatches.
    [chatBgInput, userMsgInput, assistantMsgInput, accentInput, chatTextInput].forEach((input) => {
        if (input) {
            attachColorSwatch(input);
        }
    });

    // Update preview colours on input changes.
    const updateColors = () => {
        setCssCustomProperty(chatContainer, '--mytutor-chat-bg', chatBgInput ? chatBgInput.value : '');
        setCssCustomProperty(chatContainer, '--mytutor-user-bg', userMsgInput ? userMsgInput.value : '');
        setCssCustomProperty(chatContainer, '--mytutor-assistant-bg', assistantMsgInput ? assistantMsgInput.value : '');
        setCssCustomProperty(chatContainer, '--mytutor-chat-text', chatTextInput ? chatTextInput.value : '');
    };

    [chatBgInput, userMsgInput, assistantMsgInput, chatTextInput].forEach((input) => {
        if (input) {
            input.addEventListener('input', updateColors);
        }
    });

    // Update accent-driven elements in the preview.
    const updateAccent = () => {
        setCssCustomProperty(chatContainer, '--mytutor-accent', accentInput ? accentInput.value : '');
    };
    if (accentInput) {
        accentInput.addEventListener('input', updateAccent);
    }

    // Apply initial colours.
    updateColors();
    updateAccent();

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

    // Avatar selector.
    const avatarSelect = document.getElementById('id_tutoravatar');

    // Filemanager thumbnail scanner — reusable.
    const fmItem = document.getElementById('id_customavatar')
        ?.closest('.fitem');
    const scanFilemanager = () => {
        if (!fmItem || !avatarSelect || avatarSelect.value !== 'custom') {
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
            return;
        }

        setAvatarUrl('');
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

    if (avatarSelect) {
        const updateAvatar = () => {
            const val = avatarSelect.value;
            if (val && val !== 'custom') {
                const url = preview.dataset['avatar' + val];
                setAvatarUrl(url || '');
            } else if (val === 'custom') {
                setAvatarUrl('');
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
        const name = clampText(tutornameInput.value, TEXT_LIMITS.tutorname) || defaultLabel;
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
        welcomeSpan.textContent = clampText(welcomeInput.value, TEXT_LIMITS.welcomemessage) || defaultWelcome;
    };
    if (welcomeInput && welcomeSpan) {
        welcomeInput.addEventListener('input', updateWelcome);
        updateWelcome();
    }

    // Theme preset selector.
    const presetSelect = document.getElementById('id_themepreset');
    if (presetSelect) {
        presetSelect.addEventListener('change', () => {
            const preset = THEME_PRESETS[presetSelect.value];
            if (!preset) {
                return;
            }
            const mapping = {
                chat_bg_color: chatBgInput,
                user_msg_color: userMsgInput,
                assistant_msg_color: assistantMsgInput,
                accent_color: accentInput,
                chat_text_color: chatTextInput,
            };
            Object.entries(mapping).forEach(([key, inp]) => {
                if (inp && preset[key]) {
                    inp.value = preset[key];
                    const swatch = inp.nextElementSibling;
                    if (swatch && swatch.type === 'color') {
                        swatch.value = preset[key];
                    }
                    inp.dispatchEvent(new Event('input', {bubbles: true}));
                }
            });
        });
    }

    // Tutor caption.
    const captionInput = document.getElementById('id_tutorcaption');
    if (captionInput) {
        const updateCaption = () => {
            if (captionEl) {
                const caption = clampText(captionInput.value, TEXT_LIMITS.tutorcaption);
                captionEl.textContent = caption;
                captionEl.style.display = caption ? '' : 'none';
            }
        };
        captionInput.addEventListener('input', updateCaption);
        updateCaption();
    }

    // Disclaimer.
    const disclaimerInput = document.getElementById('id_disclaimer');
    if (disclaimerInput) {
        const updateDisclaimer = () => {
            if (disclaimerEl) {
                const text = clampText(disclaimerInput.value, TEXT_LIMITS.disclaimer);
                disclaimerEl.textContent = text || '';
                disclaimerEl.style.display = text ? '' : 'none';
            }
        };
        disclaimerInput.addEventListener('input', updateDisclaimer);
        updateDisclaimer();
    }

    // Quick replies.
    const quickRepliesInput = document.getElementById('id_quickreplies');
    if (quickRepliesInput) {
        const updateQuickReplies = () => {
            if (!quickRepliesContainer) {
                return;
            }
            const lines = quickRepliesInput.value.split('\n')
                .map(line => clampText(line, TEXT_LIMITS.quickreply))
                .filter(Boolean)
                .slice(0, TEXT_LIMITS.quickreplycount);
            quickRepliesContainer.innerHTML = '';
            lines.forEach((line) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'mytutor-ai-quick-reply';
                btn.textContent = line;
                quickRepliesContainer.appendChild(btn);
            });
            quickRepliesContainer.style.display = lines.length ? '' : 'none';
            updateAccent();
        };
        quickRepliesInput.addEventListener('input', updateQuickReplies);
        updateQuickReplies();
    }
};
