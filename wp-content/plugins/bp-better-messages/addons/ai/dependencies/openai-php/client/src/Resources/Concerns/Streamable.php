<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 08-April-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace BetterMessages\OpenAI\Resources\Concerns;

use BetterMessages\OpenAI\Exceptions\InvalidArgumentException;

trait Streamable
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    private function ensureNotStreamed(array $parameters): void
    {
        if (! isset($parameters['stream'])) {
            return;
        }

        if ($parameters['stream'] !== true) {
            return;
        }

        throw new InvalidArgumentException('Stream option is not supported. Please use the createStreamed() method instead.');
    }

    /**
     * Set the stream parameter to true.
     *
     * @param  array<string, mixed>  $parameters
     * @return array<string, mixed>
     */
    private function setStreamParameter(array $parameters): array
    {
        $parameters['stream'] = true;

        return $parameters;
    }
}
