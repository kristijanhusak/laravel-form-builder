<?php

namespace Kris\LaravelFormBuilder\Filters\Collection;

use Kris\LaravelFormBuilder\Filters\FilterInterface;

/**
 * Class StringTrim
 *
 * @package Kris\LaravelFormBuilder\Filters\Collection
 * @author  Djordje Stojiljkovic <djordjestojilljkovic@gmail.com>
 */
class StringTrim implements FilterInterface
{
    /**
     * List of characters provided to the trim() function
     *
     * If null the trim will be invoked with default behaviours (trimming whitespace)
     *
     * @var string|null
     */
    protected $charList;

    /**
     * StringTrim constructor.
     *
     * @param null $options
     */
    public function __construct($options = null)
    {
        if (array_key_exists('charlist', $options)) {
            $this->setCharList($options['charlist']);
        }
    }

    /**
     * @param  $charList
     *
     * @return \Kris\LaravelFormBuilder\Filters\Collection\StringTrim
     */
    public function setCharList($charList)
    {
        $this->charList = $charList;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getCharList()
    {
        return $this->charList;
    }

    /**
     * @param  mixed  $value
     * @param  array  $options
     * @return string
     */
    public function filter($value, $options = [])
    {
        $value = (string) $value;
        if ($this->getCharList() === null) {
            return $this->trimUnicode($value);
        }

        return $this->trimUnicode($value, $this->getCharList());
    }

    /**
     * Unicode aware trim method
     * Fixes a PHP problem
     *
     * @param  string $value
     * @param  string $charList
     *
     * @return string
     */
    protected function trimUnicode($value, $charList = '\\\\s')
    {
        $chars = preg_replace(
            array( '/[\^\-\]\\\]/S', '/\\\{4}/S', '/\//'),
            array( '\\\\\\0', '\\', '\/' ),
            $charList
        );

        $pattern = '^[' . $chars . ']*|[' . $chars . ']*$';
        return preg_replace("/$pattern/sSD", '', $value);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'StringTrim';
    }
}