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

import Ajax from 'core/ajax';

/**
 * Repository for block_mytutor_ai webservice calls.
 *
 * @module     block_mytutor_ai/repository
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Ask a course-scoped question through block_mytutor_ai.
 *
 * @param {Number} courseid Course identifier.
 * @param {String} question User question.
 * @return {Promise}
 */
export const askQuestion = (courseid, question) => {
    return Ajax.call([{
        methodname: 'block_mytutor_ai_ask_question',
        args: {
            courseid,
            question,
        },
    }])[0];
};
