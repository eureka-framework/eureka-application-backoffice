<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Service;

/**
 * Class CookieService
 *
 * @author Romain Cottard
 */
class CookieService
{
    /** @var array $defaultOptions */
    private array $defaultOptions;

    /** @var \DateTimeImmutable $dateNow */
    private \DateTimeImmutable $dateNow;

    /**
     * Class constructor.
     *
     * @param \DateTimeImmutable $dateNow
     * @param string $domain
     * @param int $defaultLifeTime
     * @param string $path
     * @param bool|string $isSecure
     * @param bool|string $isHttpOnly
     * @param string $sameSite
     */
    public function __construct(
        \DateTimeImmutable $dateNow,
        string $domain,
        int $defaultLifeTime = 2592000,
        string $path = '/',
        bool $isSecure = true,
        bool $isHttpOnly = true,
        string $sameSite = 'None' // None || Lax  || Strict
    ) {
        $this->dateNow = $dateNow;

        //~ Build default options
        $this->defaultOptions = [
            'expires'  => $this->dateNow->getTimestamp() + $defaultLifeTime,
            'path'     => $path,
            'domain'   => $domain,
            'secure'   => $isSecure,
            'httponly' => $isHttpOnly,
            'samesite' => $sameSite,
        ];
    }

    /**
     * @param string $name
     * @param string $value
     * @param array $options
     * @return void
     */
    public function set(string $name, string $value, array $options = [])
    {
        $options += $this->defaultOptions; // override default option & add missing options

        setcookie($name, $value, $options);
    }
}
