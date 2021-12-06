<?php

namespace Seb\SamlBundle\Security;

use Seb\AuthenticatorBundle\Security\AuthenticatedTokenProviders\AuthenticatedToken;
use Symfony\Component\Security\Core\User\UserInterface;

class SamlAuthenticatedToken extends AuthenticatedToken
{
    public function __construct(UserInterface $user, string $providerKey, array $roles, array $attributes)
    {
        parent::__construct($user, $providerKey, $roles);
        $this->setAttributes($attributes);
    }
}
