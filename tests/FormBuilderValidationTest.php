<?php

namespace {

    use Kris\LaravelFormBuilder\Events\AfterFieldCreation;
    use Kris\LaravelFormBuilder\Events\AfterFormCreation;
    use Kris\LaravelFormBuilder\Form;
    use Kris\LaravelFormBuilder\FormBuilder;
    use Kris\LaravelFormBuilder\FormHelper;
    use Kris\LaravelFormBuilder\Traits\ValidatesWhenResolved;

    class TestForm extends Form
    {
        use ValidatesWhenResolved;

        public function buildForm()
        {
            $this->add('name', 'text', ['rules' => ['required', 'min:3']]);
            $this->add('email', 'text', ['rules' => ['required', 'email', 'min:3']]);
        }

    }

    class TestController
    {

        public function validate(TestForm $form)
        {

        }

    }

    class FormBuilderValidationTest extends FormBuilderTestCase
    {
        public function setUp(): void
        {
            parent::setUp();
            $this->app
                ->make('Illuminate\Contracts\Http\Kernel')
                ->pushMiddleware('Illuminate\Session\Middleware\StartSession');
        }

        public function testItValidatesWhenResolved()
        {
            Route::post('/test', TestController::class.'@validate');

            $this->post('/test', ['email' => 'foo@bar.com'])
                ->assertRedirect('/')
                ->assertSessionHasErrors(['name']);
        }

        public function testItDoesNotValidateGetRequests()
        {
            Route::get('/test', TestController::class.'@validate');

            $this->get('/test', ['email' => 'foo@bar.com'])
                ->assertStatus(200);
        }
    }
}
