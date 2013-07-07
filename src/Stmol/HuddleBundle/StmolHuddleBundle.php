<?php

namespace Stmol\HuddleBundle;

use Stmol\HuddleBundle\DependencyInjection\Security\Factory\CookieFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class StmolHuddleBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new CookieFactory());
    }
}
