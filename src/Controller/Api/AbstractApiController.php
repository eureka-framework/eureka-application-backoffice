<?php

/*
 * Copyright (c) Deezer
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Controller\Api;

use Eureka\Kernel\Http\Controller\Controller;
use Psr\Http\Message\ResponseInterface;

/**
 * Class AbstractApiController
 *
 * @author Romain Cottard
 */
abstract class AbstractApiController extends Controller
{
    /**
     * @param mixed $data
     * @param array<mixed> $errors
     * @return ResponseInterface
     */
    protected function getResponseJsonSuccess(mixed $data, array $errors = []): ResponseInterface
    {
        $content = ['data' => $data];

        if (!empty($errors)) {
            $content['errors'] = $errors;
        }

        return $this->getResponseJson($content);
    }

    /**
     * @param array<mixed> $errors
     * @param int $httpCode
     * @param mixed $data
     * @return ResponseInterface
     */
    protected function getResponseJsonError(array $errors, int $httpCode = 500, mixed $data = null): ResponseInterface
    {
        $content = [
            'data'   => $data,
            'errors' => $errors,
        ];

        return $this->getResponseJson($content, $httpCode);
    }

    /**
     * @param \Exception $exception
     * @param int $httpCode
     * @return array<string, string>
     */
    protected function getErrorItem(\Throwable $exception, int $httpCode = 500): array
    {
        $title = self::HTTP_CODE_MESSAGES[$httpCode] ?? 'Unknown';

        return [
            'status' => (string) $httpCode,
            'title'  => $title,
            'code'   => !empty($exception->getCode()) ? (string) $exception->getCode() : '1000',
            'detail' => !empty($exception->getMessage()) ? $exception->getMessage() : 'Undefined message',
        ];
    }
}
