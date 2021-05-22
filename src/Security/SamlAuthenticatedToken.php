<?php

namespace Seb\SamlBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class SamlAuthenticatedToken extends PostAuthenticationGuardToken
{
    public function __construct(UserInterface $user, string $providerKey, array $roles, array $attributes)
    {
        parent::__construct($user, $providerKey, $roles);
        $this->setAttributes($attributes);
    }
}
