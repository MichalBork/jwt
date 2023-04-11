<?php

namespace App\Application\Domain\User;

use App\Application\Infrastructure\Persistence\Doctrine\Repository\UserRepository;
use App\Application\Infrastructure\Persistence\JWT\JWTEncodableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class), ORM\HasLifecycleCallbacks, ORM\Table(name: 'users')]
class User implements PasswordAuthenticatedUserInterface, UserInterface, JsonSerializable, JWTEncodableInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private string $email;

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: SessionToken::class, cascade: [
        'persist',
        'remove'
    ])]
    private SessionToken $sessionToken;
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Roles::class, cascade: [
        'persist',
        'remove'
    ])]
    private Collection $roles;

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
        $this->roles = new ArrayCollection();
        $this->roles->add(Roles::fromUser($this, 'ADMIN'));
        $this->createSessionToken();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $hashPassword): void
    {
        $this->password = $hashPassword;
    }

    public function getRoles(): array
    {
        return $this->roles->toArray();
    }

    public function eraseCredentials()
    {
        $this->password = '';
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function createSessionToken(): SessionToken
    {
        if (!isset($this->sessionToken)) {
            $this->sessionToken = SessionToken::fromUser($this);
            return $this->sessionToken;
        }
        return $this->sessionToken->generateNewId();
    }

    public function __toString()
    {
        return $this->email;
    }

    public function jsonSerialize(): array
    {
        return [
            'email' => $this->email,
            'roles' => $this->roles
        ];
    }

    public function getArrayForEncode(): array
    {
        return [
            'email' => $this->email,
            'roles' => $this->roles->toArray(),
            'sessionToken' => $this->sessionToken->getId(),
        ];
    }
}
