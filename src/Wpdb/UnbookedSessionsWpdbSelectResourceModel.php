<?php

namespace RebelCode\Storage\Resource\WordPress\Wpdb;

use Dhii\Expression\ExpressionInterface;
use Dhii\Expression\LogicalExpressionInterface;
use Dhii\Output\TemplateInterface;
use Dhii\Storage\Resource\Sql\EntityFieldInterface;
use stdClass;
use Traversable;
use wpdb;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * The SELECT resource model for retrieving unbooked sessions.
 *
 * @since [*next-version*]
 */
class UnbookedSessionsWpdbSelectResourceModel extends AbstractBaseWpdbSelectResourceModel
{
    /**
     * The internal expression to be added to any consumer-given condition.
     *
     * @since [*next-version*]
     *
     * @var ExpressionInterface
     */
    protected $internalCondition;

    /**
     * The fields to group by.
     *
     * @since [*next-version*]
     *
     * @var string[]|Stringable[]|EntityFieldInterface[]|stdClass|Traversable
     */
    protected $grouping;

    /**
     * Description
     *
     * @since [*next-version*]
     *
     * @var object
     */
    protected $exprBuilder;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param wpdb                                                              $wpdb           The WPDB instance to
     *                                                                                          use to prepare and
     *                                                                                          execute queries.
     * @param TemplateInterface                                                 $template       The template for
     *                                                                                          rendering SQL
     *                                                                                          expressions.
     * @param array|stdClass|Traversable                                        $tables         The tables names
     *                                                                                          (values) mapping to
     *                                                                                          their aliases (keys)
     *                                                                                          or null for no aliasing.
     * @param string[]|Stringable[]                                             $fieldColumnMap A map of field names
     *                                                                                          to table column names.
     * @param LogicalExpressionInterface[]                                      $joins          A list of JOIN
     *                                                                                          expressions to use in
     *                                                                                          SELECT queries.
     * @param ExpressionInterface                                               $condition      The internal
     *                                                                                          condition to use.
     * @param string[]|Stringable[]|EntityFieldInterface[]|stdClass|Traversable $grouping       The fields to group by.
     * @param object                                                            $exprBuilder    The expression builder.
     */
    public function __construct(
        wpdb $wpdb,
        TemplateInterface $template,
        $tables,
        $fieldColumnMap,
        $joins,
        $condition,
        $grouping,
        $exprBuilder
    ) {
        $this->_init($wpdb, $template, $tables, $fieldColumnMap, $joins);
        $this->internalCondition = $condition;
        $this->grouping          = $grouping;
        $this->exprBuilder       = $exprBuilder;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function select(
        LogicalExpressionInterface $condition = null,
        $ordering = null,
        $limit = null,
        $offset = null
    ) {
        $condition = $this->exprBuilder->and($this->internalCondition, $condition);

        return parent::select($condition, $ordering, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getSqlJoinType(ExpressionInterface $expression)
    {
        return 'LEFT';
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getSqlSelectGrouping()
    {
        return $this->grouping;
    }
}
