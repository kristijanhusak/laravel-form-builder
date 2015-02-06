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
     * @param       $data
     * @return Form
     */
    public function create($formClass, array $options = [], array $data = [])
    {
        $form = $this->container
            ->make($this->getNamespaceFromConfig() . $formClass)
            ->setFormHelper($this->formHelper)
            ->setFormOptions($options)
            ->addData($data);

        $form->buildForm();

        return $form;
    }

    /**
     * Get the namespace from the config
     *
     * @return string
     */
    protected function getNamespaceFromConfig()
    {
        $namespace = $this->formHelper->getConfig('default_namespace');

        if (!$namespace) {
            return '';
        }

        return $namespace . '\\';
    }

    /**
     * Get instance of the empty form which can be modified
     *
     * @param array $options
     * @param array $data
     * @return Form
     */
    public function plain(array $options = [], array $data = [])
    {
        return $this->container
            ->make('Kris\LaravelFormBuilder\Form')
            ->setFormHelper($this->formHelper)
            ->setFormOptions($options)
            ->addData($data);
    }
}
