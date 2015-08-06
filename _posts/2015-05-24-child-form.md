---
layout: page
title: "Child form"
category: field
order: 7
date: 2015-05-24 20:52:52
---

You can add one form as a child in another form. This will render all fields from that child form and wrap them in name provided:

``` php
<?php
class PostForm
{
    public function buildForm()
    {
        $this
            ->add('title', 'text')
            ->add('body', 'textarea');
    }
}

class GenderForm
{
    public function buildForm()
    {
        $this
            ->add('gender', 'select', [
                'choices' => $this->getData('genders')
            ]);
    }
}

class SongForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('name', 'text')
            ->add('gender', 'form', [
                'class' => 'App\Forms\GenderForm',
                'formOptions' => []
                // Passed to gender form as data,
                'data' => ['genders' => ['m' => 'Male', 'f' => 'Female']]
            ])
            ->add('post', 'form', [
                'class' => $this->formBuilder->create('App\Forms\PostForm')
            ])
            ->add('lyrics', 'textarea');
    }
}
```

This renders:
```html
    <div class="form-group">
        <label for="name" class="control-label">name</label>
        <input type="text" name="name" id="name">
    </div>
    <div class="form-group">
        <label for="gender[gender]" class="control-label">Gender</label>
        <select id="gender[gender]" name="gender[gender]">
            <option value="m">Male</option>
            <option value="f">Female</option>
        </select>
    </div>
    <div class="form-group">
        <label for="post[title]" class="control-label">Title</label>
        <input type="text" name="post[title]" id="post[title]">
    </div>
    <div class="form-group">
        <label for="post[body]" class="control-label">Body</label>
        <textarea id="post[body]" name="post[body]"></textarea>
    </div>
```

Beside inherited, there are some additional options available:

1. `class` (String|Form) (Default: null) - Full path to the Form class or Form instance.
2. `formOptions` (Array) (Default: `[]`) - Options that will be passed to the Form if class passed as string
3. `data` (Array) (Default: `[]`) - Data to be added to the form

Each of these represent the parameters for the `FormBuilder::create()` method:

```php
<?php
FormBuilder::create($class, $formOptions, $data);
```
