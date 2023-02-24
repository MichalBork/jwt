<?php

namespace App\Application\Domain\User;


use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: null), ORM\HasLifecycleCallbacks, ORM\Table(name: 'session_tokens')]
class SessionToken
{


    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private UuidInterface $id;

    #[ORM\Column(type: 'string')]
    #[ORM\OneToOne(mappedBy: 'sessionToken', inversedBy: 'sessionToken', targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'email', referencedColumnName: 'user')]
    private User $user;

    private function __construct(User $user)
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


    public static function fromUser(User $user): self
    {
        return new self($user);
    }


    public function generateNewId(): self
    {
        $this->id = Uuid::uuid4();
        return $this;
    }



}