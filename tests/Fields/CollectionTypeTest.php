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
            'type' => 'select',
            'data' => [['id' => 1], ['id' => 2], ['id' => 3]],
            'options' => [
                'choices' => ['m' => 'male', 'f' => 'female'],
                'label' => false
            ]
        ];

        $this->fieldExpetations('text', Mockery::any(), null);
        $this->fieldExpetations('collection', Mockery::any());

        $emailsCollection = new CollectionType('emails', 'collection', $this->plainForm, $options);

        $this->assertEquals(3, count($emailsCollection->getChildren()));
        $this->assertInstanceOf('Kris\LaravelFormBuilder\Fields\SelectType', $emailsCollection->prototype());
    }

    /** @test */
    public function it_creates_collection_with_child_form()
    {
        $form = clone $this->plainForm;

        $form->add('name', 'text')
            ->add('gender', 'choice', [
                'choices' => ['m' => 'male', 'f' => 'female']
            ])
             ->add('published', 'checkbox');

        $data = new \Illuminate\Support\Collection([
            ['name' => 'john doe', 'gender' => 'm'],
            ['name' => 'jane doe', 'gender' => 'f']
        ]);

        $options = [
            'type' => 'form',
            'data' => $data,
            'options' => [
                'class' => $form
            ]
        ];

        $this->fieldExpetations('collection', Mockery::any());

        $childFormCollection = new CollectionType('emails', 'collection', $this->plainForm, $options);

        $childFormCollection->render();

        $this->assertEquals(2, count($childFormCollection->getChildren()));
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function it_throws_exception_when_requesting_prototype_while_it_is_disabled()
    {
        $options = [
            'type' => 'text',
            'prototype' => false
        ];

        $this->fieldExpetations('collection', Mockery::any());

        $childFormCollection = new CollectionType('emails', 'collection', $this->plainForm, $options);

        $childFormCollection->render();

        $childFormCollection->prototype();
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function it_throws_exception_when_creating_nonexisting_type()
    {
        $options = [
            'type' => 'nonexisting'
        ];

        $this->fieldExpetations('collection', Mockery::any());

        $childFormCollection = new CollectionType('emails', 'collection', $this->plainForm, $options);
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function it_throws_exception_when_data_is_not_iterable()
    {
        $options = [
            'type' => 'text',
            'data' => 'invalid'
        ];

        $this->fieldExpetations('collection', Mockery::any());

        $childFormCollection = new CollectionType('emails', 'collection', $this->plainForm, $options);
    }
}
