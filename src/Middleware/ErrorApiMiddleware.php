<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Middleware;

use Eureka\Kernel\Http\Controller\ErrorControllerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class ErrorMiddleware
 *
 * @author Romain Cottard
 */
class ErrorApiMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly ErrorControllerInterface $controller) {}

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * @param ServerRequestInterface $serverRequest
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $serverRequest, RequestHandlerInterface $handler): ResponseInterface
    {
        $clientAcceptJsonResponse = (strpos($serverRequest->getHeaderLine('Accept'), 'application/json') !== false);

        //~ If client does not accept an json response, do no try catch error with error controller linked
        //~ to *this* handler
        if (!$clientAcceptJsonResponse) {
            return $handler->handle($serverRequest);
        }

        try {
            $response = $handler->handle($serverRequest);
        } catch (\Exception $exception) {
            $response = $this->getErrorResponse($serverRequest, $exception);
        }

        return $response;
    }

    /**
     * Get Error response.
     *
     * @param ServerRequestInterface $serverRequest
     * @param \Exception $exception
     * @return ResponseInterface
     */
    private function getErrorResponse(ServerRequestInterface $serverRequest, \Exception $exception): ResponseInterface
    {
        $this->controller->preAction($serverRequest);
        $response = $this->controller->error($serverRequest, $exception);
        $this->controller->postAction();

        return $response;
    }
}
