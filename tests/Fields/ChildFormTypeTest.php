<?php

use Kris\LaravelFormBuilder\Fields\ChoiceType;
use Kris\LaravelFormBuilder\Fields\CollectionType;
use Kris\LaravelFormBuilder\Fields\SelectType;
use Kris\LaravelFormBuilder\Form;

class ChildFormTypeTest extends FormBuilderTestCase
{
    /** @test */
    public function it_implicitly_inherits_language_name()
    {
        $plainParent = $this->formBuilder->plain(['language_name' => 'test_name']);
        $plainChild = $this->formBuilder->plain();

        $plainParent->add('a form', 'form', ['class' => $plainChild]);

        $this->assertEquals($plainParent->getLanguageName(), $plainChild->getLanguageName());
        $this->assertEquals('test_name', $plainChild->getLanguageName());
    }

    /** @test */
    public function it_implicitly_inherits_translation_template()
    {
        $plainParent = $this->formBuilder->plain(['translation_template' => 'form.{name}.{type}']);
        $plainChild = $this->formBuilder->plain();

        $plainParent->add('a form', 'form', ['class' => $plainChild]);

        $this->assertEquals($plainParent->getTranslationTemplate(), $plainChild->getTranslationTemplate());
        $this->assertEquals('form.{name}.{type}', $plainChild->getTranslationTemplate());
    }

    /** @test */
    public function it_does_not_overwrite_language_name()
    {
        $plainParent = $this->formBuilder->plain(['language_name' => 'test_name']);
        $plainChild = $this->formBuilder->plain(['language_name' => 'different_name']);

        $plainParent->add('a form', 'form', ['class' => $plainChild]);

        $this->assertEquals('different_name', $plainChild->getLanguageName());
        $this->assertEquals('test_name', $plainParent->getLanguageName());
    }

    /** @test */
    public function it_does_not_overwrite_translation_template()
    {
        $plainParent = $this->formBuilder->plain(['translation_template' => 'test.{name}']);
        $plainChild = $this->formBuilder->plain(['translation_template' => 'different.{name}']);

        $plainParent->add('a form', 'form', ['class' => $plainChild]);

        $this->assertEquals('different.{name}', $plainChild->getTranslationTemplate());
        $this->assertEquals('test.{name}', $plainParent->getTranslationTemplate());
    }

    /**
     * @test
     */
    public function it_allow_destroy()
    {
        $parentForm = $this->formBuilder->plain([
            'model' => [
                'user' => [
                    'id' => 1,
                    'email' => 'foo@test.com',
                    '_destroy'=>true,
                ]
            ]
        ]);

        $childForm = $this->formBuilder->plain()->add('id')->add('email');

        $requiredFieldsForAllowDestroy = ['_destroy', 'id'];

        $parentForm->add('user', 'form', [
            'class' => $childForm,
            'allow_destroy' => true,
            'required_fields_for_allow_destroy' => $requiredFieldsForAllowDestroy
        ]);

        $childFormFields = $parentForm->getField('user')->getChildren();

        $this->assertEquals($requiredFieldsForAllowDestroy, array_keys($childFormFields));
        $this->assertArrayNotHasKey('email', $childFormFields);
    }
}