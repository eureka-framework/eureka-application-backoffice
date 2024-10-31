<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Service;

use Application\Domain\User\Repository\UserRepositoryInterface;
use Eureka\Component\Password\PasswordChecker;
use Eureka\Kernel\Http\Exception\HttpBadRequestException;
use Eureka\Kernel\Http\Exception\HttpForbiddenException;
use Eureka\Kernel\Http\Exception\HttpUnauthorizedException;
use Eureka\Component\Orm\Exception\EntityNotExistsException;
use Eureka\Component\Orm\Exception\InvalidQueryException;
use Eureka\Component\Orm\Exception\OrmException;
use Lcobucci\JWT\Token;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class LoginService
 *
 * @author Romain Cottard
 */
class LoginService
{
    public function __construct(
        private readonly JsonWebTokenService $jsonWebTokenService,
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordChecker $passwordChecker,
    ) {}

    /**
     * @param ServerRequestInterface $serverRequest
     * @return Token
     * @throws InvalidQueryException
     * @throws OrmException
     * @throws \JsonException
     */
    public function login(ServerRequestInterface $serverRequest): Token
    {
        /** @var array{email?: string, password?: string} $body */
        $body = $serverRequest->getParsedBody();

        $email    = isset($body['email']) ? \trim($body['email']) : '';
        $password = isset($body['password']) ? \trim($body['password']) : '';

        if ($email === '') {
            throw new HttpBadRequestException('Error with email (empty or not well formatted value)', 1200);
        }

        if ($password === '') {
            throw new HttpBadRequestException('Error with password (empty or not well formatted value)', 1201);
        }

        try {
            //~ Retrieve user
            $user = $this->userRepository->findByEmail($email);
        } catch (EntityNotExistsException $exception) {
            throw new HttpUnauthorizedException('Invalid email or password', 1202);
        }

        if (!$user->isEnabled()) {
            throw new HttpForbiddenException('Account disabled', 1203);
        }

        //~ Verify password
        if (!$this->passwordChecker->verify($password, $user->getPassword())) {
            throw new HttpUnauthorizedException('Invalid email or password', 1202); // Same code to limit hack info
        }

        $token = $this->jsonWebTokenService->generateToken($user->getId(), time());

        //~ Register token for this user
        $user->registerToken($token);
        $this->userRepository->persist($user);

        return $token;
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @return void
     * @throws \JsonException
     * @throws OrmException
     */
    public function logout(ServerRequestInterface $serverRequest): void
    {
        $token = $this->jsonWebTokenService->getTokenFromServerRequest($serverRequest);

        $userId = (int) $token->claims()->get('uid', 0);
        $user = $this->userRepository->findById($userId);

        $user->revokeToken($token);
        $this->userRepository->persist($user);
    }
}
