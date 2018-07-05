<?php
/**
 * @link http://www.atomframework.net/
 * @copyright Copyright (c) 2017 Safarov Alisher
 * @license https://github.com/atomwares/atom-dispatcher/blob/master/LICENSE (MIT License)
 */

namespace Atom\Dispatcher;

use Atom\Interfaces\DispatcherInterface;
use Atom\Http\Server\RequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

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
     * @var RequestHandlerInterface
     */
    protected $fallbackHandler;

    /**
     * Dispatcher constructor.
     *
     *  @param RequestHandlerInterface $fallbackHandler
     */
    public function __construct($fallbackHandler)
    {
        $this->fallbackHandler = $fallbackHandler;
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
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        while ($middleware = array_pop($this->middleware)) {
            $handler = new RequestHandler($middleware, $handler);
        }

        return $handler->handle($request);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->process($request, $this->fallbackHandler);
    }
}
