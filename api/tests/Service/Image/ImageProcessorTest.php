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

    #[DataProvider('rasterFormats')]
    public function testResizesAndConvertsToAvifWithoutTrustingTheFilename(string $encoder): void {
        $source = imagecreatetruecolor(120, 60);
        $path = $this->temporaryPath();
        $encoder($source, $path);

        $result = new ImageProcessor(100000, 10000, 40, 85)->process(new UploadedFile($path, 'image.txt', 'text/plain', null, true));

        $info = getimagesizefromstring($result->data);
        self::assertSame(IMAGETYPE_AVIF, $info[2]);
        self::assertSame(40, $result->width);
        self::assertSame(20, $result->height);
        self::assertSame(hash('sha256', $result->data), $result->sha256);
    }

    public static function rasterFormats(): iterable {
        yield 'JPEG' => ['imagejpeg'];

        yield 'PNG' => ['imagepng'];

        yield 'WebP' => ['imagewebp'];

        yield 'AVIF' => ['imageavif'];

        yield 'GIF' => ['imagegif'];

        yield 'BMP' => ['imagebmp'];
    }

    public function testRasterizesSvgWithTransparencyToAvif(): void {
        $path = $this->temporaryPath();
        file_put_contents($path, '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 60"><rect width="60" height="60" fill="red"/></svg>');

        $result = new ImageProcessor(100000, 10000, 40, 85)->process(new UploadedFile($path, 'image.txt', 'text/plain', null, true));

        self::assertSame(IMAGETYPE_AVIF, getimagesizefromstring($result->data)[2]);
        self::assertSame(40, $result->width);
        self::assertSame(20, $result->height);
        $decoded = imagecreatefromstring($result->data);
        self::assertGreaterThan(200, imagecolorsforindex($decoded, imagecolorat($decoded, 5, 10))['red']);
        self::assertSame(127, imagecolorsforindex($decoded, imagecolorat($decoded, 35, 10))['alpha']);
    }

    public function testBoundsSvgRenderingByThePixelLimit(): void {
        $path = $this->temporaryPath();
        file_put_contents($path, '<svg xmlns="http://www.w3.org/2000/svg" width="1000000" height="1000000"><rect width="100%" height="100%"/></svg>');

        $result = new ImageProcessor(100000, 100, 2400, 85)->process(new UploadedFile($path, 'image.svg', null, null, true));

        self::assertSame(10, $result->width);
        self::assertSame(10, $result->height);
    }

    #[DataProvider('svgFileReferences')]
    public function testSvgCannotEmbedLocalFiles(bool $relative): void {
        $imagePath = $this->temporaryPath();
        $source = imagecreatetruecolor(12, 6);
        imagefill($source, 0, 0, imagecolorallocate($source, 255, 0, 0));
        imagepng($source, $imagePath);
        $path = $this->temporaryPath();
        $base = $relative ? ' xml:base="file://'.dirname($imagePath).'/"' : '';
        $href = $relative ? basename($imagePath) : 'file://'.$imagePath;
        file_put_contents($path, '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="6"'.$base.'><image href="'.$href.'" width="12" height="6"/></svg>');

        $result = new ImageProcessor(100000, 10000, 40, 85)->process(new UploadedFile($path, 'image.svg', null, null, true));

        $decoded = imagecreatefromstring($result->data);
        self::assertSame(127, imagecolorsforindex($decoded, imagecolorat($decoded, 5, 5))['alpha']);
    }

    public static function svgFileReferences(): iterable {
        yield 'absolute file URL' => [false];

        yield 'relative URL with XML base' => [true];
    }

    public function testUsesTheFirstFrameOfAnimatedGif(): void {
        $source = imagecreate(12, 6);
        imagecolorallocate($source, 255, 0, 0);
        $blue = imagecolorallocate($source, 0, 0, 255);
        ob_start();
        imagegif($source);
        $first = ob_get_clean();
        imagefill($source, 0, 0, $blue);
        ob_start();
        imagegif($source);
        $second = ob_get_clean();
        $path = $this->temporaryPath();
        file_put_contents($path, substr($first, 0, -1).substr($second, strpos($second, "\x2c")));

        $result = new ImageProcessor(100000, 10000, 40, 85)->process(new UploadedFile($path, 'animated.gif', null, null, true));

        $decoded = imagecreatefromstring($result->data);
        $color = imagecolorsforindex($decoded, imagecolorat($decoded, 5, 3));
        self::assertGreaterThan(200, $color['red']);
        self::assertLessThan(30, $color['blue']);
    }

    public function testPreservesGifTransparency(): void {
        $source = imagecreate(12, 6);
        $transparent = imagecolorallocate($source, 0, 0, 0);
        imagecolortransparent($source, $transparent);
        $path = $this->temporaryPath();
        imagegif($source, $path);

        $result = new ImageProcessor(100000, 10000, 40, 85)->process(new UploadedFile($path, 'image.gif', null, null, true));

        self::assertSame(12, $result->width);
        self::assertSame(6, $result->height);
        $decoded = imagecreatefromstring($result->data);
        self::assertSame(127, imagecolorsforindex($decoded, imagecolorat($decoded, 0, 0))['alpha']);
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
        yield 'not an image' => ['not an image', 100000, 10000];

        yield 'SVG without dimensions or content' => ['<svg xmlns="http://www.w3.org/2000/svg"/>', 100000, 10000];

        yield 'SVG with doctype' => ['<!DOCTYPE svg [<!ENTITY content "test">]><svg xmlns="http://www.w3.org/2000/svg" width="12" height="6"><text>&content;</text></svg>', 100000, 10000];

        yield 'malformed SVG' => ['<svg xmlns="http://www.w3.org/2000/svg" width="12" height="6"><rect></svg>', 100000, 10000];

        yield 'unsupported XML' => ['<html xmlns="http://www.w3.org/1999/xhtml"/>', 100000, 10000];
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
