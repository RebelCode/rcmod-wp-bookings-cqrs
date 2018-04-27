<?php

namespace RebelCode\Storage\Resource\WordPress\Wpdb;

use Dhii\Expression\LogicalExpressionInterface;
use Dhii\Expression\TermInterface;
use Dhii\Output\TemplateInterface;
use Dhii\Storage\Resource\Sql\EntityFieldInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use stdClass;
use Traversable;
use wpdb;

/**
 * A specialized resource model for retrieving booking statuses.
 *
 * @since [*next-version*]
 */
class BookingStatusWpdbSelectResourceModel extends AbstractBaseWpdbSelectResourceModel
{
    /**
     * The columns to group by.
     *
     * @since [*next-version*]
     *
     * @var EntityFieldInterface|TermInterface|Traversable
     */
    protected $groupColumns;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param wpdb                         $wpdb               The WPDB instance to use to prepare and execute queries.
     * @param TemplateInterface            $expressionTemplate The template for rendering SQL expressions.
     * @param array|stdClass|Traversable   $tables             The tables names (values) mapping to their aliases (keys)
     *                                                         or null for no aliasing.
     * @param string[]|Stringable[]        $fieldColumnMap     A map of field names to table column names.
     * @param LogicalExpressionInterface[] $joins              A list of JOIN expressions to use in SELECT queries.
     */
    public function __construct(
        wpdb $wpdb,
        TemplateInterface $expressionTemplate,
        $tables,
        $fieldColumnMap,
        $joins = []
    ) {
        $this->_init($wpdb, $expressionTemplate, $tables, $fieldColumnMap, $joins);
    }

    /**
     * Retrieves the list of columns to group by.
     *
     * @since [*next-version*]
     *
     * @return TermInterface|EntityFieldInterface|Traversable The list of columns.
     */
    protected function _getGroupColumns()
    {
        return $this->groupColumns;
    }

    /**
     * Sets the list of columns to group by.
     *
     * @since [*next-version*]
     *
     * @param TermInterface|EntityFieldInterface|Traversable $groupColumns The list of columns.
     */
    protected function _setGroupColumns($groupColumns)
    {
        $this->groupColumns = $groupColumns;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _buildSqlWhereClause(
        LogicalExpressionInterface $condition = null,
        array $valueHashMap = []
    ) {
        if ($condition === null) {
            return '';
        }

        $rendered = $this->_renderSqlCondition($condition, $valueHashMap);
        $rendered = $this->_normalizeString($rendered);

        $groupColumns = $this->_buildSqlColumnList($this->_getGroupColumns());

        return sprintf('GROUP BY %1$s HAVING %1%s', $groupColumns, $rendered);
    }
}
