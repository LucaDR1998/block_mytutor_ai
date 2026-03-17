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

namespace block_mytutor_ai\local\provider\vector;
use block_mytutor_ai\local\provider\provider_catalog;
use block_mytutor_ai\local\provider\vector_store_interface;

/**
 * Stub Qdrant backend.
 *
 * @package    block_mytutor_ai
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qdrant_store implements vector_store_interface
{
    // TODO

    /**
     * Return the provider short name.
     *
     * @return string
     */
    public function get_name(): string
    {
        return 'qdrant';
    }
    /**
     * Run a local readiness check.
     *
     * @return array
     */
    public function ping(): array
    {
        $endpoint = (string) get_config('block_mytutor_ai', 'qdrantendpoint');

        if ($endpoint === '') {
            return [
                'success' => false,
                'message' => get_string(
                    'providerrequiresendpoint',
                    'block_mytutor_ai',
                    provider_catalog::get_provider_label('vector', $this->get_name())
                ),
            ];
        }

        return [
            'success' => true,
            'message' => get_string(
                'providerstubready',
                'block_mytutor_ai',
                provider_catalog::get_provider_label('vector', $this->get_name())
            ),
        ];
    }
    /**
     * Placeholder upsert implementation.
     *
     * @param int $courseid Course identifier.
     * @param array $chunks Indexed chunk payloads.
     * @return void
     */
    public function upsert_chunks(int $courseid, array $chunks): void
    {
        return;
    }
    /**
     * Placeholder vector search.
     *
     * @param int $courseid Course identifier.
     * @param array $embedding Query embedding.
     * @param int $limit Maximum records to return.
     * @return array
     */
    public function search(int $courseid, array $embedding, int $limit = 5): array
    {
        return [];
    }
}
