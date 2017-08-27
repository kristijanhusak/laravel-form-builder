<?php

namespace Kris\LaravelFormBuilder\Events;

use Kris\LaravelFormBuilder\Form;

class AfterFormCreation
{
    /**
     * The form instance.
     *
     * @var Form
     */
    protected $form;

    /**
     * Create a new after form creation instance.
     *
     * @param  Form $form
     * @return void
     */
    public function __construct(Form $form) {
        $this->form = $form;
        $this->filterFields();
    }

    /**
     * Return the event's form.
     *
     * @return Form
     */
    public function getForm() {
        return $this->form;
    }

    /**
     * Init filter field process on Form creation.
     *
     * @return void
     */
    public function filterFields()
    {
        $this->form->filterFields();
    }
}
