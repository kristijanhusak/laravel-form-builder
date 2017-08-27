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
     * @param  mixed  $value
     * @param  array  $options
     * @return string
     */
    public function filter($value, $options = [])
    {
        return trim((string) $value);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'StringTrim';
    }
}