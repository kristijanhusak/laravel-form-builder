<?php  namespace Kris\LaravelFormBuilder;

use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * @param Container  $container
     * @param FormHelper $formHelper
     */
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
        $class = $this->getNamespaceFromConfig() . $formClass;

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(
                'Form class with name ' . $class . ' does not exist.'
            );
        }

        /** @var Form $form */
        $form = $this->container
            ->make($class)
            ->setFormHelper($this->formHelper)
            ->setFormBuilder($this)
            ->setFormOptions($options)
            ->addData($data);

        // reset model to match named form logic
        if ($form->getName()) {
            if ($form->getModel() instanceof Model && method_exists($form->getModel(), 'toArray')) {
                $form->setModel([$form->getName() => $form->getModel()->toArray()]);
            } elseif (is_array($form->getModel())) {
                $form->setModel([$form->getName() => $form->getModel()]);
            }
        }

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
            ->setFormBuilder($this)
            ->setFormOptions($options)
            ->addData($data);
    }
}
