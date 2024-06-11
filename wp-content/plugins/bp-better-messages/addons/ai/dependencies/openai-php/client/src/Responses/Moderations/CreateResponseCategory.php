<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 08-April-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace BetterMessages\OpenAI\Responses\Moderations;

use BetterMessages\OpenAI\Enums\Moderations\Category;

final class CreateResponseCategory
{
    private function __construct(
        public readonly Category $category,
        public readonly bool $violated,
        public readonly float $score,
    ) {
    }

    /**
     * @param  array{category: string, violated: bool, score: float}  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            Category::from($attributes['category']),
            $attributes['violated'],
            $attributes['score'],
        );
    }
}
