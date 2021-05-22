<?php

namespace Seb\SamlBundle\Security;

use Seb\AuthenticatorBundle\Security\CredentialsInterface;

class SamlCredentials implements CredentialsInterface
{
    private $attributes;
    private $username;

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }
}
