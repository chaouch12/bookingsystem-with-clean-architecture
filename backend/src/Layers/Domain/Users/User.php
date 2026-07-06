<?php

declare(strict_types=1);

namespace App\Layers\Domain\Users;

use App\Entity\common\Entity;
use App\Entity\common\SetTimestampTrait;
use App\Layers\Domain\Users\Enum\UserRoleType;
use App\Layers\Domain\Users\Enum\UserStatusType;
use App\Layers\Domain\Users\Repository\UserRepository;
use App\Layers\Domain\Users\ValueObject\Email;
use App\Layers\Domain\Users\ValueObject\HashedPassword;
use App\Layers\Domain\Users\ValueObject\Name;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Table(name: 'user'),
    ORM\Entity(repositoryClass: UserRepository::class),
    ORM\HasLifecycleCallbacks]
final class User extends Entity implements UserInterface, PasswordAuthenticatedUserInterface
{
    use SetTimestampTrait;

    #[ORM\Embedded(class: HashedPassword::class, columnPrefix: false)]
    private HashedPassword $password;

    #[ORM\Embedded(class: Email::class, columnPrefix: false)]
    private Email $email;

    #[ORM\Embedded(class: Name::class, columnPrefix: false)]
    private Name $name;

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

    private function __construct(HashedPassword $password, UserStatusType $status, UserRoleType $role, Email $email, Name $name)
    {
        parent::__construct();
        $this->role = $role;
        $this->status = $status;
        $this->password = $password;
        $this->email = $email;
        $this->name = $name;
        $this->setTimestampsToNow();
    }

    public static function create(HashedPassword $password, UserStatusType $status, UserRoleType $role, Email $email, Name $name): self
    {
        return new self($password, $status, $role, $email, $name);
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getPassword(): string
    {
        return $this->password->value;
    }

    public function getHashedPassword(): HashedPassword
    {
        return $this->password;
    }

    public function setPassword(HashedPassword $password): void
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

    public function getEmail(): string
    {
        return $this->email->value;
    }

    public function getEmailAddress(): Email
    {
        return $this->email;
    }

    public function setEmail(Email $email): void
    {
        $this->email = $email;
    }

    public function getName(): string
    {
        return $this->name->value;
    }

    public function getUserName(): Name
    {
        return $this->name;
    }

    public function setName(Name $name): void
    {
        $this->name = $name;
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
