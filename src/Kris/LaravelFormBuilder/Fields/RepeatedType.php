<?php

namespace Kris\LaravelFormBuilder\Fields;

use Illuminate\Support\Arr;

class RepeatedType extends ParentType
{

    /**
     * Get the template, can be config variable or view path.
     *
     * @return string
     */
    protected function getTemplate()
    {
        return 'repeated';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaults()
    {
        return [
            'type' => 'password',
            'second_name' => null,
            'first_options' => ['label' => 'Password'],
            'second_options' => ['label' => 'Password confirmation']
        ];
    }

    /**
     * @inheritdoc
     */
    public function getAllAttributes()
    {
        // Collect all children's attributes.
        return $this->parent->getFormHelper()->mergeAttributes($this->children);
    }

    /**
     * @inheritdoc
     */
    protected function createChildren()
    {
        $this->prepareOptions();

        $firstName = $this->getRealName();
        $secondName = $this->getOption('second_name');

        if (is_null($secondName)) {
            $secondName = $firstName.'_confirmation';
        }

        // merge field rules and first field rules
        $firstOptions = $this->getOption('first_options');
        $firstOptions['rules'] = $this->normalizeRules(Arr::pull($firstOptions, 'rules', []));
        if ($mainRules = $this->getOption('rules')) {
            $firstOptions['rules'] = $this->mergeRules($mainRules, $firstOptions['rules']);
        }

        $sameRule = 'same:' . $secondName;
        if (!in_array($sameRule, $firstOptions['rules'])) {
            $firstOptions['rules'][] = $sameRule;
        }

        $form = $this->parent->getFormBuilder()->plain([
            'name' => $this->parent->getName(),
            'model' => $this->parent->getModel()
        ])
        ->add($firstName, $this->getOption('type'), $firstOptions)
        ->add($secondName, $this->getOption('type'), $this->getOption('second_options'));

        $this->children['first'] = $form->getField($firstName);
        $this->children['second'] = $form->getField($secondName);
    }

}
