<?php

namespace App\Tests\Integration\Image;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Process\Process;

/**
 * @internal
 */
class CaddyImageDeliveryTest extends TestCase {
    private static string $directory;
    private static string $address;
    private static Process $server;

    public static function setUpBeforeClass(): void {
        self::$directory = sys_get_temp_dir().'/caddy-image-test-'.bin2hex(random_bytes(6));
        $fs = new Filesystem();
        $fs->mkdir([self::$directory.'/public', self::$directory.'/upstream']);
        $socket = stream_socket_server('tcp://127.0.0.1:0');
        self::$address = stream_socket_get_name($socket, false);
        fclose($socket);
        $socket = stream_socket_server('tcp://127.0.0.1:0');
        $upstream = stream_socket_get_name($socket, false);
        fclose($socket);
        file_put_contents(self::$directory.'/image', 'image bytes');
        $fixture = '<?php header("Content-Type: image/webp"); header(\'ETag: "image-hash"\'); header("Cache-Control: private, no-cache");';
        $fixture .= 'if (isset($_GET["denied"])) { http_response_code(403); exit; }';
        $fixture .= 'if (isset($_GET["s3"])) {';
        $fixture .= 'header("X-Image-Scheme: http"); header("X-Image-Upstream: '.$upstream.'"); header("X-Image-Host: '.$upstream.'"); header("X-Image-Uri: /index.php?signature=secret%2Bvalue&credential=test%2Fscope");';
        $fixture .= '} else { header("X-Image-File: '.self::$directory.'/image"); }';
        file_put_contents(self::$directory.'/public/index.php', $fixture);
        file_put_contents(self::$directory.'/upstream/index.php', '<?php file_put_contents('.var_export(self::$directory.'/upstream-uri', true).', $_SERVER["REQUEST_URI"]); file_put_contents('.var_export(self::$directory.'/upstream-request', true).', json_encode(getallheaders())); header("Content-Type: image/webp"); header("ETag: storage-etag"); header("Cache-Control: public, max-age=3600"); header("Set-Cookie: secret=value"); echo "image bytes";');

        $config = file_get_contents(dirname(__DIR__, 3).'/docker/caddy/Caddyfile');
        $config = str_replace(['admin 0.0.0.0:2019', '{$SERVER_NAME:localhost}', 'root /app/public'], ['admin off', 'http://'.self::$address, 'root '.self::$directory.'/public'], $config);
        $config .= "\nhttp://{$upstream} {\nroot ".self::$directory."/upstream\nphp_server\n}\n";
        file_put_contents(self::$directory.'/Caddyfile', $config);
        self::$server = new Process(['frankenphp', 'run', '--config', self::$directory.'/Caddyfile'], null, ['NUM_THREADS' => '2', 'LOG_LEVEL' => 'error']);
        self::$server->start();
        for ($attempt = 0; $attempt < 100; ++$attempt) {
            $connection = @stream_socket_client('tcp://'.self::$address, $errorCode, $error, 0.1);
            if (false !== $connection) {
                fclose($connection);

                return;
            }
            if (!self::$server->isRunning()) {
                throw new \RuntimeException(self::$server->getErrorOutput());
            }
            usleep(100000);
        }

        throw new \RuntimeException('Caddy did not start: '.self::$server->getErrorOutput());
    }

    public static function tearDownAfterClass(): void {
        self::$server->stop();
        new Filesystem()->remove(self::$directory);
    }

    public function testCaddyServesLocalBytesAndPreservesTheApplicationEtag(): void {
        $response = HttpClient::create()->request('GET', 'http://'.self::$address.'/image');
        self::assertSame('image bytes', $response->getContent());
        $headers = $response->getHeaders();
        self::assertSame(['"image-hash"'], $headers['etag']);
        self::assertSame(['private, no-cache'], $headers['cache-control']);
        self::assertArrayNotHasKey('x-image-file', $headers);
    }

    public function testCaddyProxiesStorageWithoutLeakingCredentialsOrItsSignedUrl(): void {
        $response = HttpClient::create()->request('GET', 'http://'.self::$address.'/image?s3=1', ['headers' => ['Cookie' => 'jwt=secret', 'Authorization' => 'Bearer secret']]);
        self::assertSame('image bytes', $response->getContent());
        $headers = $response->getHeaders();
        self::assertSame(['"image-hash"'], $headers['etag']);
        self::assertSame(['private, no-cache'], $headers['cache-control']);
        self::assertArrayNotHasKey('set-cookie', $headers);
        foreach (array_keys($headers) as $name) {
            self::assertStringStartsNotWith('x-image-', $name);
        }
        $upstreamHeaders = array_change_key_case(json_decode(file_get_contents(self::$directory.'/upstream-request'), true));
        self::assertArrayNotHasKey('cookie', $upstreamHeaders);
        self::assertArrayNotHasKey('authorization', $upstreamHeaders);
        self::assertSame('/index.php?signature=secret%2Bvalue&credential=test%2Fscope', file_get_contents(self::$directory.'/upstream-uri'));
    }

    public function testClientHeadersCannotTriggerFileDelivery(): void {
        $response = HttpClient::create()->request('GET', 'http://'.self::$address.'/image?denied=1', ['headers' => ['X-Image-File' => self::$directory.'/image']]);
        self::assertSame(403, $response->getStatusCode());
        self::assertSame('', $response->getContent(false));
    }

    public function testHeadReturnsHeadersWithoutTheLocalFileBody(): void {
        $response = HttpClient::create()->request('HEAD', 'http://'.self::$address.'/image');
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('', $response->getContent());
        self::assertSame(['"image-hash"'], $response->getHeaders()['etag']);
    }
}
