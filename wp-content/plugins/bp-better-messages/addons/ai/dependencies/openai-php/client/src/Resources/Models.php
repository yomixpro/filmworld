<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 08-April-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace BetterMessages\OpenAI\Resources;

use BetterMessages\OpenAI\Contracts\Resources\ModelsContract;
use BetterMessages\OpenAI\Responses\Models\DeleteResponse;
use BetterMessages\OpenAI\Responses\Models\ListResponse;
use BetterMessages\OpenAI\Responses\Models\RetrieveResponse;
use BetterMessages\OpenAI\ValueObjects\Transporter\Payload;
use BetterMessages\OpenAI\ValueObjects\Transporter\Response;

final class Models implements ModelsContract
{
    use Concerns\Transportable;

    /**
     * Lists the currently available models, and provides basic information about each one such as the owner and availability.
     *
     * @see https://platform.openai.com/docs/api-reference/models/list
     */
    public function list(): ListResponse
    {
        $payload = Payload::list('models');

        /** @var Response<array{object: string, data: array<int, array{id: string, object: string, created: int, owned_by: string}>}> $response */
        $response = $this->transporter->requestObject($payload);

        return ListResponse::from($response->data(), $response->meta());
    }

    /**
     * Retrieves a model instance, providing basic information about the model such as the owner and permissioning.
     *
     * @see https://platform.openai.com/docs/api-reference/models/retrieve
     */
    public function retrieve(string $model): RetrieveResponse
    {
        $payload = Payload::retrieve('models', $model);

        /** @var Response<array{id: string, object: string, created: int, owned_by: string}> $response */
        $response = $this->transporter->requestObject($payload);

        return RetrieveResponse::from($response->data(), $response->meta());
    }

    /**
     * Delete a fine-tuned model. You must have the Owner role in your organization.
     *
     * @see https://platform.openai.com/docs/api-reference/fine-tunes/delete-model
     */
    public function delete(string $model): DeleteResponse
    {
        $payload = Payload::delete('models', $model);

        /** @var Response<array{id: string, object: string, deleted: bool}> $response */
        $response = $this->transporter->requestObject($payload);

        return DeleteResponse::from($response->data(), $response->meta());
    }
}
