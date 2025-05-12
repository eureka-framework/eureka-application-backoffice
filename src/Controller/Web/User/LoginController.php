<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Controller\Web\User;

use Application\Controller\Web\AbstractWebController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class LoginController
 *
 * @author Romain Cottard
 */
class LoginController extends AbstractWebController
{
    /**
     * @param ServerRequestInterface $serverRequest
     * @return ResponseInterface
     */
    public function index(ServerRequestInterface $serverRequest): ResponseInterface
    {
        return $this->getResponse($this->render('@app/user/login.html.twig'));
    }
}
