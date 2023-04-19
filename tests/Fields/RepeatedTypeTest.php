<?php

use Kris\LaravelFormBuilder\Fields\RepeatedType;

class RepeatedTypeTest extends FormBuilderTestCase
{
    /** @test */
    public function it_creates_repeated_as_two_inputs()
    {
        $repeatedForm = $this->formBuilder->plain();

        $repeated = new RepeatedType('password', 'repeated', $this->plainForm, []);

        $repeated->render();

        $this->assertEquals(2, count($repeated->getChildren()));

        $this->assertInstanceOf('Kris\LaravelFormBuilder\Fields\InputType', $repeated->first);
        $this->assertInstanceOf('Kris\LaravelFormBuilder\Fields\InputType', $repeated->second);
        $this->assertNull($repeated->third);
    }

    /** @test */
    public function it_checks_if_field_rendered_by_children()
    {
        $repeated = new RepeatedType('password', 'repeated', $this->plainForm, [
            'type' => 'file'
        ]);

        $this->assertFalse($repeated->isRendered());

        $repeated->first->render();

        $this->assertTrue($repeated->isRendered());

        $this->assertTrue($this->plainForm->getFormOption('files'));
    }

    /** @test */
    public function handles_validation_rules_properly()
    {
        // has no other rules
        $plainForm = $this->formBuilder->plain();
        $plainForm->add('password', 'repeated');
        $repeated = $plainForm->getField('password');

        $this->assertContains('same:password_confirmation', $repeated->first->getOption('rules'));

        $this->request['password'] = '123';
        $this->request['password_confirmation'] = '124';

        $valid = $plainForm->isValid();
        $this->assertFalse($valid);

        $errors = [
            'password' => [
                'The Password field must match password confirmation.',
            ],
        ];
        $this->assertEquals($errors, $plainForm->getErrors());

        // has own rules
        $plainForm = $this->formBuilder->plain();
        $plainForm->add('password', 'repeated', [
            'rules' => 'required|min:5',
        ]);
        $plainForm->renderForm();

        $rules = ['password' => ['required', 'min:5', 'same:password_confirmation']];
        $this->assertEquals($rules, $plainForm->getRules());

        $valid = $plainForm->isValid();
        $this->assertFalse($valid);

        $errors = [
            'password' => [
                'The Password field must be at least 5 characters.',
                'The Password field must match password confirmation.',
            ]
        ];
        $this->assertEquals($errors, $plainForm->getErrors());

        // has own rules on field and in first field options
        $plainForm = $this->formBuilder->plain();
        $plainForm->add('password', 'repeated', [
            'rules' => 'required',
            'first_options' => [
                'rules' => 'min:5',
            ]
        ]);
        $rules = ['password' => ['required', 'min:5', 'same:password_confirmation']];
        $this->assertEquals($rules, $plainForm->getRules());
        $valid = $plainForm->isValid();
        $this->assertFalse($valid);

        $errors = [
            'password' => [
                'The Password field must be at least 5 characters.',
                'The Password field must match password confirmation.',
            ]
        ];
        $this->assertEquals($errors, $plainForm->getErrors());

        // has rules only in field options
        $plainForm = $this->formBuilder->plain();
        $plainForm->add('password', 'repeated', [
            'rules' => 'required',
            'first_options' => [
                'rules' => 'required|min:5',
            ]
        ]);
        $rules = ['password' => ['required', 'min:5', 'same:password_confirmation']];
        $this->assertEquals($rules, $plainForm->getRules());

        $valid = $plainForm->isValid();
        $this->assertFalse($valid);

        $errors = [
            'password' => [
                'The Password field must be at least 5 characters.',
                'The Password field must match password confirmation.',
            ]
        ];
        $this->assertEquals($errors, $plainForm->getErrors());
    }

}
