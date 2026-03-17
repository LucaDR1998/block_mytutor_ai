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
 * Provider labels and option catalog.
 *
 * @package    block_mytutor_ai
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_catalog {
    /**
     * Chat providers available in the skeleton.
     *
     * @return array
     */
    public static function get_chat_provider_options(): array {
        return [
            'gemini' => get_string('providergemini', 'block_mytutor_ai'),
            'openai' => get_string('provideropenai', 'block_mytutor_ai'),
            'ollama' => get_string('providerollama', 'block_mytutor_ai'),
        ];
    }

    /**
     * Embedding providers available in the skeleton.
     *
     * @return array
     */
    public static function get_embedding_provider_options(): array {
        return [
            'gemini' => get_string('providergemini', 'block_mytutor_ai'),
            'openai' => get_string('provideropenai', 'block_mytutor_ai'),
            'ollama' => get_string('providerollama', 'block_mytutor_ai'),
        ];
    }

    /**
     * Vector stores available in the skeleton.
     *
     * @return array
     */
    public static function get_vector_store_options(): array {
        return [
            'pgvector' => get_string('providerpgvector', 'block_mytutor_ai'),
            'qdrant' => get_string('providerqdrant', 'block_mytutor_ai'),
        ];
    }

    /**
     * Resolve a provider label by type and internal name.
     *
     * @param string $type Provider type.
     * @param string $name Provider short name.
     * @return string
     */
    public static function get_provider_label(string $type, string $name): string {
        $options = match ($type) {
            'chat' => self::get_chat_provider_options(),
            'embedding' => self::get_embedding_provider_options(),
            'vector' => self::get_vector_store_options(),
            default => [],
        };

        return $options[$name] ?? $name;
    }

    /**
     * Resolve a Moodle AI provider display name from its component name.
     *
     * @param string $component Full plugin component, for example aiprovider_gemini.
     * @return string
     */
    public static function get_core_ai_provider_label(string $component): string {
        $plugininfo = \core_plugin_manager::instance()->get_plugin_info($component);
        if ($plugininfo) {
            return $plugininfo->displayname;
        }

        return $component;
    }
}
