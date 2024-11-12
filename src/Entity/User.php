<?php

namespace App\Entity;

use App\Entity\AuthToken\AuthToken;
use App\Entity\Timepoints\Post;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[UniqueEntity(fields: 'email', message: 'This email is already in use')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableEntity;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/[A-Z][a-z]+( [A-Z][a-z]+)*/', message: 'Invalid firstname')]
    private string $firstName;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $password;

    #[Assert\Regex(pattern: '/(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[$+%&:\-!?]).{8,}/', message: 'Invalid password, it must contains an uppercase character, a lower case character, a digit and a symbol between $, +, %, &, :, -, !, and ?')]
    private ?string $plainPassword;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false, options: ['default' => false])]
    private bool $enabled = false;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false, options: ['default' => false])]
    private bool $verified = false;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: AuthToken::class)]
    private Collection $authTokens;

    #[ORM\ManyToMany(targetEntity: Family::class, mappedBy: 'users')]
    private Collection $families;

    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'author')]
    private Collection $posts;

    public function __construct()
    {
        $this->authTokens = new ArrayCollection();
        $this->families = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): User
    {
        $this->id = $id;
        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setFirstName(string $firstName): User
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    public function setPassword(string $password): User
    {
        $this->password = $password;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): User
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }

    public function setVerified(bool $verified): User
    {
        $this->verified = $verified;
        return $this;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): User
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function getAuthTokens(): Collection
    {
        return $this->authTokens;
    }

    public function addAuthToken(AuthToken $authToken): User
    {
        if (false === $this->authTokens->contains($authToken)) {
            $this->authTokens->add($authToken);
            $authToken->setUser($this);
        }
        return $this;
    }

    public function getFamilies(): Collection
    {
        return $this->families;
    }

    public function addFamily(Family $family): User
    {
        if(false === $this->families->contains($family)) {
            $this->families->add($family);
            $family->addUser($this);
        }
        return $this;
    }

    public function removeFamily(Family $family): User
    {
        if ($this->families->removeElement($family)) {
            $family->removeUser($this);
        }
        return $this;
    }

    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): User
    {
        if (false === $this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setAuthor($this);
        }
        return $this;
    }

    public function removePost(Post $post): User
    {
        $this->posts->removeElement($post);
        return $this;
    }
}
