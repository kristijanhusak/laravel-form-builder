<?php

namespace Kris\LaravelFormBuilder\Filters\Collection;

use Kris\LaravelFormBuilder\Filters\FilterInterface;

/**
 * Class BaseName
 *
 * @package Kris\LaravelFormBuilder\Filters\Collection
 * @author  Djordje Stojiljkovic <djordjestojilljkovic@gmail.com>
 */
class BaseName implements FilterInterface
{
    /**
     * @param string $value
     * @param array $options
     *
     * @return string
     */
    public function filter($value, $options = [])
    {
        $value = (string) $value;
        return basename($value);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'BaseName';
    }
}