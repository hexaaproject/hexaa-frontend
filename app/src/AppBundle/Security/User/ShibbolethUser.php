<?php

namespace AppBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ShibbolethUser implements UserInterface, UserProviderInterface
{
    private $eppn;
    private $displayName;
    private $email;

    // public function __construct(string $eppn, string $displayName, string $email)
    // public function __construct(string $username, string $password, string $salt, $roles)    
    // {
    //     $this->eppn = $eppn;
    //     $this->displayName = $displayName;
    //     $this->email = $email;
    // }

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


    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getUsername()
    {
        return $_SERVER['eduPersonPrincipalName']; // TODO configból az attrnevet
    }

    public function eraseCredentials()
    {
    }

    public function loadUserByUsername($username)
    {
        return $this;
    }

    public function refreshUser(UserInterface $user)
    {
        # code...
    }

    public function supportsClass($value='')
    {
        # code...does not exist.
    }

}
