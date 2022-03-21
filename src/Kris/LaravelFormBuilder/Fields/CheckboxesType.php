<?php

namespace Kris\LaravelFormBuilder\Fields;

use Illuminate\Database\Eloquent\Collection;

class CheckboxesType extends RadiosType
{
    protected function getTemplate()
    {
        return 'checkboxes';
    }

    public function setValue($value)
    {
        if ($value instanceof Collection) {
            $value = $value->modelKeys();
        }

        parent::setValue($value);
    }
}
