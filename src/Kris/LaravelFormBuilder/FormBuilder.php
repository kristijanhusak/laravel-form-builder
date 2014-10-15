<?php  namespace Kris\LaravelFormBuilder;

use Illuminate\Contracts\Container\Container;

class FormBuilder
{

    /**
     * @var Container
     */
    private $container;

    /**
     * @var FormHelper
     */
    private $formHelper;

    public function __construct(Container $container, FormHelper $formHelper)
    {
        $this->container = $container;
        $this->formHelper = $formHelper;
    }

    /**
     * @param       $formClass
     * @param       $options
     * @return Form
     */
    public function create($formClass, array $options = [])
    {
        $form = $this->container->make($formClass)->setFormHelper($this->formHelper);

        $form->buildForm();

        return $form->setFormOptions($options);
    }

    /**
     * @param $options
     * @return Form
     */
    public function plain(array $options = [])
    {
        return $this->container
            ->make('Kris\LaravelFormBuilder\Form')
            ->setFormHelper($this->formHelper)
            ->setFormOptions($options);
    }
}