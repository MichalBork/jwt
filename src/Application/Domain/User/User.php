<?php

namespace App\Application\Domain\User;

use App\Application\Infrastructure\Persistence\Doctrine\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class), ORM\HasLifecycleCallbacks, ORM\Table(name: 'users')]
class User implements PasswordAuthenticatedUserInterface,UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string',unique: true)]
    private string $email;

    #[ORM\Column(type: 'string')]
    private string $password;


    #[ORM\OneToMany(mappedBy: 'email', targetEntity: Roles::class, cascade: ['persist'])]
    private Collection $roles;

    #[ORM\OneToOne(mappedBy: 'email', targetEntity: SessionToken::class, cascade: ['persist'])]
    private ?SessionToken $sessionToken;


    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
        $this->roles = new ArrayCollection();
        $this->roles->add(Roles::fromUser($this, 'ROLE_USER'));
    }

    public function getId(): int
    {
        return $this->id;
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
        // TODO: Implement getRoles() method.
        return $this->roles->toArray();
    }

    public function eraseCredentials()
    {

        // TODO: Implement eraseCredentials() method.
        $this->password = '';
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
        // TODO: Implement getUserIdentifier() method.
    }

    public function createSessionToken(): SessionToken
    {
        if ($this->sessionToken === null) {
            $this->sessionToken = SessionToken::fromUser($this);
            return $this->sessionToken;
        }
       return $this->sessionToken->generateNewId();

    }
}