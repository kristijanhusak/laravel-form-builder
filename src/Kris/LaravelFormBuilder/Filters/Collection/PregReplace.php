<?php

namespace Kris\LaravelFormBuilder\Filters\Collection;

use Kris\LaravelFormBuilder\Filters\FilterInterface;

/**
 * Class PregReplace
 *
 * @package Kris\LaravelFormBuilder\Filters\Collection
 * @author  Djordje Stojiljkovic <djordjestojilljkovic@gmail.com>
 */
class PregReplace implements FilterInterface
{
    /**
     * Pattern to match
     *
     * @var mixed $pattern
     */
    protected $pattern = null;

    /**
     * Replacement against matches.
     *
     * @var mixed $replacement
     */
    protected $replacement = '';

    /**
     * PregReplace constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        if (array_key_exists('pattern', $options)) {
            $this->setPattern($options['pattern']);
        }

        if (array_key_exists('replace', $options)) {
            $this->setReplacement($options['replace']);
        }
    }

    /**
     * Set the match pattern for the regex being called within filter().
     *
     * @param mixed $pattern - first arg of preg_replace
     *
     * @return \Kris\LaravelFormBuilder\Filters\Collection\PregReplace
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * Get currently set match pattern.
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Set the Replacement pattern/string for the preg_replace called in filter.
     *
     * @param mixed $replacement - same as the second argument of preg_replace
     *
     * @return \Kris\LaravelFormBuilder\Filters\Collection\PregReplace
     */
    public function setReplacement($replacement)
    {
        $this->replacement = $replacement;
        return $this;
    }

    /**
     * Get currently set replacement value.
     *
     * @return string
     */
    public function getReplacement()
    {
        return $this->replacement;
    }

    /**
     * @param  mixed $value
     * @param  array $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function filter($value, $options = [])
    {
        if ($this->getPattern() == null) {
            $ex = new \Exception(get_class($this) . ' does not have a valid MatchPattern set.');
            throw $ex;
        }

        return preg_replace($this->getPattern(), $this->getReplacement(), $value);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'PregReplace';
    }
}