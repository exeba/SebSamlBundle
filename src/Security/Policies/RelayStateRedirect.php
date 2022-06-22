<?php

namespace Seb\SamlBundle\Security\Policies;

use Seb\AuthenticatorBundle\Security\SuccessfulAuthenticationPolicy;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class RelayStateRedirect implements SuccessfulAuthenticationPolicy
{
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($this->hasRelayState($request)) {
            return new RedirectResponse($this->getRelayState($request));
        }

        return null;
    }

    private function hasRelayState(Request $request)
    {
        return $request->attributes->has('RelayState');
    }

    private function getRelayState(Request $request)
    {
        return $request->attributes->get('RelayState');
    }
}
