<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by __root__ on 08-April-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace BetterMessages\Symfony\Component\HttpClient\Response;

use BetterMessages\GuzzleHttp\Promise\Create;
use BetterMessages\GuzzleHttp\Promise\PromiseInterface as GuzzlePromiseInterface;
use Http\Promise\Promise as HttplugPromiseInterface;
use BetterMessages\Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * @internal
 */
final class HttplugPromise implements HttplugPromiseInterface
{
    private GuzzlePromiseInterface $promise;

    public function __construct(GuzzlePromiseInterface $promise)
    {
        $this->promise = $promise;
    }

    public function then(?callable $onFulfilled = null, ?callable $onRejected = null): self
    {
        return new self($this->promise->then(
            $this->wrapThenCallback($onFulfilled),
            $this->wrapThenCallback($onRejected)
        ));
    }

    public function cancel(): void
    {
        $this->promise->cancel();
    }

    public function getState(): string
    {
        return $this->promise->getState();
    }

    /**
     * @return Psr7ResponseInterface|mixed
     */
    public function wait($unwrap = true): mixed
    {
        $result = $this->promise->wait($unwrap);

        while ($result instanceof HttplugPromiseInterface || $result instanceof GuzzlePromiseInterface) {
            $result = $result->wait($unwrap);
        }

        return $result;
    }

    private function wrapThenCallback(?callable $callback): ?callable
    {
        if (null === $callback) {
            return null;
        }

        return static fn ($value) => Create::promiseFor($callback($value));
    }
}
