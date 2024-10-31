<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Service;

use Eureka\Kernel\Http\Exception\HttpBadRequestException;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class JsonWebToken
 * Exception Code Range: 1060-1069
 *
 * @author Romain Cottard
 */
class JsonWebTokenService
{
    public const int EXPIRATION_DELAY = 604800; // 7 days

    public function __construct(private readonly Configuration $configuration) {}

    /**
     * @param int $userId
     * @param int $currentTimestamp
     * @param int $expirationDelay
     * @return Token
     * @throws \Exception
     */
    public function generateToken(
        int $userId,
        int $currentTimestamp,
        int $expirationDelay = self::EXPIRATION_DELAY,
    ): Token {
        $dateIssue      = (new \DateTimeImmutable())->setTimestamp($currentTimestamp);
        $dateExpiration = (new \DateTimeImmutable())->setTimestamp($currentTimestamp + $expirationDelay);

        return $this->configuration->builder()
            ->issuedAt($dateIssue)
            ->withClaim('uid', $userId)
            ->expiresAt($dateExpiration)
            ->getToken($this->configuration->signer(), $this->configuration->signingKey())
        ;
    }

    public function generateSignUpToken(
        string $name,
        string $email,
        int $currentTimestamp,
        int $expirationDelay = self::EXPIRATION_DELAY,
    ): Token {
        $dateIssue      = (new \DateTimeImmutable())->setTimestamp($currentTimestamp);
        $dateExpiration = (new \DateTimeImmutable())->setTimestamp($currentTimestamp + $expirationDelay);

        return $this->configuration->builder()
            ->issuedAt($dateIssue)
            ->withClaim('name', $name)
            ->withClaim('email', $email)
            ->expiresAt($dateExpiration)
            ->getToken($this->configuration->signer(), $this->configuration->signingKey())
        ;
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @return UnencryptedToken
     */
    public function getTokenFromServerRequest(ServerRequestInterface $serverRequest): UnencryptedToken
    {
        $authString = $serverRequest->getHeaderLine('Authorization');

        if (empty($authString)) {
            $cookie     = $serverRequest->getCookieParams();
            $authString = $cookie['token'] ?? '';
        }

        if (!\str_starts_with($authString, 'JWT ') || \strlen($authString) <= 4) {
            throw new HttpBadRequestException('Invalid Authorization Header', 1060);
        }

        /** @var non-empty-string $tokenString */
        $tokenString = \substr($authString, 4);

        return $this->parseToken($tokenString);
    }

    /**
     * @param non-empty-string $tokenString
     * @return UnencryptedToken
     * @throws \InvalidArgumentException
     */
    public function parseToken(string $tokenString): UnencryptedToken
    {
        /** @var UnencryptedToken $token */
        $token = $this->configuration->parser()->parse($tokenString);

        return $token;
    }

    /**
     * @param Token $token
     * @return bool
     */
    public function isValidToken(Token $token): bool
    {
        try {
            $constraints = $this->configuration->validationConstraints();

            $this->configuration->validator()
                ->assert($token, ...$constraints)
            ;

            return true;
        } catch (RequiredConstraintsViolated) {
            return false;
        }
    }

    /**
     * @phpstan-param non-empty-string $tokenString
     * @return array<mixed>
     */
    public function getTokenClaims(string $tokenString): array
    {
        /** @var UnencryptedToken $token */
        $token = $this->configuration->parser()->parse($tokenString);
        return $token->claims()->all();
    }
}
