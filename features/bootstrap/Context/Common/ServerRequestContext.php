<?php

/*
 * Copyright (c) Deezer
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Behat\Context\Common;

use Application\Behat\Helper\JsonWebTokenServiceAwareTrait;
use Application\Behat\Helper\ServiceMockAwareTrait;
use Application\Behat\Mock\UserTrait;
use Application\Domain\User\Repository\UserRepositoryInterface;
use Behat\Behat\Context\Context;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Trait RequestAwareTrait
 *
 * @author Romain Cottard
 */
class ServerRequestContext implements Context
{
    use JsonWebTokenServiceAwareTrait;
    use UserTrait;
    use ServiceMockAwareTrait;

    /** @var array<string, string> $requestHeaders  */
    private static array $requestHeaders = [];

    /** @var array<string, string> $requestQueryParams  */
    private static array $requestQueryParams = [];

    /** @var array<string, string> $requestBodyFields  */
    private static array $requestBodyFields = [];
    private static array $requestFiles = [];

    public function __construct(private readonly ?string $fileUploadPath = null) {}

    /**
     * @BeforeScenario
     *
     * @return void
     * @throws \Exception
     */
    public function initialize(): void
    {
        $this->resetServerRequest();

        //~ Always required
        $this->iSetRequestHeaderNameWithValue('Accept', 'application/json');
        $this->iSetRequestHeaderNameWithValue('Content-Type', 'application/json');
    }

    /**
     * @return void
     */
    public function resetServerRequest(): void
    {
        self::$requestHeaders = [];
        self::$requestQueryParams = [];
        self::$requestBodyFields = [];
        self::$requestFiles = [];
    }

    /**
     * @Given I set request header name :arg1 with value :arg2
     */
    public function iSetRequestHeaderNameWithValue(string $name, string $value): void
    {
        self::$requestHeaders[$name] = $value;
    }

    /**
     * @Given I set request query parameter field :arg1 with value :arg2
     */
    public function iSetRequestQueryParameterFieldWithValue(string $field, string $value): void
    {
        self::$requestQueryParams[$field] = $value;
    }

    /**
     * @Given I set request query parameter field :arg1 with array values :arg12
     *
     * @param string $field
     * @param mixed $value
     */
    public function iSetRequestQueryParametersFieldWithArrayValue(string $field, mixed $value): void
    {
        self::$requestQueryParams[$field][] = $value;
    }

    /**
     * @Given I set request query parameter field :arg1 with empty value
     */
    public function iSetRequestQueryParameterFieldWithEmptyValue(string $field): void
    {
        self::iSetRequestQueryParameterFieldWithValue($field, '');
    }

    /**
     * @Given I omit to set request query parameter field :arg1
     */
    public function iOmitToSetRequestQueryParameterField(string $field): void
    {
        if (isset(self::$requestQueryParams[$field])) {
            unset(self::$requestQueryParams[$field]);
        }
    }

    /**
     * @Given I set request body field :arg1 with value :arg2
     *
     * @$value string|int|float|bool|null|array<mixed>
     */
    public function iSetRequestBodyFieldWithValue(string $field, string|int|float|bool|null|array $value): void
    {
        $value = $value === "null" ? null : $value;
        self::$requestBodyFields[$field] = $value;
    }

    /**
     * @Given I set request body field :arg1 with array value: :arg2 => :arg3
     */
    public function iSetRequestBodyFieldWithArrayValue(string $field, int|string $index, mixed $value): void
    {
        self::$requestBodyFields[$field][$index] = $value;
    }

    /**
     * @Given I set request body field :arg1 with json array value: :arg2 => :arg3
     *
     * @throws \JsonException
     */
    public function iSetRequestBodyFieldWithJsonArrayValue(string $field, int|string $index, string $value): void
    {
        self::$requestBodyFields[$field][$index] = json_decode($value, flags: JSON_THROW_ON_ERROR);
    }

    /**
     * @Given I set request body array field :arg1 with value :arg2
     */
    public function iSetRequestBodyArrayFieldWithValue(string $field, mixed $value): void
    {
        self::$requestBodyFields[0][$field] = $value;
    }

    /**
     * @Given I set request body field :arg1 with empty value
     */
    public function iSetRequestBodyFieldWithEmptyValue(string $field): void
    {
        $this->iSetRequestBodyFieldWithValue($field, '');
    }

