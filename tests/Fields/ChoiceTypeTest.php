<?php

use Kris\LaravelFormBuilder\Fields\ChoiceType;

class ChoiceTypeTest extends FormBuilderTestCase
{
    /** @test */
    public function it_creates_choice_as_select()
    {
        $options = [
            'attr' => ['class' => 'choice-class'],
            'choices' => ['yes' => 'Yes', 'no' => 'No'],
            'selected' => 'yes'
        ];

        $choice = new ChoiceType('some_choice', 'choice', $this->plainForm, $options);

        $choice->render();

        $this->assertEquals(1, count($choice->getChildren()));

        $this->assertEquals('yes', $choice->getOption('selected'));
    }

    /** @test */
    public function it_creates_choice_as_checkbox_list()
    {
        $options = [
            'attr' => ['class' => 'choice-class-something'],
            'choices' => [1 => 'monday', 2 => 'tuesday'],
            'selected' => 'tuesday',
            'multiple' => true,
            'expanded' => true
        ];

        $choice = new ChoiceType('some_choice', 'choice', $this->plainForm, $options);

        $choice->render();

        $this->assertEquals(2, count($choice->getChildren()));

        $this->assertEquals('tuesday', $choice->getOption('selected'));

        $this->assertContainsOnlyInstancesOf('Kris\LaravelFormBuilder\Fields\CheckableType', $choice->getChildren());
    }

    /** @test */
    public function it_creates_choice_as_radio_buttons()
    {
        $options = [
            'attr' => ['class' => 'choice-class-something'],
            'choices' => [1 => 'yes', 2 => 'no'],
            'selected' => 'no',
            'expanded' => true
        ];

        $choice = new ChoiceType('some_choice', 'choice', $this->plainForm, $options);

        $choice->render();

        $this->assertEquals(2, count($choice->getChildren()));

        $this->assertInstanceOf(
            'Kris\LaravelFormBuilder\Fields\CheckableType',
            $choice->getChild(1)
        );

        $this->assertEquals('no', $choice->getOption('selected'));

        $this->assertContainsOnlyInstancesOf('Kris\LaravelFormBuilder\Fields\CheckableType', $choice->getChildren());
    }

    /** @test */
    public function it_sets_proper_name_for_multiple()
    {
        $this->plainForm->add('users', 'select', [
            'choices' => [1 => 'user1', 2 => 'user2'],
            'attr' => [
                'multiple' => 'multple'
            ]
        ]);

        $this->plainForm->renderForm();

        $this->assertEquals('users[]', $this->plainForm->users->getName());
    }

    /** @test */
    public function it_can_override_choices()
    {
        $options = [
            'choices' => ['yes' => 'Yes', 'no' => 'No'],
            'selected' => 'test',
            'data_override' => function ($choices, $field) {
                $choices['test'] = 'test';

                return $choices;
            }
        ];

        $choice = new ChoiceType('some_choice', 'choice', $this->plainForm, $options);

        $choice->render();

        $this->assertEquals(3, count($choice->getOption('choices')));

        $this->assertEquals('test', $choice->getOption('selected'));
    }

    /** @test */
    public function it_disables_select()
    {
        $options = [
            'attr' => ['class' => 'choice-class'],
            'choices' => ['yes' => 'Yes', 'no' => 'No'],
            'selected' => 'yes'
        ];
        $field = new ChoiceType('some_choice', 'choice', $this->plainForm, $options);
        $children = $field->getChildren();
        
        // there shall be no 'disabled' attribute set beforehand
        $this->assertArrayNotHasKey('disabled', $field->getOption('attr'));
        foreach ($children as $child) {
            $this->assertArrayNotHasKey('disabled', $child->getOption('attr'));
        }

        $field->disable();

        // there shall be 'disabled' attribute set after
        $this->assertArrayHasKey('disabled', $field->getOption('attr'));
        $this->assertEquals('disabled', $field->getOption('attr')['disabled']);
        foreach ($children as $child) {
            $this->assertArrayHasKey('disabled', $child->getOption('attr'));
            $this->assertEquals('disabled', $child->getOption('attr')['disabled']);
        }
    }

    /** @test */
    public function it_disables_checkbox_list()
    {
        $options = [
            'attr' => ['class' => 'choice-class-something'],
            'choices' => [1 => 'monday', 2 => 'tuesday'],
            'selected' => 'tuesday',
            'multiple' => true,
            'expanded' => true
        ];

        $field = new ChoiceType('some_choice', 'choice', $this->plainForm, $options);
        $children = $field->getChildren();

        // there shall be no 'disabled' attribute set beforehand
        $this->assertArrayNotHasKey('disabled', $field->getOption('attr'));
        foreach ($children as $child) {
            $this->assertArrayNotHasKey('disabled', $child->getOption('attr'));
        }

        $field->disable();

        // there shall be 'disabled' attribute set after
        $this->assertArrayHasKey('disabled', $field->getOption('attr'));
        $this->assertEquals('disabled', $field->getOption('attr')['disabled']);
        foreach ($children as $child) {
            $this->assertArrayHasKey('disabled', $child->getOption('attr'));
            $this->assertEquals('disabled', $child->getOption('attr')['disabled']);
        }
    }
    
