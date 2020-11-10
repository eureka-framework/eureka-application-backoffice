<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Controller\Api\Authentication;

use Application\Controller\Common\AbstractApiController;
use Application\Service\CookieService;
use Application\Service\JsonWebTokenService;
use Application\Service\LoginService;
use Eureka\Component\Orm\Exception\InvalidQueryException;
use Eureka\Component\Orm\Exception\OrmException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Safe\Exceptions\JsonException;

/**
 * Class TokenController
 *
 * @author Romain Cottard
 */
class TokenController extends AbstractApiController
{
    /** @var JsonWebTokenService $jsonWebTokenService */
    private JsonWebTokenService $jsonWebTokenService;

    /** @var LoginService $userLoginService */
    private LoginService $userLoginService;

    /** @var CookieService $cookieService */
    private CookieService $cookieService;

    /**
     * TokenController constructor.
     *
     * @param JsonWebTokenService $jsonWebTokenService
     * @param LoginService $userLoginService
     * @param CookieService $cookieService
     */
    public function __construct(
        JsonWebTokenService $jsonWebTokenService,
        LoginService $userLoginService,
        CookieService $cookieService
    ) {
        $this->jsonWebTokenService = $jsonWebTokenService;
        $this->userLoginService    = $userLoginService;
        $this->cookieService       = $cookieService;
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @return ResponseInterface
     * @throws InvalidQueryException
     * @throws OrmException
     * @throws JsonException
     */
    public function get(ServerRequestInterface $serverRequest): ResponseInterface
    {
        $token = $this->userLoginService->login($serverRequest);

        //~ In web application context, we also persist the token in the cookie (secure & http only token)
        $this->cookieService->set('Authorization', 'JWT '. (string) $token);

        return $this->getResponseJsonSuccess(['token' => (string) $token]);
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @return ResponseInterface
     */
    public function verify(ServerRequestInterface $serverRequest): ResponseInterface
    {
        $token = $this->jsonWebTokenService->getTokenFromServerRequest($serverRequest);

        $content = [
            'valid'   => $this->jsonWebTokenService->isValidToken($token),
            'expired' => $token->isExpired(),
        ];

        return $this->getResponseJsonSuccess($content);
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @return ResponseInterface
     * @throws JsonException|OrmException
     */
    public function revoke(ServerRequestInterface $serverRequest): ResponseInterface
    {
        $this->userLoginService->logout($serverRequest);

        return $this->getResponseJsonSuccess('ok');
    }
}
