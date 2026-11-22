<?php

namespace App\Service\Image;

use Aws\CommandInterface;
use Aws\Middleware;
use Aws\S3\S3Client;

final class S3ClientFactory {
    public function create(array $options): S3Client {
        if ('' === ($options['endpoint'] ?? null)) {
            unset($options['endpoint']);
        }

        $client = new S3Client($options);
        $client->getHandlerList()->appendInit(Middleware::mapCommand(static function (CommandInterface $command): CommandInterface {
            if (in_array($command->getName(), ['PutObject', 'CreateMultipartUpload'], true)) {
                unset($command['ACL']);
            }

            return $command;
        }), 'image-storage.no-acl');

        return $client;
    }
}
