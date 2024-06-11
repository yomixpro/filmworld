<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 08-April-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace BetterMessages\OpenAI\Responses\FineTunes;

use BetterMessages\OpenAI\Contracts\ResponseContract;
use BetterMessages\OpenAI\Responses\Concerns\ArrayAccessible;

/**
 * @implements ResponseContract<array{batch_size: ?int, learning_rate_multiplier: ?float, n_epochs: int, prompt_loss_weight: float}>
 */
final class RetrieveResponseHyperparams implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{batch_size: ?int, learning_rate_multiplier: ?float, n_epochs: int, prompt_loss_weight: float}>
     */
    use ArrayAccessible;

    private function __construct(
        public readonly ?int $batchSize,
        public readonly ?float $learningRateMultiplier,
        public readonly int $nEpochs,
        public readonly float $promptLossWeight,
    ) {
    }

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{batch_size: ?int, learning_rate_multiplier: ?float, n_epochs: int, prompt_loss_weight: float}  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            $attributes['batch_size'],
            $attributes['learning_rate_multiplier'],
            $attributes['n_epochs'],
            $attributes['prompt_loss_weight'],
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'batch_size' => $this->batchSize,
            'learning_rate_multiplier' => $this->learningRateMultiplier,
            'n_epochs' => $this->nEpochs,
            'prompt_loss_weight' => $this->promptLossWeight,
        ];
    }
}
