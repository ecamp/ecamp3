<?php

namespace eCamp\Lib\Mail;

use Interop\Container\ContainerInterface;
use Laminas\Mail\Transport\TransportInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Mime\Mime;
use Laminas\View\View;

class LaminasMailFactory implements FactoryInterface {
    /**
     * @param string $requestedName
     *
     * @return ProviderInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $config = $container->get('Config');

        $mailTransport = $container->get(TransportInterface::class);
        $view = $container->get(View::class);
        $templateConfig = $config['ecamp']['laminas_mail'];

        return new LaminasMail($mailTransport, $view, $templateConfig);
    }
}
