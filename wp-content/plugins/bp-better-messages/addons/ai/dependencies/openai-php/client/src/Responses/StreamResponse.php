<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 08-April-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace BetterMessages\OpenAI\Responses;

use Generator;
use BetterMessages\OpenAI\Contracts\ResponseHasMetaInformationContract;
use BetterMessages\OpenAI\Contracts\ResponseStreamContract;
use BetterMessages\OpenAI\Exceptions\ErrorException;
use BetterMessages\OpenAI\Responses\Meta\MetaInformation;
use BetterMessages\Psr\Http\Message\ResponseInterface;
use BetterMessages\Psr\Http\Message\StreamInterface;

/**
 * @template TResponse
 *
 * @implements ResponseStreamContract<TResponse>
 */
final class StreamResponse implements ResponseHasMetaInformationContract, ResponseStreamContract
{
    /**
     * Creates a new Stream Response instance.
     *
     * @param  class-string<TResponse>  $responseClass
     */
    public function __construct(
        private readonly string $responseClass,
        private readonly ResponseInterface $response,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): Generator
    {
        while (! $this->response->getBody()->eof()) {
            $line = $this->readLine($this->response->getBody());

            if (! str_starts_with($line, 'data:')) {
                continue;
            }

            $data = trim(substr($line, strlen('data:')));

            if ($data === '[DONE]') {
                break;
            }

            /** @var array{error?: array{message: string|array<int, string>, type: string, code: string}} $response */
            $response = json_decode($data, true, flags: JSON_THROW_ON_ERROR);

            if (isset($response['error'])) {
                throw new ErrorException($response['error']);
            }

            yield $this->responseClass::from($response);
        }
    }

    /**
     * Read a line from the stream.
     */
    private function readLine(StreamInterface $stream): string
    {
        $buffer = '';

        while (! $stream->eof()) {
            if ('' === ($byte = $stream->read(1))) {
                return $buffer;
            }
            $buffer .= $byte;
            if ($byte === "\n") {
                break;
            }
        }

        return $buffer;
    }

    public function meta(): MetaInformation
    {
        // @phpstan-ignore-next-line
        return MetaInformation::from($this->response->getHeaders());
    }
}
