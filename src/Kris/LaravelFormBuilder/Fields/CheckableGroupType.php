<?php

namespace Kris\LaravelFormBuilder\Fields;

class CheckableGroupType extends ChildFormType
{
    /**
     * @inheritdoc
     */
    protected function getTemplate()
    {
        return 'checkable_group';
    }

    /**
     * @inheritdoc
     */
    protected function createChildren()
    {
        parent::createChildren();

        // $child is radio or checkbox.
        foreach ($this->children as $key => $child) {

            $id = $child->name;

            // Set child ID to child name
            $child->options['attr']['id'] = $id;

            if ($child->type === 'radio') {
                // Set child label attribute "for" to child name.
                $child->options['label_attr']['for'] = $id;

                // Set the child name to this child form name,
                // because Radio's names must all be the same.
                $child->name = $this->name;
            }
        }
    }
}
