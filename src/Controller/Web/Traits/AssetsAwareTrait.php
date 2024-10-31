<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Controller\Web\Traits;

/**
 * Trait AssetsAwareTrait
 *
 * @author Romain Cottard
 */
trait AssetsAwareTrait
{
    /** @var string $rootDirectory */
    protected string $rootDirectory;

    /** @var array<string, string[]> $jsFiles */
    protected array $jsFiles = [];

    /** @var array<string, string[]> $cssFiles */
    protected array $cssFiles = [];

    /**
     * @param string $rootDirectory
     * @return void
     */
    public function setRootDirectory(string $rootDirectory): void
    {
        $this->rootDirectory = $rootDirectory;
    }

    /**
     * @return array<string, string[]>
     */
    protected function getCssFiles(): array
    {
        return $this->cssFiles;
    }

    /**
     * @return array<string, string[]>
     */
    protected function getJsFiles(): array
    {
        return $this->jsFiles;
    }

    /**
     * @return void
     * @throws \JsonException
     */
    protected function initializeAssets(): void
    {
        $entryFile    = $this->rootDirectory . '/web/assets/entrypoints.json';
        $manifestFile = $this->rootDirectory . '/web/assets/manifest.json';

        if (!\is_readable($entryFile) || !\is_readable($manifestFile)) {
            throw new \RuntimeException('entrypoints.json or manifest.json file is not readable.');
        }

        /** @var array{entrypoints: array<string, array{js: string[], css: string[]}>} $entrypoints */
        $entrypoints = \json_decode((string) \file_get_contents($entryFile), true, flags: \JSON_THROW_ON_ERROR);

        $this->cssFiles = [];
        $this->jsFiles  = [];

        $existingFiles = [
            'js'  => [],
            'css' => [],
        ];

        foreach ($entrypoints['entrypoints'] as $entryName => $entrypoint) {
            $this->jsFiles[$entryName]  = [];
            $this->cssFiles[$entryName] = [];

            foreach ($entrypoint['js'] as $file) {
                if (!isset($existingFiles['js'][$file])) {
                    $existingFiles['js'][$file]  = true;
                    $this->jsFiles[$entryName][] = $file;
                }
            }

            foreach ($entrypoint['css'] as $file) {
                if (!isset($existingFiles['css'][$file])) {
                    $existingFiles['css'][$file]  = true;
                    $this->cssFiles[$entryName][] = $file;
                }
            }
        }
    }
}
