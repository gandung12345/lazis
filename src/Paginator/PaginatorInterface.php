<?php

declare(strict_types=1);

namespace Schnell\Paginator;

use Psr\Http\Message\RequestInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface PaginatorInterface
{
    /**
     * @psalm-api
     *
     * @return int
     */
    public function getTotalRows(): int;

    /**
     * @param int $totalRows
     * @return void
     */
    public function setTotalRows(int $totalRows): void;

    /**
     * @psalm-api
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @return \Schnell\Paginator\PageInterface
     */
    public function getMetadata(RequestInterface $request): PageInterface;
}
