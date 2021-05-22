<?php

namespace Seb\SamlBundle\Security;

use Seb\AuthenticatorBundle\Security\AuthenticatedTokenProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SamlAuthenticatedTokenProvider implements AuthenticatedTokenProviderInterface
{
    private $attributesStorage;

    public function __construct(SamlAttributesStorage $attributesStorage)
    {
        $this->attributesStorage = $attributesStorage;
    }

    public function createAuthenticatedToken(UserInterface $user, $providerKey)
    {
        return new SamlAuthenticatedToken(
            $user, $providerKey, $user->getRoles(), $this->attributesStorage->getAttributes($user->getUsername()));
    }
}
