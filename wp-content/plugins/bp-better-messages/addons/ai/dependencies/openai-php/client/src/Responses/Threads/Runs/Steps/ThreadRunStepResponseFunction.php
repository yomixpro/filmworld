<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 08-April-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace BetterMessages\OpenAI\Responses\Threads\Runs\Steps;

use BetterMessages\OpenAI\Contracts\ResponseContract;
use BetterMessages\OpenAI\Responses\Concerns\ArrayAccessible;
use BetterMessages\OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{name: string, arguments: string, output: ?string}>
 */
final class ThreadRunStepResponseFunction implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{name: string, arguments: string, output: ?string}>
     */
    use ArrayAccessible;

    use Fakeable;

    private function __construct(
        public string $name,
        public string $arguments,
        public ?string $output,
    ) {
    }

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{name: string, arguments: string, output?: ?string}  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            $attributes['name'],
            $attributes['arguments'],
            $attributes['output'] ?? null,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'arguments' => $this->arguments,
            'output' => $this->output,
        ];
    }
}
