<?php

namespace Seb\SamlBundle\Security;

use OneLogin\Saml2\Auth;
use Psr\Log\LoggerInterface;
use Seb\AuthenticatorBundle\Security\CredentialsProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\HttpUtils;

class SamlCredentialsProvider implements CredentialsProviderInterface
{
    private $httpUtils;
    private $oneLoginAuth;
    private $attributesStorage;
    private $options;
    private $logger;

    public function __construct(
        HttpUtils $httpUtils,
        Auth $oneLoginAuth,
        SamlAttributesStorage $attributesStorage,
        LoggerInterface $logger,
        array $options = null
    ) {
        $this->httpUtils = $httpUtils;
        $this->oneLoginAuth = $oneLoginAuth;
        $this->attributesStorage = $attributesStorage;
        $this->options = $options;
        $this->logger = $logger;
    }

    public function supports(Request $request)
    {
        return $request->isMethod('POST')
            && $this->httpUtils->checkRequestPath($request, $this->options['check_path']);
    }

    public function getCredentials(Request $request)
    {
        $this->oneLoginAuth->processResponse();
        if ($this->oneLoginAuth->getErrors()) {
            $this->logger->error($this->oneLoginAuth->getLastErrorReason());
            throw new AuthenticationException($this->oneLoginAuth->getLastErrorReason());
        }

        if (isset($this->options['use_attribute_friendly_name']) && $this->options['use_attribute_friendly_name']) {
            $attributes = $this->oneLoginAuth->getAttributesWithFriendlyName();
        } else {
            $attributes = $this->oneLoginAuth->getAttributes();
        }
        $attributes['sessionIndex'] = $this->oneLoginAuth->getSessionIndex();
        $token = new SamlCredentials();
        $token->setAttributes($attributes);

        if (isset($this->options['username_attribute'])) {
            if (!array_key_exists($this->options['username_attribute'], $attributes)) {
                $this->logger->error(sprintf('Found attributes: %s', print_r($attributes, true)));
                throw new \Exception(sprintf("Attribute '%s' not found in SAML data", $this->options['username_attribute']));
            }

            $username = $attributes[$this->options['username_attribute']][0];
        } else {
            $username = $this->oneLoginAuth->getNameId();
        }
        $token->setUsername($username);
        $this->attributesStorage->setAttributes($username, $attributes);

        return $token;
    }

    public function supportsRememberMe()
    {
        return true;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->options['login_path']);
    }
}
