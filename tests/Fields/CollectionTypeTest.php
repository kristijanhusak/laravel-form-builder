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
        $this->request->shouldReceive('old')->andReturn([['id' => 1]]);

        $emailsCollection = new CollectionType('emails', 'collection', $this->plainForm, $options);

        $this->assertEquals(3, count($emailsCollection->getChildren()));
        $this->assertInstanceOf('Kris\LaravelFormBuilder\Fields\SelectType', $emailsCollection->prototype());
    }

    /** @test */
    public function it_uses_old_input_if_available()
    {
        $options = [
            'type' => 'select',
            'data' => [['id' => 4], ['id' => 5], ['id' => 6]],
            'options' => [
                'choices' => ['m' => 'male', 'f' => 'female'],
                'label' => false
            ]
        ];

        $this->fieldExpetations('text', Mockery::any(), null);
        $this->fieldExpetations('collection', Mockery::any());
        $this->request->shouldReceive('old')->andReturn([['id' => 1], ['id' => 2]]);

        $emailsCollection = new CollectionType('emails', 'collection', $this->plainForm, $options);

        $this->assertEquals(2, count($emailsCollection->getChildren()));
        $this->assertInstanceOf('Kris\LaravelFormBuilder\Fields\SelectType', $emailsCollection->prototype());
    }

    /** @test */
    public function it_creates_collection_with_child_form()
    {
        $form = clone $this->plainForm;
        $this->request->shouldReceive('old');

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
     */
    public function it_throws_exception_when_requesting_prototype_while_it_is_disabled()
    {
        $options = [
            'type' => 'text',
            'prototype' => false
        ];

        $this->fieldExpetations('collection', Mockery::any());
        $this->request->shouldReceive('old');

        $childFormCollection = new CollectionType('emails', 'collection', $this->plainForm, $options);

        $childFormCollection->render();

        try {
            $childFormCollection->prototype();
        } catch (\Exception $e) {
            return;
        }

        $this->fail('Exception was not thrown when asked for prototype when disabled.');
    }

    /**
     * @test
     */
    public function it_throws_exception_when_creating_nonexisting_type()
    {
        $options = [
            'type' => 'nonexisting'
        ];

        $this->request->shouldReceive('old');
        $this->fieldExpetations('collection', Mockery::any());

        try {
            $childFormCollection = new CollectionType('emails', 'collection', $this->plainForm, $options);
        } catch (\Exception $e) {
            return;
        }

        $this->fail('Exception was not thrown when creating non existing type in collection.');
    }

    /**
     * @test
     */
    public function it_throws_exception_when_data_is_not_iterable()
    {
        $options = [
            'type' => 'text',
            'data' => 'invalid'
        ];

        $this->request->shouldReceive('old');
        $this->fieldExpetations('collection', Mockery::any());

        try {
            $childFormCollection = new CollectionType('emails', 'collection', $this->plainForm, $options);
        } catch (\Exception $e) {
            return;
        }

        $this->fail('Exception was not thrown for non iterable collection data');
    }
}
