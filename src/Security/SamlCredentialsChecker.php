<?php

namespace Seb\SamlBundle\Security;

use Seb\AuthenticatorBundle\Security\CredentialsCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SamlCredentialsChecker implements CredentialsCheckerInterface
{
    public function checkCredentials($credentials, UserInterface $user)
    {
        if ($credentials instanceof SamlCredentials) {
            // The existence of the token implies the validity of the credentials
            return true;
        } else {
            return false;
        }
    }
}
