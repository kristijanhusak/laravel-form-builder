<?php
namespace {

    use Kris\LaravelFormBuilder\Form;
    use Kris\LaravelFormBuilder\FormBuilder;
    use Kris\LaravelFormBuilder\FormHelper;

    class FormBuilderTest extends FormBuilderTestCase
    {

        /** @test */
        public function it_creates_plain_form_and_sets_options_on_it()
        {
            $options = [
                'method' => 'PUT',
                'url' => '/some/url/1',
                'model' => $this->model
            ];

            $plainForm = $this->formBuilder->plain($options);

            $this->assertEquals('PUT', $plainForm->getMethod());
            $this->assertEquals('/some/url/1', $plainForm->getUrl());
            $this->assertEquals($this->model, $plainForm->getModel());
            $this->assertNull($plainForm->buildForm());
        }

        /** @test */
        public function it_creates_custom_form_and_sets_options_on_it()
        {
            $options = [
                'method' => 'POST',
                'url' => '/posts',
                'data' => ['dummy_choices' => [1 => 'choice_1', 2 => 'choice_2']]
            ];

            $customForm = $this->formBuilder->create('CustomDummyForm', $options);

            $this->assertEquals('POST', $customForm->getMethod());
            $this->assertEquals($this->request, $customForm->getRequest());
            $this->assertEquals('/posts', $customForm->getUrl());
            $this->assertEquals([1 => 'choice_1', 2 => 'choice_2'], $customForm->getData('dummy_choices'));
            $this->assertInstanceOf('Kris\\LaravelFormBuilder\\Form', $customForm);
            $this->assertArrayHasKey('title', $customForm->getFields());
            $this->assertArrayHasKey('body', $customForm->getFields());
        }

        /**
         * @test
         * @expectedException \InvalidArgumentException
         */
        public function it_throws_exception_if_child_form_is_not_valid_class()
        {
            $this->plainForm->add('song', 'form', [
                'class' => 'nonvalid'
            ]);
        }

        /**
         * @test
         * @expectedException \InvalidArgumentException
         */
        public function it_throws_exception_if_child_form_class_is_not_passed()
        {
            $this->plainForm->add('song', 'form', [
                'class' => null
            ]);
        }

        /**
         * @test
         * @expectedException \InvalidArgumentException
         */
        public function it_throws_exception_if_child_form_class_is_not_valid_format()
        {
            $this->plainForm->add('song', 'form', [
                'class' => 1
            ]);
        }

        /** @test */
        public function it_can_set_form_helper_once_and_call_build_form()
        {
            $form = $this->formBuilder->create('CustomDummyForm');

            $this->assertEquals($this->formHelper, $form->getFormHelper());
            $this->assertEquals($this->formBuilder, $form->getFormBuilder());
            $this->assertArrayHasKey('title', $form->getFields());
            $this->assertArrayHasKey('body', $form->getFields());
        }

        /** @test */
        public function it_appends_default_namespace_from_config_on_building()
        {
            $form =  new LaravelFormBuilderTest\Forms\NamespacedDummyForm();
            $config = $this->config;
            $config['default_namespace'] = 'LaravelFormBuilderTest\Forms';
            $formHelper = new FormHelper($this->view, $this->request, $config);
            $formBuilder = new FormBuilder($this->app, $formHelper);

            $formBuilder->create('NamespacedDummyForm');
        }

    }

    class CustomDummyForm extends Form
    {
        public function buildForm()
        {
            $this->add('title', 'text')
                ->add('body', 'textarea');
        }
    }
}

namespace LaravelFormBuilderTest\Forms {

    use Kris\LaravelFormBuilder\Form;

    class NamespacedDummyForm extends Form
    {
    }
}
