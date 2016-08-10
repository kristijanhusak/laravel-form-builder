<?php namespace Kris\LaravelFormBuilder\Events;

use Illuminate\Contracts\Validation\Validator;
use Kris\LaravelFormBuilder\Form;

class AfterFormValidation {

    /**
     * @var $form Form
     */
    protected $form;

    /**
     * @var $validator Validator
     */
    protected $validator;

    /**
     * @var $valid bool
     */
    protected $valid;

    /**
     * Create a new event instance.
     */
    public function __construct(Form $form, Validator $validator, $valid) {
        $this->form = $form;
        $this->validator = $validator;
        $this->valid = $valid;
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

    /**
     * Return wether the validation passed.
     */
    public function isValid() {
        return $this->valid;
    }

}
