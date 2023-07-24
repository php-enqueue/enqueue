<?php

namespace Enqueue\Tests\Symfony\Client;

use Enqueue\Client\SpoolProducer;
use Enqueue\Symfony\Client\FlushSpoolProducerListener;
use Enqueue\Test\ClassExtensionTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class FlushSpoolProducerListenerTest extends TestCase
{
    use ClassExtensionTrait;

    public function testShouldImplementEventSubscriberInterface()
    {
        $this->assertClassImplements(EventSubscriberInterface::class, FlushSpoolProducerListener::class);
    }

    public function testShouldSubscribeOnKernelTerminateEvent()
    {
        $events = FlushSpoolProducerListener::getSubscribedEvents();

        self::assertIsArray($events);
        $this->assertArrayHasKey(KernelEvents::TERMINATE, $events);

        $this->assertEquals('flushMessages', $events[KernelEvents::TERMINATE]);
    }

    public function testShouldSubscribeOnConsoleTerminateEvent()
    {
        $events = FlushSpoolProducerListener::getSubscribedEvents();

        self::assertIsArray($events);
        $this->assertArrayHasKey(ConsoleEvents::TERMINATE, $events);

        $this->assertEquals('flushMessages', $events[ConsoleEvents::TERMINATE]);
    }

    public function testCouldBeConstructedWithSpoolProducerAsFirstArgument()
    {
        new FlushSpoolProducerListener($this->createSpoolProducerMock());
    }

    public function testShouldFlushSpoolProducerOnFlushMessagesCall()
    {
        $producerMock = $this->createSpoolProducerMock();
        $producerMock
            ->expects($this->once())
            ->method('flush')
        ;

        $listener = new FlushSpoolProducerListener($producerMock);

        $listener->flushMessages();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|SpoolProducer
     */
    private function createSpoolProducerMock()
    {
        return $this->createMock(SpoolProducer::class);
    }
}
