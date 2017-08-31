<?php

namespace Kris\LaravelFormBuilder\Filters;

use Kris\LaravelFormBuilder\Filters\Exception\InvalidInstanceException;
use Kris\LaravelFormBuilder\Filters\Exception\UnableToResolveFilterException;

/**
 * Class FilterResolver
 *
 * @package Kris\LaravelFormBuilder\Filters
 * @author  Djordje Stojiljkovic <djordjestojilljkovic@gmail.com>
 */
class FilterResolver
{
    /**
     * Method instance used to resolve filter parameter to
     * FilterInterface object from filter Alias or object itself.
     *
     * @param  mixed $filter
     *
     * @return FilterInterface
     *
     * @throws Exception\UnableToResolveFilterException
     * @throws Exception\InvalidInstanceException
     */
    public static function instance($filter)
    {
        if (!is_string($filter)) {
            return self::validateFilterInstance($filter);
        }

        if (class_exists($filter)) {
            return self::validateFilterInstance(new $filter());
        }

        if ($filter = FilterResolver::resolveFromCollection($filter)) {
            return self::validateFilterInstance($filter);
        }

        $ex = new UnableToResolveFilterException();
        throw $ex;
    }

    /**
     * @param $filter
     *
     * @throws \Exception
     *
     * @return mixed
     */
    private static function validateFilterInstance($filter)
    {
        if (!$filter instanceof FilterInterface) {
            $ex = new InvalidInstanceException();
            throw $ex;
        }

        return $filter;
    }

    /**
     * @param  $filterName
     *
     * @return FilterInterface|null
     */
    public static function resolveFromCollection($filterName)
    {
        $filterClass = self::getCollectionNamespace() . $filterName;
        if (class_exists($filterClass)) {
            return new $filterClass;
        }
    }

    /**
     * @return string
     */
    public static function getCollectionNamespace()
    {
        return "\\Kris\\LaravelFormBuilder\\Filters\\Collection\\";
    }
}