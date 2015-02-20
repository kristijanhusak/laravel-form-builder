<?php  namespace Kris\LaravelFormBuilder\Fields;

use Kris\LaravelFormBuilder\Form;

trait BuildChildFormTrait
{
    /**
     * @return Form
     * @throws \Exception
     */
    protected function getClassFromOptions()
    {
        $class = array_get($this->options, 'class');

        if (!$class) {
            throw new \InvalidArgumentException(
                'Please provide full name or instance of Form class.'
            );
        }

        if (is_string($class)) {
            return $this->parent->getFormBuilder()->create(
                $class,
                $this->getOption('formOptions'),
                $this->getOption('data')
            );
        }

        if ($class instanceof Form) {
            return $class;
        }

        throw new \InvalidArgumentException(
            'Class provided does not exist or it passed in wrong format.'
        );

    }
}