<?php

use Kris\LaravelFormBuilder\Fields\EntityType;
use Kris\LaravelFormBuilder\Form;

class EntityTypeTest extends FormBuilderTestCase
{
    /** @test */
    public function it_uses_default_choices_for_entity_type()
    {
        $choices = ['yes' => 'Yes', 'no' => 'No'];
        $options = [
            'attr' => ['class' => 'choice-class'],
            'choices' => $choices,
            'selected' => 'yes'
        ];

        $choice = new EntityType('some_entity', 'entity', $this->plainForm, $options);
        $choice->render();

        $this->assertEquals(1, count($choice->getChildren()));
        $this->assertEquals($choices, $choice->getOption('choices'));
        $this->assertEquals('yes', $choice->getOption('selected'));
    }

    /** @test */
    public function it_uses_passed_class_model_to_fetch_all()
    {
        $mdl = new DummyModel();
        $options = [
            'class' => 'DummyModel'
        ];

        $choice = new EntityType('entity_choice', 'entity', $this->plainForm, $options);
        $choice->render();

        $expected = [
            1 => 'English',
            2 => 'French',
            3 => 'Serbian'
        ];;

        $this->assertEquals($expected, $choice->getOption('choices'));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_model_class_not_provided()
    {
        $this->expectException(\InvalidArgumentException::class);

        $options = [];

        $choice = new EntityType('entity_choice', 'entity', $this->plainForm, $options);
    }

    /** @test */
    public function it_uses_query_builder_to_filter_choices()
    {
        $mdl = new DummyModel();
        $options = [
            'class' => 'DummyModel',
            'property' => 'short_name',
            'query_builder' => function (DummyModel $model) {
                return $model->getData()->filter(function($val) {
                    return $val['id'] > 1;
                });
            }
        ];

        $choice = new EntityType('entity_choice', 'entity', $this->plainForm, $options);
        $choice->render();

        $expected = [
            2 => 'Fr',
            3 => 'Rs'
        ];

        $this->assertEquals($expected, $choice->getOption('choices'));
    }

    /** @test */
    public function options_are_passed_to_the_children()
    {
        $mdl = new DummyModel();
        $options = [
            'class' => 'DummyModel'
        ];

        $choice = new EntityType('entity_choice', 'entity', $this->plainForm, $options);
        $choice->setOption('attr.data-key', 'value');

        $field = $choice->render();
        
        $expectedField = '<div class="form-group">
            <label for="entity_choice" class="control-label">Entity choice</label>
            <select class="form-control" data-key="value" id="entity_choice" name="entity_choice"><option value="1">English</option><option value="2">French</option><option value="3">Serbian</option></select>
            </div>';
            
            $this->assertXmlStringEqualsXmlString(trim($field), $expectedField);
        }

    /** @test */
    public function it_disables()
    {
        $choices = ['yes' => 'Yes', 'no' => 'No'];
        $options = [
            'attr' => ['class' => 'choice-class'],
            'choices' => $choices,
            'selected' => 'yes'
        ];

        $choice = new EntityType('some_entity', 'entity', $this->plainForm, $options);
        $children = $choice->getChildren();

        $this->assertArrayNotHasKey('disabled', $choice->getOption('attr'));
        foreach ($children as $child) {
            $this->assertArrayNotHasKey('disabled', $child->getOption('attr'));
        }

        $choice->disable();

        $this->assertArrayHasKey('disabled', $choice->getOption('attr'));
        $this->assertEquals('disabled', $choice->getOption('attr')['disabled']);
        foreach ($children as $child) {
            $this->assertArrayHasKey('disabled', $child->getOption('attr'));
            $this->assertEquals('disabled', $child->getOption('attr')['disabled']);
        }
    }
    
    /** @test */
    public function it_enables()
    {
        $choices = ['yes' => 'Yes', 'no' => 'No'];
        $options = [
            'attr' => ['class' => 'choice-class', 'disabled' => 'disabled'],
            'choices' => $choices,
            'selected' => 'yes'
        ];

        $choice = new EntityType('some_entity', 'entity', $this->plainForm, $options);
        $children = $choice->getChildren();

        $this->assertArrayHasKey('disabled', $choice->getOption('attr'));
        $this->assertEquals('disabled', $choice->getOption('attr')['disabled']);
        foreach ($children as $child) {
            $this->assertArrayHasKey('disabled', $child->getOption('attr'));
            $this->assertEquals('disabled', $child->getOption('attr')['disabled']);
        }

        $choice->enable();

        $this->assertArrayNotHasKey('disabled', $choice->getOption('attr'));
        foreach ($children as $child) {
            $this->assertArrayNotHasKey('disabled', $child->getOption('attr'));
        }
    }
}
