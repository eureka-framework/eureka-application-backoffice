<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Controller\Common\Traits;

/**
 * Trait AssetsAwareTrait
 *
 * @author Romain Cottard
 */
trait AssetsAwareTrait
{
    /** @var string $rootDirectory */
    protected string $rootDirectory;

    /** @var array $jsFiles */
    protected array $jsFiles = [];

    /** @var array $cssFiles */
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
     * @return array
     */
    protected function getCssFiles(): array
    {
        return $this->cssFiles;
    }

    /**
     * @return array
     */
    protected function getJsFiles(): array
    {
        return $this->jsFiles;
    }

    /**
     * @return void
     */
    protected function initializeAssets(): void
    {
        $entryFile    = $this->rootDirectory . '/web/assets/entrypoints.json';
        $manifestFile = $this->rootDirectory . '/web/assets/manifest.json';

        if (!is_readable($entryFile) || !is_readable($manifestFile)) {
            throw new \RuntimeException('entrypoints.json or manifest.json file is not readable.');
        }

        $entrypoints = json_decode(file_get_contents($entryFile), true);

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
                    $existingFiles['js'][$file] = true;
                    $this->jsFiles[$entryName][]  = $file;
                }
            }

            foreach ($entrypoint['css'] as $file) {

                if (!isset($existingFiles['css'][$file])) {
                    $existingFiles['css'][$file] = true;
                    $this->cssFiles[$entryName][]  = $file;
                }
            }
        }
    }
}
