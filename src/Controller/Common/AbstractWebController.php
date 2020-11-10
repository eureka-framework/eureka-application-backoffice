<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Controller\Common;

use Application\Controller\Common\Traits\AssetsAwareTrait;
use Application\Controller\Common\Traits\TwigAwareTrait;
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
     * @param ServerRequestInterface|null $request
     * @return void
     */
    public function preAction(?ServerRequestInterface $request = null): void
    {
        parent::preAction($request);

        $this->setServerRequest($request);
        $this->initializeAssets();

        $menu = $this->buildMenu();

        $currentUri = $request->getUri();
        $currentUriImage = $currentUri
            ->withPath('')
            ->withFragment('')
            ->withQuery('')
        ;

        $this->getContext()
            ->add('currentUser', $request !== null ? $request->getAttribute('currentUser') : null)
            ->add('menuLeft', $menu['left'])
            ->add('menuRight', $menu['right'])
            ->add('menuState', $this->getMenuState($request))
            ->add('meta', $this->getMeta())
            ->add('cssFiles', $this->getCssFiles())
            ->add('jsFiles', $this->getJsFiles())
            ->add('flashNotifications', $this->getAllFlashNotification())
            ->add('flashFormErrors', $this->getFormErrors())
            ->add('currentUrl', (string) $currentUri)
            ->add('baseUrlImage', (string) $currentUriImage)
            ->add('user', $request->getAttribute('user'))
        ;

        $this->getSession()->clearFlash();
    }

    /**
     * @return array
     */
    protected function buildMenu(): array
    {
        return [
            'left'  => $this->getMenu(),
            'right' => $this->getMenu(false),
        ];
    }
}
