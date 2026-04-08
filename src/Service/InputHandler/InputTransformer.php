<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Service\InputHandler;

use Eureka\Component\Validation\Exception\ValidationException;
use Eureka\Component\Validation\ValidatorFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

class InputTransformer
{
    public function __construct(
        private readonly ValidatorFactoryInterface $validatorFactory,
    ) {}

    /**
     * @template T of object
     * @param class-string<T> $className
     * @return array{0: T, 1: array<string, string>}
     * @throws \ReflectionException
     */
    public function transform(ServerRequestInterface $serverRequest, string $className): array
    {
        $dtoAttributes = $this->getAttributes($className);

        $values = [];
        $errors = [];

        foreach ($dtoAttributes as $dtoAttribute) {
            try {
                $values[$dtoAttribute->name] = $this->getValue($dtoAttribute, $serverRequest);
            } catch (ValidationException $exception) {
                $errors[$dtoAttribute->name] = $exception->getMessage() . "(attribute $dtoAttribute->name)";
            }
        }

        return [new $className(...$values), $errors];
    }

    /**
     * @param class-string $className
     * @return list<DTOAttribute> $attributes
     * @throws \ReflectionException
     */
    private function getAttributes(string $className): array
    {
        $reflection = new \ReflectionClass($className);
        $properties = $reflection->getProperties();

        $attributes = [];
        foreach ($properties as $property) {
            foreach ($property->getAttributes(DTOAttribute::class) as $attribute) {
                $attributes[] = $attribute->newInstance();
            }
        }

        return $attributes;
    }

    private function getValue(
        DTOAttribute $dtoAttribute,
        ServerRequestInterface $serverRequest,
    ): int|float|bool|string|null {
        $body  = (array) $serverRequest->getParsedBody();
        $query = $serverRequest->getQueryParams();

        /** @var string|array<mixed> $value */
        $value = match ($dtoAttribute->from) {
            DTOAttribute::FROM_BODY  => $body[$dtoAttribute->name] ?? null,
            DTOAttribute::FROM_QUERY => $query[$dtoAttribute->name] ?? null,
            default => throw new \DomainException('Unknown DTOAttribute type !'),
        };

        if (\is_array($value)) {
            throw new \UnexpectedValueException(); // TODO: handle array
        }

        return $this->castValue($dtoAttribute->type, $value, $dtoAttribute->options);
    }

    /**
     * @param array<string, int|float|bool|string|null> $options
     * @throws ValidationException
     */
    private function castValue(string $type, mixed $value, array $options): int|float|bool|string|null
    {
        $validator = $this->validatorFactory->getValidator($type);

        /** @var int|float|bool|string|null $value */
        $value = $validator->validate($value, $options); // validate and auto cast with real type if necessary

        return $value;
    }
}
