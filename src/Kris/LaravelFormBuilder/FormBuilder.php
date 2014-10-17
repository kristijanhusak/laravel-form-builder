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
        $form = $this->container
            ->make($formClass)
            ->setUrl($this->container->request->getUri())
            ->setFormHelper($this->formHelper);

        $form->buildForm();

        return $form->setFormOptions($options);
    }

    /**
     * Get instance of the empty form which can be modified
     *
     * @param $options
     * @return Form
     */
    public function plain(array $options = [])
    {
        return $this->container
            ->make('Kris\LaravelFormBuilder\Form')
            ->setFormHelper($this->formHelper)
            ->setUrl($this->container->request->getUri())
            ->setFormOptions($options);
    }
}
