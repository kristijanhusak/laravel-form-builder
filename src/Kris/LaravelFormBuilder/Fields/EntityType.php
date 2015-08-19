<?php namespace  Kris\LaravelFormBuilder\Fields;

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
            'property_key' => 'id',
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

        $class = $this->getOption('class');
        $queryBuilder = $this->getOption('query_builder');
        $key = $this->getOption('property_key');
        $value = $this->getOption('property');

        if (!$class || !class_exists($class)) {
            throw new \InvalidArgumentException(sprintf(
                'Please provide valid "class" option for entity field [%s] in form class [%s]',
                $this->getRealName(),
                get_class($this->parent)
            ));
        }

        $class = app($class);

        if ($queryBuilder instanceof \Closure) {
            $data = $queryBuilder($class);
            if (is_object($data) && method_exists($data, 'lists')) {
                $data = $data->lists($value, $key);
            }
        } elseif ($class instanceof Collection) {
            $data = $class->lists($value, $key);
        } else {
            $data = [];
        }

        if ($data instanceof Collection) {
            $data = $data->all();
        }


        $this->options['choices'] = $data;

        return parent::createChildren();
    }
}
