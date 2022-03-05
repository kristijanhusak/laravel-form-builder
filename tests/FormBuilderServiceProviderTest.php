<?php

namespace {

    use App\FormBuilderStuff\MyFormBuilder;
    use App\FormBuilderStuff\SomeService;

    class FormBuilderServiceProviderTest extends FormBuilderTestCase
    {

        protected function resolveApplicationConfiguration($app)
        {
            parent::resolveApplicationConfiguration($app);

            $app['config']->set('laravel-form-builder.form_builder_class', MyFormBuilder::class);
        }

        /** @test */
        public function it_dependency_injects()
        {
            // The right form builder class is used for the container form builder.
            $this->assertInstanceOf(MyFormBuilder::class, $this->app['laravel-form-builder']);

            // Dependency injected MyFormBuilder in SomeService is from the container.
            $service = $this->app->build(SomeService::class);
            $this->assertIdentical($service->fb, $this->app['laravel-form-builder']);

            // Dependency injected standard Kris FormBuilder also resolves to that container MyFormBuilder.
            $fb2 = $this->app->call([$service, 'krisFB']);
            $this->assertIdentical($fb2, $this->app['laravel-form-builder']);
        }

    }

}

namespace App\FormBuilderStuff {
    use Kris\LaravelFormBuilder\FormBuilder;

    class MyFormBuilder extends FormBuilder
    {

    }

    class SomeService
    {
        public $fb;
        public function __construct(MyFormBuilder $fb)
        {
            $this->fb = $fb;
        }

        public function krisFB(FormBuilder $fb)
        {
            return $fb;
        }
    }
}
