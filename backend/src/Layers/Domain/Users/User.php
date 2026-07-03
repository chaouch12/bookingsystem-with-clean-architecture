<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\common\Entity;
use App\Entity\common\SetTimestampTrait;
use App\Layers\Domain\Users\Enum\UserRoleType;
use App\Layers\Domain\Users\Enum\UserStatusType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use SensitiveParameter;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use UserRepository;

#[ORM\Table(name: 'user'),
    ORM\Entity(repositoryClass: UserRepository::class),
    ORM\HasLifecycleCallbacks]
class User extends Entity implements UserInterface, PasswordAuthenticatedUserInterface
{
    use SetTimestampTrait;

    #[ORM\Column(
        name: 'user_password',
        type: Types::STRING,
        length: 40,
        nullable: false
    )]
    private string $password;

    #[ORM\Column(
        name: 'user_email',
        type: Types::STRING,
        length: 255,
        nullable: false
    )]
    private string $email;

    #[ORM\Column(
        name: 'user_role',
        type: Types::STRING,
        length: 16,
        nullable: false,
        enumType: UserRoleType::class
    )]
    private UserRoleType $role;

    #[ORM\Column(
        name: 'user_status',
        type: Types::STRING,
        length: 8,
        nullable: false,
        enumType: UserStatusType::class
    )]
    private ?UserStatusType $status;

    private function __construct(#[SensitiveParameter] string $password, UserStatusType $status, UserRoleType $role, string $email)
    {
        parent::__construct();
        $this->role = $role;
        $this->status = $status;
        $this->password = $password;
        $this->email = $email;
        $this->setTimestampsToNow();
    }

    public static function create(#[SensitiveParameter] string $password, UserStatusType $status, UserRoleType $role, string $email): self
    {
        return new self($password, $status, $role, $email);
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(#[SensitiveParameter] ?string $password): void
    {
        $this->password = $password;
    }

    public function getRole(): UserRoleType
    {
        return $this->role;
    }

    public function setRole(UserRoleType $role): void
    {
        $this->role = $role;
    }

    public function getStatus(): ?UserStatusType
    {
        return $this->status;
    }

    public function setStatus(?UserStatusType $status): void
    {
        $this->status = $status;
    }

    public function getRoles(): array
    {
        $roles = [];
        $roles[] = 'ROLE_USER';
        $roles[] = strtoupper('ROLE_'.$this->getRole()->value);

        return array_unique($roles);
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(#[SensitiveParameter] ?string $email): void
    {
        $this->email = $email;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->id;
    }
}
