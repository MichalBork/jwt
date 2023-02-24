<?php

namespace App\Application\Domain\User;

use App\Application\Infrastructure\Persistence\Doctrine\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity(repositoryClass: null), ORM\HasLifecycleCallbacks, ORM\Table(name: 'user_roles')]
class Roles implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'roles')]
    #[ORM\JoinColumn(name: 'user', referencedColumnName: 'email')]
    private User $user;


    private function __construct(string $role, User $user)
    {
        $this->name = $role;
        $this->user = $user;
    }

    public static function fromUser(User $user, string $role): self
    {
        return new self($role, $user);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function jsonSerialize(): array
    {
        return ['role' => $this->name];
    }
}
