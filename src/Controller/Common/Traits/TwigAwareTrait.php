<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Controller\Common\Traits;

use Eureka\Kernel\Http\Service\DataCollection;
use Twig\Environment;

/**
 * Trait TwigControllerAwareTrait
 *
 * @author Romain Cottard
 */
trait TwigAwareTrait
{
    /** @var Environment */
    private Environment $twig;

    /** @var DataCollection|null $context Data collection object. */
    protected ?DataCollection $context = null;

    /**
     * @param Environment $twig
     * @return void
     */
    public function setTwig(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Get context.
     *
     * @return DataCollection
     */
    protected function getContext()
    {
        if ($this->context === null) {
            $this->context = new DataCollection();
        }

        return $this->context;
    }

    /**
     * @param string $name
     * @return string
     * @throws
     */
    protected function render(string $name): string
    {
        $template = $this->twig->load($name);

        return $template->render($this->getContext()->toArray());
    }
}
