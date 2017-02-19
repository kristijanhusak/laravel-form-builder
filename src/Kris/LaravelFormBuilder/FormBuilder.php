<?php

namespace Kris\LaravelFormBuilder;

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
     * @var string
     */
    protected $plainFormClass = Form::class;

    /**
     * @param Container  $container
     * @param FormHelper $formHelper
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(Container $container, FormHelper $formHelper, EventDispatcher $eventDispatcher)
    {
        $this->container = $container;
        $this->formHelper = $formHelper;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Create a Form instance.
     *
     * @param string $formClass The name of the class that inherits \Kris\LaravelFormBuilder\Form.
     * @param array $options|null
     * @param array $data|null
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
     * Get the namespace from the config.
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
     * Get the plain form class.
     *
     * @return string
     */
    public function getFormClass() {
        return $this->plainFormClass;
    }

    /**
     * Set the plain form class.
     *
     * @param string $class
     */
    public function setFormClass($class) {
        $parent = Form::class;
        if (!is_a($class, $parent, true)) {
            throw new \InvalidArgumentException("Class must be or extend $parent; $class is not.");
        }

        $this->plainFormClass = $class;
    }

    /**
     * Get instance of the empty form which can be modified.
     *
     * @param array $options
     * @param array $data
     * @return \Kris\LaravelFormBuilder\Form
     */
    public function plain(array $options = [], array $data = [])
    {
        $form = $this->container
            ->make($this->plainFormClass)
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
