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

namespace BetterMessages\Symfony\Component\HttpClient;

use BetterMessages\Symfony\Contracts\HttpClient\HttpClientInterface;
use BetterMessages\Symfony\Contracts\HttpClient\ResponseInterface;
use BetterMessages\Symfony\Contracts\HttpClient\ResponseStreamInterface;
use BetterMessages\Symfony\Contracts\Service\ResetInterface;

/**
 * Eases with writing decorators.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
trait DecoratorTrait
{
    private HttpClientInterface $client;

    public function __construct(?HttpClientInterface $client = null)
    {
        $this->client = $client ?? HttpClient::create();
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        return $this->client->request($method, $url, $options);
    }

    public function stream(ResponseInterface|iterable $responses, ?float $timeout = null): ResponseStreamInterface
    {
        return $this->client->stream($responses, $timeout);
    }

    public function withOptions(array $options): static
    {
        $clone = clone $this;
        $clone->client = $this->client->withOptions($options);

        return $clone;
    }

    public function reset(): void
    {
        if ($this->client instanceof ResetInterface) {
            $this->client->reset();
        }
    }
}
