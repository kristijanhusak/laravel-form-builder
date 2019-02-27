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
     * @param Container $container
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
     * Fire an event.
     *
     * @param object $event
     * @return array|null
     */
    public function fireEvent($event)
    {
        return $this->eventDispatcher->dispatch($event);
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

        $form = $this->setDependenciesAndOptions($this->container->make($class), $options, $data);

        $form->buildForm();

        $this->eventDispatcher->dispatch(new AfterFormCreation($form));

        $form->filterFields();

        return $form;
    }

    /**
     * @param $items
     * @param array $options
     * @param array $data
     * @return mixed
     */
    public function createByArray($items, array $options = [], array $data = [])
    {
        $form = $this->setDependenciesAndOptions(
            $this->container->make($this->plainFormClass),
            $options,
            $data
        );

        $this->buildFormByArray($form, $items);

        $this->eventDispatcher->dispatch(new AfterFormCreation($form));

        $form->filterFields();

        return $form;
    }

    /**
     * @param $form
     * @param $items
     */
    public function buildFormByArray($form, $items)
    {
        foreach ($items as $item) {
            if (!isset($item['name'])) {
                throw new \InvalidArgumentException(
                    'Name is not set in form array.'
                );
            }
            $name = $item['name'];
            $type = isset($item['type']) && $item['type'] ? $item['type'] : '';
            $modify = isset($item['modify']) && $item['modify'] ? $item['modify'] : false;
            unset($item['name']);
            unset($item['type']);
            unset($item['modify']);
            $form->add($name, $type, $item, $modify);
        }
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
        $form = $this->setDependenciesAndOptions(
            $this->container->make($this->plainFormClass),
            $options,
            $data
        );

        $this->eventDispatcher->dispatch(new AfterFormCreation($form));

        $form->filterFields();

        return $form;
    }

    /**
     * Set depedencies and options on existing form instance
     *
     * @param \Kris\LaravelFormBuilder\Form $instance
     * @param array $options
     * @param array $data
     * @return \Kris\LaravelFormBuilder\Form
     */
    public function setDependenciesAndOptions($instance, array $options = [], array $data = [])
    {
        return $instance
            ->addData($data)
            ->setRequest($this->container->make('request'))
            ->setFormHelper($this->formHelper)
            ->setEventDispatcher($this->eventDispatcher)
            ->setFormBuilder($this)
            ->setValidator($this->container->make('validator'))
            ->setFormOptions($options);
    }
}
