<?php

namespace App\Tests\Security;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Exception\ItemNotFoundException;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Operations;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use ApiPlatform\Metadata\ResourceAccessCheckerInterface;
use App\Entity\Activity;
use App\Entity\Camp;
use App\Security\ResourceReadAccessChecker;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouterInterface;

/**
 * @internal
 */
#[AllowMockObjectsWithoutExpectations]
class ResourceReadAccessCheckerTest extends TestCase {
    private const ITEM_IRI = '/api/activities/abc';
    private const COLLECTION_IRI = '/api/camps/c1/activities';

    private MockObject|RouterInterface $router;
    private MockObject|ResourceMetadataCollectionFactoryInterface $metadataFactory;
    private MockObject|ResourceAccessCheckerInterface $accessChecker;
    private IriConverterInterface|MockObject $iriConverter;
    private ManagerRegistry|MockObject $managerRegistry;
    private ResourceReadAccessChecker $checker;

    public function setUp(): void {
        parent::setUp();

        $this->router = $this->createMock(RouterInterface::class);
        $this->metadataFactory = $this->createMock(ResourceMetadataCollectionFactoryInterface::class);
        $this->accessChecker = $this->createMock(ResourceAccessCheckerInterface::class);
        $this->iriConverter = $this->createMock(IriConverterInterface::class);
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);

