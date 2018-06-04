<?php

namespace RebelCode\Storage\Resource\WordPress\Module;

use Dhii\Data\Container\ContainerGetCapableTrait;
use Dhii\Data\Container\CreateContainerExceptionCapableTrait;
use Dhii\Data\Container\CreateNotFoundExceptionCapableTrait;
use Dhii\Data\Container\NormalizeKeyCapableTrait;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Exception\CreateOutOfRangeExceptionCapableTrait;
use Dhii\Factory\Exception\CreateCouldNotMakeExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Output\DefaultPlaceholderValueAwareTrait;
use Dhii\Output\NormalizeTokenDelimiterCapableTrait;
use Dhii\Output\PlaceholderTemplate;
use Dhii\Output\TemplateFactoryInterface;
use Dhii\Output\TemplateInterface;
use Dhii\Output\TokenEndAwareTrait;
use Dhii\Output\TokenStartAwareTrait;
use Dhii\Util\Normalization\NormalizeStringableCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;

/**
 * A factory implementation for creating SQL placeholder templates.
 *
 * @since [*next-version*]
 */
class SqlPlaceholderTemplateFactory implements TemplateFactoryInterface
{
    /* @since [*next-version*] */
    use TokenStartAwareTrait;

    /* @since [*next-version*] */
    use TokenEndAwareTrait;

    /* @since [*next-version*] */
    use DefaultPlaceholderValueAwareTrait;

    /* @since [*next-version*] */
    use ContainerGetCapableTrait;

    /* @since [*next-version*] */
    use NormalizeKeyCapableTrait;

    /* @since [*next-version*] */
    use NormalizeStringCapableTrait;

    /* @since [*next-version*] */
    use NormalizeStringableCapableTrait;

    /* @since [*next-version*] */
    use NormalizeTokenDelimiterCapableTrait;

    /* @since [*next-version*] */
    use CreateContainerExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateNotFoundExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateCouldNotMakeExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateOutOfRangeExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* @since [*next-version*] */
    use StringTranslatingTrait;

    /**
     * Constructor.
     *
     * @param int|float|bool|string|Stringable|null $tokenStart   The delimiter that marks the start of a token.
     * @param int|float|bool|string|Stringable|null $tokenEnd     The delimiter that marks the end of a token.
     * @param int|float|bool|string|Stringable|null $tokenDefault The default value to use for placeholder tokens that
     *                                                            do not map to a value.
     *
     * @throws InvalidArgumentException If the token start, token end or delimiter are invalid.
     */
    public function __construct($tokenStart, $tokenEnd, $tokenDefault)
    {
        $this->_setTokenStart($tokenStart);
        $this->_setTokenEnd($tokenEnd);
        $this->_setDefaultPlaceholderValue($tokenDefault);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @return TemplateInterface The new template.
     */
    public function make($config = null)
    {
        try {
            $template = $this->_containerGet($config, static::K_TEMPLATE);
        } catch (NotFoundExceptionInterface $exception) {
            throw $this->_createCouldNotMakeException(
                $this->__('Config has missing "template" data'), null, null, $this, $config
            );
        }

        return new PlaceHolderTemplate(
            $template,
            $this->_getTokenStart(),
            $this->_getTokenEnd(),
            $this->_getDefaultPlaceholderValue()
        );
    }
}
