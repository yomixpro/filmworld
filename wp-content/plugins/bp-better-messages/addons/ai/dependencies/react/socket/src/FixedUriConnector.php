<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 08-April-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace BetterMessages\React\Socket;

/**
 * Decorates an existing Connector to always use a fixed, preconfigured URI
 *
 * This can be useful for consumers that do not support certain URIs, such as
 * when you want to explicitly connect to a Unix domain socket (UDS) path
 * instead of connecting to a default address assumed by an higher-level API:
 *
 * ```php
 * $connector = new \BetterMessages\React\Socket\FixedUriConnector(
 *     'unix:///var/run/docker.sock',
 *     new \BetterMessages\React\Socket\UnixConnector()
 * );
 *
 * // destination will be ignored, actually connects to Unix domain socket
 * $promise = $connector->connect('localhost:80');
 * ```
 */
class FixedUriConnector implements ConnectorInterface
{
    private $uri;
    private $connector;

    /**
     * @param string $uri
     * @param ConnectorInterface $connector
     */
    public function __construct($uri, ConnectorInterface $connector)
    {
        $this->uri = $uri;
        $this->connector = $connector;
    }

    public function connect($_)
    {
        return $this->connector->connect($this->uri);
    }
}
