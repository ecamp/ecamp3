<?php

namespace App\Tests\Service\Image;

use App\Service\Image\ImageProcessor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @internal
 */
class ImageProcessorTest extends TestCase {
    private array $paths = [];

    protected function tearDown(): void {
        foreach ($this->paths as $path) {
            unlink($path);
        }
        parent::tearDown();
    }

    public function testResizesAndConvertsToWebpWithoutTrustingTheFilename(): void {
        $source = imagecreatetruecolor(120, 60);
        $path = $this->temporaryPath();
        imagepng($source, $path);

        $result = new ImageProcessor(100000, 10000, 40, 85)->process(new UploadedFile($path, 'image.txt', 'text/plain', null, true));

        $info = getimagesizefromstring($result->data);
        self::assertSame(IMAGETYPE_WEBP, $info[2]);
        self::assertSame(40, $result->width);
        self::assertSame(20, $result->height);
        self::assertSame(hash('sha256', $result->data), $result->sha256);
    }

    public function testPreservesTransparencyAndDoesNotUpscale(): void {
        $source = imagecreatetruecolor(12, 6);
        imagealphablending($source, false);
        imagesavealpha($source, true);
        imagefill($source, 0, 0, imagecolorallocatealpha($source, 0, 0, 0, 127));
        $path = $this->temporaryPath();
        imagepng($source, $path);

        $result = new ImageProcessor(100000, 10000, 40, 85)->process(new UploadedFile($path, 'image.png', null, null, true));

        self::assertSame(12, $result->width);
        self::assertSame(6, $result->height);
        $decoded = imagecreatefromstring($result->data);
        self::assertSame(127, imagecolorsforindex($decoded, imagecolorat($decoded, 0, 0))['alpha']);
    }

    public function testAppliesExifOrientationAndRemovesMetadata(): void {
        $source = imagecreatetruecolor(12, 6);
        $path = $this->temporaryPath();
        imagejpeg($source, $path);
        $jpeg = file_get_contents($path);
        $exif = "Exif\0\0".'II'.pack('vV', 42, 8).pack('v', 1).pack('vvVV', 0x0112, 3, 1, 6).pack('V', 0);
        file_put_contents($path, substr($jpeg, 0, 2)."\xff\xe1".pack('n', strlen($exif) + 2).$exif.substr($jpeg, 2));

        $result = new ImageProcessor(100000, 10000, 40, 85)->process(new UploadedFile($path, 'image.jpg', null, null, true));

        self::assertSame(6, $result->width);
        self::assertSame(12, $result->height);
        self::assertStringNotContainsString('Exif', $result->data);
    }

    #[DataProvider('invalidImages')]
    public function testRejectsInvalidImages(string $data, int $maxBytes, int $maxPixels): void {
        $path = $this->temporaryPath();
        file_put_contents($path, $data);

        $this->expectException(UnprocessableEntityHttpException::class);
        new ImageProcessor($maxBytes, $maxPixels, 40, 85)->process(new UploadedFile($path, 'image.png', null, null, true));
    }

    public static function invalidImages(): iterable {
        yield 'not an image' => ['<svg xmlns="http://www.w3.org/2000/svg"/>', 100000, 10000];
        $image = imagecreatetruecolor(12, 6);
        ob_start();
        imagepng($image);
        $png = ob_get_clean();

        yield 'too many bytes' => [$png, 1, 10000];

        yield 'too many pixels' => [$png, 100000, 71];

        yield 'truncated file' => [substr($png, 0, 33), 100000, 10000];
    }

    private function temporaryPath(): string {
        $path = tempnam(sys_get_temp_dir(), 'image-test-');
        $this->paths[] = $path;

        return $path;
    }
}
