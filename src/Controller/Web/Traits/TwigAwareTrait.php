<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Controller\Web\Traits;

use Application\VO\TwigContext;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Trait TwigControllerAwareTrait
 *
 * @author Romain Cottard
 */
trait TwigAwareTrait
{
    private Environment $twig;
    protected ?TwigContext $context = null;

    /**
     * @param Environment $twig
     * @return void
     */
    public function setTwig(Environment $twig): void
    {
        $this->twig = $twig;
    }

    /**
     * Get context.
     *
     * @return TwigContext
     */
    protected function getContext(): TwigContext
    {
        if ($this->context === null) {
            $this->context = new TwigContext();
        }

        return $this->context;
    }

    /**
     * @param string $name
     * @return string
     * @throws LoaderError|RuntimeError|SyntaxError
     */
    protected function render(string $name): string
    {
        $template = $this->twig->load($name);

        return $template->render($this->getContext()->toArray());
    }
}
