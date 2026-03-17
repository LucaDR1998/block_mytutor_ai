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

use block_mytutor_ai\local\provider\embedding\gemini_provider as gemini_embedding_provider;
use block_mytutor_ai\local\provider\embedding\ollama_provider as ollama_embedding_provider;
use block_mytutor_ai\local\provider\embedding\openai_provider as openai_embedding_provider;
use block_mytutor_ai\local\provider\vector\pgvector_store;
use block_mytutor_ai\local\provider\vector\qdrant_store;

/**
 * Factory for the local RAG provider layer.
 *
 * @package    block_mytutor_ai
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_factory {
    /**
     * Create the configured embedding provider.
     *
     * @param string|null $provider Optional provider override.
     * @return embedding_provider_interface
     */
    public static function create_embedding_provider(?string $provider = null): embedding_provider_interface {
        $provider = $provider ?? self::get_active_embedding_provider_name();

        return match ($provider) {
            'gemini' => new gemini_embedding_provider(),
            'openai' => new openai_embedding_provider(),
            'ollama' => new ollama_embedding_provider(),
            default => throw new \coding_exception('Unsupported embedding provider: ' . $provider),
        };
    }

    /**
     * Create the configured vector store.
     *
     * @param string|null $provider Optional provider override.
     * @return vector_store_interface
     */
    public static function create_vector_store(?string $provider = null): vector_store_interface {
        $provider = $provider ?? self::get_active_vector_store_name();

        return match ($provider) {
            'pgvector' => new pgvector_store(),
            'qdrant' => new qdrant_store(),
            default => throw new \coding_exception('Unsupported vector store: ' . $provider),
        };
    }

    /**
     * Return the active embedding provider name.
     *
     * @return string
     */
    public static function get_active_embedding_provider_name(): string {
        return self::get_configured_provider('embeddingprovider', 'openai');
    }

    /**
     * Return the active vector store name.
     *
     * @return string
     */
    public static function get_active_vector_store_name(): string {
        return self::get_configured_provider('vectorstore', 'qdrant');
    }

    /**
     * Read the configured provider name with a fallback.
     *
     * @param string $configkey Config key.
     * @param string $fallback Fallback provider.
     * @return string
     */
    private static function get_configured_provider(string $configkey, string $fallback): string {
        $configured = (string) get_config('block_mytutor_ai', $configkey);
        return $configured !== '' ? $configured : $fallback;
    }
}