        $this->checker = new ResourceReadAccessChecker(
            $this->router,
            $this->metadataFactory,
            $this->accessChecker,
            $this->iriConverter,
            $this->managerRegistry,
        );
    }

    #[DataProvider('invalidIris')]
    public function testRejectsIrisWithQueryStringOrFragment(string $iri) {
        $this->router->expects(self::never())->method('match');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('without query string or fragment');

        $this->checker->check($iri);
    }

    public static function invalidIris(): array {
        return [
            'query' => ['/api/activities/abc?foo=bar'],
            'fragment' => ['/api/activities/abc#foo'],
        ];
    }

    public function testRejectsIriWithoutMatchingRoute() {
        $this->router->method('match')->willThrowException(new ResourceNotFoundException());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No route matches');

        $this->checker->check('/nope');
    }

    #[DataProvider('nonResourceRouteParameters')]
    public function testRejectsRouteThatIsNoResource(array $parameters) {
        $this->router->method('match')->willReturn($parameters);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('does not reference a resource');

        $this->checker->check('/api/docs');
    }

    public static function nonResourceRouteParameters(): array {
        return [
            'no api route' => [['_route' => 'docs']],
            'no operation name' => [['_api_resource_class' => Activity::class]],
            'no resource class' => [['_api_operation_name' => 'get']],
        ];
    }

    public function testRejectsNonGetOperation() {
        $this->mockRoute(Activity::class, 'put', new Put());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('does not reference a readable resource');

        $this->checker->check(self::ITEM_IRI);
    }

    public function testItemWithoutSecurityIsGranted() {
        $operation = new Get(class: Activity::class);
        $this->mockRoute(Activity::class, 'get', $operation);
        $this->iriConverter->method('getResourceFromIri')->willReturnCallback(function (string $iri) {
            self::assertSame(self::ITEM_IRI, $iri);

            return new Activity();
        });
        $this->accessChecker->expects(self::never())->method('isGranted');

        $result = $this->checker->check(self::ITEM_IRI);

        self::assertTrue($result->granted);
        self::assertSame(self::ITEM_IRI, $result->iri);
        self::assertSame(Activity::class, $result->resourceClass);
        self::assertSame($operation, $result->operation);
    }

    #[DataProvider('booleans')]
    public function testItemSecurityIsEvaluatedWithObject(bool $granted) {
        $activity = new Activity();
        $this->mockRoute(Activity::class, 'get', new Get(class: Activity::class, security: 'is_granted("CAMP_MEMBER", object)'));
        $this->iriConverter->method('getResourceFromIri')->willReturn($activity);
        $this->accessChecker->expects(self::once())->method('isGranted')
            ->with(Activity::class, 'is_granted("CAMP_MEMBER", object)', ['object' => $activity])
            ->willReturn($granted)
        ;

        self::assertSame($granted, $this->checker->check(self::ITEM_IRI)->granted);
    }

    public static function booleans(): array {
        return ['granted' => [true], 'denied' => [false]];
    }

    #[DataProvider('hiddenByProvider')]
    public function testItemHiddenByStateProviderIsNotGranted(\Throwable $exception) {
        $this->mockRoute(Activity::class, 'get', new Get(class: Activity::class));
        $this->iriConverter->method('getResourceFromIri')->willThrowException($exception);

        self::assertFalse($this->checker->check(self::ITEM_IRI)->granted);
    }

    public static function hiddenByProvider(): array {
        return [
            '403' => [new AccessDeniedHttpException()],
            '404' => [new NotFoundHttpException()],
        ];
    }

    public function testMissingItemThrows() {
        $this->mockRoute(Activity::class, 'get', new Get(class: Activity::class));
        $this->iriConverter->method('getResourceFromIri')->willThrowException(new ItemNotFoundException());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('does not exist');

        $this->checker->check(self::ITEM_IRI);
    }

    public function testOtherHttpErrorsOfItemAreRethrown() {
        $this->mockRoute(Activity::class, 'get', new Get(class: Activity::class));
        $this->iriConverter->method('getResourceFromIri')->willThrowException(new BadRequestHttpException());

        $this->expectException(BadRequestHttpException::class);

        $this->checker->check(self::ITEM_IRI);
    }

    public function testCollectionWithoutSecurityIsGranted() {
        $operation = new GetCollection(class: Activity::class);
        $this->mockRoute(Activity::class, 'get_collection', $operation, ['campId' => 'c1']);
        $this->iriConverter->expects(self::never())->method('getResourceFromIri');
        $this->accessChecker->expects(self::never())->method('isGranted');

        $result = $this->checker->check(self::COLLECTION_IRI);

        self::assertTrue($result->granted);
        self::assertSame($operation, $result->operation);
    }

    #[DataProvider('booleans')]
    public function testCollectionSecurityIsEvaluatedWithoutObject(bool $granted) {
        $this->mockRoute(Activity::class, 'get_collection', new GetCollection(class: Activity::class, security: 'is_authenticated()'));
        $this->accessChecker->expects(self::once())->method('isGranted')
            ->with(Activity::class, 'is_authenticated()', [])
            ->willReturn($granted)
        ;

        self::assertSame($granted, $this->checker->check(self::COLLECTION_IRI)->granted);
    }

    #[DataProvider('booleans')]
    public function testCollectionUriVariableSecurityIsEvaluatedWithParent(bool $granted) {
        $camp = new Camp();
        $this->mockParent($camp);
        $this->mockRoute(Activity::class, 'get_collection', new GetCollection(
            class: Activity::class,
            uriVariables: ['campId' => new Link(
                fromClass: Camp::class,
                toProperty: 'camp',
                security: 'is_granted("CAMP_MEMBER", camp)',
            )],
        ), ['campId' => 'c1']);
        $this->accessChecker->expects(self::once())->method('isGranted')
            ->with(Camp::class, 'is_granted("CAMP_MEMBER", camp)', ['camp' => $camp])
            ->willReturn($granted)
        ;

        self::assertSame($granted, $this->checker->check(self::COLLECTION_IRI)->granted);
    }

    public function testCollectionUriVariableSecurityObjectNameTakesPrecedence() {
        $camp = new Camp();
        $this->mockParent($camp);
        $this->mockRoute(Activity::class, 'get_collection', new GetCollection(
            class: Activity::class,
            uriVariables: ['campId' => new Link(
                fromClass: Camp::class,
                toProperty: 'camp',
                security: 'x',
                securityObjectName: 'parent',
            )],
        ), ['campId' => 'c1']);
        $this->accessChecker->expects(self::once())->method('isGranted')
            ->with(Camp::class, 'x', ['parent' => $camp])
            ->willReturn(true)
        ;

        self::assertTrue($this->checker->check(self::COLLECTION_IRI)->granted);
    }

    public function testDeniedUriVariableSecurityShortCircuitsOperationSecurity() {
        $this->mockParent(new Camp());
        $this->mockRoute(Activity::class, 'get_collection', new GetCollection(
            class: Activity::class,
            security: 'is_authenticated()',
            uriVariables: ['campId' => new Link(fromClass: Camp::class, toProperty: 'camp', security: 'x')],
        ), ['campId' => 'c1']);
        $this->accessChecker->expects(self::once())->method('isGranted')->willReturn(false);

        self::assertFalse($this->checker->check(self::COLLECTION_IRI)->granted);
    }

    public function testGrantedUriVariableSecurityStillRequiresOperationSecurity() {
        $this->mockParent(new Camp());
        $this->mockRoute(Activity::class, 'get_collection', new GetCollection(
            class: Activity::class,
            security: 'is_authenticated()',
            uriVariables: ['campId' => new Link(fromClass: Camp::class, toProperty: 'camp', security: 'x')],
        ), ['campId' => 'c1']);
        $this->accessChecker->expects(self::exactly(2))->method('isGranted')
            ->willReturnOnConsecutiveCalls(true, false)
        ;

        self::assertFalse($this->checker->check(self::COLLECTION_IRI)->granted);
    }

    public function testUriVariableWithoutSecurityIsSkipped() {
        $this->managerRegistry->expects(self::never())->method('getManagerForClass');
        $this->mockRoute(Activity::class, 'get_collection', new GetCollection(
            class: Activity::class,
            uriVariables: ['campId' => new Link(fromClass: Camp::class, toProperty: 'camp')],
        ), ['campId' => 'c1']);
        $this->accessChecker->expects(self::never())->method('isGranted');

        self::assertTrue($this->checker->check(self::COLLECTION_IRI)->granted);
    }

    public function testMissingParentOfCollectionThrows() {
        $this->mockParent(null);
        $this->mockRoute(Activity::class, 'get_collection', new GetCollection(
            class: Activity::class,
            uriVariables: ['campId' => new Link(fromClass: Camp::class, toProperty: 'camp', security: 'x')],
        ), ['campId' => 'c1']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('parent resource does not exist');

        $this->checker->check(self::COLLECTION_IRI);
    }

    public function testCollectionWithSecuredLinkButNoRouteParameterThrows() {
        $this->mockRoute(Activity::class, 'get_collection', new GetCollection(
            class: Activity::class,
            uriVariables: ['campId' => new Link(fromClass: Camp::class, toProperty: 'camp', security: 'x')],
        ));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('parent resource does not exist');

        $this->checker->check(self::COLLECTION_IRI);
    }

    private function mockParent(?object $parent): void {
        $manager = $this->createMock(ObjectManager::class);
        $manager->method('find')->willReturnCallback(function (string $class, mixed $id) use ($parent) {
            self::assertSame([Camp::class, 'c1'], [$class, $id]);

            return $parent;
        });
        $this->managerRegistry->method('getManagerForClass')->willReturnCallback(function (string $class) use ($manager) {
            self::assertSame(Camp::class, $class);

            return $manager;
        });
    }

    private function mockRoute(string $resourceClass, string $operationName, HttpOperation $operation, array $extraParameters = []): void {
        $this->router->method('match')->willReturn([
            '_api_resource_class' => $resourceClass,
            '_api_operation_name' => $operationName,
        ] + $extraParameters);

        $collection = new ResourceMetadataCollection($resourceClass, [
            (new ApiResource())->withOperations(new Operations([$operationName => $operation])),
        ]);
        $this->metadataFactory->method('create')->willReturnCallback(function (string $class) use ($resourceClass, $collection) {
            self::assertSame($resourceClass, $class);

            return $collection;
        });
    }
}
