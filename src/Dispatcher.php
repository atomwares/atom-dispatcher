<?php
/**
 * @link http://www.atomframework.net/
 * @copyright Copyright (c) 2017 Safarov Alisher
 * @license https://github.com/atomwares/atom-dispatcher/blob/master/LICENSE (MIT License)
 */

namespace Atom\Dispatcher;

use Atom\Http\Middleware\Delegate;
use Atom\Interfaces\DispatcherInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Dispatcher
 *
 * @package Atom\Middleware
 */
class Dispatcher implements DispatcherInterface
{
    /**
     * @var MiddlewareInterface|MiddlewareInterface[]|callable|callable[]
     */
    protected $middleware = [];

    /**
     * Dispatcher constructor.
     *
     * @param MiddlewareInterface|MiddlewareInterface[]|callable|callable[] $middleware
     */
    public function __construct($middleware = [])
    {
        $this->set($middleware);
    }

    /**
     * @param MiddlewareInterface|MiddlewareInterface[]|callable|callable[] $middleware
     *
     * @return $this
     */
    public function set($middleware)
    {
        $this->middleware = is_array($middleware) ? $middleware : [$middleware];

        return $this;
    }

    /**
     * @param MiddlewareInterface|MiddlewareInterface[]|callable|callable[] $middleware
     *
     * @return $this
     */
    public function add($middleware)
    {
        $this->middleware = array_merge(
            $this->middleware,
            is_array($middleware) ? $middleware : [$middleware]
        );

        return $this;
    }

    /**
     * @return MiddlewareInterface|MiddlewareInterface[]|callable|callable[]
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        while ($middleware = array_pop($this->middleware)) {
            $delegate = new Delegate($middleware, $delegate);
        }

        return $delegate->process($request);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request)
    {
        return $this->process($request, new Delegate());
    }
}
