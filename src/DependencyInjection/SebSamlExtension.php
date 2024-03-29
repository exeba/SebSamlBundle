<?php

namespace Seb\SamlBundle\DependencyInjection;

use Seb\AuthenticatorBundle\Security\Policies\CreateUserIfAuthSucceeds;
use Seb\AuthenticatorBundle\Security\Policies\ThrowOnMissingUser;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class SebSamlExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $container->setParameter('seb_saml.config', $config);
        $container->setParameter('seb_saml.config.one_login_settings', $config['onelogin_saml_settings']);
        $container->setParameter('seb_saml.config.failure_path', $config['failure_path']);

        $container->setDefinition('seb_saml.missing_user', $this->missingUserPolicyDefinition($config));

        if (array_key_exists('user_provider', $config)) {
            $container->getDefinition('seb_saml.passport_provider')
                ->setArgument(0, new Reference("security.user.provider.concrete.{$config['user_provider']}"));
        }
    }

    private function missingUserPolicyDefinition(array $config)
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
}
