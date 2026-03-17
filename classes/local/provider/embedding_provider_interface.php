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

namespace block_mytutor_ai\local\provider;

/**
 * Contract for embedding providers.
 *
 * @package    block_mytutor_ai
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface embedding_provider_interface {
    /**
     * Return the provider short name.
     *
     * @return string
     */
    public function get_name(): string;

    /**
     * Run a local readiness check.
     *
     * @return array
     */
    public function ping(): array;

    /**
     * Generate embeddings for a list of texts.
     *
     * @param array $texts Texts to embed.
     * @return array
     */
    public function embed_texts(array $texts): array;
}
