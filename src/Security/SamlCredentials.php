<?php

namespace Seb\SamlBundle\Security;

use Seb\AuthenticatorBundle\Security\CredentialsInterface as SebCredentialsInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CredentialsInterface;

class SamlCredentials implements SebCredentialsInterface, CredentialsInterface
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

    public function isResolved(): bool
    {
        return true;
    }
}
