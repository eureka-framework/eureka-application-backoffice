<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Controller\Web;

use Application\Controller\Web\Traits\AssetsAwareTrait;
use Application\Controller\Web\Traits\TwigAwareTrait;
use Eureka\Component\Web\Menu\Menu;
use Eureka\Component\Web\Menu\MenuControllerAwareTrait;
use Eureka\Component\Web\Meta\MetaControllerAwareTrait;
use Eureka\Component\Web\Session\SessionAwareTrait;
use Eureka\Kernel\Http\Controller\Controller;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class HomeController
 *
 * @author Romain Cottard
 */
abstract class AbstractWebController extends Controller
{
    use AssetsAwareTrait;
    use MenuControllerAwareTrait;
    use MetaControllerAwareTrait;
    use SessionAwareTrait;
    use TwigAwareTrait;

    /**
     * @throws \JsonException
     */
    public function preAction(?ServerRequestInterface $serverRequest = null): void
    {
        if (empty($serverRequest)) {
            throw new \UnexpectedValueException('Server request is empty!');
        }

        parent::preAction($serverRequest);

        $this->initializeAssets();

        $menu = $this->buildMenu();

        $currentUri = $serverRequest->getUri();
        $currentUriImage = $currentUri
            ->withPath('')
            ->withFragment('')
            ->withQuery('')
        ;

        $this->getContext()
            ->add('currentUser', $serverRequest->getAttribute('currentUser'))
            ->add('menuLeft', $menu['left'])
            ->add('menuRight', $menu['right'])
            ->add('menuState', $this->getMenuState($serverRequest))
            ->add('meta', $this->getMeta())
            ->add('cssFiles', $this->getCssFiles())
            ->add('jsFiles', $this->getJsFiles())
            ->add('flashNotifications', $this->getAllFlashNotification())
            ->add('flashFormErrors', $this->getFormErrors())
            ->add('currentUrl', (string) $currentUri)
            ->add('baseUrlImage', (string) $currentUriImage)
            ->add('user', $serverRequest->getAttribute('user'))
        ;

        $this->getSession()?->clearFlash();
    }

    /**
     * @return array{left: Menu, right: Menu}
     */
    protected function buildMenu(): array
    {
        return [
            'left'  => $this->getMenu(),
            'right' => $this->getMenu(false),
        ];
    }
}
