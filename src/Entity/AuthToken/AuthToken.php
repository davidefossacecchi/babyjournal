<?php

namespace App\Entity\AuthToken;
use App\Entity\User;
use App\Schema\AuthTokenIdGenerator;
use App\Security\Token\AuthTokenManager;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: Types::STRING)]
#[ORM\DiscriminatorMap([
    AuthTokenType::PASSWORD_RESET->value => PasswordResetToken::class,
    AuthTokenType::EMAIL_VERIFICATION->value => EmailVerificationToken::class,
    AuthTokenType::FAMILY_INVITATION->value => FamilyInvitationToken::class,
])]
abstract class AuthToken
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: Types::STRING)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(AuthTokenIdGenerator::class)]
    private string $selector;

    #[ORM\Column(type: Types::STRING)]
    private string $verifier;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'authTokens')]
    private User $user;

    #[ORM\Column(type: Types::SMALLINT, options: ['unsigned' => true])]
    private int $usages;

    private ?string $plainVerifier;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): AuthToken
    {
        $this->id = $id;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): AuthToken
    {
        $this->user = $user;
        $user->addAuthToken($this);
        return $this;
    }

    public function getSelector(): string
    {
        return $this->selector;
    }

    public function setSelector(string $selector): AuthToken
    {
        $this->selector = $selector;
        return $this;
    }

    public function getVerifier(): string
    {
        return $this->verifier;
    }

    public function setVerifier(string $verifier): AuthToken
    {
        $this->verifier = $verifier;
        return $this;
    }

    public function getUsages(): int
    {
        return $this->usages;
    }

    public function setUsages(int $usages): AuthToken
    {
        $this->usages = $usages;
        return $this;
    }

    public function setPlainVerifier(string $plainVerifier): AuthToken
    {
        $this->plainVerifier = $plainVerifier;
        return $this;
    }

    public function getPlainVerifier(): ?string
    {
        return $this->plainVerifier;
    }

    public function isUsable(): bool
    {
        if ($this->hasReachedMaxUsages()) {
            return false;
        }

        $createdAt = $this->getCreatedAt();

        // a not persisted token is not usable yet
        if (empty($createdAt)) {
            return false;
        }

        return \DateTimeImmutable::createFromInterface($createdAt)->add($this->getTTL()) >= new \DateTime();
    }


    abstract public function hasReachedMaxUsages(): bool;

    abstract public function getTTL(): \DateInterval;
}
