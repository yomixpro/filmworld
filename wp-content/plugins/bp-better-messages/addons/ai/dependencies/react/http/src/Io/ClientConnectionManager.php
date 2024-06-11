<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 08-April-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace BetterMessages\React\Http\Io;

use BetterMessages\Psr\Http\Message\UriInterface;
use BetterMessages\React\EventLoop\LoopInterface;
use BetterMessages\React\EventLoop\TimerInterface;
use BetterMessages\React\Promise\PromiseInterface;
use BetterMessages\React\Socket\ConnectionInterface;
use BetterMessages\React\Socket\ConnectorInterface;

/**
 * [Internal] Manages outgoing HTTP connections for the HTTP client
 *
 * @internal
 * @final
 */
class ClientConnectionManager
{
    /** @var ConnectorInterface */
    private $connector;

    /** @var LoopInterface */
    private $loop;

    /** @var string[] */
    private $idleUris = array();

    /** @var ConnectionInterface[] */
    private $idleConnections = array();

    /** @var TimerInterface[] */
    private $idleTimers = array();

    /** @var \Closure[] */
    private $idleStreamHandlers = array();

    /** @var float */
    private $maximumTimeToKeepAliveIdleConnection = 0.001;

    public function __construct(ConnectorInterface $connector, LoopInterface $loop)
    {
        $this->connector = $connector;
        $this->loop = $loop;
    }

    /**
     * @return PromiseInterface<ConnectionInterface>
     */
    public function connect(UriInterface $uri)
    {
        $scheme = $uri->getScheme();
        if ($scheme !== 'https' && $scheme !== 'http') {
            return \BetterMessages\React\Promise\reject(new \InvalidArgumentException(
                'Invalid request URL given'
            ));
        }

        $port = $uri->getPort();
        if ($port === null) {
            $port = $scheme === 'https' ? 443 : 80;
        }
        $uri = ($scheme === 'https' ? 'tls://' : '') . $uri->getHost() . ':' . $port;

        // Reuse idle connection for same URI if available
        foreach ($this->idleConnections as $id => $connection) {
            if ($this->idleUris[$id] === $uri) {
                assert($this->idleStreamHandlers[$id] instanceof \Closure);
                $connection->removeListener('close', $this->idleStreamHandlers[$id]);
                $connection->removeListener('data', $this->idleStreamHandlers[$id]);
                $connection->removeListener('error', $this->idleStreamHandlers[$id]);

                assert($this->idleTimers[$id] instanceof TimerInterface);
                $this->loop->cancelTimer($this->idleTimers[$id]);
                unset($this->idleUris[$id], $this->idleConnections[$id], $this->idleTimers[$id], $this->idleStreamHandlers[$id]);

                return \BetterMessages\React\Promise\resolve($connection);
            }
        }

        // Create new connection if no idle connection to same URI is available
        return $this->connector->connect($uri);
    }

    /**
     * Hands back an idle connection to the connection manager for possible future reuse.
     *
     * @return void
     */
    public function keepAlive(UriInterface $uri, ConnectionInterface $connection)
    {
        $scheme = $uri->getScheme();
        assert($scheme === 'https' || $scheme === 'http');

        $port = $uri->getPort();
        if ($port === null) {
            $port = $scheme === 'https' ? 443 : 80;
        }

        $this->idleUris[] = ($scheme === 'https' ? 'tls://' : '') . $uri->getHost() . ':' . $port;
        $this->idleConnections[] = $connection;

        $that = $this;
        $cleanUp = function () use ($connection, $that) {
            // call public method to support legacy PHP 5.3
            $that->cleanUpConnection($connection);
        };

        // clean up and close connection when maximum time to keep-alive idle connection has passed
        $this->idleTimers[] = $this->loop->addTimer($this->maximumTimeToKeepAliveIdleConnection, $cleanUp);

        // clean up and close connection when unexpected close/data/error event happens during idle time
        $this->idleStreamHandlers[] = $cleanUp;
        $connection->on('close', $cleanUp);
        $connection->on('data', $cleanUp);
        $connection->on('error', $cleanUp);
    }

    /**
     * @internal
     * @return void
     */
    public function cleanUpConnection(ConnectionInterface $connection) // private (PHP 5.4+)
    {
        $id = \array_search($connection, $this->idleConnections, true);
        if ($id === false) {
            return;
        }

        assert(\is_int($id));
        assert($this->idleTimers[$id] instanceof TimerInterface);
        $this->loop->cancelTimer($this->idleTimers[$id]);
        unset($this->idleUris[$id], $this->idleConnections[$id], $this->idleTimers[$id], $this->idleStreamHandlers[$id]);

        $connection->close();
    }
}
