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

namespace block_mytutor_ai\local\rag;

use block_mytutor_ai\local\provider\provider_factory;
use block_mytutor_ai\persistent\chunk;
use block_mytutor_ai\persistent\document;
use block_mytutor_ai\persistent\index_queue;
use block_mytutor_ai\task\reindex_course_task;

/**
 * Manage indexing jobs and the local source/chunk store.
 *
 * @package    block_mytutor_ai
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class index_manager {
    /** @var string Queue status: queued. */
    public const STATUS_QUEUED = 'queued';

    /** @var string Queue status: running. */
    public const STATUS_RUNNING = 'running';

    /** @var string Queue status: completed. */
    public const STATUS_COMPLETED = 'completed';

    /** @var string Queue status: failed. */
    public const STATUS_FAILED = 'failed';

    /** @var string Queue status: never run. */
    public const STATUS_NEVER = 'never';

    /** @var content_extractor Extractor instance. */
    private content_extractor $extractor;

    /** @var chunk_processor Chunk processor instance. */
    private chunk_processor $chunkprocessor;

    /**
     * Constructor.
     *
     * @param content_extractor|null $extractor Optional extractor override.
     * @param chunk_processor|null $chunkprocessor Optional chunk processor override.
     */
    public function __construct(?content_extractor $extractor = null, ?chunk_processor $chunkprocessor = null) {
        $this->extractor = $extractor ?? new content_extractor();
        $this->chunkprocessor = $chunkprocessor ?? new chunk_processor();
    }

    /**
     * Queue an asynchronous course reindex.
     *
     * @param int $courseid Course identifier.
     * @return int
     */
    public function queue_course_reindex(int $courseid): int {
        $time = time();
        $queue = new index_queue(0, (object) [
            'courseid' => $courseid,
            'status' => self::STATUS_QUEUED,
            'errormessage' => null,
            'timecreated' => $time,
            'timestarted' => 0,
            'timecompleted' => 0,
        ]);
        $queue->create();

        reindex_course_task::queue((int) $queue->get('id'), $courseid);

        return (int) $queue->get('id');
    }

    /**
     * Execute a course reindex synchronously.
     *
     * @param int $courseid Course identifier.
     * @param int $queueid Optional queue entry identifier.
     * @return array
     */
    public function run_course_reindex(int $courseid, int $queueid = 0): array {
        $queue = $queueid ? index_queue::get_record(['id' => $queueid], MUST_EXIST) : false;

        if ($queue) {
            $queue->set('status', self::STATUS_RUNNING);
            $queue->set('timestarted', time());
            $queue->update();
        }

        try {
            $course = get_course($courseid);
            $items = $this->extractor->extract_course_content($course);
            $this->replace_course_index($courseid, $items);

            $result = [
                'documents' => document::count_records(['courseid' => $courseid]),
                'chunks' => chunk::count_records(['courseid' => $courseid]),
            ];

            if ($queue) {
                $queue->set('status', self::STATUS_COMPLETED);
                $queue->set('errormessage', null);
                $queue->set('timecompleted', time());
                $queue->update();
            }

            return $result;
        } catch (\Throwable $exception) {
            if ($queue) {
                $queue->set('status', self::STATUS_FAILED);
                $queue->set('errormessage', $exception->getMessage());
                $queue->set('timecompleted', time());
                $queue->update();
            }

            throw $exception;
        }
    }

    /**
     * Return the current course indexing status.
     *
     * @param int $courseid Course identifier.
     * @return array
     */
    public function get_course_index_status(int $courseid): array {
        $latestrecords = index_queue::get_records(['courseid' => $courseid], 'timecreated', 'DESC', 0, 1);
        $latest = $latestrecords[0] ?? null;

        return [
            'documents' => document::count_records(['courseid' => $courseid]),
            'chunks' => chunk::count_records(['courseid' => $courseid]),
            'status' => $latest ? (string) $latest->get('status') : self::STATUS_NEVER,
            'timecompleted' => $latest ? (int) $latest->get('timecompleted') : 0,
            'errormessage' => $latest ? (string) ($latest->get('errormessage') ?? '') : '',
        ];
    }

    /**
     * Replace the course index with new content items.
     *
     * @param int $courseid Course identifier.
     * @param array $items Extracted content items.
     * @return void
     */
    private function replace_course_index(int $courseid, array $items): void {
        $this->delete_course_index($courseid);

        $time = time();
        $vectors = [];
        foreach ($items as $item) {
            $document = new document(0, (object) [
                'courseid' => $courseid,
                'cmid' => (int) ($item['cmid'] ?? 0),
                'contextid' => (int) $item['contextid'],
                'component' => (string) $item['component'],
                'itemtype' => (string) $item['itemtype'],
                'itemid' => (int) ($item['itemid'] ?? 0),
                'title' => (string) $item['title'],
                'sourcehash' => sha1((string) $item['content']),
                'timemodified' => $time,
                'timecreated' => $time,
            ]);
            $document->create();

            $chunksize = max(200, (int) get_config('block_mytutor_ai', 'chunksize'));
            $overlap = max(0, (int) get_config('block_mytutor_ai', 'chunkoverlap'));
            $chunks = $this->chunkprocessor->chunk_text((string) $item['content'], $chunksize, $overlap);

            foreach ($chunks as $index => $content) {
                $chunk = new chunk(0, (object) [
                    'documentid' => (int) $document->get('id'),
                    'courseid' => $courseid,
                    'contextid' => (int) $item['contextid'],
                    'sequencenumber' => $index + 1,
                    'content' => $content,
                    'embeddingref' => null,
                    'metadata' => $this->build_metadata_json($item),
                    'timemodified' => $time,
                    'timecreated' => $time,
                ]);
                $chunk->create();

                $vectors[] = [
                    'chunkid' => (int) $chunk->get('id'),
                    'documentid' => (int) $document->get('id'),
                    'content' => $content,
                    'metadata' => $item,
                ];
            }
        }

        if (!empty($vectors)) {
            $embeddingprovider = provider_factory::create_embedding_provider();
            $vectorstore = provider_factory::create_vector_store();
            $embeddings = $embeddingprovider->embed_texts(array_column($vectors, 'content'));

            foreach ($vectors as $index => $vector) {
                $vectors[$index]['embedding'] = $embeddings[$index] ?? [];
            }

            $vectorstore->upsert_chunks($courseid, $vectors);
        }
    }

    /**
     * Delete the local index for a course.
     *
     * @param int $courseid Course identifier.
     * @return void
     */
    private function delete_course_index(int $courseid): void {
        foreach (chunk::get_records(['courseid' => $courseid]) as $chunkrecord) {
            $chunkrecord->delete();
        }

        foreach (document::get_records(['courseid' => $courseid]) as $documentrecord) {
            $documentrecord->delete();
        }
    }

    /**
     * Encode chunk metadata as JSON.
     *
     * @param array $item Source metadata.
     * @return string
     */
    private function build_metadata_json(array $item): string {
        $metadata = [
            'component' => $item['component'] ?? '',
            'itemtype' => $item['itemtype'] ?? '',
            'itemid' => $item['itemid'] ?? 0,
            'title' => $item['title'] ?? '',
        ];

        return json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}';
    }
}
