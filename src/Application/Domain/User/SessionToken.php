<?php

namespace App\Application\Domain\User;

use App\Application\Infrastructure\Persistence\Doctrine\Repository\SessionUserRepository;
use App\Application\Infrastructure\Persistence\Doctrine\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: SessionUserRepository::class), ORM\HasLifecycleCallbacks, ORM\Table(name: 'session_token')]
class SessionToken implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private UuidInterface $id;

    #[ORM\OneToOne(inversedBy: 'sessionToken', targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_email', referencedColumnName: 'email')]
    private User $user;

    public function __construct(User $user)
    {
        $this->id = Uuid::uuid4();
        $this->user = $user;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public static function fromUser(User $user): self
    {
        return new self($user);
    }

    public function generateNewId(): self
    {
        $this->id = Uuid::uuid4();
        return $this;
    }

    public function jsonSerialize(): array
    {
        return ['id' => $this->id];
    }
}
