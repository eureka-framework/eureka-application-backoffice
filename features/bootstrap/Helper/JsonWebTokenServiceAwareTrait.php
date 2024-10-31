<?php

/*
 * Copyright (c) Deezer
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Behat\Helper;

use Application\Behat\Context\Common\ClientApplicationContext;
use Application\Service\JsonWebTokenService;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\Assert;

/**
 * Trait JsonWebTokenServiceAwareTrait
 *
 * @author Romain Cottard
 */
trait JsonWebTokenServiceAwareTrait
{
    private JsonWebTokenService $jwtService;

    /**
     * @BeforeScenario
     */
    public function initializeJsonWebTokenService(): void
    {
        $this->jwtService = ClientApplicationContext::getService(JsonWebTokenService::class);
    }

    /**
     * @param string $tokenState
     * @param int $userId
     * @return Token
     * @throws \Exception
     */
    protected function getTokenWithState(string $tokenState, int $userId): Token
    {
        return match ($tokenState) {
            'invalid' => $this->createInvalidToken($userId),
            'expired' => $this->createToken($userId, time() - 86400, 3600),
            default => $this->createToken($userId),
        };
    }

    /**
     * @param int $userId
     * @param int|null $timestamp
     * @param int $delay
     * @return Token
     * @throws \Exception
     */
    protected function createToken(int $userId, ?int $timestamp = null, int $delay = JsonWebTokenService::EXPIRATION_DELAY): Token
    {
        return $this->jwtService->generateToken($userId, $timestamp ?? time(), $delay);
    }

    /**
     * @param int $userId
     * @param int|null $timestamp
     * @param int $delay
     * @return Token
     * @throws \Exception
     */
    protected function createInvalidToken(int $userId, ?int $timestamp = null, int $delay = JsonWebTokenService::EXPIRATION_DELAY): Token
    {
        /** @var Configuration $config */
        $config = ClientApplicationContext::getContainer()->get('app.auth.jwt.configuration.invalid_key');
        return (new JsonWebTokenService($config))
            ->generateToken($userId, $timestamp ?? time(), $delay)
        ;
    }

    /**
     * @param string $tokenString
     * @return Token
     */
    protected function getTokenFromString(string $tokenString): Token
    {
        return $this->jwtService->parseToken($tokenString);
    }

    /**
     * @param string|Token $token
     * @return void
     */
    protected function assertTokenIsValid($token): void
    {
        if (!$token instanceof Token) {
            $token = $this->getTokenFromString($token);
        }

        Assert::assertTrue($this->jwtService->isValidToken($token));
    }

    /**
     * @param string|Token $token
     * @return void
     */
    protected function assertTokenIsNotExpired(Token|string $token): void
    {
        if (!$token instanceof Token) {
            $token = $this->getTokenFromString($token);
        }

        Assert::assertFalse($token->isExpired(new \DateTimeImmutable()));
    }
}
