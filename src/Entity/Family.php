<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity]
class Family
{
    use TimestampableEntity;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 50)]
    private string $name;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'families')]
    #[ORM\JoinTable(name: 'users_families')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Family
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Family
    {
        $this->name = $name;
        return $this;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): Family
    {
        if (false === $this->users->contains($user)) {
            $this->users->add($user);
            $user->addFamily($this);
        }
        return $this;
    }

    public function removeUser(User $user): Family
    {
        if ($this->users->removeElement($user)) {
            $user->removeFamily($this);
        }
        return $this;
    }
}