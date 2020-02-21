---
layout: page
title: "View helpers"
category: form
order: 5
date: 2015-06-03 14:12:08
---

Rendering forms in the views are done by form function helpers. Here's the complete list:

* [form](#form)
* [form_start](#form_start)
* [form_rest](#form_rest)
* [form_end](#form_end)
* [form_row](#form_row)
* [form_label](#form_label)
* [form_widget](#form_widget)
* [form_error](#form_error)
* [form_until](#form_until)

Example form class:

```php
<?php namespace App\Forms;

use Kris\LaravelFormBuilder\Form

class PostForm extends Form
{
    public function buildForm()
    {
        $this->add('title')
            ->add('body')
            ->add('submit', 'submit');
    }
}
```

### form
This renders the complete form.

```
form($form, $formOptions = []);
```

```html
<form action="/" method="GET">
    <input type="hidden" name="_token" value="randomstring">
    <div class="form-group">
        <label for="title">Title</label>
        <input type="text" name="title" id="title" class="form-control">
    </div>
    <div class="form-group">
        <label for="body">Body</label>
        <textarea id="body" name="body" class="form-control"></textarea>
    </div>
    <button type="submit">Submit</button>
</form>
```

### form_start

This renders the start tag of the form (In Laravel's Form builder `Form::open()`):

```
form_start($form, $formOptions = []);
```

```html
<form action="/" method="GET">
<input type="hidden" name="_token" value="randomstring">
```

### form_rest

This renders the rest of the fields that are not already rendered:

```
form_start($form)
form_row($form->body)
<h1>Other fields</h1>
form_rest($form)
```

```html
<form action="/" method="GET">
    <input type="hidden" name="_token" value="randomstring">
    <div class="form-group">
        <label for="body">Body</label>
        <textarea id="body" name="body" class="form-control"></textarea>
    </div>
    <div class="form-group">
        <label for="title">Title</label>
        <input type="text" name="title" id="title" class="form-control">
    </div>
    <h1>Other fields</h1>
    <button type="submit">Submit</button>
<!-- Note the missing </form>, form_end is required -->
```

### form_end

This renders the rest of the fields(can be changed) and closing form tag

**Default**:

```
form_start($form)
form_row($form->title)
<h1>Other fields</h1>
form_end($form, $renderRest = true);
```

```html
<form action="/" method="GET">
    <input type="hidden" name="_token" value="randomstring">
    <div class="form-group">
        <label for="title">Title</label>
        <input type="text" name="title" id="title" class="form-control">
    </div>
    <h1>Other fields</h1>
    <div class="form-group">
        <label for="body">Body</label>
        <textarea id="body" name="body" class="form-control"></textarea>
    </div>
    <button type="submit">Submit</button>
</form>
```

If you **do not want rest of the fields**, pass false as 2nd argument:


```
form_start($form)
form_row($form->title)
<h1>Other fields</h1>
form_end($form, false);
```

```html
<form action="/" method="GET">
    <input type="hidden" name="_token" value="randomstring">
    <div class="form-group">
        <label for="title">Title</label>
        <input type="text" name="title" id="title" class="form-control">
    </div>
    <h1>Other fields</h1>
</form>
```

### form_row

Renders single field:

```
form_row($form->title, $options = ['attr' => ['class' => 'some-input']]);
```

```html
<div class="form-group">
    <label for="title">Title</label>
    <input type="text" name="title" id="title" class="some-input">
    <!-- If help_block option is set -->
    <div class="help-block">This is help text.</div>

    <!-- only if validation errors exists -->
    <div class="text-danger">This field is required.</div>
</div>
```

### form_label

Renders only the label of the field

```
form_label($form->title);
```

```html
<label for="title">Title</label>
```

### form_widget

Renders only the field element

```
form_widget($form->title);
```

```html
<input type="text" name="title" id="title" class="some-input">
```

### form_error

Renders only the field errors (If there are any)

```
form_error($form->title);
```

```html
<!-- only if validation errors exists -->
<div class="text-danger">This field is required.</div>
```

### form_until

Renders the form until the provided field.

```
form_start($form);
form_until($form, 'submit');
form_end($form, false);
```

```html
<form action="/" method="GET">
    <input type="hidden" name="_token" value="randomstring">
    <div class="form-group">
        <label for="title">Title</label>
        <input type="text" name="title" id="title" class="form-control">
    </div>
    <div class="form-group">
        <label for="body">Body</label>
        <textarea id="body" name="body" class="form-control"></textarea>
    </div>
</form>
```
