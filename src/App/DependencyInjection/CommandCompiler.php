<?php

namespace Pnl\App\DependencyInjection;

use Pnl\App\AbstractCommand;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

final class CommandCompiler implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        foreach ($container->getServiceIds() as $id) {
            try {
                $serviceDefinition = $container->getDefinition($id);
                $class = $serviceDefinition->getClass();

                if ($class && class_exists($class)) {
                    $reflection = new \ReflectionClass($class);

                    if ($reflection->isSubclassOf(AbstractCommand::class)) {
                        $serviceDefinition->addTag('command');
                    }
                }
            } catch (ServiceNotFoundException $e) {
                continue;
            }
        }
    }
}
