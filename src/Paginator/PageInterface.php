<?php

declare(strict_types=1);

namespace Schnell\Paginator;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface PageInterface
{
    /**
     * @psalm-api
     *
     * @return int
     */
    public function getTotalCount(): int;

    /**
     * @psalm-api
     *
     * @param int $totalCount
     * @return void
     */
    public function setTotalCount(int $totalCount): void;

    /**
     * @return int
     */
    public function getPage(): int;

    /**
     * @psalm-api
     *
     * @param int $page
     * @return void
     */
    public function setPage(int $page): void;

    /**
     * @return int
     */
    public function getPerPage(): int;

    /**
     * @psalm-api
     *
     * @param int $perPage
     * @return void
     */
    public function setPerPage(int $perPage): void;

    /**
     * @return int
     */
    public function getOffset(): int;

    /**
     * @psalm-api
     *
     * @param int $offset
     * @return void
     */
    public function setOffset(int $offset): void;

    /**
     * @return int
     */
    public function getTotalPage(): int;

    /**
     * @psalm-api
     *
     * @param int $totalPage
     * @return void
     */
    public function setTotalPage(int $totalPage): void;
}
