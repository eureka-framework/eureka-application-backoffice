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

/**
 * Class Helper
 *
 * @author Romain Cottard
 */
class TwigHelper
{
    /** @var Router */
    private Router $router;

    /** @var array $assetsManifest */
    private array $assetsManifest;

    /**
     * TwigHelper constructor.
     *
     * @param Router $router
     * @param string $webAssetsPath
     */
    public function __construct(Router $router, string $webAssetsPath)
    {
        $this->router = $router;

        $this->initializeAssetsManifest($webAssetsPath);
    }

    /**
     * @param string $webAssetsPath
     * @return void
     */
    private function initializeAssetsManifest(string $webAssetsPath): void
    {
        $manifestFile = $webAssetsPath . '/manifest.json';

        if (!is_readable($manifestFile)) {
            throw new \RuntimeException('manifest.json file is not readable.');
        }

        $this->assetsManifest = json_decode(file_get_contents($manifestFile), true);
    }

    /**
     * @return array
     */
    public function getCallbackFunctions(): array
    {
        return [
            'path'  => [$this, 'path'],
            'image' => [$this, 'image'],
            'asset' => [$this, 'asset'],
        ];
    }

    /**
     * @param  string $routeName
     * @param  array $params
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
        $filePath = trim($baseUrl, ' /') . '/' . ltrim($filename, '/');
        if (!isset($this->assetsManifest[$filePath])) {
            return '';
        }

        return $this->assetsManifest[$filePath];
    }
}
