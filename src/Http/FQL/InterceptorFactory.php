<?php

declare(strict_types=1);

namespace Schnell\Http\FQL;

use Doctrine\ORM\QueryBuilder;
use Psr\Http\Message\RequestInterface;
use Schnell\Entity\EntityInterface;
use Schnell\Http\FQL\Lexer;
use Schnell\Http\FQL\Parser;

/**
 * @psalm-api
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class InterceptorFactory implements InterceptorFactoryInterface
{
    /**
     * @var \Psr\Http\Message\RequestInterface|null
     */
    private ?RequestInterface $request;

    /**
     * @var \Doctrine\ORM\QueryBuilder|null
     */
    private ?QueryBuilder $queryBuilder;

    /**
     * @var \Schnell\Entity\EntityInterface|null
     */
    private ?EntityInterface $entity;

    /**
     * @psalm-api
     *
     * @param \Psr\Http\Message\RequestInterface|null $request
     * @param \Doctrine\ORM\QueryBuilder|null $queryBuilder
     * @param \Schnell\Entity\EntityInterface|null $entity
     * @return static
     */
    public function __construct(
        ?RequestInterface $request,
        ?QueryBuilder $queryBuilder,
        ?EntityInterface $entity
    ) {
        $this->setRequest($request);
        $this->setQueryBuilder($queryBuilder);
        $this->setEntity($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest(): ?RequestInterface
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequest(?RequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryBuilder(): ?QueryBuilder
    {
        return $this->queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function setQueryBuilder(?QueryBuilder $queryBuilder): void
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity(): ?EntityInterface
    {
        return $this->entity;
    }

    /**
     * {@inheritDoc}
     */
    public function setEntity(?EntityInterface $entity): void
    {
        $this->entity = $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function createInterceptor(): ?InterceptorInterface
    {
        if (($queryParams = $this->getFilterQuery()) === null) {
            return null;
        }

        $lexer = new Lexer();
        $lexer->setBuffer($queryParams);
        $lexer->lex();

        $parser = new Parser(
            $lexer->getTokens(),
            $this->getQueryBuilder(),
            $this->getEntity()
        );

        $parser->parse();

        return new Interceptor(
            $this->getQueryBuilder(),
            $parser->getAst(),
            $this->getEntity()
        );
    }

    /**
     * @return string|null
     */
    private function getFilterQuery(): ?string
    {
        /**
         * @psalm-suppress PossiblyNullReference
         * @psalm-suppress UndefinedInterfaceMethod
         */
        $queryParams = $this->getRequest()->getQueryParams();

        if (!isset($queryParams['filter']) || $queryParams['filter'] === '') {
            return null;
        }

        return $queryParams['filter'];
    }
}
