<?php  namespace Kris\LaravelFormBuilder\Fields;

use Kris\LaravelFormBuilder\Form;

abstract class ParentType extends FormField
{

    /**
     * @var FormField[]
     */
    protected $children;

    /**
     * Populate children array
     *
     * @return mixed
     */
    abstract protected function createChildren();

    /**
     * @param       $name
     * @param       $type
     * @param Form  $parent
     * @param array $options
     */
    public function __construct($name, $type, Form $parent, array $options = [])
    {
        parent::__construct($name, $type, $parent, $options);
        $this->createChildren();
        $this->checkIfFileType();
    }

    /**
     * @param array $options
     * @param bool  $showLabel
     * @param bool  $showField
     * @param bool  $showError
     * @return string
     */
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

    /**
     * Get a child of the choice field
     *
     * @return mixed
     */
    public function getChild($key)
    {
        return array_get($this->children, $key);
    }

    /**
     * @inheritdoc
     */
    public function isRendered()
    {
        foreach ($this->children as $key => $child) {
            if ($child->isRendered()) {
                return true;
            }
        }

        return parent::isRendered();
    }

    /**
     * Rebuild the children array
     *
     * @return mixed
     */
    protected function rebuild()
    {
        $this->children = [];
        $this->createChildren();

        return $this;
    }

    /**
     * Get child dynamically
     *
     * @param $name
     * @return FormField
     */
    public function __get($name)
    {
        return $this->getChild($name);
    }

    /**
     * Check if field has type property and if it's file add enctype/multipart to form
     */
    protected function checkIfFileType()
    {
        if ($this->getOption('type') === 'file') {
            $this->parent->setFormOption('files', true);
        }
    }
}
