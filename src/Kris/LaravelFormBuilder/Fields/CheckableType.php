<?php

namespace  Kris\LaravelFormBuilder\Fields;

class CheckableType extends FormField
{

    const DEFAULT_VALUE = 1;

    /**
     * @inheritdoc
     */
    protected $valueProperty = 'checked';

    /**
     * @inheritdoc
     */
    protected function getTemplate()
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function getDefaults()
    {
        return [
            'attr' => ['class' => null, 'id' => $this->getName()],
            'value' => self::DEFAULT_VALUE,
            'checked' => null
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function isValidValue($value)
    {
        return $value !== null;
    }
}
