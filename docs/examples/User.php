<?php

namespace App\Entities;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use Doctrine\ORM\Mapping AS ORM;
use LaravelDoctrine\Extensions\Timestamps\Timestamps;
use LaravelDoctrine\ORM\Auth\Authenticatable;

/**
 * @ORM\Entity()
 * @ORM\Table()
 */
class User implements AuthorizableContract,
                      AuthenticatableContract,
                      CanResetPasswordContract
{
    use CanResetPassword;
    use Authorizable;
    use Authenticatable;
    use Timestamps;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * @ORM\Column(type="string")
     */
    protected string $name;

    /**
     * @ORM\Column(type="string")
     */
    protected string $email;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
