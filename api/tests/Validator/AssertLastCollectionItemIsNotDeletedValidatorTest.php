<?php

namespace App\Tests\Validator;

use App\Validator\AssertLastCollectionItemIsNotDeleted;
use App\Validator\AssertLastCollectionItemIsNotDeletedValidator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\PersistentCollection;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
class AssertLastCollectionItemIsNotDeletedValidatorTest extends ConstraintValidatorTestCase {

    private MockObject|RequestStack $requestStack;
    private MockObject|EntityManagerInterface $em;

    public function testExpectsMatchingAnnotation() {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, new Email());
    }

    public function testExpectsRealPropertyName() {
        $this->setObject(new \stdClass());
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, new AssertLastCollectionItemIsNotDeleted());
    }

    public function testExpectsNonNullCollection() {
        $this->setObject(new AssertLastCollectionItemNotDeletedValidatorTestClass(null));
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, new AssertLastCollectionItemIsNotDeleted());
    }

    public function testEmptyInvalid() {
        // given
        $collectionMock = new PersistentCollection($this->em, new ClassMetadata(AssertLastCollectionItemNotDeletedValidatorTestClass::class), new ArrayCollection([]));
        $object = new AssertLastCollectionItemNotDeletedValidatorTestClass($collectionMock);
        $this->setObject($object);

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())->method('getMethod')->willReturn('DELETE');
        $this->requestStack->expects($this->once())->method('getCurrentRequest')->willReturn($requestMock);

        // when
        $this->validator->validate($object->a, new AssertLastCollectionItemIsNotDeleted());

        // then
        $this->buildViolation('Cannot delete the last entry.')
            ->setInvalidValue($collectionMock)
            ->setCode(AssertLastCollectionItemIsNotDeleted::IS_EMPTY_ERROR)
            ->assertRaised()
        ;
    }

    public function testEmptyValidIfMethodIsNotDelete() {
        // given
        $collectionMock = new PersistentCollection($this->em, new ClassMetadata(AssertLastCollectionItemNotDeletedValidatorTestClass::class), new ArrayCollection([]));
        $object = new AssertLastCollectionItemNotDeletedValidatorTestClass($collectionMock);
        $this->setObject($object);

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())->method('getMethod')->willReturn('POST');
        $this->requestStack->expects($this->once())->method('getCurrentRequest')->willReturn($requestMock);

        // when
        $this->validator->validate($object->a, new AssertLastCollectionItemIsNotDeleted());

        // then
        $this->assertNoViolation();
    }

    public function testNotEmptyValid() {
        // given
        $collectionMock = new PersistentCollection($this->em, new ClassMetadata(AssertLastCollectionItemNotDeletedValidatorTestClass::class), new ArrayCollection([
            'foo',
            'bar',
        ]));
        $object = new AssertLastCollectionItemNotDeletedValidatorTestClass($collectionMock);
        $this->setObject($object);

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())->method('getMethod')->willReturn('DELETE');
        $this->requestStack->expects($this->once())->method('getCurrentRequest')->willReturn($requestMock);

        // when
        $this->validator->validate($object->a, new AssertLastCollectionItemIsNotDeleted());

        // then
        $this->assertNoViolation();
    }

    protected function createValidator(): ConstraintValidatorInterface {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->em = $this->createMock(EntityManagerInterface::class);

        return new AssertLastCollectionItemIsNotDeletedValidator($this->requestStack, $this->em);
    }
}

class AssertLastCollectionItemNotDeletedValidatorTestClass {
    #[AssertLastCollectionItemIsNotDeleted()]
    public $a;

    public function __construct($a) {
        $this->a = $a;
    }
}
