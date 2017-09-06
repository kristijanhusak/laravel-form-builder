<?php

namespace  Kris\LaravelFormBuilder\Fields;

class CheckableType extends FormField
{

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
            'value' => 1,
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
