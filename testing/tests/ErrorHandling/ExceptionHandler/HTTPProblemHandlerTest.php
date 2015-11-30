<?php
/**
 * @copyright ©2015 Quicken Loans Inc. All rights reserved. Trade Secret,
 *    Confidential and Proprietary. Any dissemination outside of Quicken Loans
 *    is strictly prohibited.
 */

namespace QL\Panthor\ErrorHandling\ExceptionHandler;

use Exception as BaseException;
use Mockery;
use PHPUnit_Framework_TestCase;
use QL\Panthor\ErrorHandling\ExceptionRendererInterface;
use QL\Panthor\Exception\Exception;
use QL\Panthor\Exception\HTTPProblemException;
use QL\Panthor\Exception\RequestException;
use QL\Panthor\Testing\MockeryAssistantTrait;

class HTTPProblemHandlerTest extends PHPUnit_Framework_TestCase
{
    use MockeryAssistantTrait;

    public function testCanHandleNotFoundException()
    {
        $renderer = Mockery::mock(ExceptionRendererInterface::CLASS);

        $handler = new HTTPProblemHandler($renderer);

        $handled = $handler->getHandledExceptions();
        $this->assertCount(1, $handled);

        $handled = $handled[0];

        $this->assertNotInstanceOf($handled, new BaseException);
        $this->assertNotInstanceOf($handled, new Exception);
        $this->assertNotInstanceOf($handled, new RequestException);

        $this->assertInstanceOf($handled, new HTTPProblemException(500, ''));
    }

    public function testDoesNotHandleIfExceptionNotRequestException()
    {
        $renderer = Mockery::mock(ExceptionRendererInterface::CLASS);

        $handler = new HTTPProblemHandler($renderer);

        $this->assertFalse($handler->handle(new Exception));
        $this->assertFalse($handler->handle(new RequestException));
        $this->assertFalse($handler->handle(new BaseException));
    }

    public function testStatusAndContextPassedToRenderer()
    {
        $renderer = Mockery::mock(ExceptionRendererInterface::CLASS);
        $this->spy($renderer, 'render', [410, $this->buildSpy('renderer')]);

        $handler = new HTTPProblemHandler($renderer);

        $ex = new HTTPProblemException(410, 'Error occured', [
            'data' => 'abcd'
        ]);

        $this->assertTrue($handler->handle($ex));

        $context = $this->getSpy('renderer');
        $context = $context();

        $this->assertCount(4, $context);

        $this->assertSame('Error occured', $context['message']);
        $this->assertSame(410, $context['status']);
        $this->assertSame('Problem', $context['severity']);
        $this->assertSame($ex, $context['exception']);
    }
}