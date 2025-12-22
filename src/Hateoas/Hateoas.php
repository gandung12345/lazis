<?php

declare(strict_types=1);

namespace Schnell\Hateoas;

use Psr\Http\Message\RequestInterface;
use Schnell\Paginator\PageInterface;

use function array_keys;
use function array_values;
use function join;
use function strval;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class Hateoas implements HateoasInterface
{
    /**
     * @var array
     */
    private array $data;

    /**
     * @var \Schnell\Paginator\PageInterface
     */
    private PageInterface $page;

    /**
     * @var \Psr\Http\Message\RequestInterface
     */
    private RequestInterface $request;

    /**
     * @psalm-api
     *
     * @param array $data
     * @param \Schnell\Paginator\PageInterface $page
     * @param \Psr\Http\Message\RequestInterface $request
     * @return static
     */
    public function __construct(
        array $data,
        PageInterface $page,
        RequestInterface $request
    ) {
        $this->setData($data);
        $this->setPage($page);
        $this->setRequest($request);
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getPage(): PageInterface
    {
        return $this->page;
    }

    /**
     * {@inheritdoc}
     */
    public function setPage(PageInterface $page): void
    {
        $this->page = $page;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequest(RequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(): array
    {
        return [
            'data' => $this->getData(),
            'meta' => $this->generatePaginationMetadata(),
            '_links' => $this->generateLinkMetadata()
        ];
    }

    /**
     * @return array
     */
    private function generatePaginationMetadata(): array
    {
        return [
            'page' => $this->getPage()->getPage(),
            'perPage' => $this->getPage()->getPerPage(),
            'offset' => $this->getPage()->getOffset(),
            'totalCount' => $this->getPage()->getTotalCount(),
            'pageCount' => $this->getPage()->getTotalPage()
        ];
    }

    /**
     * @return array
     */
    private function generateLinkMetadata(): array
    {
        return [
            'self' => $this->generatePagedLink(HateoasInterface::CURRENT),
            'prev' => $this->generatePagedLink(HateoasInterface::PREV),
            'next' => $this->generatePagedLink(HateoasInterface::NEXT)
        ];
    }

    /**
     * @param int $direction
     * @return array|null
     */
    private function generatePagedLink(
        int $direction = HateoasInterface::CURRENT
    ): array|null {
        /** @psalm-suppress UndefinedInterfaceMethod */
        $serverParams = $this->getRequest()->getServerParams();
        /** @psalm-suppress UndefinedInterfaceMethod */
        $queryParams = $this->getRequest()->getQueryParams();

        if (
            !isset($queryParams['page']) ||
            !isset($queryParams['perPage'])
        ) {
            return null;
        }

        if (
            $direction === HateoasInterface::PREV &&
            $this->getPage()->getPage() === 1
        ) {
            return ['href' => null];
        }

        if (
            $direction === HateoasInterface::NEXT &&
            $queryParams['page'] >= $this->getPage()->getTotalPage()
        ) {
            return ['href' => null];
        }

        $queryParams['page'] = $direction === HateoasInterface::PREV
            ? strval($this->getPage()->getPage() - 1)
            : ($direction === HateoasInterface::NEXT
                ? strval($this->getPage()->getPage() + 1)
                : $queryParams['page']);

        /** @psalm-suppress PossiblyInvalidArgument */
        $queryStringList = array_map(
            function (string $a, string $b) {
                return sprintf("%s=%s", $a, $b);
            },
            array_keys($queryParams),
            array_values($queryParams)
        );

        $link = sprintf(
            "%s://%s%s?%s",
            $this->getRequest()->getUri()->getScheme(),
            $serverParams['HTTP_HOST'],
            $this->getRequest()->getUri()->getPath(),
            join('&', $queryStringList)
        );

        return ['href' => $link];
    }
}
