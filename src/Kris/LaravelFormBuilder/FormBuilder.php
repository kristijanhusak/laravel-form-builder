<?php  namespace Kris\LaravelFormBuilder;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Kris\LaravelFormBuilder\Events\AfterFormCreation;

class FormBuilder
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var FormHelper
     */
    protected $formHelper;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @param Container  $container
     * @param FormHelper $formHelper
     */
    public function __construct(Container $container, FormHelper $formHelper, EventDispatcher $eventDispatcher)
    {
        $this->container = $container;
        $this->formHelper = $formHelper;
        $this->eventDispatcher = $eventDispatcher;
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
            ->addData($data)
            ->setRequest($this->container->make('request'))
            ->setFormHelper($this->formHelper)
            ->setEventDispatcher($this->eventDispatcher)
            ->setFormBuilder($this)
            ->setValidator($this->container->make('validator'))
            ->setFormOptions($options);

        $form->buildForm();

        $this->eventDispatcher->fire(new AfterFormCreation($form));

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
        $form = $this->container
            ->make('Kris\LaravelFormBuilder\Form')
            ->addData($data)
            ->setRequest($this->container->make('request'))
            ->setFormHelper($this->formHelper)
            ->setEventDispatcher($this->eventDispatcher)
            ->setFormBuilder($this)
            ->setValidator($this->container->make('validator'))
            ->setFormOptions($options);

        $this->eventDispatcher->fire(new AfterFormCreation($form));

        return $form;
    }
}
