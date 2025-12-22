<?php

declare(strict_types=1);

namespace Schnell\Hateoas;

use Psr\Http\Message\RequestInterface;
use Schnell\Paginator\PageInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface HateoasInterface
{
    /**
     * @var int
     */
    public const int CURRENT = 1;

    /**
     * @var int
     */
    public const int PREV = 2;

    /**
     * @var int
     */
    public const int NEXT = 4;

    /**
     * @return array
     */
    public function getData(): array;

    /**
     * @param array $data
     * @return void
     */
    public function setData(array $data): void;

    /**
     * @return \Schnell\Paginator\PageInterface
     */
    public function getPage(): PageInterface;

    /**
     * @param \Schnell\Paginator\PageInterface $page
     * @return void
     */
    public function setPage(PageInterface $page): void;

    /**
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getRequest(): RequestInterface;

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @return void
     */
    public function setRequest(RequestInterface $request): void;

    /**
     * @return array
     */
    public function generate(): array;
}
