<?php

namespace App\Tests\State;

use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Category;
use App\Entity\ContentType;
use App\Service\Image\CampImageCopyService;
use App\Service\Image\CampImageReadAccess;
use App\State\CategoryCreateProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @internal
 */
class CategoryCreateProcessorTest extends TestCase {
    private CategoryCreateProcessor $processor;
    private EntityManagerInterface|Stub $entityManagerStub;
    private Category $category;

    protected function setUp(): void {
        $this->category = new Category();

        $this->entityManagerStub = $this->createStub(EntityManagerInterface::class);
        $decoratedProcessor = $this->createStub(ProcessorInterface::class);
        $this->processor = new CategoryCreateProcessor(
            $decoratedProcessor,
            $this->entityManagerStub,
            new CampImageCopyService($this->entityManagerStub, new CampImageReadAccess($this->entityManagerStub, $this->createStub(Security::class))),
        );
    }

    public function testCreatesNewContentNodeBeforeCreate() {
        // given
        $repositoryMock = $this->createStub(EntityRepository::class);
        $repositoryMock->method('findOneBy')->willReturnCallback(function (array $criteria): object {
            $result = new ContentType();
            $result->name = $criteria['name'];

            return $result;
        });
        $this->entityManagerStub->method('getRepository')->willReturn($repositoryMock);

        // when
        $data = $this->processor->onBefore($this->category, new Post());

        // then
        $this->assertNotNull($data->getRootContentNode());
        $this->assertEquals('ColumnLayout', $data->getRootContentNode()->contentType->name);
    }
}
