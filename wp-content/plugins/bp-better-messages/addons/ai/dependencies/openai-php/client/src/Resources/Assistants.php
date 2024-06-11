<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 08-April-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace BetterMessages\OpenAI\Resources;

use BetterMessages\OpenAI\Contracts\Resources\AssistantsContract;
use BetterMessages\OpenAI\Contracts\Resources\AssistantsFilesContract;
use BetterMessages\OpenAI\Responses\Assistants\AssistantDeleteResponse;
use BetterMessages\OpenAI\Responses\Assistants\AssistantListResponse;
use BetterMessages\OpenAI\Responses\Assistants\AssistantResponse;
use BetterMessages\OpenAI\ValueObjects\Transporter\Payload;
use BetterMessages\OpenAI\ValueObjects\Transporter\Response;

final class Assistants implements AssistantsContract
{
    use Concerns\Transportable;

    /**
     * Create an assistant with a model and instructions.
     *
     * @see https://platform.openai.com/docs/api-reference/assistants/object
     *
     * @param  array<string, mixed>  $parameters
     */
    public function create(array $parameters): AssistantResponse
    {
        $payload = Payload::create('assistants', $parameters);

        /** @var Response<array{id: string, object: string, created_at: int, name: ?string, description: ?string, model: string, instructions: ?string, tools: array<int, array{type: 'code_interpreter'}|array{type: 'retrieval'}|array{type: 'function', function: array{description: string, name: string, parameters: array<string, mixed>}}>, file_ids: array<int, string>, metadata: array<string, string>}> $response */
        $response = $this->transporter->requestObject($payload);

        return AssistantResponse::from($response->data(), $response->meta());
    }

    /**
     * Retrieves an assistant.
     *
     * @see https://platform.openai.com/docs/api-reference/assistants/getAssistant
     */
    public function retrieve(string $id): AssistantResponse
    {
        $payload = Payload::retrieve('assistants', $id);

        /** @var Response<array{id: string, object: string, created_at: int, name: ?string, description: ?string, model: string, instructions: ?string, tools: array<int, array{type: 'code_interpreter'}|array{type: 'retrieval'}|array{type: 'function', function: array{description: string, name: string, parameters: array<string, mixed>}}>, file_ids: array<int, string>, metadata: array<string, string>}> $response */
        $response = $this->transporter->requestObject($payload);

        return AssistantResponse::from($response->data(), $response->meta());
    }

    /**
     * Modifies an assistant.
     *
     * @see https://platform.openai.com/docs/api-reference/assistants/modifyAssistant
     *
     * @param  array<string, mixed>  $parameters
     */
    public function modify(string $id, array $parameters): AssistantResponse
    {
        $payload = Payload::modify('assistants', $id, $parameters);

        /** @var Response<array{id: string, object: string, created_at: int, name: ?string, description: ?string, model: string, instructions: ?string, tools: array<int, array{type: 'code_interpreter'}|array{type: 'retrieval'}|array{type: 'function', function: array{description: string, name: string, parameters: array<string, mixed>}}>, file_ids: array<int, string>, metadata: array<string, string>}> $response */
        $response = $this->transporter->requestObject($payload);

        return AssistantResponse::from($response->data(), $response->meta());
    }

    /**
     * Delete an assistant.
     *
     * @see https://platform.openai.com/docs/api-reference/assistants/deleteAssistant
     */
    public function delete(string $id): AssistantDeleteResponse
    {
        $payload = Payload::delete('assistants', $id);

        /** @var Response<array{id: string, object: string, deleted: bool}> $response */
        $response = $this->transporter->requestObject($payload);

        return AssistantDeleteResponse::from($response->data(), $response->meta());
    }

    /**
     * Returns a list of assistants.
     *
     * @see https://platform.openai.com/docs/api-reference/assistants/listAssistants
     *
     * @param  array<string, mixed>  $parameters
     */
    public function list(array $parameters = []): AssistantListResponse
    {
        $payload = Payload::list('assistants', $parameters);

        /** @var Response<array{object: string, data: array<int, array{id: string, object: string, created_at: int, name: ?string, description: ?string, model: string, instructions: ?string, tools: array<int, array{type: 'code_interpreter'}|array{type: 'retrieval'}|array{type: 'function', function: array{description: string, name: string, parameters: array<string, mixed>}}>, file_ids: array<int, string>, metadata: array<string, string>}>, first_id: ?string, last_id: ?string, has_more: bool}> $response */
        $response = $this->transporter->requestObject($payload);

        return AssistantListResponse::from($response->data(), $response->meta());
    }

    /**
     * Manage files attached to an assistant.
     *
     * @see https://platform.openai.com/docs/api-reference/assistants
     */
    public function files(): AssistantsFilesContract
    {
        return new AssistantsFiles($this->transporter);
    }
}
