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

    /**
     * @test
     * @dataProvider dataForFieldRendering
     */
    public function it_render(array $options, string $expectedHtml)
    {
        $this->plainForm->add('users', 'choice', $options);

        $this->plainForm->renderForm();

        $renderedView = $this->plainForm->users->render();

        $this->assertStringContainsString($expectedHtml, $renderedView);
    }

    /**
     * @return array{options: array<string, mixed>, expected_html: string}
     */
    public static function dataForFieldRendering(): array
    {
        return [
            'select field' => [
                'options' => [
                    'choices' => [1 => 'user1', 2 => 'user2'],
                    'expanded' => false,
                    'multiple' => false,
                ],
                'expected_html' => '<select class="form-control" id="users" name="users">',
            ],
            'multiple select field' => [
                'options' => [
                    'choices' => [1 => 'user1', 2 => 'user2'],
                    'expanded' => false,
                    'multiple' => true,
                ],
                'expected_html' => '<select class="form-control" multiple id="users[]" name="users[]">',
            ],
            'radio field' => [
                'options' => [
                    'choices' => [1 => 'user1', 2 => 'user2'],
                    'expanded' => true,
                    'multiple' => false,
                ],
                'expected_html' => '<input id="users_1" name="users" type="radio" value="1">',
            ],
            'checkbox field' => [
                'options' => [
                    'choices' => [1 => 'user1', 2 => 'user2'],
                    'expanded' => true,
                    'multiple' => true,
                ],
                'expected_html' => '<input id="users_1" name="users[]" type="checkbox" value="1">',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider dataForModifiedFieldRendering
     */
    public function it_render_modified_field(array $options, array $modifyOptions, string $expectedHtml)
    {
        $this->plainForm->add('users', 'choice', $options);

        $this->plainForm->modify('users', 'choice', $modifyOptions);

        $this->plainForm->renderForm();

        $renderedView = $this->plainForm->users->render();

        $this->assertStringContainsString($expectedHtml, $renderedView);
    }

    /**
     * @return array{options: array<string, mixed>, modify_options: array<string, mixed>, expected_html: string}
     */
    public static function dataForModifiedFieldRendering(): array
    {
        return [
            'modified select field' => [
                'options' => [
                    'choices' => [1 => 'user1', 2 => 'user2'],
                    'expanded' => false,
                    'multiple' => false,
                ],
                'modify_options' => [
                    'rules' => 'required',
                ],
                'expected_html' => '<select class="form-control" required="required" id="users" name="users">',
            ],
            'modified multiple select field' => [
                'options' => [
                    'choices' => [1 => 'user1', 2 => 'user2'],
                    'expanded' => false,
                    'multiple' => true,
                ],
                'modify_options' => [
                    'rules' => 'required',
                ],
                'expected_html' => '<select class="form-control" required="required" multiple id="users[]" name="users[]">',
            ],
            'modified radio field' => [
                'options' => [
                    'choices' => [1 => 'user1', 2 => 'user2'],
                    'expanded' => true,
                    'multiple' => false,
                ],
                'modify_options' => [
                    'rules' => 'required',
                ],
                'expected_html' => '<input id="users_1" required="required" name="users" type="radio" value="1">',
            ],
            'modified checkbox field' => [
                'options' => [
                    'choices' => [1 => 'user1', 2 => 'user2'],
                    'expanded' => true,
                    'multiple' => true,
                ],
                'modify_options' => [
                    'rules' => 'required',
                ],
                'expected_html' => '<input id="users_1" required="required" name="users[]" type="checkbox" value="1">',
            ],
            'modified select to checkbox field' => [
                'options' => [
                    'choices' => [1 => 'user1', 2 => 'user2'],
                    'expanded' => false,
                    'multiple' => false,
                ],
                'modify_options' => [
                    'expanded' => true,
                    'multiple' => true,
                ],
                'expected_html' => '<input id="users_1" name="users[]" type="checkbox" value="1">',
            ],
            'modified multiple select to select field' => [
                'options' => [
                    'choices' => [1 => 'user1', 2 => 'user2'],
                    'expanded' => false,
                    'multiple' => true,
                ],
                'modify_options' => [
                    'expanded' => false,
                    'multiple' => false,
                ],
                'expected_html' => '<select class="form-control" id="users" name="users">',
            ],
            'modified multiple select to radio field' => [
                'options' => [
                    'choices' => [1 => 'user1', 2 => 'user2'],
                    'expanded' => false,
                    'multiple' => true,
                ],
                'modify_options' => [
                    'expanded' => true,
                    'multiple' => false,
                ],
                'expected_html' => '<input id="users_1" name="users" type="radio" value="1">',
            ],
            'modified multiple select to checkbox field' => [
                'options' => [
                    'choices' => [1 => 'user1', 2 => 'user2'],
                    'expanded' => false,
                    'multiple' => true,
                ],
                'modify_options' => [
                    'expanded' => true,
                    'multiple' => true,
                ],
                'expected_html' => '<input id="users_1" name="users[]" type="checkbox" value="1">',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider dataForFieldRenderingWithOptions
     */
    public function it_render_field_with_options(array $options, array $renderOptions, string $expectedHtml)
    {
        $this->plainForm->add('users', 'choice', $options);

        $this->plainForm->renderForm();

        $renderedView = $this->plainForm->users->render($renderOptions);

        $this->assertStringContainsString($expectedHtml, $renderedView);
    }

    /**
     * @return array{options: array<string, mixed>, render_options: array<string, mixed>, expected_html: string}
     */
    public static function dataForFieldRenderingWithOptions(): array
    {
        return [
            'make select to checkbox field' => [
                'options' => [
                    'choices' => [1 => 'user1', 2 => 'user2'],
                    'expanded' => false,
                    'multiple' => false,
                ],
                'render_options' => [
                    'expanded' => true,
                    'multiple' => true,
                ],
                'expected_html' => '<input id="users_1" name="users[]" type="checkbox" value="1">',
            ],
            'make multiple select to radio field' => [
                'options' => [
                    'choices' => [1 => 'user1', 2 => 'user2'],
                    'expanded' => false,
                    'multiple' => true,
                ],
                'render_options' => [
                    'expanded' => true,
                    'multiple' => false,
                ],
                'expected_html' => '<input id="users_1" name="users" type="radio" value="1">',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider dataForChildrenFormRendering
     */
    public function it_render_children_form(array $options, string $expectedHtml)
    {
        $this->plainForm->add('form', 'form', [
            'class' => $this->formBuilder->plain()->modify('users', 'choice', $options),
        ]);

        $this->plainForm->renderForm();

        $renderedView = $this->plainForm->form->users->render();

        $this->assertStringContainsString($expectedHtml, $renderedView);
    }

    /**
     * @return array{options: array<string, mixed>, expected_html: string}
     */
    public static function dataForChildrenFormRendering(): array
    {
        return [
            'children select field' => [
                'options' => [
                    'choices' => [1 => 'user1', 2 => 'user2'],
                    'expanded' => false,
                    'multiple' => false,
                ],
                'expected_html' => '<select class="form-control" id="form[users]" name="form[users]">',
            ],
            'children multiple select field' => [
                'options' => [
                    'choices' => [1 => 'user1', 2 => 'user2'],
                    'expanded' => false,
                    'multiple' => true,
                ],
                'expected_html' => '<select class="form-control" multiple id="form[users][]" name="form[users][]">',
            ],
            'children radio field' => [
                'options' => [
                    'choices' => [1 => 'user1', 2 => 'user2'],
                    'expanded' => true,
                    'multiple' => false,
                ],
                'expected_html' => '<input id="form_users_1" name="form[users]" type="radio" value="1">',
            ],
            'children checkbox field' => [
                'options' => [
                    'choices' => [1 => 'user1', 2 => 'user2'],
                    'expanded' => true,
                    'multiple' => true,
                ],
                'expected_html' => '<input id="form_users_1" name="form[users][]" type="checkbox" value="1">',
            ],
        ];
    }

    /** @test */
    public function it_render_and_modify_multiple_times_with_multiple_option()
    {
        $expectedHtml = '<select class="form-control" multiple id="users[]" name="users[]">';

        $this->plainForm->add('users', 'choice', [
            'choices' => [1 => 'user1', 2 => 'user2'],
            'expanded' => false,
            'multiple' => true,
        ]);

        $this->assertStringContainsString($expectedHtml, $this->plainForm->users->render());

        $expectedHtml = '<select class="form-control" required="required" multiple id="users[]" name="users[]">';

        $this->plainForm->modify('users', 'choice', [
            'rules' => 'required',
        ]);

        $this->assertStringContainsString($expectedHtml, $this->plainForm->users->render());

        $this->plainForm->modify('users', 'choice', [
            'rules' => 'required',
        ]);

        $this->assertStringContainsString($expectedHtml, $this->plainForm->users->render());
    }
}
