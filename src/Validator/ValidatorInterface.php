<?php

declare(strict_types=1);

namespace Schnell\Validator;

use Psr\Http\Message\RequestInterface;
use Schnell\Schema\SchemaInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface ValidatorInterface
{
    /**
     * @psalm-api
     *
     * @return \Psr\Http\Message\RequestInterface|null
     */
    public function getRequest(): RequestInterface|null;

    /**
     * @param \Psr\Http\Message\RequestInterface|null $request
     * @return void
     */
    public function setRequest(RequestInterface|null $request): void;

    /**
     * @psalm-api
     *
     * @param \Psr\Http\Message\RequestInterface|null $request
     * @return \Schnell\Validator\ValidatorInterface
     */
    public function withRequest(
        RequestInterface|null $request
    ): ValidatorInterface;

    /**
     * @psalm-api
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array|null $data
     * @return bool
     * @throws \Schnell\Exception\ValidatorException
     */
    public function assign(SchemaInterface $schema, ?array $data = null): bool;

    /**
     * @psalm-api
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return array
     * @throws \Schnell\Exception\ValidatorException
     */
    public function assignMultiple(SchemaInterface $schema): array;

    /**
     * @psalm-api
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return bool
     * @throws \Schnell\Exception\ValidatorException
     */
    public function assignOptional(SchemaInterface $schema): bool;

    /**
     * @psalm-api
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return void
     * @throws \Schnell\Exception\ValidatorException
     */
    public function validateSchema(SchemaInterface $schema): void;
}
