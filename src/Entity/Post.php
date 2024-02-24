<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Entity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
#[Entity]
class Post
{
    use TimestampableEntity;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

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

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Post
    {
        $this->id = $id;
        return $this;
    }

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
}
