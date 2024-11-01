<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Domain\User\Entity\Abstracts;

use Eureka\Component\Orm\EntityInterface;
use Eureka\Component\Orm\Traits;
use Eureka\Component\Validation\Exception\ValidationException;
use Eureka\Component\Validation\ValidatorFactoryInterface;
use Eureka\Component\Validation\ValidatorEntityFactoryInterface;
use Application\Domain\User\Entity\User;
use Application\Domain\User\Repository\UserRepositoryInterface;

/**
 * Abstract User data class.
 *
 * /!\ AUTO GENERATED FILE. DO NOT EDIT THIS FILE.
 * You can add your specific code in child class: User
 *
 * @author Eureka Orm Generator
 */
abstract class AbstractUser implements EntityInterface
{
    /** @use Traits\EntityTrait<UserRepositoryInterface, User> */
    use Traits\EntityTrait;

    /** @var int $id Property id */
    protected int $id = 0;

    /** @var bool $isEnabled Property isEnabled */
    protected bool $isEnabled = true;

    /** @var int $privileges Property privileges */
    protected int $privileges = 0;

    /** @var string $email Property email */
    protected string $email = '';

    /** @var string $password Property password */
    protected string $password = '';

    /** @var string $firstName Property firstName */
    protected string $firstName = '';

    /** @var string $lastName Property lastName */
    protected string $lastName = '';

    /** @var string $pseudo Property pseudo */
    protected string $pseudo = '';

    /** @var string $tokenHashList Property tokenHashList */
    protected string $tokenHashList = '[]';

    /** @var string|null $dateFirstAccess Property dateFirstAccess */
    protected ?string $dateFirstAccess = null;

    /** @var string|null $dateLastAccess Property dateLastAccess */
    protected ?string $dateLastAccess = null;

    /** @var string $dateCreate Property dateCreate */
    protected string $dateCreate = 'current_timestamp()';

    /** @var string|null $dateUpdate Property dateUpdate */
    protected ?string $dateUpdate = null;

    /**
     * AbstractEntity constructor.
     *
     * @param UserRepositoryInterface $repository
     * @param ValidatorFactoryInterface|null $validatorFactory
     * @param ValidatorEntityFactoryInterface|null $validatorEntityFactory
     */
    public function __construct(
        UserRepositoryInterface $repository,
        ?ValidatorFactoryInterface $validatorFactory = null,
        ?ValidatorEntityFactoryInterface $validatorEntityFactory = null
    ) {
        $this->setRepository($repository);
        $this->setValidatorFactories($validatorFactory, $validatorEntityFactory);

        $this->initializeValidatorConfig();
    }

    protected function initializeValidatorConfig(): void
    {
        $this->setValidatorConfig([
            'user_id' => [
                'type'      => 'integer',
                'options'   => ['min_range' => -9.223372036854776E+18, 'max_range' => 9223372036854775807],
            ],
            'user_is_enabled' => [
                'type'      => 'boolean',
                'options'   => [],
            ],
            'user_privileges' => [
                'type'      => 'integer',
                'options'   => ['min_range' => -9.223372036854776E+18, 'max_range' => 9223372036854775807],
            ],
            'user_email' => [
                'type'      => 'string',
                'options'   => ['min_length' => 0, 'max_length' => 150],
            ],
            'user_password' => [
                'type'      => 'string',
                'options'   => ['min_length' => 0, 'max_length' => 100],
            ],
            'user_first_name' => [
                'type'      => 'string',
                'options'   => ['min_length' => 0, 'max_length' => 150],
            ],
            'user_last_name' => [
                'type'      => 'string',
                'options'   => ['min_length' => 0, 'max_length' => 150],
            ],
            'user_pseudo' => [
                'type'      => 'string',
                'options'   => ['min_length' => 0, 'max_length' => 150],
            ],
            'user_token_hash_list' => [
                'type'      => 'string',
                'options'   => ['min_length' => 0, 'max_length' => 1000],
            ],
            'user_date_first_access' => [
                'type'      => 'datetime',
                'options'   => [],
            ],
            'user_date_last_access' => [
                'type'      => 'datetime',
                'options'   => [],
            ],
            'user_date_create' => [
                'type'      => 'datetime',
                'options'   => [],
            ],
            'user_date_update' => [
                'type'      => 'datetime',
                'options'   => [],
            ],
        ]);
    }

