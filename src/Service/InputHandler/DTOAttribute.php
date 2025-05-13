<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Service\InputHandler;

#[\Attribute]
readonly class DTOAttribute
{
    public const string FROM_BODY = 'body';
    public const string FROM_QUERY = 'query';
    /**
     * @param array<string, int|float|bool|string|null> $options
     */
    public function __construct(
        public string $name,
        public string $from = self::FROM_BODY, // query|body
        public string $type = 'string',
        public array $options = [],
    ) {}
}
