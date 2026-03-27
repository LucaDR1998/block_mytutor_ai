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

/**
 * Language strings for block_mytutor_ai
 *
 * @package    block_mytutor_ai
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'MyTutor AI';
$string['mytutor_ai:addinstance'] = 'Add a new MyTutor AI block';
$string['mytutor_ai:use'] = 'Use the MyTutor AI block';
$string['mytutor_ai:chat'] = 'Chat with the AI course assistant';
$string['mytutor_ai:manage'] = 'Manage AI indexing for the course';
$string['privacy:metadata'] = 'The MyTutor AI block stores course indexing metadata only and does not currently store personal user data.';

$string['enabled'] = 'Enable MyTutor AI';
$string['enabled_desc'] = 'Enable the course assistant block and the base RAG pipeline.';
$string['settingsskeleton'] = 'Base plugin skeleton';
$string['settingsskeleton_desc'] = 'This first iteration creates the Moodle block structure, admin settings, DB schema and stubbed provider layer needed for the RAG MVP.';
$string['settingsproviders'] = 'Provider configuration';
$string['settingsretrieval'] = 'Retrieval defaults';
$string['chatmanagedbycoreai'] = 'Chat and LLM configuration is managed through Moodle AI. Configure providers in {$a->providerslink}.';

$string['embeddingprovider'] = 'Embedding provider';
$string['embeddingprovider_desc'] = 'Choose the provider used to generate embeddings.';
$string['embeddingprovider_help'] = 'The local embedding provider used to generate vector embeddings from course content. Chat completions are managed through Moodle AI providers.';
$string['vectorstore'] = 'Vector store';
$string['vectorstore_desc'] = 'Choose the vector backend targeted by the indexing pipeline.';
$string['vectorstore_help'] = 'The database backend used to store and search vector embeddings. Qdrant is a standalone service; pgvector requires PostgreSQL with the pgvector extension.';

$string['settingscredentials'] = 'Embedding provider credentials';
$string['settingsvectorstore'] = 'Vector store settings';
$string['apikey'] = 'API key';
$string['apikey_desc'] = 'API key used by the selected embedding provider when it requires one.';
$string['apikey_help'] = 'The API key used to authenticate the embedding provider. Required for OpenAI and Gemini embeddings; not needed for Ollama.';
$string['endpoint'] = 'Provider endpoint';
$string['endpoint_desc'] = 'Base URL for the embedding provider server when running Ollama locally.';
$string['endpoint_help'] = 'Base URL of the embedding provider server. Required for Ollama embeddings (for example http://localhost:11434). Leave empty for cloud embedding providers that use their default endpoints.';
$string['embeddingmodel'] = 'Embedding model';
$string['embeddingmodel_desc'] = 'Model name used by the embedding provider.';
$string['embeddingmodel_help'] = 'The model identifier sent to the embedding provider. Examples: text-embedding-3-small (OpenAI), gemini-embedding-001 (Gemini), nomic-embed-text (Ollama).';
$string['qdrantendpoint'] = 'Qdrant endpoint';
$string['qdrantendpoint_desc'] = 'Base URL for the Qdrant instance used by the vector layer.';
$string['qdrantendpoint_help'] = 'Base URL of the Qdrant server (e.g. http://localhost:6333).';
$string['qdrantcollection'] = 'Qdrant collection';
$string['qdrantcollection_desc'] = 'Collection name reserved for course chunks.';
$string['qdrantcollection_help'] = 'The Qdrant collection name where course chunk embeddings are stored.';
$string['pgvectortablename'] = 'pgvector table name';
$string['pgvectortablename_desc'] = 'Table name reserved for future pgvector-backed storage.';
$string['pgvectortablename_help'] = 'The PostgreSQL table used to store vector embeddings when pgvector is selected.';
$string['chunksize'] = 'Chunk size';
$string['chunksize_desc'] = 'Approximate number of characters per chunk during indexing.';
$string['chunksize_help'] = 'The approximate number of characters per text chunk when course content is split for indexing. Larger chunks retain more context but reduce retrieval precision.';
$string['chunkoverlap'] = 'Chunk overlap';
$string['chunkoverlap_desc'] = 'Number of characters preserved between adjacent chunks.';
$string['chunkoverlap_help'] = 'The number of characters shared between consecutive chunks. Overlap helps avoid losing context at chunk boundaries.';
$string['retrievaltopk'] = 'Top K retrieved chunks';
$string['retrievaltopk_desc'] = 'Maximum number of chunks returned to the prompt builder.';
$string['retrievaltopk_help'] = 'The maximum number of relevant chunks retrieved from the vector store and included in the prompt sent to the chat provider.';

$string['chatheading'] = 'AI Course Assistant';
$string['chatplaceholder'] = 'Ask a question about this course...';
$string['emptyconversation'] = 'Ask a course question to start the conversation.';
$string['thinkingtext'] = 'Thinking...';
$string['assistantlabel'] = 'Assistant';
$string['userlabel'] = 'You';
$string['errortext'] = 'The assistant could not answer right now.';
$string['backenddisabled'] = 'The MyTutor AI backend is disabled in the block settings.';
$string['coreaichatnotconfigured'] = 'No Moodle AI provider is configured for text generation. Configure an enabled AI provider before using the course assistant.';
$string['coreaichatnotconfiguredwithlinks'] = 'No Moodle AI provider is configured for text generation. Configure providers in {$a->providerslink} before using the course assistant.';
$string['coreaigenerationfailed'] = 'The Moodle AI provider could not generate an answer: {$a}';
$string['chatprovidermoodleai'] = 'Moodle AI';
$string['sendbutton'] = 'Send';
$string['nopermission'] = 'You do not have permission to use the AI assistant in this course.';
$string['pluginisdisabled'] = 'MyTutor AI is disabled in the site configuration.';
$string['questionempty'] = 'The question cannot be empty.';

$string['providergemini'] = 'Gemini';
$string['provideropenai'] = 'OpenAI';
$string['providerollama'] = 'Ollama';
$string['providerpgvector'] = 'pgvector';
$string['providerqdrant'] = 'Qdrant';
$string['providerrequiresapikey'] = '{$a} requires an API key before it can be used.';
$string['providerrequiresendpoint'] = '{$a} requires a reachable endpoint URL before it can be used.';
$string['pgvectorrequirespgsql'] = 'pgvector requires Moodle to run on PostgreSQL with the pgvector extension installed.';
$string['providerstubready'] = '{$a} is configured for the skeleton layer. The real network integration is not implemented yet.';

// Chat personalization.
$string['settingspersonalization'] = 'Chat personalization';
$string['chat_bg_color'] = 'Chat background colour';
$string['chat_bg_color_help'] = 'Background colour of the chat container. Enter a hex colour code (e.g. #ffffff).';
$string['user_msg_color'] = 'User message colour';
$string['user_msg_color_help'] = 'Background colour of user message bubbles. Enter a hex colour code (e.g. #0f6cbf).';
$string['assistant_msg_color'] = 'Assistant message colour';
$string['assistant_msg_color_help'] = 'Background colour of assistant message bubbles. Enter a hex colour code (e.g. #f7f9fc).';
$string['tutoravatar'] = 'Tutor avatar';
$string['tutoravatar_help'] = 'Choose a built-in avatar image to display next to assistant messages.';
$string['tutoravatar_none'] = 'None';
$string['tutoravatar_option'] = 'Avatar {$a}';
$string['tutoravatar_custom'] = 'Custom image';
$string['customavatar'] = 'Custom avatar image';
$string['customavatar_help'] = 'Upload a custom image to use as the tutor avatar. This overrides the built-in avatar selection. Recommended size: 64×64 pixels. Max 512 KB.';
$string['tutorname'] = 'Tutor display name';
$string['tutorname_help'] = 'The name shown as the assistant label in chat messages. Leave empty to use the default. Maximum 40 characters.';
$string['welcomemessage'] = 'Welcome message';
$string['welcomemessage_help'] = 'Greeting text shown in the chat when no messages have been sent yet. Leave empty to use the default. Maximum 160 characters.';
$string['welcomemessage_default'] = 'Ask a course question to start the conversation.';
$string['chatpreview'] = 'Chat preview';
$string['chatpreviewsidebarhint'] = 'Expand this section to display the live preview in the right column while you edit the chat settings above.';
$string['invalidcolour'] = 'Invalid colour value "{$a}". Please enter a valid hex colour code (e.g. #0f6cbf).';
$string['previewusermsg'] = 'Hello, can you help me?';
$string['previewassistantmsg'] = 'Of course! Let me explain...';

// Quick replies.
$string['quickreplies'] = 'Quick replies';
$string['quickreplies_help'] = 'Predefined questions shown as clickable buttons below the welcome message. Enter one question per line. Maximum 5 suggested replies, up to 40 characters each.';
$string['quickrepliesmaxcount'] = 'You can add at most {$a} quick replies.';
$string['quickrepliesmaxlength'] = 'Each quick reply can contain at most {$a} characters.';
$string['quickreplies_placeholder'] = 'What topics does this course cover?';

// Disclaimer.
$string['disclaimer'] = 'Disclaimer';
$string['disclaimer_help'] = 'Optional text shown below the chat input area. Use this to inform users that AI responses may not always be accurate. Maximum 180 characters.';
$string['previewdisclaimer'] = 'AI responses may contain inaccuracies. Always verify important information.';

// Tutor caption.
$string['tutorcaption'] = 'Tutor caption';
$string['tutorcaption_help'] = 'A short description shown below the tutor name in the chat header (e.g. "Your AI study assistant"). Only visible when an avatar is configured. Maximum 60 characters.';

// Accent colour.
$string['accent_color'] = 'Accent colour';
$string['accent_color_help'] = 'Colour applied to the send button, focus rings and interactive elements. Enter a hex colour code (e.g. #0f6cbf). Leave empty to inherit from the Moodle theme.';

// Theme presets.
$string['themepreset'] = 'Theme preset';
$string['themepreset_help'] = 'Apply a predefined colour scheme. Selecting a preset fills in the colour fields below which you can then adjust manually.';
$string['themepreset_none'] = 'None';
$string['themepreset_light'] = 'Light';
$string['themepreset_dark'] = 'Dark';
$string['themepreset_ocean'] = 'Ocean';
$string['themepreset_warm'] = 'Warm';

// Chat text colour.
$string['chat_text_color'] = 'Chat text colour';
$string['chat_text_color_help'] = 'Text colour used for the tutor name, assistant messages and general chat text. Enter a hex colour code (e.g. #162134). Leave empty to use the default dark text.';