    /**
     * Get cache key
     *
     * @return string
     */
    public function getCacheKey(): string
    {
        return 'application.user.' . $this->getId();
    }

    /**
     * Get value for property "user_id"
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get value for property "user_is_enabled"
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * Get value for property "user_privileges"
     *
     * @return int
     */
    public function getPrivileges(): int
    {
        return $this->privileges;
    }

    /**
     * Get value for property "user_email"
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Get value for property "user_password"
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Get value for property "user_first_name"
     *
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * Get value for property "user_last_name"
     *
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * Get value for property "user_pseudo"
     *
     * @return string
     */
    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    /**
     * Get value for property "user_token_hash_list"
     *
     * @return string
     */
    public function getTokenHashList(): string
    {
        return $this->tokenHashList;
    }

    /**
     * Get value for property "user_date_first_access"
     *
     * @return string|null
     */
    public function getDateFirstAccess(): ?string
    {
        return $this->dateFirstAccess;
    }

    /**
     * Get value for property "user_date_last_access"
     *
     * @return string|null
     */
    public function getDateLastAccess(): ?string
    {
        return $this->dateLastAccess;
    }

    /**
     * Get value for property "user_date_create"
     *
     * @return string
     */
    public function getDateCreate(): string
    {
        return $this->dateCreate;
    }

    /**
     * Get value for property "user_date_update"
     *
     * @return string|null
     */
    public function getDateUpdate(): ?string
    {
        return $this->dateUpdate;
    }

    /**
     * Set value for property "user_id"
     *
     * @param  int $id
     * @return $this
     * @throws ValidationException
     */
    public function setId(int $id): self
    {
        $this->validateInput('user_id', $id);

        if ($this->exists() && $this->id !== $id) {
            $this->markFieldAsUpdated('id');
        }

        $this->id = $id;

        return $this;
    }

    /**
     * Set auto increment value.
     *
     * @param  integer $id
     * @return $this
     * @throws ValidationException
     */
    public function setAutoIncrementId(int $id): static
    {
        return $this->setId($id);
    }

