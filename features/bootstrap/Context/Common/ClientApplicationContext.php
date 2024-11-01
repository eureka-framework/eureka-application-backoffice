<?php

/*
 * Copyright (c) Deezer
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Behat\Context\Common;

use Behat\Behat\Context\Context;
use Eureka\Kernel\Http\Application\Application;
use Eureka\Kernel\Http\Kernel;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Class ClientApplicationContext
 *
 * @author Romain Cottard
 */
class ClientApplicationContext implements Context
{
    private static Kernel $kernel;
    private static Application $application;
    private static ?ResponseInterface $response = null;
    private static ?\stdClass $responseObject = null;

    /**
     * Class constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        //~ Pre-initialize application for base container (used in many context constructors)
        $this->initialize();
    }

    /**
     * @return void
     *
     * @BeforeSuite
     */
    public static function prepare(): void
    {
        //~ Enable bypass final class for clients
    }

    /**
     * When a service is already instantiate in container, it could not be replaced.
     * So, the initialization must be done before each scenario to allow update of service in container.
     *
     * @BeforeScenario
     *
     * @return void
     * @throws \Exception
     */
    public function initialize(): void
    {
        //~ When kernel is already defined, reset the container before resetting the kernel to avoid memory leak
        if (isset(self::$kernel)) {
            self::getContainer()->reset();
        }

        //~ Reset vars & force reset application & kernel properties (to ensure we have no memory leak)
        self::$responseObject = null;
        self::$response       = null;

        //~ Always start a new kernel & application
        self::$kernel      = $this->getKernel();
        self::$application = new Application(self::$kernel);
    }

    /**
     * @When I send the request to the endpoint :arg1 with http method :arg2
     *
     * @param string $path
     * @param string $method
     * @return void
     * @throws \JsonException
     */
    public function iSendTheRequestToTheEndpointWithHttpMethod(string $path, string $method): void
    {
        $serverRequest = ServerRequestContext::createServerRequest($method, $path);

        //~ Get response from app
        self::$response = self::$application->run($serverRequest);

        //~ Get content response à json object decoded
        $responseString = (string) self::$response->getBody();
        self::$responseObject = (object) \json_decode($responseString, flags: JSON_THROW_ON_ERROR);

        //~ Rewind response body (for further usage)
        self::$response->getBody()->rewind();

    }

    /**
     * @When I send :arg1 requests to the endpoint :arg2 with http method :arg3
     *
     * @param int $number
     * @param string $path
     * @param string $method
     * @return void
     * @throws \JsonException
     */
    public function iSendRequestsToTheEndpointWithHttpMethod(int $number, string $path, string $method): void
    {
        $serverRequest = ServerRequestContext::createServerRequest($method, $path);

        //~ Skip N-1 first requests
        for ($n = 1; $n < $number; $n++) {
            self::$application->run($serverRequest);
        }

        //~ Keep response from app only for the last request
        self::$response = self::$application->run($serverRequest);

        //~ Get content response as json object decoded
        $responseString = (string) self::$response->getBody();
        self::$responseObject = (object) \json_decode($responseString, flags: JSON_THROW_ON_ERROR);

        //~ Rewind response body (for further usage)
        self::$response->getBody()->rewind();

    }

    public static function getContainer(): ContainerInterface&ResetInterface
    {
        /** @var ContainerInterface&ResetInterface $container */
        $container = self::$kernel->getContainer();

        return $container;
    }

    /**
     * @template T
     * @param class-string<T> $serviceName
     * @return T
     */
    public static function getService(string $serviceName)
    {
        /** @var T $service */
        $service = self::getContainer()->get($serviceName);

        return $service;
    }

    /**
     * @return ResponseInterface
     */
    public static function getResponse(): ResponseInterface
    {
        if (self::$response === null) {
            throw new \UnexpectedValueException('Response is null !');
        }

        return self::$response;
    }

    /**
     * @return \stdClass
     */
    public static function getResponseContentObject(): \stdClass
    {
        if (self::$responseObject === null) {
            throw new \UnexpectedValueException('Response object is null !');
        }

        return self::$responseObject;
    }

    /**
     * @return Kernel
     * @throws \Exception
     */
    private static function getKernel(): Kernel
    {
        $root  = (string) realpath(__DIR__ . '/../../../..');
        $env   = 'test';
        $debug = true;

        return new Kernel($root, $env, $debug);
    }
}
