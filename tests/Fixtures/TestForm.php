<?php

use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\Traits\ValidatesWhenResolved;

class TestForm extends Form
{
    use ValidatesWhenResolved;

    public function buildForm()
    {
        $this->add('name', 'text', ['rules' => ['required', 'min:3']]);
        $this->add('email', 'text', ['rules' => ['required', 'email', 'min:3']]);
    }

}