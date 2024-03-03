<?php

namespace App\Entity\Timepoints;

use App\Entity\Family;
use App\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Post extends TimePoint
{
    #[ORM\Column(type: Types::STRING, length: 1024)]
    private string $imagePath;

    #[ORM\Column(type: Types::STRING, length: 512)]
    private string $hash;

    #[ORM\Column(type: Types::STRING, length: 1024)]
    private string $caption;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    private User $author;

    #[ORM\ManyToOne(inversedBy: 'posts', targetEntity: Family::class)]
    private Family $family;

    public function getImagePath(): string
    {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath): Post
    {
        $this->imagePath = $imagePath;
        return $this;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): Post
    {
        $this->hash = $hash;
        return $this;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): Post
    {
        $this->author = $author;
        $this->author->addPost($this);
        return $this;
    }

    public function getFamily(): Family
    {
        return $this->family;
    }

    public function setFamily(Family $family): Post
    {
        $this->family = $family;
        $this->family->addPost($this);
        return $this;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }

    public function setCaption(string $caption): Post
    {
        $this->caption = $caption;
        return $this;
    }

    #[ORM\PrePersist]
    public function setNowDate()
    {
        $this->setDate(new \DateTimeImmutable());
    }
}
