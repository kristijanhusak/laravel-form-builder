<?php  namespace Kris\LaravelFormBuilder\Fields;

use Kris\LaravelFormBuilder\Form;

abstract class ParentType extends FormField
{

    /**
     * @var mixed
     */
    protected $children;

    abstract protected function createChildren();

    public function __construct($name, $type, Form $parent, array $options = [])
    {
        parent::__construct($name, $type, $parent, $options);
        $this->createChildren();
    }

    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true)
    {
        $options['children'] = $this->children;

        return parent::render($options, $showLabel, $showField, $showError);
    }

    /**
     * Get all children of the choice field
     *
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }
}
