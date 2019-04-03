<?php

namespace Kris\LaravelFormBuilder\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class EntityType extends ChoiceType
{

    /**
     * @inheritdoc
     */
    protected function getDefaults()
    {
        $defaults = [
            'class' => null,
            'query_builder' => null,
            'property' => 'name',
            'property_key' => null,
        ];

        return array_merge(parent::getDefaults(), $defaults);
    }

    /**
     * @inheritdoc
     */
    protected function createChildren()
    {
        if ($this->getOption('choices')) {
            return parent::createChildren();
        }

        $entity = $this->getOption('class');
        $queryBuilder = $this->getOption('query_builder');
        $key = $this->getOption('property_key');
        $value = $this->getOption('property');

        if (!$entity || !class_exists($entity)) {
            throw new \InvalidArgumentException(sprintf(
                'Please provide valid "class" option for entity field [%s] in form class [%s]',
                $this->getRealName(),
                get_class($this->parent)
            ));
        }

        $entity = new $entity();

        if ($key === null) {
            $key = $entity->getKeyName();
        }

        if ($queryBuilder instanceof \Closure) {
            $data = $queryBuilder($entity, $this->parent);
        } else {
            $data = $entity;
        }

        if ($value instanceof \Closure) {
            $data = $this->get($data);
        } else {
            $data = $this->pluck($value, $key, $data);
        }

        if ($data instanceof Collection) {
            $data = $data->all();
        }

        if ($value instanceof \Closure) {
            $part = [];
            foreach ($data as $item) {
                $part[$item->__get($key)] = $value($item);
            }

            $data = $part;
        }

        $this->options['choices'] = $data;

        return parent::createChildren();
    }

    /**
     * Pluck data.
     *
     * @param string $value
     * @param string $key
     * @param mixed $data
     *
     * @return mixed
     * */
    protected function pluck($value, $key, $data)
    {
        if (!is_object($data)) {
            return $data;
        }

        if (method_exists($data, 'pluck') || $data instanceof Model) {
            //laravel 5.3.*
            return $data->pluck($value, $key);
        } elseif (method_exists($data, 'lists')) {
            //laravel 5.2.*
            return $data->lists($value, $key);
        }
    }

    protected function get($data)
    {
        if (!is_object($data)) {
            return $data;
        }

        if (method_exists($data, 'get') || $data instanceof Model) {
            //laravel 5.3.*
            return $data->get();
        }
    }
}
