<?php

namespace Seb\SamlBundle\Controller;

use OneLogin\Saml2\Auth;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class SamlController
{
    use TargetPathTrait;

    protected $samlAuth;

    public function __construct(Auth $samlAuth)
    {
        $this->samlAuth = $samlAuth;
    }

    public function loginAction(Request $request)
    {
        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $session = $targetPath = null;

        if ($request->hasSession()) {
            $session = $request->getSession();
            $targetPath = $this->getTargetPath($session, 'mail');
        }

        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if ($error) {
            throw new \RuntimeException($error->getMessage());
        }

        $this->samlAuth->login($targetPath);
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
