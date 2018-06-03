        if ($instance instanceof EntityServiceAware\[ServiceName]Aware) {
            $instance->set[ServiceName]($container->get(EntityService\[ServiceName]::class));
        }