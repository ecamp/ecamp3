<?php

namespace App\Tests\State\Util;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\State\ProcessorInterface;
use App\State\Util\AbstractRemoveProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class AbstractRemoveProcessorTest extends TestCase {
    private MockObject|ProcessorInterface $decoratedProcessor;
    private MockObject|AbstractRemoveProcessor $processor;

    protected function setUp(): void {
        $this->decoratedProcessor = $this->createMock(ProcessorInterface::class);

        $this->processor = $this->getMockForAbstractClass(
            AbstractRemoveProcessor::class,
            [
                $this->decoratedProcessor,
            ],
            mockedMethods: ['onBefore', 'onAfter']
        );
    }

    public function testCallsOnBeforeAndOnAfterOnDelete() {
        $this->processor->expects(self::once())->method('onBefore');
        $this->processor->expects(self::once())->method('onAfter');
        $this->decoratedProcessor->expects(self::once())->method('process');

        $this->processor->process(new \stdClass(), new Delete());
    }
}
