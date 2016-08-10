<?php namespace Kris\LaravelFormBuilder\Events;

use Kris\LaravelFormBuilder\Fields\FormField;
use Kris\LaravelFormBuilder\Form;

class AfterFieldCreation {

    /**
     * @var $form Form
     */
    protected $form;

    /**
     * @var $field FormField
     */
    protected $field;

    /**
     * Create a new event instance.
     */
    public function __construct(Form $form, FormField $field) {
        $this->form = $form;
        $this->field = $field;
    }

    /**
     * Return the event's form.
     */
    public function getForm() {
        return $this->form;
    }

    /**
     * Return the event's field.
     */
    public function getField() {
        return $this->field;
    }

}
