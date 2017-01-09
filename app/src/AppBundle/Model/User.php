<?php

namespace AppBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

class ShibbolethUser implements UserInterface, EquatableInterface, UserProviderInterface
{
    private $eppn;
    private $displayName;
    private $email;

    public function __construct(string $eppn, string $displayName, string $email)
    {
        $this->eppn = $eppn;
        $this->displayName = $displayName;
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEppn(): string
    {
        return $this->eppn;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }



}