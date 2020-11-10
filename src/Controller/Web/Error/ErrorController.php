<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Controller\Web\Error;

use Application\Controller\Common\AbstractWebController;
use Eureka\Component\Web\Notification\NotificationType;
use Eureka\Kernel\Http\Controller\ErrorControllerInterface;
use Eureka\Kernel\Http\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Class ErrorController
 *
 * @author Romain Cottard
 */
class ErrorController extends AbstractWebController implements ErrorControllerInterface
{
    /**
     * @param ServerRequestInterface $serverRequest
     * @param \Exception $exception
     * @return ResponseInterface
     * @throws
     */
    public function error(ServerRequestInterface $serverRequest, \Exception $exception): ResponseInterface
    {
        //~ Handle authentication errors & redirect to user login page
        if ($exception->getCode() >= 1050 && $exception->getCode() <= 1054 || $exception->getCode() >= 1060) {
            $this->addFlashNotification($exception->getMessage(), NotificationType::ERROR);
            $this->redirect($this->getRouteUri('user_login'));
        }

        switch (true) {
            case $exception instanceof Exception\HttpBadRequestException:
                $httpCode = 400;
                break;
            case $exception instanceof Exception\HttpUnauthorizedException:
                $httpCode = 401;
                break;
            case $exception instanceof Exception\HttpForbiddenException:
                $httpCode = 403;
                break;
            case $exception instanceof RouteNotFoundException:
            case $exception instanceof Exception\HttpNotFoundException:
                $httpCode = 404;
                break;
            case $exception instanceof Exception\HttpMethodNotAllowedException:
                $httpCode = 405;
                break;
            case $exception instanceof Exception\HttpConflictException:
                $httpCode = 409;
                break;
            case $exception instanceof Exception\HttpTooManyRequestsException:
                $httpCode = 429;
                break;
            case $exception instanceof Exception\HttpServiceUnavailableException:
                $httpCode = 503;
                break;
            default:
                $httpCode = 500;
        }

        $template = $httpCode < 500 ? 'Error4XX.twig' : 'Error5XX.twig';

        $this->getContext()
            ->add('httpCode', $httpCode)
            ->add('exception', $exception)
        ;

        return $this->getResponse($this->render('@common/Error/' . $template), $httpCode);
    }
}
