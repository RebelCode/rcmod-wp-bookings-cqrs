<?php

namespace RebelCode\Storage\Resource\WordPress\Module;

use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Exception\CreateOutOfRangeExceptionCapableTrait;
use Dhii\Exception\CreateRuntimeExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Util\Normalization\NormalizeIntCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;
use mysqli;
use RuntimeException;

/**
 * Performs database migrations through WPDB.
 *
 * @since [*next-version*]
 */
class Migrator
{
    /* @since [*next-version*] */
    use NormalizeIntCapableTrait;

    /* @since [*next-version*] */
    use NormalizeStringCapableTrait;

    /* @since [*next-version*] */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateOutOfRangeExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateRuntimeExceptionCapableTrait;

    /* @since [*next-version*] */
    use StringTranslatingTrait;

    /**
     * The filename for "up" migrations.
     *
     * @since [*next-version*]
     */
    const UP_MIGRATION_FILENAME = 'up.sql';

    /**
     * The filename for "down" migrations.
     *
     * @since [*next-version*]
     */
    const DOWN_MIGRATION_FILENAME = 'up.sql';

    /**
     * The migrations directory path.
     *
     * @since [*next-version*]
     *
     * @var string|Stringable
     */
    protected $migrationsDir;

    /**
     * The current database version.
     *
     * @since [*next-version*]
     *
     * @var int|string|Stringable
     */
    protected $dbVersion;

    /**
     * The mysqli handle.
     *
     * @since [*next-version*]
     *
     * @var mysqli
     */
    protected $mysqli;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param mysqli                $mysqli        The mysqli handle.
     * @param string|Stringable     $migrationsDir The migrations directory path.
     * @param int|string|Stringable $dbVersion     The current database version.
     */
    public function __construct($mysqli, $migrationsDir, $dbVersion)
    {
        $this->mysqli = $mysqli;
        $this->_setMigrationsDir($migrationsDir);
        $this->_setDbVersion($dbVersion);
    }

    /**
     * Retrieves the mysqli handle.
     *
     * @since [*next-version*]
     *
     * @return mysqli The mysqli handle.
     */
    protected function _getMysqli()
    {
        return $this->mysqli;
    }

    /**
     * Sets the mysqli handle.
     *
     * @since [*next-version*]
     *
     * @param mysqli $mysqli The mysqli handle.
     */
    protected function _setMysqli($mysqli)
    {
        if (!($mysqli instanceof mysqli)) {
            throw $this->_createInvalidArgumentException(
                $this->__('Argument is not a mysqli handle'), null, null, $mysqli
            );
        }

        $this->mysqli = $mysqli;
    }

    /**
     * Retrieves the migrations directory path.
     *
     * @since [*next-version*]
     *
     * @return string|Stringable The migrations directory path.
     */
    protected function _getMigrationsDir()
    {
        return $this->migrationsDir;
    }

    /**
     * Sets the migrations directory path.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable $migrationsDir The migrations directory path.
     */
    protected function _setMigrationsDir($migrationsDir)
    {
        $this->migrationsDir = $this->_normalizeString($migrationsDir);
    }

    /**
     * Retrieves the current database version.
     *
     * @since [*next-version*]
     *
     * @return int|string|Stringable The current database version.
     */
    protected function _getDbVersion()
    {
        return $this->dbVersion;
    }

    /**
     * Sets the current database version.
     *
     * @since [*next-version*]
     *
     * @param int|string |Stringable $dbVersion The current database version.
     */
    protected function _setDbVersion($dbVersion)
    {
        $this->dbVersion = $this->_normalizeInt($dbVersion);
    }

    /**
     * Performs database migration.
     *
     * @since [*next-version*]
     *
     * @param int|string|Stringable $target The migration target - can be a version, state, preset, etc.
     *
     * @throws InvalidArgumentException If the given target is invalid.
     * @throws RuntimeException If failed to migrate to the given target.
     */
    public function migrate($target)
    {
        $target     = $this->_normalizeInt($target);
        $current    = $this->_getDbVersion();
        $difference = $target - $current;

        // No migration needed
        if ($difference === 0) {
            return;
        }

        // Maximise the values to 1, since DB version 0 is not a real version and represents an uninitialized DB state
        $current = max(1, $current);
        $target  = max(1, $target);
        // Get the list of migration versions to run
        $migrations = range($current, $target);
        // Determine the file names to look for, depending on migration direction
        $filename = ($difference < 0)
            ? static::DOWN_MIGRATION_FILENAME
            : static::UP_MIGRATION_FILENAME;
        // The root migrations directory
        $directory = $this->_getMigrationsDir();

        foreach ($migrations as $_version) {
            $_path = implode(DIRECTORY_SEPARATOR, [$directory, sprintf('%1$s-%2$s', $_version, $filename)]);

            $this->_runMigrationFile($_path);
        }
    }

    /**
     * Runs the migration at a given file path.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable $filePath The path to the migration file.
     *
     * @throws RuntimeException If failed to read the migration file or if the migration failed to execute.
     */
    protected function _runMigrationFile($filePath)
    {
        $mysqli   = $this->_getMysqli();
        $sqlQuery = $this->_readSqlMigrationFile($filePath);
        $sqlQuery = $this->_replaceSqlTokens($sqlQuery);
        $success  = $mysqli->multi_query($sqlQuery);

        if (!$success) {
            throw $this->_createRuntimeException($mysqli->error);
        }
    }

    /**
     * Replaces placeholder tokens in the SQL.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable $sql The SQL.
     *
     * @return string|Stringable The SQL with the replaced placeholder tokens.
     */
    protected function _replaceSqlTokens($sql)
    {
        return str_replace('${table_prefix}', 'wp_eddbk_', $sql);
    }

    /**
     * Reads the SQL from a migration file.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable $filePath The path to the migration file.
     *
     * @return string|Stringable The read SQL.
     *
     * @throws RuntimeException If failed to read the migration file.
     */
    protected function _readSqlMigrationFile($filePath)
    {
        $filePath = $this->_normalizeString($filePath);

        if (is_file($filePath) && is_readable($filePath)) {
            return file_get_contents($filePath);
        }

        throw $this->_createRuntimeException($this->__('Cannot read migration file "%s"', [$filePath]), null, null);
    }
}
