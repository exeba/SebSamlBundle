<?php

namespace Seb\SamlBundle\Controller;

use OneLogin\Saml2\Auth;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class SamlController
{
    use TargetPathTrait;

    private $samlAuth;
    private $security;
    private $httpUtils;
    private $defaultTargetPath;

    public function __construct(
        Auth $samlAuth,
        Security $security,
        HttpUtils $httpUtils,
        $defaultTargetPath = 'homepage'
    ) {
        $this->samlAuth = $samlAuth;
        $this->security = $security;
        $this->httpUtils = $httpUtils;
        $this->defaultTargetPath = $defaultTargetPath;
    }

    public function loginAction(Request $request)
    {
        // Prevent authentication loops
        $this->assertUnauthenticatedUser();

        $this->assertNoAuthenticationErrors($request);

        $this->samlAuth->login($this->getRelayState($request));
    }

    private function assertUnauthenticatedUser()
    {
        if ($this->security->getUser()) {
            throw new \RuntimeException('User is already authenticated');
        }
    }

    private function assertNoAuthenticationErrors(Request $request)
    {
        $authError = $this->getAndClearAuthenticationError($request);
        if ($authError) {
            throw new \RuntimeException($authError->getMessage());
        }
    }

    private function getRelayState(Request $request)
    {
        if ($request->hasSession()) {
            $session = $request->getSession();
            $targetPath = $this->getTargetPath($session, 'main');
        }

        return $targetPath ?? $this->httpUtils->generateUri($request, $this->defaultTargetPath);
    }

    private function getAndClearAuthenticationError(Request $request)
    {
        $authErrorKey = Security::AUTHENTICATION_ERROR;

        if ($request->attributes->has($authErrorKey)) {
            return $request->attributes->get($authErrorKey);
        }

        if ($request->hasSession() && $request->getSession()->has($authErrorKey)) {
            $error = $request->getSession()->has($authErrorKey);
            $request->getSession()->remove($authErrorKey);

            return $error;
        }

        return null;
    }

    public function metadataAction()
    {
        $metadata = $this->samlAuth->getSettings()->getSPMetadata();

        $response = new Response($metadata);
        $response->headers->set('Content-Type', 'xml');

        return $response;
    }

    public function assertionConsumerServiceAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall.');
    }

    public function singleLogoutServiceAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }
}
