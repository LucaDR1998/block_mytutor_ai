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

namespace block_mytutor_ai\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use block_mytutor_ai\local\provider\provider_factory;

/**
 * External API to run local provider checks.
 *
 * @package    block_mytutor_ai
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_provider_connection extends external_api {
    /**
     * Parameters definition.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'type' => new external_value(PARAM_ALPHAEXT, 'Provider type: embedding or vector'),
            'provider' => new external_value(PARAM_ALPHAEXT, 'Provider short name'),
        ]);
    }

    /**
     * Execute a readiness check for a provider.
     *
     * @param string $type Provider type.
     * @param string $provider Provider short name.
     * @return array
     */
    public static function execute(string $type, string $provider): array {
        [
            'type' => $type,
            'provider' => $provider,
        ] = self::validate_parameters(self::execute_parameters(), [
            'type' => $type,
            'provider' => $provider,
        ]);

        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('moodle/site:config', $context);

        $instance = match ($type) {
            'embedding' => provider_factory::create_embedding_provider($provider),
            'vector' => provider_factory::create_vector_store($provider),
            default => throw new \invalid_parameter_exception('Unknown provider type: ' . $type),
        };

        $result = $instance->ping();

        return [
            'success' => (bool) ($result['success'] ?? false),
            'message' => (string) ($result['message'] ?? ''),
            'providerclass' => get_class($instance),
        ];
    }

    /**
     * Return definition.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Was the provider ready'),
            'message' => new external_value(PARAM_TEXT, 'Human readable status'),
            'providerclass' => new external_value(PARAM_RAW_TRIMMED, 'Resolved PHP class name'),
        ]);
    }
}
