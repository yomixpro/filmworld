<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 08-April-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace BetterMessages\Psr\Http\Client;

use BetterMessages\Psr\Http\Message\RequestInterface;
use BetterMessages\Psr\Http\Message\ResponseInterface;

interface ClientInterface
{
    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws \BetterMessages\Psr\Http\Client\ClientExceptionInterface If an error happens while processing the request.
     */
    public function sendRequest(RequestInterface $request): ResponseInterface;
}
