<?php

namespace Vinorcola\PrivateUserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Vinorcola\PrivateUserBundle\Model\Config;
use Vinorcola\PrivateUserBundle\Model\EmailModel;

class VinorcolaPrivateUserExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processedConfig = $this->processConfiguration(new Configuration(), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');

        $container->getDefinition(Config::class)->setArgument('$config', $processedConfig);
        $this->injectConfigInServices($container, $processedConfig);
    }

    private function injectConfigInServices(ContainerBuilder $container, array $config): void
    {
        $emailModel = $container->getDefinition(EmailModel::class);
        $emailModel->setArgument('$fromAddress', $config['sending_email_address']);
    }
}
