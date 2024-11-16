<?php

namespace App\Entity;

use App\Entity\AuthToken\FamilyInvitationToken;
use App\Entity\Timepoints\Post;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity]
class Family
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Constraints\NotBlank]
    private string $name;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'families')]
    #[ORM\JoinTable(name: 'users_families')]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'family', targetEntity: Post::class)]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $posts;

    #[ORM\OneToMany(mappedBy: 'family', targetEntity: Child::class)]
    private Collection $children;

    #[ORM\OneToMany(mappedBy: 'family', targetEntity: FamilyInvitationToken::class)]
    private Collection $invitations;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->children = new ArrayCollection();
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

    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): Family
    {
        if (false === $this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setFamily($this);
        }
        return $this;
    }

    public function removePost(Post $post): Family
    {
        $this->posts->removeElement($post);
        return $this;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Child $child): Family
    {
        if (false === $this->children->contains($child)) {
            $this->children->add($child);
            $child->setFamily($this);
        }
        return $this;
    }

    public function removeChild(Child $child): static
    {
        $this->children->removeElement($child);
        return $this;
    }

    public function addInvitation(FamilyInvitationToken $invitation): static
    {
        if (false === $this->invitations->contains($invitation)) {
            $this->invitations->add($invitation);
            $invitation->setFamily($this);
        }
        return $this;
    }

    public function removeInvitation(FamilyInvitationToken $invitation): static
    {
        $this->invitations->removeElement($invitation);
        return $this;
    }

    public function getInvitations(): Collection
    {
        return $this->invitations;
    }
}
