<?php namespace Kris\LaravelFormBuilder\Events;

use Kris\LaravelFormBuilder\Form;

class AfterFormCreation {

    /**
     * @var $form Form
     */
    protected $form;

    /**
     * Create a new event instance.
     */
    public function __construct(Form $form) {
        $this->form = $form;
    }

    /**
     * Return the event's form.
     */
    public function getForm() {
        return $this->form;
    }

}
