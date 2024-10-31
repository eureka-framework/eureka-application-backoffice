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
use PHPUnit\Framework\Assert;

/**
 * Class CommonResponseContext
 *
 * @author Romain Cottard
 */
class CommonResponseContext implements Context
{
    /**
     * @Then I should get a success response
     */
    public function iShouldGetASuccessResponse(): void
    {
        $response = ClientApplicationContext::getResponse();
        $content  = ClientApplicationContext::getResponseContentObject();

        Assert::assertSame(200, $response->getStatusCode(), var_export($content, true));

        Assert::assertTrue(property_exists($content, 'data'), var_export($content, true));
        Assert::assertFalse(property_exists($content, 'errors'), var_export($content, true));
        Assert::assertNotNull($content->data);
    }

    /**
     * @Then I should get a success response with errors
     */
    public function iShouldGetASuccessResponseWithErrors(): void
    {
        $response = ClientApplicationContext::getResponse();
        $content  = ClientApplicationContext::getResponseContentObject();

        Assert::assertSame(200, $response->getStatusCode(), var_export($content, true));

        Assert::assertTrue(property_exists($content, 'data'), var_export($content, true));
        Assert::assertTrue(property_exists($content, 'errors'), var_export($content, true));
        Assert::assertNotNull($content->data);
        Assert::assertNotNull($content->errors);
    }

    /**
     * @Then I should get a success response with null data
     */
    public function iShouldGetASuccessResponseWithNullData(): void
    {
        $response = ClientApplicationContext::getResponse();
        $content  = ClientApplicationContext::getResponseContentObject();

        Assert::assertSame(200, $response->getStatusCode(), var_export($content, true));

        Assert::assertTrue(property_exists($content, 'data'), var_export($content, true));
        Assert::assertFalse(property_exists($content, 'errors'), var_export($content, true));
        Assert::assertNull($content->data);
    }

    /**
     * @Then I should get an error response
     */
    public function iShouldGetAnErrorResponse(): void
    {
        $response = ClientApplicationContext::getResponse();
        $content  = ClientApplicationContext::getResponseContentObject();

        Assert::assertNotEquals(200, $response->getStatusCode(), var_export($content, true));

        Assert::assertTrue(property_exists($content, 'errors'), var_export($content, true));
        Assert::assertNotEmpty($content->errors, var_export($content, true));
    }

    /**
     * @Then The response contains status code :arg1 and error code :arg2 in first error item
     *
     * @param int $httpCode
     * @param string $errorCode
     */
    public function theResponseContainsStatusCodeAndErrorCodeInFirstErrorItem(int $httpCode, string $errorCode): void
    {
        $response = ClientApplicationContext::getResponse();
        $content  = ClientApplicationContext::getResponseContentObject();
        $error    = reset($content->errors);

        $contentStringMessage = var_export($content, true);
        Assert::assertSame($httpCode, $response->getStatusCode(), $contentStringMessage);
        Assert::assertEquals((string) $httpCode, $error->status, $contentStringMessage);
        Assert::assertEquals($errorCode, $error->code, $contentStringMessage);
    }

    /**
     * @Then The response contains status code :arg1
     *
     * @param int $httpCode
     */
    public function theResponseContainsStatusCode(int $httpCode): void
    {
        $response = ClientApplicationContext::getResponse();
        $content  = ClientApplicationContext::getResponseContentObject();

        $contentStringMessage = var_export($content, true);
        Assert::assertSame($httpCode, $response->getStatusCode(), $contentStringMessage);
    }

    /**
     * @Then The response does not have errors
     * @Then The response has :errors errors
     */
    public function theResponseHasXErrors(int $errorsNumber = 0): void
    {
        $content = ClientApplicationContext::getResponseContentObject();
        $errors = $content->errors ?? [];

        Assert::assertEquals($errorsNumber, count($errors), var_export($content, true));
    }

    /**
     * @Then The response contains a value number :arg1
     *
     * @param int $value
     */
    public function theResponseContainsAValueNumber(int $value): void
    {
        $content = ClientApplicationContext::getResponseContentObject();

        Assert::assertEquals($value, (int) $content->data, var_export($content, true));
    }

    /**
     * @Then The response contains an empty array
     */
    public function theResponseContainsAnEmptyArray(): void
    {
        $content = ClientApplicationContext::getResponseContentObject();
        $expected = [];

        Assert::assertEquals($expected, $content->data, var_export($content, true));
    }

    /**
     * @Then The response contains a value string :arg1
     *
     * @param string $value
     */
    public function theResponseContainsAValueString(string $value): void
    {
        $content = ClientApplicationContext::getResponseContentObject();

        Assert::assertEquals($value, (string) $content->data, var_export($content, true));
    }

    /**
     * @Then The response contains a boolean value :arg1
     *
     * @param bool $value
     */
    public function theResponseContainsABooleanValue(bool $value): void
    {
        $content = ClientApplicationContext::getResponseContentObject();

        Assert::assertEquals($value, (bool) $content->data, var_export($content, true));
    }

    /**
     * @Then The response counts number of elements equal to :arg1
     *
     * @param int $number
     */
    public function theResponseCountsNumberOfElementsEqualTo(int $number): void
    {
        $content = ClientApplicationContext::getResponseContentObject();

        Assert::assertCount($number, (array) $content->data, var_export($content, true));
    }
}
