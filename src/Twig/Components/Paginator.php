<?php

namespace App\Twig\Components;
use App\Pagination\Pagination;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Paginator
{
    private int $currentPage;
    private string $routeName;

    private array $routeParameters;

    private array $queryParams;

    public Pagination $pagination;

    public function __construct(RequestStack $requestStack, private string $pageParam, private UrlGeneratorInterface $urlGenerator)
    {
        $currentRequest = $requestStack->getCurrentRequest();
        $selectedPage = $currentRequest->query->getInt($pageParam, 1);
        $this->routeName = $currentRequest->attributes->get('_route');
        $this->routeParameters = $currentRequest->attributes->all('_route_params');
        $this->queryParams = $currentRequest->query->all();
        $this->currentPage = max($selectedPage, 1);
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getPageParam(): string
    {
        return $this->pageParam;
    }

    public function getPageUrl(int $page)
    {
        return $this->urlGenerator->generate($this->routeName, array_merge($this->routeParameters, $this->queryParams, [$this->pageParam => $page]));
    }
}