    /** @test */
    public function it_disables_radios_list()
    {
        $options = [
            'attr' => ['class' => 'choice-class-something'],
            'choices' => [1 => 'yes', 2 => 'no'],
            'selected' => 'no',
            'expanded' => true
        ];

        $field = new ChoiceType('some_choice', 'choice', $this->plainForm, $options);
        $children = $field->getChildren();

        // there shall be no 'disabled' attribute set beforehand
        $this->assertArrayNotHasKey('disabled', $field->getOption('attr'));
        foreach ($children as $child) {
            $this->assertArrayNotHasKey('disabled', $child->getOption('attr'));
        }

        $field->disable();

        // there shall be 'disabled' attribute set after
        $this->assertArrayHasKey('disabled', $field->getOption('attr'));
        $this->assertEquals('disabled', $field->getOption('attr')['disabled']);
        foreach ($children as $child) {
            $this->assertArrayHasKey('disabled', $child->getOption('attr'));
            $this->assertEquals('disabled', $child->getOption('attr')['disabled']);
        }
    }
    
    /** @test */
    public function it_enables_select()
    {
        $options = [
            'attr' => ['class' => 'choice-class', 'disabled' => 'disabled'],
            'choices' => ['yes' => 'Yes', 'no' => 'No'],
            'selected' => 'yes'
        ];
        $field = new ChoiceType('some_choice', 'choice', $this->plainForm, $options);
        $children = $field->getChildren();

        // there shall be 'disabled' attribute set beforehand
        $this->assertArrayHasKey('disabled', $field->getOption('attr'));
        $this->assertEquals('disabled', $field->getOption('attr')['disabled']);
        foreach ($children as $child) {
            $this->assertArrayHasKey('disabled', $child->getOption('attr'));
            $this->assertEquals('disabled', $child->getOption('attr')['disabled']);
        }


        $field->enable();

        // there shall be no 'disabled' attribute set after
        $this->assertArrayNotHasKey('disabled', $field->getOption('attr'));
        foreach ($children as $child) {
            $this->assertArrayNotHasKey('disabled', $child->getOption('attr'));
        }
    }

    /** @test */
    public function it_enables_checkbox_list()
    {
        $options = [
            'attr' => ['class' => 'choice-class-something', 'disabled' => 'disabled'],
            'choices' => [1 => 'monday', 2 => 'tuesday'],
            'selected' => 'tuesday',
            'multiple' => true,
            'expanded' => true
        ];

        $field = new ChoiceType('some_choice', 'choice', $this->plainForm, $options);
        $children = $field->getChildren();

        // there shall be 'disabled' attribute set beforehand
        $this->assertArrayHasKey('disabled', $field->getOption('attr'));
        $this->assertEquals('disabled', $field->getOption('attr')['disabled']);
        foreach ($children as $child) {
            $this->assertArrayHasKey('disabled', $child->getOption('attr'));
            $this->assertEquals('disabled', $child->getOption('attr')['disabled']);
        }


        $field->enable();

        // there shall be no 'disabled' attribute set after
        $this->assertArrayNotHasKey('disabled', $field->getOption('attr'));
        foreach ($children as $child) {
            $this->assertArrayNotHasKey('disabled', $child->getOption('attr'));
        }
    }
    
    /** @test */
    public function it_enables_radios_list()
    {
        $options = [
            'attr' => ['class' => 'choice-class-something', 'disabled' => 'disabled'],
            'choices' => [1 => 'yes', 2 => 'no'],
            'selected' => 'no',
            'expanded' => true
        ];

        $field = new ChoiceType('some_choice', 'choice', $this->plainForm, $options);
        $children = $field->getChildren();

        // there shall be 'disabled' attribute set beforehand
        $this->assertArrayHasKey('disabled', $field->getOption('attr'));
        $this->assertEquals('disabled', $field->getOption('attr')['disabled']);
        foreach ($children as $child) {
            $this->assertArrayHasKey('disabled', $child->getOption('attr'));
            $this->assertEquals('disabled', $child->getOption('attr')['disabled']);
        }


        $field->enable();

        // there shall be no 'disabled' attribute set after
        $this->assertArrayNotHasKey('disabled', $field->getOption('attr'));
        foreach ($children as $child) {
            $this->assertArrayNotHasKey('disabled', $child->getOption('attr'));
        }
    }
}
