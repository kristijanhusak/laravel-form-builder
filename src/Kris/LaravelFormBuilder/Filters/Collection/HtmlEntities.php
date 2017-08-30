<?php

namespace Kris\LaravelFormBuilder\Filters\Collection;

use Kris\LaravelFormBuilder\Filters\FilterInterface;

/**
 * Class BaseName
 *
 * @package Kris\LaravelFormBuilder\Filters\Collection
 * @author  Djordje Stojiljkovic <djordjestojilljkovic@gmail.com>
 */
class HtmlEntities implements FilterInterface
{

    /**
     * Second arg of htmlentities function.
     *
     * @var integer
     */
    protected $quoteStyle;

    /**
     * Third arg of htmlentities function.
     *
     * @var string
     */
    protected $encoding;

    /**
     * Fourth arg of htmlentities function.
     *
     * @var string
     */
    protected $doubleQuote;

    /**
     * HtmlEntities constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if (!isset($options['quotestyle'])) {
            $options['quotestyle'] = ENT_COMPAT;
        }

        if (!isset($options['encoding'])) {
            $options['encoding'] = 'UTF-8';
        }

        if (isset($options['charset'])) {
            $options['encoding'] = $options['charset'];
        }

        if (!isset($options['doublequote'])) {
            $options['doublequote'] = true;
        }

        $this->setQuoteStyle($options['quotestyle']);
        $this->setEncoding($options['encoding']);
        $this->setDoubleQuote($options['doublequote']);
    }

    /**
     * @return integer
     */
    public function getQuoteStyle()
    {
        return $this->quoteStyle;
    }

    /**
     * @param integer $style
     *
     * @return \Kris\LaravelFormBuilder\Filters\Collection\HtmlEntities
     */
    public function setQuoteStyle($style)
    {
        $this->quoteStyle = $style;
        return $this;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @param  string $encoding
     *
     * @return \Kris\LaravelFormBuilder\Filters\Collection\HtmlEntities
     */
    public function setEncoding($encoding)
    {
        $this->encoding = (string) $encoding;
        return $this;
    }

    /**
     * Returns the charSet property
     *
     * Proxies to {@link getEncoding()}
     *
     * @return string
     */
    public function getCharSet()
    {
        return $this->getEncoding();
    }

    /**
     * Sets the charSet property.
     *
     * Proxies to {@link setEncoding()}.
     *
     * @param  string $charSet
     *
     * @return \Kris\LaravelFormBuilder\Filters\Collection\HtmlEntities
     */
    public function setCharSet($charSet)
    {
        return $this->setEncoding($charSet);
    }

    /**
     * Returns the doubleQuote property.
     *
     * @return boolean
     */
    public function getDoubleQuote()
    {
        return $this->doubleQuote;
    }

    /**
     * Sets the doubleQuote property.
     *
     * @param  boolean $doubleQuote
     *
     * @return \Kris\LaravelFormBuilder\Filters\Collection\HtmlEntities
     */
    public function setDoubleQuote($doubleQuote)
    {
        $this->doubleQuote = (boolean) $doubleQuote;
        return $this;
    }

    /**
     * @param  string $value
     * @param  array $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function filter($value, $options = [])
    {
        $value    = (string) $value;
        $filtered = htmlentities(
            $value,
            $this->getQuoteStyle(),
            $this->getEncoding(),
            $this->getDoubleQuote()
        );

        if (strlen($value) && !strlen($filtered)) {
            if (!function_exists('iconv')) {
                $ex = new \Exception('Encoding mismatch has resulted in htmlentities errors.');
                throw $ex;
            }

            $enc      = $this->getEncoding();
            $value    = iconv('', $enc . '//IGNORE', $value);
            $filtered = htmlentities($value, $this->getQuoteStyle(), $enc, $this->getDoubleQuote());

            if (!strlen($filtered)) {
                $ex = new \Exception('Encoding mismatch has resulted in htmlentities errors.');
                throw $ex;
            }
        }

        return $filtered;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'HtmlEntities';
    }
}