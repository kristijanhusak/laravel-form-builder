<?php namespace Kris\LaravelFormBuilder\Events;

use Illuminate\Contracts\Validation\Validator;
use Kris\LaravelFormBuilder\Form;

class BeforeFormValidation {

    /**
     * @var $form Form
     */
    protected $form;

    /**
     * @var $validator Validator
     */
    protected $validator;

    /**
     * Create a new event instance.
     */
    public function __construct(Form $form, Validator $validator) {
        $this->form = $form;
        $this->validator = $validator;
    }

    /**
     * Return the event's form.
     */
    public function getForm() {
        return $this->form;
    }

    /**
     * Return the event's validator.
     */
    public function getValidator() {
        return $this->validator;
    }

}
