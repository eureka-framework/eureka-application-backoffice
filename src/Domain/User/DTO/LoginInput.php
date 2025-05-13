<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Domain\User\DTO;

use Application\Service\InputHandler\DTOAttribute;

class LoginInput
{
    public function __construct(
        #[DTOAttribute('login', type: 'email')]
        public readonly string $login,
        #[DTOAttribute('password', type: 'string', options: ['min_length' => 8])]
        public readonly string $password,
    ) {}
}
