<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Service\Twig;

use Application\Exception\TwigHelperException;
use Symfony\Component\Routing\Router;

/**
 * Class Helper
 *
 * @author Romain Cottard
 */
class TwigHelper
{
    /** @var array<string> $assetsManifest */
    private array $assetsManifest;

    /**
     * TwigHelper constructor.
     *
     * @param Router $router
     * @param string $webAssetsPath
     */
    public function __construct(private readonly Router $router, string $webAssetsPath)
    {
        $this->initializeAssetsManifest($webAssetsPath);
    }

    /**
     * @param string $webAssetsPath
     * @return void
     */
    private function initializeAssetsManifest(string $webAssetsPath): void
    {
        $manifestFile = $webAssetsPath . '/manifest.json';

        if (!\is_readable($manifestFile)) {
            throw new TwigHelperException('manifest.json file is not readable.', 1100);
        }


        try {
            /** @var array<string> $json */
            $json = \json_decode(
                (string) \file_get_contents($manifestFile),
                true,
                flags: \JSON_THROW_ON_ERROR,
            );

            $this->assetsManifest = $json;
        } catch (\JsonException $exception) {
            throw new TwigHelperException('Unable to decode manifest.json file!', 1101, $exception);
        }
    }

    /**
     * @return array<string, callable>
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
