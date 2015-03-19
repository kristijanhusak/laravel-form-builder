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

        $form = $this->container
            ->make($class)
            ->setFormHelper($this->formHelper)
            ->setFormBuilder($this)
            ->setFormOptions($options)
            ->addData($data);

        $form->buildForm();

        return $form;
    }

    /**
     * Create named form to group fields in this form by a name.
     * Fields are named like name[field].
     *
     * @param $name
     * @param $formClass
     * @param array $options
     * @param array $data
     * @return Form
     */
    public function createNamedForm($name, $formClass, array $options = [], array $data = [])
    {
        $dataReal = $data;
        if (empty($dataReal)) {
            if (isset($options['model']) && is_object($options['model']) && method_exists($options['model'], 'toArray')) {
                $dataReal = $options['model']->toArray();
            }
        }

        return $this->plain(
            $options
        )->add(
            $name,
            'form',
            [
                'class' => $this->create($formClass, $options, $dataReal),
                'label' => isset($options['label']) ? $options['label'] : false
            ]
        );
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
