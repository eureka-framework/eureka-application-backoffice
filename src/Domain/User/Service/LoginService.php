<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Domain\User\Service;

use Application\Domain\User\Repository\UserRepositoryInterface;
use Application\Service\CookieService;
use Application\Service\JsonWebTokenService;
use Eureka\Component\Orm\Exception\EntityNotExistsException;
use Eureka\Component\Orm\Exception\InvalidQueryException;
use Eureka\Component\Orm\Exception\OrmException;
use Eureka\Component\Password\PasswordChecker;
use Eureka\Kernel\Http\Exception\HttpForbiddenException;
use Eureka\Kernel\Http\Exception\HttpUnauthorizedException;
use Application\Domain\User\DTO;
use Lcobucci\JWT\Token;
use Psr\Clock\ClockInterface;
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
        private readonly CookieService $cookieService,
        private readonly ClockInterface $clock,
    ) {}

    /**
     * @throws InvalidQueryException
     * @throws OrmException
     * @throws \JsonException
     * @throws \Exception
     */
    public function login(DTO\LoginInput $dto): Token
    {
        try {
            //~ Retrieve user
            $user = $this->userRepository->findByEmail($dto->login);
        } catch (EntityNotExistsException $exception) {
            throw new HttpUnauthorizedException('Invalid email or password', 1202, $exception);
        }

        if (!$user->isEnabled()) {
            throw new HttpForbiddenException('Account disabled', 1203);
        }

        //~ Verify password
        if (!$this->passwordChecker->verify($dto->password, $user->getPassword())) {
            throw new HttpUnauthorizedException('Invalid email or password', 1202); // Same code to limit hack info
        }

        $token = $this->jsonWebTokenService->generateToken($user->getId(), time());

        //~ Register token for this user
        $user->registerToken($token);
        $user->setDateLastAccess($this->clock->now()->format('Y-m-d H:i:s'));
        $this->userRepository->persist($user);

        $this->cookieService->set(name: 'authorization', value: 'JWT ' . $token->toString());

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

        $userId = $token->claims()->get('uid', 0);
        if (!\is_numeric($userId)) {
            throw new \UnexpectedValueException('Token does not contain a valid uid!');
        }

        $user = $this->userRepository->findById((int) $userId);

        $user->revokeToken($token);
        $this->userRepository->persist($user);
    }
}
