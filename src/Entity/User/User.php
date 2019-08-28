<?php

namespace App\Entity\User;

use App\Entity\ModelInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="users")
 */
class User implements UserInterface, ModelInterface
{
    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(type="uuid", unique=true)
     */
    private $security;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * User constructor.
     * @param string $id
     * @param string $email
     * @param string $password
     * @throws Exception
     */
    public function __construct(string $id, string $email, string $password)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;

        $this->security = Uuid::uuid4()->toString();
        $this->enabled = false;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    public static function transformIgnoredAttributes(): array
    {
        return [
            'password',
            'security',
            'roles',
            'salt',
        ];
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string|null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return string
     */
    public function getSecurity(): string
    {
        return $this->security;
    }

    /**
     * @param string $security
     * @return bool
     * @throws Exception
     */
    public function activate(string $security): bool
    {
        if ($this->getSecurity() === $security) {
            $this->security = Uuid::uuid4()->toString();
            $this->enabled = true;
        }
        return $this->isEnabled();
    }

    public function updatePassword(string $password)
    {
        $this->password = $password;
    }
}
