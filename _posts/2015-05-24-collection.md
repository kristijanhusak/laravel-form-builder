---
layout: page
title: "Collection"
category: field
order: 8
date: 2015-05-24 20:52:57
---

* [Collection of child forms](#collection-of-child-forms)
* [Prototype](#prototype)
* [Options](#options)
* [Validated collection items (**read if it saves too few items**)](#validated-collection-items)

Collections are used for working with array of data, mostly used for relationships (OneToMany, ManyToMany).

It can be any type that is available in the package. Here are some examples:

```php
<?php
use Kris\LaravelFormBuilder\Form;

class PostForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('title', 'text')
            ->add('body', 'textarea')
            ->add('tags', 'collection', [
                'type' => 'text',
                'property' => 'name',    // Which property to use on the tags model for value, defualts to id
                'data' => [],            // Data is automatically bound from model, here we can override it
                'options' => [    // these are options for a single type
                    'label' => false,
                    'attr' => ['class' => 'tag']
                ]
            ]);
    }
}
```

And in controller:

```php
<?php

class MainController extends Controller
{
    public function edit($id, FormBuilder $formBuilder)
    {
        $post = Post::findOrFail($id);
        // Post model contains this data
        // $post = [
        //     'id' => 1,
        //     'title' => 'lorem ipsum',
        //     'body' => 'dolor sit'
        //     'tags' => [
        //         ['id' => 1, 'name' => 'work', 'desc' => 'For work'],
        //         ['id' => 2, 'name' => 'personal', 'desc' => 'For personal usage']
        //     ]
        // ]

        // Collection field type will automatically pull tags data from the model,
        // If we want to override the data, we can pass `data` option to the field

        $form = $formBuilder->create('App\Forms\PostForm', [
            'model' => $post
        ]);

        return view('posts.edit', compact('form'));
    }
}
```

Then the view will contain:

```html
<form method="POST" action="/post/1">
    <div class="form-group">
        <label for="title" class="control-label">Title</label>
        <input type="text" id="title" class="form-control" name="title" value="lorem ipsum">
    </div>
    <div class="form-group">
        <label for="body" class="control-label">Body</label>
        <textarea id="body" name="body">dolor sit</textarea>
    </div>
    <div class="form-group">
        <label for="tags" class="control-label">Tags</label>
        <div class="form-group">
            <input type="text" id="tags[0]" class="tag" name="tags[0]" value="work">
        </div>
        <div class="form-group">
            <input type="text" id="tags[1]" class="tag" name="tags[1]" value="personal">
        </div>
    </div>
</form>
```

#### Collection of child forms

Child forms can be also used in a collection:

```php

<?php
use Kris\LaravelFormBuilder\Form;

class TagsForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('name', 'text')
            ->add('desc', 'textarea');
    }
}

class PostForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('title', 'text')
            ->add('body', 'textarea')
            ->add('tags', 'collection', [
                'type' => 'form',
                'options' => [    // these are options for a single type
                    'class' => 'App\Forms\TagsForm'
                    'label' => false,
                ]
            ]);
    }
}
```

And with same controller setup as above, we get this:

```html
<form method="POST" action="/post/1">
    <div class="form-group">
        <label for="title" class="control-label">Title</label>
        <input type="text" id="title" class="form-control" name="title" value="lorem ipsum">
    </div>
    <div class="form-group">
        <label for="body" class="control-label">Body</label>
        <textarea id="body" name="body" class="form-control">dolor sit</textarea>
    </div>
    <div class="form-group">
        <label for="tags" class="control-label">Tags</label>
        <div class="form-group">
            <div class="form-group">
                <label for="tags[0][name]">Name</label>
                <input type="text" id="tags[0][name]" class="form-control" name="tags[0][name]" value="work">
            </div>
            <div class="form-group">
                <label for="tags[0][desc]">Desc</label>
                <textarea id="tags[0][desc]" name="tags[0][desc]" class="form-control">For work</textarea>
            </div>
        </div>
        <div class="form-group">
            <div class="form-group">
                <label for="tags[1][name]">Name</label>
                <input type="text" id="tags[1][name]" class="form-control" name="tags[1][name]" value="personal">
            </div>
            <div class="form-group">
                <label for="tags[1][desc]">Desc</label>
                <textarea id="tags[1][desc]" name="tags[1][desc]" class="form-control">For personal usage</textarea>
            </div>
        </div>
    </div>
```

#### Prototype

If you need to dynamically generate HTML for additional elements in the collection, you can use `prototype()` method on the form field. Let's use example above:

```html
@extends('app')

@section('content')
    {!! form_start($form) !!}
    <div class="collection-container" data-prototype="{% raw %}{{ form_row($form->tags->prototype()){% endraw %} }}"> // Use {% raw %}{{ }}{% endraw %} here to escape html
        {!! form_row($form->tags) !!}
    </div>
    {!! form_end($form) !!}
    <button type="button" class="add-to-collection">Add to collection</button>
    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.add-to-collection').on('click', function(e) {
                e.preventDefault();
                var container = $('.collection-container');
                var count = container.children().length;
                var proto = container.data('prototype').replace(/__NAME__/g, count);
                container.append(proto);
            });
        });
    </script>
@endsection
```

`data-prototype` will contain:

```html
<div class="form-group">
    <div class="form-group">
        <label for="tags[__NAME__][name]">Name</label>
        <input type="text" id="tags[__NAME__][name]" class="form-control" name="tags[__NAME__][name]">
    </div>
    <div class="form-group">
        <label for="tags[__NAME__][desc]">Desc</label>
        <textarea id="tags[__NAME__][desc]" class="form-control" name="tags[__NAME__][desc]"></textarea>
    </div>
</div>
```

And clicking on the button `.add-to-collection` will automatically generate proper html from the prototype.

Prototype can be configured in the form class:

```php
use Kris\LaravelFormBuilder\Form;

class PostForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('title', 'text')
            ->add('body', 'textarea')
            ->add('tags', 'collection', [
                'type' => 'text',
                'property' => 'name',
                'prototype' => true,            // Should prototype be generated. Default: true
                'prototype_name' => '__NAME__' // Value used for replacing when generating new elements from prototype, default: __NAME__
                'options' => [
                    'label' => false,
                    'attr' => ['class' => 'tag']
                ]
            ]);
    }
}
```

### Options

Beside inherited, there are some additional options available:

- `type` (String) (Default: null) - Type of the collection item
- `options` (Array) (Default: `['is_child' => true]`) - Options that will be used for the collection item
- `data` (Array) (Default: `[]`) - This is the `value` for the collection
- `property` (String) (Default: `id`) - Property to be used when pulling data from model
- `prototype` (Boolean) (Default: `true`) - Should [prototype](#prototype) be generated
- `prototype_name` (String) (Default: `__NAME__`) - Namespace in the prototype that is generated
- `empty_row` (Boolean) (Default: `true`) - Add an empty row if no data provided
- `prefer_input` (Boolean) (Default: `false`) - Uses POST input items for validation instead of configured model data items

### Validated collection items

The POST request only validates items that it knows from the configured model. If the GET request (form display) builds a form with data with 4 items, and the POST request (form validation) builds a form without data, only 1 collection item will be accepted and validated, because the form doesn't know there are 4.

Always build your form in POST **exactly** like you built it in GET! Same model, same data, same buttons etc.

Some forms let the user add more rows with Javascript (the `prototype`). In that case, you should set `prefer_input = true`, because the POST data will contain items unknown to the built form. In most cases, that is not necessary and not smart.

Simple example of bad and good POST action:

```php
// GET request: show form with all items
public function edit(Post $post, FormBuilder $formBuilder)
    $form = $formBuilder->create('App\Forms\PostForm', [
        'model' => $post
    ]);
}

// BAD
public function store(Post $post, FormBuilder $formBuilder)
    // BAD: Form built without data!
    $form = $formBuilder->create('App\Forms\PostForm');
    $form->redirectIfNotValid();
    
    // BAD RESULT: only 1 collection item
    $values = $form->getFieldValues();
}

// GOOD
public function store(Post $post, FormBuilder $formBuilder)
    // GOOD: Form built exactly like in edit()
    $form = $formBuilder->create('App\Forms\PostForm', [
        'model' => $post
    ]);
    $form->redirectIfNotValid();
    
    // GOOD RESULT: all collection items, exactly like in the form HTML in edit()
    $values = $form->getFieldValues();
}
```
