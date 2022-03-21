<?php

namespace Kris\LaravelFormBuilder\Fields;

use Illuminate\Database\Eloquent\Collection;

class RadiosType extends FormField
{
    protected $valueProperty = 'selected';

    protected function getTemplate()
    {
        return 'radios';
    }

    public function getDefaults()
    {
        return [
            'choices' => [],
            'option_attributes' => [],
            'selected' => null
        ];
    }
}
