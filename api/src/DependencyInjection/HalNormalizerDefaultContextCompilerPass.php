<?php

namespace App\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class HalNormalizerDefaultContextCompilerPass implements CompilerPassInterface {
    /**
     * Sets the same serializer defaults as App\Serializer\SerializerContextBuilder as default context of the
     * HAL ItemNormalizer, so that they also apply when serializing outside of a request, for example in
     * PublishMercureUpdatesListener. Without this, the HAL normalizer would skip empty to-one relations in _links.
     */
    public function process(ContainerBuilder $container): void {
        $definition = $container->getDefinition('api_platform.hal.normalizer.item');

        // $defaultContext is the 8th constructor argument (index 7), see api-platform's hal.php
        $definition->replaceArgument(7, array_merge($definition->getArgument(7), [
            'skip_null_to_one_relations' => false,
            'skip_null_values' => false,
        ]));
    }
}
