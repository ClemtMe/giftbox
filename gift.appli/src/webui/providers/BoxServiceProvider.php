<?php
namespace gift\appli\webui\providers;

use gift\appli\core\application\usecases\BoxInterface;
use gift\appli\core\application\usecases\BoxAcces;
use Psr\Container\ContainerInterface;

class BoxServiceProvider
{
    public function __invoke(ContainerInterface $container): void
    {
        $container->set(BoxInterface::class, function () {
            return new BoxAccess();
        });
    }
}
