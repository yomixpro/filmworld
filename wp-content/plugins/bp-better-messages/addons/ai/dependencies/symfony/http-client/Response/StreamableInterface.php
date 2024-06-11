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

use BetterMessages\Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use BetterMessages\Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use BetterMessages\Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use BetterMessages\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
interface StreamableInterface
{
    /**
     * Casts the response to a PHP stream resource.
     *
     * @return resource
     *
     * @throws TransportExceptionInterface   When a network error occurs
     * @throws RedirectionExceptionInterface On a 3xx when $throw is true and the "max_redirects" option has been reached
     * @throws ClientExceptionInterface      On a 4xx when $throw is true
     * @throws ServerExceptionInterface      On a 5xx when $throw is true
     */
    public function toStream(bool $throw = true);
}
