<?php

use Grafikart\EventManager;

class EventManagerTest extends \PHPUnit\Framework\TestCase {

    /**
     * @var EventManager
     */
    private $manager;

    public function setUp()
    {
        $this->manager = new EventManager();
        parent::setUp();
    }

    /**
     * Génère un faux évènement
     * @param string $eventName
     * @param string $target
     * @return PHPUnit_Framework_MockObject_MockObject|\Psr\EventManager\EventInterface
     */
    private function makeEvent(string $eventName = 'default.event', $target = 'target') {
        $event = $this->getMockBuilder(\Psr\EventManager\EventInterface::class)->getMock();
        $event->method('getName')->willReturn($eventName);
        return $event;
    }

    public function testTriggerEvent() {
        $event = $this->makeEvent();
        $this->manager->attach($event->getName(), function () {
            echo 'Event1';
        });
        $this->manager->trigger($event->getName());
        $this->expectOutputString('Event1');
    }

    public function testTriggerEventWithEventObject() {
        $event = $this->makeEvent();
        $this->manager->attach($event->getName(), function () { echo 'Event1'; });
        $this->manager->trigger($event->getName());
        $this->expectOutputString($event);
        $this->expectOutputString('Event1');
    }

    public function testTriggerMultipleEvent() {
        $event = $this->makeEvent();
        $this->manager->attach($event->getName(), function () { echo 'Event1'; });
        $this->manager->attach($event->getName(), function () { echo 'Event2'; });
        $this->manager->trigger($event->getName());
        $this->expectOutputRegex('/Event1/');
        $this->expectOutputRegex('/Event2/');
    }

    public function testTriggerOrderWithPriority() {
        $event = $this->makeEvent();
        $this->manager->attach($event->getName(), function () { echo 'Event1'; }, 1000);
        $this->manager->attach($event->getName(), function () { echo 'Event3'; });
        $this->manager->attach($event->getName(), function () { echo 'Event2'; }, 100);
        $this->manager->trigger($event->getName());
        $this->expectOutputString('Event1Event2Event3');
    }

    public function testDetachListener() {
        $event = $this->makeEvent();
        $cb2 = function () { echo 'Event2'; };
        $this->manager->attach($event->getName(), function () { echo 'Event1'; });
        $this->manager->attach($event->getName(), $cb2);
        $this->manager->detach($event->getName(), $cb2);
        $this->manager->trigger($event->getName());
        $this->expectOutputString('Event1');
    }

    public function testClearListeners() {
        $event = $this->makeEvent('a');
        $event2 = $this->makeEvent('b');
        $this->manager->attach($event->getName(), function () { echo 'Event1'; });
        $this->manager->attach($event->getName(), function () { echo 'Event2'; });
        $this->manager->attach($event2->getName(), function () { echo 'Event21'; });
        $this->manager->clearListeners($event->getName());
        $this->manager->trigger($event->getName());
        $this->manager->trigger($event2->getName());
        $this->expectOutputString('Event21');
    }

    public function testTriggerOrderWithStopPropagation() {
        $event = $this->makeEvent();
        $this->manager->attach($event->getName(), function () { echo 'Event1'; }, 1000);
        $this->manager->attach($event->getName(), function () { echo 'Event3'; });
        $this->manager->attach($event->getName(), function (\Psr\EventManager\EventInterface $event) {
            echo 'Event2';
            $event->stopPropagation(true);
        }, 100);
        $this->manager->trigger($event->getName());
        $this->expectOutputString('Event1Event2');
    }

}