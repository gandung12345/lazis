<?php

declare(strict_types=1);

namespace Schnell\Paginator;

use Psr\Http\Message\RequestInterface;

use function intval;

/**
 * @psalm-api
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class Paginator implements PaginatorInterface
{
    /**
     * @var int
     */
    private int $totalRows;

    /**
     * @psalm-api
     *
     * @param int $totalRows
     * @return static
     */
    public function __construct(int $totalRows)
    {
        $this->setTotalRows($totalRows);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalRows(): int
    {
        return $this->totalRows;
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalRows(int $totalRows): void
    {
        $this->totalRows = $totalRows;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata(RequestInterface $request): PageInterface
    {
        $page = new Page();
        /** @psalm-suppress UndefinedInterfaceMethod */
        $queryParams = $request->getQueryParams();

        $page->setTotalCount($this->getTotalRows());
        $page->setPage(
            isset($queryParams['page'])
                ? intval($queryParams['page'])
                : 1
        );
        $page->setPerPage(
            isset($queryParams['perPage'])
                ? intval($queryParams['perPage'])
                : 15
        );
        $page->setOffset(($page->getPage() - 1) * $page->getPerPage());

        if ($page->getTotalCount() < $page->getPerPage()) {
            $page->setTotalPage(1);
        } else {
            $totalPage = $page->getTotalCount() % $page->getPerPage() === 0
                ? intval($page->getTotalCount() / $page->getPerPage())
                : intval($page->getTotalCount() / $page->getPerPage()) + 1;

            $page->setTotalPage($totalPage);
        }

        return $page;
    }
}
