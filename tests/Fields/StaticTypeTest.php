<?php

use Kris\LaravelFormBuilder\Fields\StaticType;
use Kris\LaravelFormBuilder\Form;

class StaticTypeTest extends FormBuilderTestCase
{

    /** @test */
    public function it_creates_static_field()
    {
        $options = [
            'attr' => ['class' => 'static-class', 'id' => 'some_static']
        ];

        $this->plainForm->setModel(['some_static' => 'static text']);

        $expectedOptions = $this->getDefaults(
            ['class' => 'static-class', 'id' => 'some_static'],
            'some_static',
            'Some Static',
            'static text'
        );

        $expectedOptions['tag'] = 'div';

        $expectedOptions['elemAttrs'] = $this->formHelper->prepareAttributes(
            ['class' => 'static-class', 'id' => 'some_static']
        );
        $expectedOptions['labelAttrs'] = $this->formHelper->prepareAttributes(
            $expectedOptions['label_attr']
        );

        $expectedViewData = [
            'name' => 'some_static',
            'nameKey' => 'some_static',
            'type' => 'static',
            'options' => $expectedOptions,
            'showLabel' => true,
            'showField' => true,
            'showError' => false
        ];

        $this->fieldExpetations('static', $expectedViewData);

        $static = new StaticType('some_static', 'static', $this->plainForm, $options);

        $static->render();

        $this->assertEquals('static text', $static->getOption('value'));
    }
}
