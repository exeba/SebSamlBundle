<?php

namespace Seb\SamlBundle\DependencyInjection;

use OneLogin\Saml2\Auth;
use Seb\AuthenticatorBundle\Security\Guard\Authenticator;
use Seb\AuthenticatorBundle\Security\Policies\CreateUserIfAuthSucceeds;
use Seb\AuthenticatorBundle\Security\Policies\RedirectOnBadCredentials;
use Seb\AuthenticatorBundle\Security\Policies\TargetPathOrHomePageRedirect;
use Seb\AuthenticatorBundle\Security\Policies\ThrowOnMissingUser;
use Seb\SamlBundle\Controller\SamlController;
use Seb\SamlBundle\Security\SamlAttributesStorage;
use Seb\SamlBundle\Security\SamlAuthenticatedTokenProvider;
use Seb\SamlBundle\Security\SamlCredentialsChecker;
use Seb\SamlBundle\Security\SamlCredentialsProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

class SebSamlExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setDefinition('seb_saml.auth', $this->buildOneLoginAuthDef($config));
        $container->setDefinition('seb_saml.attr_storage', new Definition(SamlAttributesStorage::class));

        $authGuard = $this->buildAuthGuardDef($config);

        $container->setDefinition('seb_saml.guard', $authGuard);

        $container->setDefinition(SamlController::class,
            (new Definition(SamlController::class))->setArgument(0, new Reference('seb_saml.auth')));
    }

    public function buildAuthGuardDef($config)
    {
        $authGuard = new Definition(Authenticator::class);
        $authGuard->setArgument('$credentialsProvider', $this->buildCredentialsProviderDef($config));
        $authGuard->setArgument('$credentialsChecker', new Definition(SamlCredentialsChecker::class));
        $authGuard->setArgument('$authenticatedTokenProvider',
            (new Definition(SamlAuthenticatedTokenProvider::class))
                ->setArgument('$attributesStorage', new Reference('seb_saml.attr_storage'))
        );
        $authGuard->setArgument('$missingUserPolicy', $this->missingUserPolicyDefinition($config));
        $authGuard->setArgument('$badCredentialsPolicy', $this->badCredentialsPolicyDefinition($config));
        // TODO: allow ignore target path
        $authGuard->setArgument('$successfulAuthenticationPolicy',
            (new Definition(TargetPathOrHomePageRedirect::class))->setAutowired(true));

        return $authGuard;
    }

    public function missingUserPolicyDefinition(array $config)
    {
        $policy = $config['missing_user'] ?? 'fail';
        if ('create' === $policy) {
            $missingUser = new Definition(CreateUserIfAuthSucceeds::class);
        } else {
            $missingUser = new Definition(ThrowOnMissingUser::class);
        }
        $missingUser->setAutowired(true);

        return $missingUser;
    }

    public function badCredentialsPolicyDefinition(array $config)
    {
        $policy = new Definition(RedirectOnBadCredentials::class);
        $policy->setAutowired(true);
        $policy->setArgument(0, $config['failure_path']);

        return $policy;
    }

    public function buildCredentialsProviderDef($config)
    {
        $credentialsProvider = new Definition(SamlCredentialsProvider::class);
        $credentialsProvider->setAutowired(true);
        $credentialsProvider->setArgument('$oneLoginAuth', new Reference('seb_saml.auth'));
        $credentialsProvider->setArgument('$attributesStorage', new Reference('seb_saml.attr_storage'));
        $credentialsProvider->setArgument('$options', $config);

        return $credentialsProvider;
    }

    public function buildOneLoginAuthDef($config)
    {
        $samlAuth = new Definition(Auth::class);
        $samlAuth->setArgument(0, $config['onelogin_saml_settings']);

        return $samlAuth;
    }
}
