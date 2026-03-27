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

import Notification from 'core/notification';
import * as Repository from 'block_mytutor_ai/repository';

/**
 * Chat interface module for the MyTutor AI block.
 *
 * @module     block_mytutor_ai/chat
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const SELECTORS = {
    EMPTY_STATE: '[data-region="mytutor-ai-empty"]',
    INPUT: '[data-action="mytutor-ai-question"]',
    MESSAGES_CONTAINER: '[data-region="mytutor-ai-messages"]',
    SEND_BUTTON: '[data-action="mytutor-ai-send"]',
    QUICK_REPLY: '[data-action="mytutor-ai-quick-reply"]',
};

/**
 * Create a chat bubble element.
 *
 * @param {String} role Message role.
 * @param {String} label Header label.
 * @param {String} message Bubble text.
 * @param {Boolean} isLoading Whether the message is a loading placeholder.
 * @param {String} avatarUrl Optional avatar URL for assistant messages.
 * @return {HTMLElement}
 */
const createMessageElement = (role, label, message, isLoading = false, avatarUrl = '') => {
    const wrapper = document.createElement('div');
    wrapper.className = `mytutor-ai-message mytutor-ai-message-${role}`;
    if (isLoading) {
        wrapper.classList.add('is-loading');
    }

    const meta = document.createElement('div');
    meta.className = 'mytutor-ai-message-meta';
    meta.textContent = label;

    const body = document.createElement('div');
    body.className = 'mytutor-ai-message-body';
    body.textContent = message;

    wrapper.append(meta, body);

    // Wrap assistant messages with avatar in a flex row.
    if (role === 'assistant' && avatarUrl) {
        const row = document.createElement('div');
        row.className = 'mytutor-ai-message-assistant-row';
        const img = document.createElement('img');
        img.className = 'mytutor-ai-avatar';
        img.src = avatarUrl;
        img.alt = label;
        row.append(img, wrapper);
        return row;
    }

    return wrapper;
};

/**
 * Scroll the conversation to the latest message.
 *
 * @param {HTMLElement} container Message container.
 */
const scrollToBottom = (container) => {
    container.scrollTop = container.scrollHeight;
};

/**
 * Remove the empty state the first time a message is added.
 *
 * @param {HTMLElement} container Block root element.
 */
const clearEmptyState = (container) => {
    const emptyState = container.querySelector(SELECTORS.EMPTY_STATE);
    if (emptyState) {
        emptyState.remove();
    }
};

/**
 * Disable or enable the composer.
 *
 * @param {HTMLTextAreaElement} input Composer textarea.
 * @param {HTMLButtonElement} sendButton Composer button.
 * @param {Boolean} disabled Whether controls should be disabled.
 */
const setComposerState = (input, sendButton, disabled) => {
    input.disabled = disabled;
    sendButton.disabled = disabled;
};

/**
 * Initialise the chat interface.
 *
 * @param {string} containerId The ID of the chat container element.
 * @param {string} avatarUrl Optional tutor avatar URL.
 */
export const init = (containerId, avatarUrl = '') => {
    const container = document.getElementById(containerId);
    if (!container) {
        return;
    }

    const messagesContainer = container.querySelector(SELECTORS.MESSAGES_CONTAINER);
    const sendButton = container.querySelector(SELECTORS.SEND_BUTTON);
    const input = container.querySelector(SELECTORS.INPUT);

    if (!messagesContainer || !sendButton || !input) {
        return;
    }

    const tutorAvatar = avatarUrl || container.dataset.tutorAvatar || '';
    let isSubmitting = false;

    const submitQuestion = async() => {
        if (isSubmitting) {
            return;
        }

        const question = input.value.trim();
        if (!question) {
            return;
        }

        const courseId = parseInt(container.dataset.courseid, 10);
        const assistantLabel = container.dataset.assistantLabel || 'Assistant';
        const userLabel = container.dataset.userLabel || 'You';
        const thinkingText = container.dataset.thinkingText || 'Thinking...';
        const errorText = container.dataset.errorText || 'Something went wrong.';

        isSubmitting = true;
        setComposerState(input, sendButton, true);
        clearEmptyState(container);

        messagesContainer.appendChild(createMessageElement('user', userLabel, question));
        const loadingMessage = createMessageElement('assistant', assistantLabel, thinkingText, true, tutorAvatar);
        messagesContainer.appendChild(loadingMessage);
        scrollToBottom(messagesContainer);
        input.value = '';

        try {
            const response = await Repository.askQuestion(courseId, question);
            const providerLabel = response.chatprovider ? `${assistantLabel} · ${response.chatprovider}` : assistantLabel;
            const assistantMessage = createMessageElement('assistant', providerLabel, response.answer, false, tutorAvatar);
            loadingMessage.replaceWith(assistantMessage);
            scrollToBottom(messagesContainer);
        } catch (error) {
            loadingMessage.replaceWith(createMessageElement('assistant', assistantLabel, errorText, false, tutorAvatar));
            Notification.exception(error);
            scrollToBottom(messagesContainer);
        } finally {
            isSubmitting = false;
            setComposerState(input, sendButton, false);
            input.focus();
        }
    };

    sendButton.addEventListener('click', submitQuestion);

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            submitQuestion();
        }
    });

    // Quick reply chip click.
    messagesContainer.addEventListener('click', (e) => {
        const btn = e.target.closest(SELECTORS.QUICK_REPLY);
        if (!btn) {
            return;
        }
        input.value = btn.textContent.trim();
        submitQuestion();
    });
};
