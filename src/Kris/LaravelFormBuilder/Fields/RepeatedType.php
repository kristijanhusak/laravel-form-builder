<?php  namespace Kris\LaravelFormBuilder\Fields;

class RepeatedType extends ParentType
{

    /**
     * Get the template, can be config variable or view path
     *
     * @return string
     */
    protected function getTemplate()
    {
        return 'repeated';
    }

    protected function getDefaults()
    {
        return [
            'type' => 'password',
            'second_name' => null,
            'first_options' => ['label' => 'Password', 'is_child' => true],
            'second_options' => ['label' => 'Password confirmation', 'is_child' => true]
        ];
    }

    protected function createChildren()
    {
        $firstName = $this->getOption('real_name', $this->name);
        $secondName = $this->getOption('second_name');

        if (is_null($secondName)) {
            $secondName = $this->getOption('real_name', $this->name).'_confirmation';
        }

        $form = $this->parent->getFormBuilder()->plain([
            'name' => $this->parent->getName()
        ])
        ->add($firstName, $this->getOption('type'), $this->getOption('first_options'))
        ->add($secondName, $this->getOption('type'), $this->getOption('second_options'));

        $this->children['first'] = $form->getField($firstName);
        $this->children['second'] = $form->getField($secondName);
    }
}
