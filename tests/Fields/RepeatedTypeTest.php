<?php

use Kris\LaravelFormBuilder\Fields\RepeatedType;
use Kris\LaravelFormBuilder\Form;

class RepeatedTypeTest extends FormBuilderTestCase
{
    /** @test */
    public function it_creates_repeated_as_two_inputs()
    {
        $this->fieldExpetations('text', Mockery::any());
        $this->fieldExpetations('text', Mockery::any());
        $this->fieldExpetations('repeated', Mockery::any());
        $repeatedForm = $this->setupForm(new Form());
        // Child fields are initialized when constructing a repeated type
        // from plain form
        $this->container->shouldReceive('make')->once()->andReturn($this->plainForm);

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
        $this->fieldExpetations('text', Mockery::any());
        $this->fieldExpetations('text', Mockery::any());
        $this->fieldExpetations('repeated', Mockery::any());
        $this->container->shouldReceive('make')->andReturn($this->plainForm);

        $repeated = new RepeatedType('password', 'repeated', $this->plainForm, [
            'type' => 'file'
        ]);

        $this->assertFalse($repeated->isRendered());

        $repeated->first->render();

        $this->assertTrue($repeated->isRendered());

        $this->assertTrue($this->plainForm->getFormOption('files'));
    }
}
