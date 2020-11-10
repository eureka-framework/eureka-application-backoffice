<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Middleware;

use Application\Service\Twig\TwigHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Routing\Router;
use Twig;
use Twig\Extra\Markdown\MarkdownExtension;

/**
 * Class TwigMiddleware
 *
 * @author Romain Cottard
 */
class TwigMiddleware implements MiddlewareInterface
{
    /** @var Router $router */
    protected Router $router;

    /** @var Twig\Environment */
    protected Twig\Environment $twig;

    /** @var array $twigPaths */
    protected array $twigPaths;

    /** @var string $webAssetsPath */
    protected string $webAssetsPath;

    /**
     * TwigMiddleware constructor.
     *
     * @param Router $router
     * @param Twig\Environment $twig
     * @param array $twigPaths
     * @param string $webAssetsPath
     */
    public function __construct(
        Router $router,
        Twig\Environment $twig,
        array $twigPaths = [],
        string $webAssetsPath = ''
    ) {
        $this->router        = $router;
        $this->twig          = $twig;
        $this->twigPaths     = $twigPaths;
        $this->webAssetsPath = $webAssetsPath;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->configurePaths($this->twigPaths);
        $this->configureFunctions($this->router);
        $this->configureExtensions();

        return $handler->handle($request);
    }

    /**
     * @param array $paths
     * @return void
     * @throws Twig\Error\LoaderError
     */
    private function configurePaths(array $paths): void
    {
        //~ Add path
        $loader = $this->twig->getLoader();
        if ($loader instanceof Twig\Loader\FilesystemLoader) {
            foreach ($paths as $path => $namespace) {
                $loader->addPath($path, $namespace);
            }
        }
    }

    /**
     * @param Router $router
     * @return void
     */
    private function configureFunctions(Router $router): void
    {
        //~ Add functions to main twig instance
        $helper = new TwigHelper($router, $this->webAssetsPath);
        foreach ($helper->getCallbackFunctions() as $name => $callback) {
            $this->twig->addFunction(new Twig\TwigFunction($name, $callback));
        }
    }

    /**
     * @return void
     */
    private function configureExtensions(): void
    {
        $this->twig->addRuntimeLoader(new class implements Twig\RuntimeLoader\RuntimeLoaderInterface {
            public function load($class)
            {
                if (Twig\Extra\Markdown\MarkdownRuntime::class === $class) {
                    return new Twig\Extra\Markdown\MarkdownRuntime(new Twig\Extra\Markdown\DefaultMarkdown());
                }
            }
        });

        $this->twig->addExtension(new MarkdownExtension());
    }
}
