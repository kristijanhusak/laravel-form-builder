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
    public function it_does_not_overwrite_language_name()
    {
        $plainParent = $this->formBuilder->plain(['language_name' => 'test_name']);
        $plainChild = $this->formBuilder->plain(['language_name' => 'different_name']);

        $plainParent->add('a form', 'form', ['class' => $plainChild]);

        $this->assertEquals('different_name', $plainChild->getLanguageName());
        $this->assertEquals('test_name', $plainParent->getLanguageName());
    }
}