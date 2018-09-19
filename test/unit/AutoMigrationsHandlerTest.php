<?php

namespace RebelCode\Storage\UnitTest\Resource\WordPress\Module;

use Exception;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Storage\Resource\WordPress\Module\AutoMigrationsHandler as TestSubject;
use Xpmock\TestCase;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class AutoMigrationsHandlerTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\Storage\Resource\WordPress\Module\AutoMigrationsHandler';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @param array $methods The methods to mock.
     *
     * @return MockObject|TestSubject
     */
    public function createInstance(array $methods = [])
    {
        return $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                    ->setMethods($methods)
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testCanBeCreated()
    {
        $subject = $this->createInstance();

        $this->assertInstanceOf(
            static::TEST_SUBJECT_CLASSNAME,
            $subject,
            'A valid instance of the test subject could not be created.'
        );

        $this->assertInstanceOf(
            'Dhii\Invocation\InvocableInterface',
            $subject,
            'Test subject does not implement expected interface.'
        );
    }

    /**
     * Tests the constructor to assert whether the given data is correctly stored and retrieved.
     *
     * @since [*next-version*]
     */
    public function testConstructor()
    {
        $target     = rand(0, 100);
        $evtManager = $this->getMockForAbstractClass('Psr\EventManager\EventManagerInterface');
        $evtFactory = $this->getMockForAbstractClass('Dhii\Event\EventFactoryInterface');
        $migrator   = $this->getMockBuilder('RebelCode\Storage\Resource\WordPress\Module\Migrator')
                           ->disableOriginalConstructor()
                           ->getMock();

        $subject = new TestSubject($migrator, $target, $evtManager, $evtFactory);
        $reflect = $this->reflect($subject);

        $this->assertSame($migrator, $reflect->_getMigrator(), 'The migrator instance is incorrect.');
        $this->assertEquals($target, $reflect->_getTargetVersion(), 'The target DB version is incorrect.');
        $this->assertSame($evtManager, $reflect->_getEventManager(), 'The event manager instance is incorrect.');
        $this->assertSame($evtFactory, $reflect->_getEventFactory(), 'The event factory instance is incorrect.');
    }

    /**
     * Tests the handler invocation to assert whether the migration is executed and the before and after events are
     * triggered.
     *
     * @since [*next-version*]
     *
     * @throws Exception
     */
    public function testInvoke()
    {
        $subject = $this->createInstance([
            '_getMigrator',
            '_getTargetVersion',
            '_getEventManager',
            '_getEventFactory',
            '_createEvent',
        ]);

        // Create mock internal data
        $target     = rand(0, 100);
        $evtManager = $this->getMockForAbstractClass('Psr\EventManager\EventManagerInterface');
        $evtFactory = $this->getMockForAbstractClass('Dhii\Event\EventFactoryInterface');
        $migrator   = $this->getMockBuilder('RebelCode\Storage\Resource\WordPress\Module\Migrator')
                           ->disableOriginalConstructor()
                           ->getMock();

        // Mock instance getters
        $subject->method('_getTargetVersion')->willReturn($target);
        $subject->method('_getMigrator')->willReturn($migrator);
        $subject->method('_getEventManager')->willReturn($evtManager);
        $subject->method('_getEventFactory')->willReturn($evtFactory);

        // Mock event creation
        $event1 = $this->getMockForAbstractClass('Psr\EventManager\EventInterface');
        $event2 = $this->getMockForAbstractClass('Psr\EventManager\EventInterface');
        $subject->expects($this->exactly(2))
                ->method('_createEvent')
                ->withConsecutive(
                    ['wp_bookings_cqrs_before_migration', ['target' => $target]],
                    ['wp_bookings_cqrs_after_migration', ['target' => $target]]
                )
                ->willReturnOnConsecutiveCalls(
                    $event1,
                    $event2
                );
        // Expect events to be triggered
        $evtManager->expects($this->exactly(2))
                   ->method('trigger')
                   ->withConsecutive(
                       [$event1],
                       [$event2]
                   );

        $migrator->expects($this->once())
                 ->method('migrate')
                 ->with($target);

        $subject->__invoke();
    }

    /**
     * Tests the handler invocation to assert whether the before and failure events are triggered when the migration
     * fails.
     *
     * @since [*next-version*]
     *
     * @throws Exception
     */
    public function testInvokeError()
    {
        $subject = $this->createInstance([
            '_getMigrator',
            '_getTargetVersion',
            '_getEventManager',
            '_getEventFactory',
            '_createEvent',
        ]);

        // Create mock internal data
        $target     = rand(0, 100);
        $evtManager = $this->getMockForAbstractClass('Psr\EventManager\EventManagerInterface');
        $evtFactory = $this->getMockForAbstractClass('Dhii\Event\EventFactoryInterface');
        $migrator   = $this->getMockBuilder('RebelCode\Storage\Resource\WordPress\Module\Migrator')
                           ->disableOriginalConstructor()
                           ->getMock();

        // Mock instance getters
        $subject->method('_getTargetVersion')->willReturn($target);
        $subject->method('_getMigrator')->willReturn($migrator);
        $subject->method('_getEventManager')->willReturn($evtManager);
        $subject->method('_getEventFactory')->willReturn($evtFactory);

        // Mock event creation
        $event1 = $this->getMockForAbstractClass('Psr\EventManager\EventInterface');
        $event2 = $this->getMockForAbstractClass('Psr\EventManager\EventInterface');
        $subject->expects($this->exactly(2))
                ->method('_createEvent')
                ->withConsecutive(
                    ['wp_bookings_cqrs_before_migration', ['target' => $target]],
                    ['wp_bookings_cqrs_on_migration_failed', ['target' => $target]]
                )
                ->willReturnOnConsecutiveCalls(
                    $event1,
                    $event2
                );
        // Expect events to be triggered
        $evtManager->expects($this->exactly(2))
                   ->method('trigger')
                   ->withConsecutive(
                       [$event1],
                       [$event2]
                   );

        $migrator->expects($this->once())
                 ->method('migrate')
                 ->with($target)
                 ->willThrowException(new Exception());

        $subject->__invoke();
    }
}