    /**
     * Set value for property "user_is_enabled"
     *
     * @param  bool $isEnabled
     * @return $this
     * @throws ValidationException
     */
    public function setIsEnabled(bool $isEnabled): self
    {
        $this->validateInput('user_is_enabled', $isEnabled);

        if ($this->exists() && $this->isEnabled !== $isEnabled) {
            $this->markFieldAsUpdated('isEnabled');
        }

        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * Set value for property "user_privileges"
     *
     * @param  int $privileges
     * @return $this
     * @throws ValidationException
     */
    public function setPrivileges(int $privileges): self
    {
        $this->validateInput('user_privileges', $privileges);

        if ($this->exists() && $this->privileges !== $privileges) {
            $this->markFieldAsUpdated('privileges');
        }

        $this->privileges = $privileges;

        return $this;
    }

    /**
     * Set value for property "user_email"
     *
     * @param  string $email
     * @return $this
     * @throws ValidationException
     */
    public function setEmail(string $email): self
    {
        $this->validateInput('user_email', $email);

        if ($this->exists() && $this->email !== $email) {
            $this->markFieldAsUpdated('email');
        }

        $this->email = $email;

        return $this;
    }

    /**
     * Set value for property "user_password"
     *
     * @param  string $password
     * @return $this
     * @throws ValidationException
     */
    public function setPassword(string $password): self
    {
        $this->validateInput('user_password', $password);

        if ($this->exists() && $this->password !== $password) {
            $this->markFieldAsUpdated('password');
        }

        $this->password = $password;

        return $this;
    }

    /**
     * Set value for property "user_first_name"
     *
     * @param  string $firstName
     * @return $this
     * @throws ValidationException
     */
    public function setFirstName(string $firstName): self
    {
        $this->validateInput('user_first_name', $firstName);

        if ($this->exists() && $this->firstName !== $firstName) {
            $this->markFieldAsUpdated('firstName');
        }

        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Set value for property "user_last_name"
     *
     * @param  string $lastName
     * @return $this
     * @throws ValidationException
     */
    public function setLastName(string $lastName): self
    {
        $this->validateInput('user_last_name', $lastName);

        if ($this->exists() && $this->lastName !== $lastName) {
            $this->markFieldAsUpdated('lastName');
        }

        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Set value for property "user_pseudo"
     *
     * @param  string $pseudo
     * @return $this
     * @throws ValidationException
     */
    public function setPseudo(string $pseudo): self
    {
        $this->validateInput('user_pseudo', $pseudo);

        if ($this->exists() && $this->pseudo !== $pseudo) {
            $this->markFieldAsUpdated('pseudo');
        }

        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * Set value for property "user_token_hash_list"
     *
     * @param  string $tokenHashList
     * @return $this
     * @throws ValidationException
     */
    public function setTokenHashList(string $tokenHashList): self
    {
        $this->validateInput('user_token_hash_list', $tokenHashList);

        if ($this->exists() && $this->tokenHashList !== $tokenHashList) {
            $this->markFieldAsUpdated('tokenHashList');
        }

        $this->tokenHashList = $tokenHashList;

        return $this;
    }

    /**
     * Set value for property "user_date_first_access"
     *
     * @param  string|null $dateFirstAccess
     * @return $this
     * @throws ValidationException
     */
    public function setDateFirstAccess(?string $dateFirstAccess): self
    {
        if ($dateFirstAccess !== null) {
            $this->validateInput('user_date_first_access', $dateFirstAccess);
        }

        if ($this->exists() && $this->dateFirstAccess !== $dateFirstAccess) {
            $this->markFieldAsUpdated('dateFirstAccess');
        }

        $this->dateFirstAccess = $dateFirstAccess;

        return $this;
    }

    /**
     * Set value for property "user_date_last_access"
     *
     * @param  string|null $dateLastAccess
     * @return $this
     * @throws ValidationException
     */
    public function setDateLastAccess(?string $dateLastAccess): self
    {
        if ($dateLastAccess !== null) {
            $this->validateInput('user_date_last_access', $dateLastAccess);
        }

        if ($this->exists() && $this->dateLastAccess !== $dateLastAccess) {
            $this->markFieldAsUpdated('dateLastAccess');
        }

        $this->dateLastAccess = $dateLastAccess;

        return $this;
    }

    /**
     * Set value for property "user_date_create"
     *
     * @param  string $dateCreate
     * @return $this
     * @throws ValidationException
     */
    public function setDateCreate(string $dateCreate): self
    {
        $this->validateInput('user_date_create', $dateCreate);

        if ($this->exists() && $this->dateCreate !== $dateCreate) {
            $this->markFieldAsUpdated('dateCreate');
        }

        $this->dateCreate = $dateCreate;

        return $this;
    }

    /**
     * Set value for property "user_date_update"
     *
     * @param  string|null $dateUpdate
     * @return $this
     * @throws ValidationException
     */
    public function setDateUpdate(?string $dateUpdate): self
    {
        if ($dateUpdate !== null) {
            $this->validateInput('user_date_update', $dateUpdate);
        }

        if ($this->exists() && $this->dateUpdate !== $dateUpdate) {
            $this->markFieldAsUpdated('dateUpdate');
        }

        $this->dateUpdate = $dateUpdate;

        return $this;
    }
}
