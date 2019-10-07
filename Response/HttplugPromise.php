<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpClient\Response;

use GuzzleHttp\Promise\PromiseInterface as GuzzlePromiseInterface;
use Http\Promise\Promise as HttplugPromiseInterface;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * @internal
 */
final class HttplugPromise implements HttplugPromiseInterface
{
    private $promise;
    private $cancel;

    public function __construct(GuzzlePromiseInterface $promise, callable $cancel = null)
    {
        $this->promise = $promise;
        $this->cancel = $cancel;
    }

    public function then(callable $onFulfilled = null, callable $onRejected = null): self
    {
        return new self($this->promise->then($onFulfilled, $onRejected));
    }

    public function cancel(): void
    {
        $this->promise->cancel();
    }

    /**
     * {@inheritdoc}
     */
    public function getState(): string
    {
        return $this->promise->getState();
    }

    /**
     * {@inheritdoc}
     *
     * @return Psr7ResponseInterface|mixed
     */
    public function wait($unwrap = true)
    {
        return $this->promise->wait($unwrap);
    }

    public function __destruct()
    {
        if ($this->cancel) {
            ($this->cancel)();
        }
    }

    public function __wakeup()
    {
        throw new \BadMethodCallException('Cannot unserialize '.__CLASS__);
    }
}
