<?php

namespace Kris\LaravelFormBuilder\Events;

use Illuminate\Contracts\Validation\Validator;
use Kris\LaravelFormBuilder\Form;

class BeforeFormValidation
{
    /**
     * The form instance.
     *
     * @var Form
     */
    protected $form;

    /**
     * The validator instance.
     *
     * @var Validator
     */
    protected $validator;

    /**
     * Create a new event instance.
     *
     * @param  Form  $form
     * @param  Validator  $validator
     * @return void
     */
    public function __construct(Form $form, Validator $validator)
    {
        $this->form = $form;
        $this->validator = $validator;
    }

    /**
     * Get the Form instance of this event.
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Get the Validator instance of this event.
     *
     * @return Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }
}
