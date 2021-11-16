<?php

namespace App\Tests\DataPersister;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestStackMockFactory {
    public static function create(\Closure $createMock): RequestStack|MockObject {
        $requestStack = $createMock(RequestStack::class);
        $request = $createMock(Request::class);
        $parameterBag = new ParameterBag(['previous_data' => null]);
        $request->attributes = &$parameterBag;
        $requestStack->method('getCurrentRequest')->willReturn($request);

        return $requestStack;
    }
}
