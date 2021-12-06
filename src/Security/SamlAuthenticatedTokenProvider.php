<?php

namespace Seb\SamlBundle\Security;

use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Seb\AuthenticatorBundle\Security\Authenticator\AuthenticatedTokenProviderInterface;

class SamlAuthenticatedTokenProvider implements AuthenticatedTokenProviderInterface
{
    private $attributesStorage;

    public function __construct(SamlAttributesStorage $attributesStorage)
    {
        $this->attributesStorage = $attributesStorage;
    }

    public function createAuthenticatedToken(Passport $passport, $providerKey)
    {
        return new SamlAuthenticatedToken(
            $passport->getUser(),
            $providerKey,
            $passport->getUser()->getRoles(),
            $this->attributesStorage->getAttributes($passport->getUser()->getUserIdentifier()));
    }
}
