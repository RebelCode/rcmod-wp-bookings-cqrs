<?php

namespace RebelCode\Storage\UnitTest\Resource\WordPress\Module;

use Dhii\Output\TemplateFactoryInterface;
use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Storage\Resource\WordPress\Module\Migrator as TestSubject;
use Xpmock\TestCase;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class MigratorTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\Storage\Resource\WordPress\Module\Migrator';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @param array $methods
     *
     * @return TestSubject|MockObject
     */
    public function createInstance(array $methods = [])
    {
        return $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                    ->disableOriginalConstructor()
                    ->setMethods($methods)
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
    }

    /**
     * Tests the constructor of the test subject to assert whether the given data and dependencies are correctly
     * stored and retrieved.
     *
     * @since [*next-version*]
     */
    public function testConstructor()
    {
        $mysqli   = $this->getMockBuilder('mysqli')->disableOriginalConstructor()->getMock();
        $migDir   = uniqid('dir-');
        $dbVer    = rand(0, 100);
        $tFactory = $this->getMockForAbstractClass('Dhii\Output\TemplateFactoryInterface');
        $pValues  = [
            uniqid('placeholder1-') => uniqid('val1-'),
            uniqid('placeholder2-') => uniqid('val2-'),
        ];

        $subject = new TestSubject($mysqli, $migDir, $dbVer, $tFactory, $pValues);
        $reflect = $this->reflect($subject);

        $this->assertSame($mysqli, $reflect->mysqli, 'The retrieved mysqli instance is incorrect.');
        $this->assertEquals($migDir, $reflect->_getMigrationsDir(), 'The retrieved migrations dir is incorrect.');
        $this->assertEquals($dbVer, $reflect->_getDbVersion(), 'The retrieved database version is incorrect.');
        $this->assertSame(
            $tFactory,
            $reflect->_getTemplateFactory(),
            'The retrieved template factory instance is incorrect.'
        );
        $this->assertEquals(
            $pValues,
            $reflect->_getPlaceholderValues(),
            'The retrieved placeholder values are incorrect.'
        );
    }

    /**
     * Tests the SQL migration file reading functionality to assert whether SQL files are correctly read.
     *
     * @since [*next-version*]
     */
    public function testReadSqlMigrationFile()
    {
        $sql = uniqid('sql-');

        $stream = vfsStream::setup('migrations', null, [
            'file.sql' => $sql,
        ]);

        $subject = $this->createInstance(['_normalizeString']);
        $reflect = $this->reflect($subject);

        $subject->method('_normalizeString')->willReturnArgument(0);

        $actual = $reflect->_readSqlMigrationFile($stream->url() . '/file.sql');

        $this->assertEquals($sql, $actual, 'The read file contents do not match the mocked VFS file contents.');
    }

    /**
     * Tests the SQL migration file reading functionality with a non-existent file to assert whether a runtime
     * exception is thrown.
     *
     * @since [*next-version*]
     */
    public function testReadSqlMigrationFileNoFile()
    {
        $sql = uniqid('sql-');

        $stream = vfsStream::setup('migrations', null, [
            'file.sql' => $sql,
        ]);

        $subject = $this->createInstance(['_normalizeString']);
        $reflect = $this->reflect($subject);

        $subject->method('_normalizeString')->willReturnArgument(0);

        $this->setExpectedException('RuntimeException');

        $reflect->_readSqlMigrationFile($stream->url() . '/non-existent.sql');
    }

    /**
     * Tests the functionality that replaces SQL tokens to assert whether the template factory and the created
     * template are correctly used.
     *
     * @since [*next-version*]
     */
    public function testReplaceSqlTokens()
    {
        // Arguments and return value
        $sql    = uniqid('sql-');
        $values = [
            uniqid('key1-') => uniqid('val1-'),
            uniqid('key2-') => uniqid('val2-'),
        ];
        $render = uniqid('render-');

        // Mock template to render with values and return the rendered result
        $template = $this->getMockForAbstractClass('Dhii\Output\TemplateInterface');
        $template->expects($this->once())
                 ->method('render')
                 ->with($values)
                 ->willReturn($render);
        // Mock the template factory to return the mock template on `make()`.
        $templateFactory = $this->getMockForAbstractClass('Dhii\Output\TemplateFactoryInterface');
        $templateFactory->expects($this->once())
                        ->method('make')
                        ->with([TemplateFactoryInterface::K_TEMPLATE => $sql])
                        ->willReturn($template);

        // Create test subject
        $subject = $this->createInstance([
            '_getTemplateFactory',
        ]);
        // Mock test subject's template factory
        $subject->expects($this->once())
                ->method('_getTemplateFactory')
                ->willReturn($templateFactory);

        $reflect = $this->reflect($subject);
        $actual  = $reflect->_replaceSqlTokens($sql, $values);

        $this->assertEquals($render, $actual, 'Rendered result does not match the expected template render.');
    }

    /**
     * Tests the SQL migration file execution functionality to assert whether the SQL is correctly processed and sent
     * as an SQL query.
     *
     * @since [*next-version*]
     */
    public function testRunMigrationFile()
    {
        $subject = $this->createInstance([
            '_getMysqli',
            '_readSqlMigrationFile',
            '_replaceSqlTokens',
            '_getPlaceholderValues',
        ]);

        // Arguments
        $filepath = uniqid('filepath-');

        // Mock test subject's mysqli instance
        $mysqli = $this->getMockBuilder('mysqli')
                       ->setMethods(['multi_query', 'more_results', 'next_result'])
                       ->disableOriginalConstructor()
                       ->getMock();
        $subject->expects($this->once())
                ->method('_getMysqli')
                ->willReturn($mysqli);

        // Mock reading the migration file
        $sql = uniqid('sql-');
        $subject->expects($this->once())
                ->method('_readSqlMigrationFile')
                ->with($filepath)
                ->willReturn($sql);

        // Mock replacing tokens
        $values      = [
            uniqid('key1-') => uniqid('val1-'),
            uniqid('key2-') => uniqid('val2-'),
        ];
        $replacedSql = uniqid('replaced-sql-');
        $subject->expects($this->once())
                ->method('_getPlaceholderValues')
                ->willReturn($values);
        $subject->expects($this->once())
                ->method('_replaceSqlTokens')
                ->with($sql, $values)
                ->willReturn($replacedSql);

        // Mock successful querying
        $mysqli->expects($this->once())
               ->method('multi_query')
               ->with($replacedSql)
               ->willReturn(true);
        // Mock indication of more results
        $mysqli->expects($this->exactly(2))
               ->method('more_results')
               ->willReturnOnConsecutiveCalls(true, false);
        // Expect next result to be fetched
        $mysqli->expects($this->once())
               ->method('next_result');

        $this->reflect($subject)->_runMigrationFile($filepath);
    }

    /**
     * Tests the SQL migration file execution functionality to assert whether an exception is thrown when the query
     * fails.
     *
     * @since [*next-version*]
     */
    public function testRunMigrationFileError()
    {
        $subject = $this->createInstance([
            '_getMysqli',
            '_readSqlMigrationFile',
            '_replaceSqlTokens',
            '_getPlaceholderValues',
        ]);

        // Arguments
        $filepath = uniqid('filepath-');

        // Mock test subject's mysqli instance
        // We are mocking stdClass and not mysqli because mysqli does not allow writing to its properties
        // Since mysqli does not have an interface, mocking an stdClass with a similar API should work just fine.
        $mysqli = $this->getMockBuilder('stdClass')
                       ->setMethods(['multi_query', 'more_results', 'next_result'])
                       ->disableOriginalConstructor()
                       ->getMock();

        $subject->expects($this->once())
                ->method('_getMysqli')
                ->willReturn($mysqli);

        // Mock reading the migration file
        $sql = uniqid('sql-');
        $subject->expects($this->once())
                ->method('_readSqlMigrationFile')
                ->with($filepath)
                ->willReturn($sql);

        // Mock replacing tokens
        $values      = [
            uniqid('key1-') => uniqid('val1-'),
            uniqid('key2-') => uniqid('val2-'),
        ];
        $replacedSql = uniqid('replaced-sql-');
        $subject->expects($this->once())
                ->method('_getPlaceholderValues')
                ->willReturn($values);
        $subject->expects($this->once())
                ->method('_replaceSqlTokens')
                ->with($sql, $values)
                ->willReturn($replacedSql);

        // Mock successful querying
        $mysqli->expects($this->once())
               ->method('multi_query')
               ->with($replacedSql)
               ->willReturn(false);
        // Mock indication of more results
        $mysqli->expects($this->once())
               ->method('more_results')
               ->willReturnOnConsecutiveCalls(false);
        // Expect next result to be fetched
        $mysqli->expects($this->never())
               ->method('next_result');

        // Mock mysqli error
        $mysqli->error = uniqid('mysqli-error-');

        // Expect exception with this message
        $this->setExpectedException('RuntimeException', $mysqli->error);

        $this->reflect($subject)->_runMigrationFile($filepath);
    }

    /**
     * Tests the migration functionality with a target that is larger than the DB version to assert whether the
     * correct SQL migration files are read and invoked to update the database.
     *
     * @since [*next-version*]
     */
    public function testMigrateUp()
    {
        $subject = $this->createInstance([
            '_normalizeInt',
            '_getDbVersion',
            '_getMigrationsDir',
            '_runMigrationFile',
        ]);

        // Pass-through for int normalization
        $subject->method('_normalizeInt')->willReturnArgument(0);

        $dbVersion     = rand(0, 4);
        $target        = rand(5, 9);
        $count         = $target - $dbVersion;
        $migrationsDir = uniqid('dir-');

        // Mock DB version and migrations dir
        $subject->method('_getDbVersion')->willReturn($dbVersion);
        $subject->method('_getMigrationsDir')->willReturn($migrationsDir);

        // Build expected params for each invocation of the `_runMigrationFile()` method
        $params = [];
        for ($i = $dbVersion + 1; $i < $target; ++$i) {
            $path     = $migrationsDir . DIRECTORY_SEPARATOR . $i . '-' . TestSubject::UP_MIGRATION_FILENAME;
            $params[] = [$path];
        }

        // Expect the migration to be invoked multiple times according to the db version and target version
        $invocation = $subject->expects($this->exactly($count))
                              ->method('_runMigrationFile');
        // We need to pass the expected params array as variadic arguments
        call_user_func_array([$invocation, 'withConsecutive'], $params);

        $subject->migrate($target);
    }

    /**
     * Tests the migration functionality with a target that is smaller than the DB version to assert whether the
     * correct SQL migration files are read and invoked to downgrade the database.
     *
     * @since [*next-version*]
     */
    public function testMigrateDown()
    {
        $subject = $this->createInstance([
            '_normalizeInt',
            '_getDbVersion',
            '_getMigrationsDir',
            '_runMigrationFile',
        ]);

        // Pass-through for int normalization
        $subject->method('_normalizeInt')->willReturnArgument(0);

        $dbVersion     = rand(5, 9);
        $target        = rand(0, 4);
        $count         = $dbVersion - $target;
        $migrationsDir = uniqid('dir-');

        // Mock DB version and migrations dir
        $subject->method('_getDbVersion')->willReturn($dbVersion);
        $subject->method('_getMigrationsDir')->willReturn($migrationsDir);

        // Build expected params for each invocation of the `_runMigrationFile()` method
        $params = [];
        for ($i = $dbVersion; $i > $target; --$i) {
            $path     = $migrationsDir . DIRECTORY_SEPARATOR . $i . '-' . TestSubject::DOWN_MIGRATION_FILENAME;
            $params[] = [$path];
        }

        // Expect the migration to be invoked multiple times according to the db version and target version
        $invocation = $subject->expects($this->exactly($count))
                              ->method('_runMigrationFile');
        // We need to pass the expected params array as variadic arguments
        call_user_func_array([$invocation, 'withConsecutive'], $params);

        $subject->migrate($target);
    }

    /**
     * Tests the migration functionality with a target that is the same as the current DB version to assert whether no
     * SQL migrations are invoked.
     *
     * @since [*next-version*]
     */
    public function testMigrateSameVersion()
    {
        $subject = $this->createInstance([
            '_normalizeInt',
            '_getDbVersion',
            '_getMigrationsDir',
            '_runMigrationFile',
        ]);

        // Pass-through for int normalization
        $subject->method('_normalizeInt')->willReturnArgument(0);

        $dbVersion     = rand(5, 9);
        $target        = $dbVersion;
        $migrationsDir = uniqid('dir-');

        // Mock DB version and migrations dir
        $subject->method('_getDbVersion')->willReturn($dbVersion);
        $subject->method('_getMigrationsDir')->willReturn($migrationsDir);

        // Expect the migration to not be invoked
        $subject->expects($this->never())
                ->method('_runMigrationFile');

        $subject->migrate($target);
    }
}
