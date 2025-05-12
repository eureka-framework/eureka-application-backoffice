<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Service\Twig;

use Symfony\Component\Routing\Router;

class TwigHelper
{
    /**
     * TwigHelper constructor.
     *
     * @param Router $router
     * @param string $webAssetsPath
     */
    public function __construct(
        private readonly Router $router,
        private readonly string $webAssetsPath,
    ) {}

    /**
     * @return array<string, callable>
     */
    public function getCallbackFunctions(): array
    {
        return [
            'importmap' => $this->importmap(...),
            'path'      => $this->path(...),
            'image'     => $this->image(...),
            'asset'     => $this->asset(...),
        ];
    }

    public function importmap(string $name): string
    {
        $importMapGenerator = new TwigImportMapGenerator($this->webAssetsPath, $name);

        return
            $importMapGenerator->css() .
            $importMapGenerator->importmap() .
            $importMapGenerator->js()
        ;
    }


    /**
     * @param  string $routeName
     * @param  array<string, string|int|float> $params
     * @return string
     */
    public function path(string $routeName, array $params = []): string
    {
        return $this->router->generate($routeName, $params);
    }

    /**
     * @param string $filename
     * @param string $baseUrl
     * @return string
     */
    public function image(string $filename, string $baseUrl = '/assets/images'): string
    {
        return $this->getRealAssetPath($filename, $baseUrl);
    }

    /**
     * @param string $filename
     * @param string $baseUrl
     * @return string
     */
    public function asset(string $filename, string $baseUrl = '/assets'): string
    {
        return $this->getRealAssetPath($filename, $baseUrl);
    }

    /**
     * @param string $filename
     * @param string $baseUrl
     * @return string
     */
    private function getRealAssetPath(string $filename, string $baseUrl): string
    {
        $filePath = \trim($baseUrl, ' /') . '/' . \ltrim($filename, '/');
        if (!isset($this->assetsManifest[$filePath])) {
            return '';
        }

        return $this->assetsManifest[$filePath];
    }
}
