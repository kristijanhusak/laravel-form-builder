---
layout: page
title: "Field basics"
category: field
order: 1
date: 2015-06-03 13:11:09
---

They all share these default options (Most of these are configurable through config.php file. Check [Customization]({{ site.baseurl }}{% post_url 2015-05-24-configuration-basics %})

```php
<?php
$options = [
    'wrapper' => ['class' => 'form-group'],
    'attr' => ['class' => 'form-control'],
    'help_block' => [
        'text' => null,
        'tag' => 'p',
        'attr' => ['class' => 'help-block']
    ],
    'default_value' => null, // Fallback value if none provided by value property or model
    'label' => $this->name,  // Field name used
    'label_show' => true,
    'label_attr' => ['class' => 'control-label', 'for' => $this->name],
    'errors' => ['class' => 'text-danger'],
    'rules' => [],           // Validation rules
    'error_messages' => []   // Validation error messages
]
```

This is html demonstration of the options:

```html
<div class="form-group">
    <label for="field_name" class="control-label">Field Name</label>
    <input type="text" name="field_name" class="form-control">
    <?php if ($errors): ?>
        <div class="text-danger">Error message</div>
    <?php endif; ?>
</div>
```

Any of these can be overriden when adding a field:

```php
<?php
$this->add('field_name', 'text', [
    'attr' => ['class' => 'form-control field-input'],
    'label' => 'This is label for field name'
]);
```

Or when rendered in the view with `form_*` helper functions:

```html
form_row($form->field_name, ['label' => 'This label will be used']);
```
