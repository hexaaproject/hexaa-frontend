<?php

namespace Hexaa\Newui;
/**
 * Created by PhpStorm.
 * User: baloo
 * Date: 2016.11.11.
 * Time: 16:02
 */
class User
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