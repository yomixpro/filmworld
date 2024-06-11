<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 08-April-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace BetterMessages\OpenAI\ValueObjects\Transporter;

use BetterMessages\OpenAI\Responses\Meta\MetaInformation;

/**
 * @template-covariant TData of array|string
 *
 * @internal
 */
final class Response
{
    /**
     * Creates a new Response value object.
     *
     * @param  TData  $data
     */
    private function __construct(
        private readonly array|string $data,
        private readonly MetaInformation $meta
    ) {
        // ..
    }

    /**
     * Creates a new Response value object from the given data and meta information.
     *
     * @param  TData  $data
     * @param  array<string, array<int, string>>  $headers
     * @return Response<TData>
     */
    public static function from(array|string $data, array $headers): self
    {
        // @phpstan-ignore-next-line
        $meta = MetaInformation::from($headers);

        return new self($data, $meta);
    }

    /**
     * Returns the response data.
     *
     * @return TData
     */
    public function data(): array|string
    {
        return $this->data;
    }

    /**
     * Returns the meta information.
     */
    public function meta(): MetaInformation
    {
        return $this->meta;
    }
}
