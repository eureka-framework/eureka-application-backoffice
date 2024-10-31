<?php

/*
 * Copyright (c) Deezer
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Behat\Helper;

use Eureka\Component\Validation\Entity\ValidatorEntityFactory;
use Eureka\Component\Validation\ValidatorFactory;

trait OrmTrait
{
    private function getValidatorFactory(): ValidatorFactory
    {
        return new ValidatorFactory();
    }

    private function getValidatorEntityFactory(): ValidatorEntityFactory
    {
        return new ValidatorEntityFactory(
            new ValidatorFactory(),
        );
    }
}
