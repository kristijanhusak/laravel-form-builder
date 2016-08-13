<?php namespace  Kris\LaravelFormBuilder\Fields;

class ButtonType extends FormField
{
    /**
     * @inheritdoc
     */
    protected function getTemplate()
    {
        return 'button';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaults()
    {
        return [
            'wrapper' => false,
            'attr' => ['type' => $this->type]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getAllAttributes()
    {
        // Don't collect input for buttons.
        return [];
    }
}
