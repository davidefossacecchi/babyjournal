<?php

namespace App\Pagination;

use Doctrine\Common\Collections\ArrayCollection;

class Pagination extends ArrayCollection
{
    public function __construct(array $elements, private int $maxResults, private int $pageLength)
    {
        parent::__construct($elements);
    }

    public function getMaxResults(): int
    {
        return $this->maxResults;
    }

    public function getPageLength(): int
    {
        return $this->pageLength;
    }

    public function getMaxPage(): int
    {
        return 0 < $this->getPageLength()
            ? ceil($this->getMaxResults() / $this->getPageLength())
            : 0;
    }
}
