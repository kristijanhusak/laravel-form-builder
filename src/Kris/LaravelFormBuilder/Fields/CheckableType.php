<?php namespace  Kris\LaravelFormBuilder\Fields;

class CheckableType extends FormField
{

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
            'default_value' => null,
            'checked' => false
        ];
    }
}
