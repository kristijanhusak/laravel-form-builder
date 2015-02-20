<?php

use Kris\LaravelFormBuilder\Fields\ChoiceType;
use Kris\LaravelFormBuilder\Fields\CollectionType;
use Kris\LaravelFormBuilder\Fields\SelectType;
use Kris\LaravelFormBuilder\Form;

class CollectionTypeTest extends FormBuilderTestCase
{
    /** @test */
    public function it_creates_collection()
    {
        $options = [
            'type' => 'email',
            'options' => [
                'label' => false
            ]
        ];

        $prototype = '<input class="form-control" id="emails[0][__NAME__]" name="emails[0][__NAME__]" type="email">';

        $this->fieldExpetations('text', Mockery::any(), null, $prototype);
        $this->fieldExpetations('child_form', Mockery::any());

        $emailsCollection = new CollectionType('emails', 'collection', $this->plainForm, $options);

        $emailsCollection->render();

        $this->assertEquals(1, count($emailsCollection->getChildren()));
        $this->assertEquals($prototype, $emailsCollection->getPrototype());
    }

    /** @test */
    public function it_creates_collection_with_child_form()
    {
        $form = clone $this->plainForm;

        $form->add('name', 'text')
             ->add('published', 'checkbox');

        $options = [
            'type' => 'form',
            'class' => $form
        ];

        $this->fieldExpetations('child_form', Mockery::any());

        $childFormCollection = new CollectionType('emails', 'collection', $this->plainForm, $options);

        $childFormCollection->render();

        $this->assertEquals(1, count($childFormCollection->getChildren()));
    }
}
