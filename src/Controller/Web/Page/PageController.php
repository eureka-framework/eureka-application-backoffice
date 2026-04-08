<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Controller\Web\Page;

use Application\Controller\Web\AbstractWebController;
use Eureka\Kernel\Http\Exception\HttpNotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PageController extends AbstractWebController
{
    private const array ALLOWED_PAGES = [
        'ui-general',
        'ui-timeline',
        'widgets-small-box',
        'widgets-info-box',
        'widgets-card',
        'forms-general',
        'tables-simple',
    ];

    /**
     * @param ServerRequestInterface $serverRequest
     * @return ResponseInterface
     */
    public function index(ServerRequestInterface $serverRequest): ResponseInterface
    {
        /** @var string $page */
        $page = $serverRequest->getAttribute('page');

        if (!in_array($page, self::ALLOWED_PAGES, true)) {
            throw new HttpNotFoundException('Page not found', 404);
        }

        return $this->getResponse($this->render("@app/page/$page.html.twig"));
    }
}