    /**
     * @Given I omit to set request body field :arg1
     */
    public function iOmitToSetRequestBodyField(string $field): void
    {
        if (isset(self::$requestBodyFields[$field])) {
            unset(self::$requestBodyFields[$field]);
        }
    }

    /**
     * @Given I set request query parameter token :tokenState for user :userId
     *
     * @throws \Exception
     */
    public function iSetRequestQueryParameterTokenForUser(string $tokenState, int $userId): void
    {
        $token = $this->getTokenWithState($tokenState, $userId);

        $this->iSetRequestQueryParameterFieldWithValue('token', $token->toString());
    }

    /**
     * @Given I set request query parameter token to create a new account
     *
     * @throws \Exception
     */
    public function iSetRequestQueryParameterTokenToCreateANewAccount(): void
    {
        $token = $this->jwtService->generateSignUpToken('user', 'user@example.com', \time());

        $this->iSetRequestQueryParameterFieldWithValue('token', $token->toString());
    }

    /**
     * @Given I set request header token :tokenState for user :userid
     *
     * @throws \Exception
     */
    public function iSetRequestHeaderTokenForUser(string $tokenState, int $userId): void
    {
        $token = $this->getTokenWithState($tokenState, $userId);

        $this->iSetRequestHeaderNameWithValue('Authorization', 'JWT ' . $token->toString());
    }

    /**
     * @Given I set request header registered token :tokenState for user :userid
     *
     * @throws \JsonException
     * @throws \Exception
     */
    public function iSetRequestHeaderRegisteredTokenForUser(string $tokenState, int $userId): void
    {
        $token = $this->getTokenWithState($tokenState, $userId);

        $userRepository = ClientApplicationContext::getService(UserRepositoryInterface::class);
        $user = $userRepository->findById($userId);
        $user->registerToken($token);
        $userRepository->persist($user);

        $this->iSetRequestHeaderNameWithValue('Authorization', 'JWT ' . $token->toString());
    }

    /**
     * @Given I set request header registered token :tokenState for mock user :userId
     *
     * @throws \JsonException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function iSetRequestHeaderRegisteredTokenForMockUser(string $tokenState, int $userId): void
    {
        $token = $this->getTokenWithState($tokenState, $userId);

        $this->registerMockService(
            [UserRepositoryInterface::class => UserRepositoryInterface::class],
            'findById',
            $this->getUser($userId, 'user_deezer@deezer.com', 'password', 'User Deezer', true, $token),
        );

        $this->iSetRequestHeaderNameWithValue('Authorization', 'JWT ' . $token->toString());
    }

    /**
     * @param array<string, mixed> $serverParams
     *
     * @throws \JsonException
     */
    public static function createServerRequest(
        string $method,
        string $path,
        array $serverParams = [],
    ): ServerRequestInterface {
        $factory = new Psr17Factory();

        //~ Build uri string with query parameters
        $uriPath = $path . (!empty(self::$requestQueryParams) ? '?' . http_build_query(self::$requestQueryParams) : '');

        $uri = $factory->createUri($uriPath);
        $serverRequest = $factory->createServerRequest($method, $uri, $serverParams);

        //~ Add query params
        if (!empty(self::$requestQueryParams)) {
            $serverRequest = $serverRequest->withQueryParams(self::$requestQueryParams);
        }

        //~ Add body content
        if (!empty(self::$requestBodyFields)) {
            $body = $factory->createStream(\json_encode(self::$requestBodyFields, flags: JSON_THROW_ON_ERROR));
            $serverRequest = $serverRequest->withBody($body)->withParsedBody(self::$requestBodyFields);
        }

        if (!empty(self::$requestFiles)) {
            $files = [];
            foreach (self::$requestFiles as $fieldName => $file) {
                $files[$fieldName] = $factory->createUploadedFile(
                    $factory->createStreamFromFile($file['filePath']),
                    error: $file['error'],
                );
            }
            $serverRequest = $serverRequest->withUploadedFiles($files);
        }

        //~ Add headers
        foreach (self::$requestHeaders as $name => $header) {
            $serverRequest = $serverRequest->withAddedHeader($name, $header);
        }

        return $serverRequest;
    }

    /**
     * @Given I set request form-data file :key with file :filename
     * @Given I set request form-data file :key with file :filename and error :error
     */
    public function iSetRequestFormDataFile(string $key, string $filename, ?int $error = 0): void
    {
        self::$requestFiles[$key]['filePath'] = $this->fileUploadPath . $filename;
        self::$requestFiles[$key]['error'] = $error;
    }
}
