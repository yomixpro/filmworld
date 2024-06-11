<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 08-April-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace BetterMessages\OpenAI\Responses\FineTuning;

use BetterMessages\OpenAI\Contracts\ResponseContract;
use BetterMessages\OpenAI\Responses\Concerns\ArrayAccessible;

/**
 * @implements ResponseContract<array{step: int, train_loss: float, train_mean_token_accuracy: float}>
 */
final class ListJobEventsResponseEventData implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{step: int, train_loss: float, train_mean_token_accuracy: float}>
     */
    use ArrayAccessible;

    private function __construct(
        public readonly int $step,
        public readonly float $trainLoss,
        public readonly float $trainMeanTokenAccuracy,
    ) {
    }

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{step: int, train_loss: float, train_mean_token_accuracy: float}  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            $attributes['step'],
            $attributes['train_loss'],
            $attributes['train_mean_token_accuracy'],
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'step' => $this->step,
            'train_loss' => $this->trainLoss,
            'train_mean_token_accuracy' => $this->trainMeanTokenAccuracy,
        ];
    }
}
