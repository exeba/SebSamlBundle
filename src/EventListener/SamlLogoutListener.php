<?php

namespace Seb\SamlBundle\EventListener;

use OneLogin\Saml2\Auth;
use OneLogin\Saml2\Error;
use Seb\SamlBundle\Security\SamlAuthenticatedToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Security\Http\HttpUtils;

class SamlLogoutListener
{
    private $httpUtils;
    private $tokenStorage;
    protected $samlAuth;

    public function __construct(
        HttpUtils $httpUtils,
        TokenStorageInterface $tokenStorage,
        Auth $samlAuth)
    {
        $this->httpUtils = $httpUtils;
        $this->tokenStorage = $tokenStorage;
        $this->samlAuth = $samlAuth;
    }

    public function onLogout(LogoutEvent $event)
    {
        if ($this->isRegularLogout($event->getRequest())) {
            return $this->redirectToLoginForm($event->getRequest());
        }

        try {
            $this->samlAuth->processSLO();

            $event->setResponse($this->redirectToLoginForm($event->getRequest()));
        } catch (Error $e) {
            $token = $event->getToken();
            $sessionIndex = $token->hasAttribute('sessionIndex') ? $token->getAttribute('sessionIndex') : null;
            $redirectUrl = $this->samlAuth->logout(null, [], $token->getUsername(), $sessionIndex, true);

            $event->setResponse(new RedirectResponse($redirectUrl));
        }
    }

    private function isRegularLogout(Request $request)
    {
        return $this->isNotSamlSLO($request) || $this->samlSLODisabled();
    }

    private function isNotSamlSLO(Request $request)
    {
        return !$this->isSamlSLO($request);
    }

    private function isSamlSLO(Request $request)
    {
        return $this->httpUtils->checkRequestPath($request, 'saml_logout')
                && ($this->tokenStorage->getToken() instanceof SamlAuthenticatedToken);
    }

    private function samlSLODisabled()
    {
        return empty($this->samlAuth->getSLOurl());
    }

    private function redirectToLoginForm(Request $request)
    {
        return $this->httpUtils->createRedirectResponse($request, 'login');
    }
}
