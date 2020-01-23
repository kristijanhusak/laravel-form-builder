<?php

use Kris\LaravelFormBuilder\Fields\ContainerType;

class ContainerTypeTest extends FormBuilderTestCase
{
    /** @test */
    public function it_adds_children_fields()
    {
        $containerType = $this->buildContainerType();

        $this->assertEquals(2, count($containerType->getChildren()));
    }

    /** @test */
    public function it_render_properly()
    {
        $this->buildContainerType()->render();
        $this->assertNotThrown();
    }

    /** @test */
    public function it_has_the_proper_field_name()
    {
        $containerType = $this->buildContainerType();
        $children = $containerType->getChildren();

        $this->assertEquals('select_field_1', $children['select_field_1']->getName());
        $this->assertEquals('select_field_2', $children['select_field_2']->getName());


        // within a named form.
        $formName = 'Foo';
        $this->plainForm->setName($formName);

        $containerType = $this->buildContainerType();
        $children = $containerType->getChildren();

        $this->assertEquals("{$formName}[select_field_1]", $children['select_field_1']->getName());
        $this->assertEquals("{$formName}[select_field_2]", $children['select_field_2']->getName());
    }

    /** @test */
    public function its_children_fields_can_be_found_from_parent_form()
    {
        $childFieldName = 'select_field_1';

        $this->plainForm->add('container_field', 'container', [
            'fields' => [
                [
                    'type' => 'select',
                    'name' => $childFieldName,
                ]
            ]
        ]);

        $childField = $this->plainForm->getField($childFieldName);

        $this->assertEquals($childFieldName, $childField->getRealName());
    }

    /** @test */
    public function it_has_value_set_given_from_parent_model()
    {
        $childFieldName = 'select_field_1';
        $childFieldValue = 'Foo';
        $formOptions = [
            'model' => [
                $childFieldName => $childFieldValue
            ]
        ];

        $form = $this->formBuilder->plain($formOptions)->add('container_field', 'container', [
            'fields' => [
                [
                    'type' => 'select',
                    'name' => $childFieldName,
                ]
            ]
        ]);

        $childField = $form->getField($childFieldName);

        $this->assertEquals($childFieldValue, $childField->getValue());
    }

    /** @test */
    public function it_get_the_proper_rules()
    {
        $containerType = $this->buildContainerType();
        $rules = $containerType->getValidationRules()->getRules();

        $this->assertEquals(['email'], $rules['select_field_1']);
        $this->assertEquals(['date'], $rules['select_field_2']);
    }

    /** @test */
    public function it_get_the_proper_attributes()
    {
        $containerType = $this->buildContainerType();
        $attributes = $containerType->getAllAttributes();

        $this->assertEquals(['select_field_1', 'select_field_2'], $attributes);
    }

    /** @test */
    public function it_throws_if_required_parameter_not_given()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->buildContainerType([
            'fields' => [
                [
                    'type' => null,
                ],
                [
                    'name' => null,
                ]
            ]
        ]);
    }

    private function buildContainerType(array $options = []): ContainerType
    {
        $fields = [
            [
                'type' => 'select',
                'name' => 'select_field_1',
                'options' => [
                    'label' => 'Label for select_field_1',
                    'choices' => ['foo' => 'Foo Label'],
                    'rules' => ['email'],
                ],
            ],
            [
                'type' => 'select',
                'name' => 'select_field_2',
                'options' => [
                    'label' => 'Label for select_field_2',
                    'choices' => ['bar' => 'Bar Label'],
                    'rules' => ['date'],
                ],
            ],
        ];

        $defaultOptions = [
            'fields' => $fields,
        ];

        return new ContainerType('container_field', 'container', $this->plainForm, $this->formHelper->mergeOptions($defaultOptions, $options));
    }

}
