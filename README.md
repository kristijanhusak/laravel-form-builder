[![Build Status](https://img.shields.io/travis/kristijanhusak/laravel-form-builder/master.svg?style=flat)](https://travis-ci.org/kristijanhusak/laravel-form-builder)
[![Coverage Status](http://img.shields.io/scrutinizer/coverage/g/kristijanhusak/laravel-form-builder.svg?style=flat)](https://scrutinizer-ci.com/g/kristijanhusak/laravel-form-builder/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/kris/laravel-form-builder.svg?style=flat)](https://packagist.org/packages/kris/laravel-form-builder)
[![Latest Stable Version](https://img.shields.io/packagist/v/kris/laravel-form-builder.svg?style=flat)](https://packagist.org/packages/kris/laravel-form-builder)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE)

# Laravel 5 form builder

Form builder for Laravel 5 inspired by Symfony's form builder. With help of Laravels FormBuilder class creates forms that can be easy modified and reused.
By default it supports Bootstrap 3.

## Laravel 4
For laravel 4 version check [laravel4-form-builder](https://github.com/kristijanhusak/laravel4-form-builder)


## Changelog
Changelog can be found [here](https://github.com/kristijanhusak/laravel-form-builder/blob/master/CHANGELOG.md)

## Table of contents
1. [Installation](#installation)
2. [Basic usage](#usage)
  1. [Usage in controllers](#usage-in-controllers)
  2. [Usage in views](#usage-in-views)
3. [Plain form](#plain-form)
4. [Child form](#child-form)
5. [Named form](#named-form)
6. [Collection](#collection)
  1. [Collection of child forms](#collection-of-child-forms)
  2. [Prototype](#prototype)
7. [Field customization](#field-customization)
8. [Changing configuration and templates](#changing-configuration-and-templates)
9. [Custom fields](#custom-fields)
10. [Contributing](#contributing)
11. [Issues and bug reporting](#issues-and-bug-reporting)

###Installation

``` json
{
    "require": {
        "kris/laravel-form-builder": "1.5.*"
    }
}
```

run `composer update`

Then add Service provider to `config/app.php`

``` php
    'providers' => [
        // ...
        'Kris\LaravelFormBuilder\FormBuilderServiceProvider'
    ]
```

And Facade (also in `config/app.php`)

``` php
    'aliases' => [
        // ...
        'FormBuilder' => 'Kris\LaravelFormBuilder\Facades\FormBuilder'
    ]

```

**Notice**: This package will add `illuminate/html` package and load Aliases (Form, Html) if they do not exist in the IoC container

### Basic usage

Creating form classes is easy. With a simple artisan command:

``` sh
    php artisan make:form Forms/PostForm
```

you create form class in path `app/Forms/PostForm.php` that looks like this:

``` php
<?php namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class PostForm extends Form
{
    public function buildForm()
    {
        // Add fields here...
    }
}
```

You can add fields which you want when creating command like this:

``` sh
php artisan make:form Forms/SongForm --fields="name:text, lyrics:textarea, publish:checkbox"
```

And that will create form in path `app/Forms/SongForm.php` with content:

``` php
<?php namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class SongForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('name', 'text')
            ->add('lyrics', 'textarea')
            ->add('publish', 'checkbox');
    }
}
```

#### Usage in controllers

Forms can be used in controller like this:

``` php
<?php namespace App/Http/Controllers;

use Illuminate\Routing\Controller;
use Kris\LaravelFormBuilder\FormBuilder;

class SongsController extends BaseController {

    public function create(FormBuilder $formBuilder)
    {
        $form = $formBuilder->create('App\Forms\SongForm', [
            'method' => 'POST',
            'url' => route('song.store')
        ]);

        return view('song.create', compact('form'));
    }

    public function store()
    {
    }
}
```

#### Usage in views

From controller they can be used in views like this:

``` html
<!-- resources/views/song/create.blade.php -->

@extend('layouts.master')

@section('content')
    {!! form($form) !!}
@endsection
```

`{!! form($form) !!}` Will generate this html:

``` html
<form method="POST" action="http://example.dev/songs">
    <input name="_token" type="hidden" value="FaHZmwcnaOeaJzVdyp4Ml8B6l1N1DLUDsZmsjRFL">
    <div class="form-group">
        <label for="name" class="control-label">name</label>
        <input type="text" class="form-control" id="name">
    </div>
    <div class="form-group">
        <label for="lyrics" class="control-label">lyrics</label>
        <textarea name="lyrics" class="form-control"></textarea>
    </div>
    <div class="form-group">
        <label for="publish" class="control-label">publish</label>
        <input type="checkbox" name="publish" id="publish">
    </div>
</form>
```

There are several helper methods that can help you customize your rendering:


``` html
<!-- This function: -->

{!! form_row($form->lyrics, ['attr' => ['class' => 'big-textarea']]) !!}

<!-- Renders this: -->

<div class="form-group">
    <label for="lyrics" class="control-label">lyrics</label>
    <textarea name="lyrics" id="lyrics" class="big-textarea"></textarea>
</div>
```

You can also split it even more:
``` html
{!! form_start($form) !!}
<form method="POST" action="http://example.dev/songs">

{!! form_label($form->publish) !!}
<label for="publish" class="control-label">publish</label>

{!! form_widget($form->publish, ['checked' => true]) !!}
<input type="checkbox" name="publish" checked="checked">

{!! form_errors($form->publish) !!}
<div class="text-danger">This field is required.</div> <!-- Rendered only if validation errors occur. -->

{!! form_rest($form) !!}
<!--
Renders the rest of the form WITHOUT closing tag.
If you want to render a closing tag also, use
form_end($form)
-->
<div class="form-group">
    <label for="name" class="control-label">name</label>
    <input type="text" class="form-control" id="name">
</div>
<div class="form-group">
    <label for="publish" class="control-label">publish</label>
    <input type="text" name="publish" id="publish">
</div>

<!--
If form_rest($form) is not called before this,
it will render the rest of the form and add
form close tag (</form>). If form_rest($form) IS
called, it will render only closing tag (</form>)

If you want to render only the closing tag, even
if form_rest($form) is not called, pass false as
second param (form_end($form, false))
-->
{!! form_end($form) !!}
</form>

```
### Plain form

If you need to quick create a small form that does not to be reused, you can use `plain` method:

``` php
<?php namespace App/Http/Controllers;

use Illuminate\Routing\Controller;

class AuthController extends BaseController {

    public function login()
    {
        $form = \FormBuilder::plain([
            'method' => 'POST',
            'url' => route('login')
        ])->add('username', 'text')->add('password', 'password')->add('login', 'submit');

        return view('auth.login', compact('form'));
    }

    public function postLogin()
    {
    }
}
```

### Child form
You can add one form as a child in another form. This will render all fields from that child form and wrap them in name provided:

``` php

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
                // Passed to gender form as data (same as calling addData($data) method),
                // works only if class is passed as string
                'data' => ['genders' => ['m' => 'Male', 'f' => 'Female']]
            ])
            ->add('song', 'form', [
                'class' => $this->formBuilder->create('App\Forms\PostForm')
            ])
            ->add('lyrics', 'textarea');
    }
}
```
So now song form will render this:
```html
    <div class="form-group">
        <label for="name" class="control-label">name</label>
        <input type="text" name="name" id="name">
    </div>
    <div class="form-group">
        <label for="song[title]" class="control-label">title</label>
        <input type="text" name="song[title]" id="song[title]">
    </div>
    <div class="form-group">
        <label for="song[body]" class="control-label">body</label>
        <textarea name="song[body]" id="song[body]"></textarea>
    </div>
    <div class="form-group">
        <label for="lyrics" class="control-label">textarea</label>
        <textarea name="lyrics" id="lyrics"></textarea>
    </div>
```

### Named form
Named forms are very similar to child forms, only difference is that they are used as standalone forms.

```php
class PostForm
{
    // Can be changed when creating a form
    protected $name = 'post';

    public function buildForm()
    {
        $this
            ->add('title', 'text', [
                'label' => 'Post title'
            ])
            ->add('body', 'textarea', [
                'label' => 'Post body'
            ]);
    }
}

class PostController {
    public function createAction()
    {
        $form = \FormBuilder::create('App\Forms\PostForm');

        // Can be set from here in 2 ways:
        // This allows flexibility to use only when needed
        // 1. way:
        $form = \FormBuilder::create('App\Forms\PostForm', [
            'name' => 'post'
        ]);

        // 2. way;
        $form = \FormBuilder::create('App\Forms\PostForm')->setName('post');
    }
}

// View
<div class="form-group">
    <label for="title" class="control-label">Post title</label>
    <textarea name="post[title]" id="title"></textarea>
</div>
<div class="form-group">
    <label for="body" class="control-label">Post body</label>
    <textarea name="post[body]" id="body"></textarea>
</div>
```

### Collection
Collections are used for working with array of data, mostly used for relationships (OneToMany, ManyToMany).

It can be any type that is available in the package. Here are some examples:

``` php
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

[Child form](#child-form) also can be used as a collection.

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
    <div class="collection-container" data-prototype="{{ form_row($form->tags->prototype()) }}"> // Use {{ }} here to escape html
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

### Field Customization
Fields can be easily customized within the class or view:

``` php
<?php namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class PostForm extends Form
{
    /**
     * By default validation error for each field is
     * shown under it. If you want to totally disable
     * showing those errors, set this to false
     */
    protected $showFieldErrors = true;

    public function buildForm()
    {
        $this
            ->add('name', 'text', [
                'wrapper' => [
                  'class' => 'name-input-container'
                ],
                'required' => true, // Adds `required` class to label, and required attribute to field
                'help_block' => [
                    'text' => 'I am help text',  // If text is set, automatically adds help text under the field. Default: null
                    'tag' => 'p'    // this is default,
                    'attr' => ['class' => 'help-block']    // Default, class pulled from config file
                ]
                'attr' => ['class' => 'input-name', 'placeholder' => 'Enter name here...'],
                'label' => 'Full name'
            ])
            ->add('bio', 'textarea', [
                'wrapper' => false      // This disables the wrapper for this field
            ])
            // This creates a select field
            ->add('subscription', 'choice', [
                'choices' => ['monthly' => 'Monthly', 'yearly' => 'Yearly'],
                'empty_value' => '==== Select subscription ===',
                'multiple' => false // This is default. If set to true, it creates select with multiple select posibility
            ])
            ->add('categories', 'entity', [
                'class' => 'App\Category', // Entity that holds data
                'property' => 'name', // Value that will be used as a label for each choice option, default: name
                'property_key' => 'id', // Value that will be used as a value for each choice option, default: id
                'query_builder' => function(App\Category $category) {   // If provided, gets data from this closure and lists it
                    return $category->where('active', 1);
                }
            ])
            // This creates radio buttons
            ->add('gender', 'choice', [
                'label' => false,    // This forces hiding label, even when calling form_row
                'choices' => ['m' => 'Male', 'f' => 'Female'],
                'selected' => 'm',
                'expanded' => true,
                'choice_options' => [ // Handles options when expanded is true and/or multiple is true
                    'wrapper' => ['class' => 'choice-wrapper'] // Shows the wrapper for each radio or checkbox, default is false
                ]
            ])
            // Static text, holds only text, no input
            ->add('address', 'static', [
                'tag' => 'div' // Tag to be used for holding static data,
                'attr' => ['class' => 'form-control-static'], // This is the default
                'default_value' => null // If nothing is passed, data is pulled from model if any
            ])
            // Automatically adds enctype="multipart/form-data" to form
            ->add('image', 'file', [
                'label' => 'Upload your image'
            ])
            // This creates a checkbox list
            ->add('languages', 'choice', [
                'choices' => [['id' => 1, 'en' => 'English'], ['id' => 2, 'de' => 'German'], ['id' => 3, 'fr' => 'France']],
                'selected' => function ($data) { // Allows handling data before passed to view for setting default values. Useful for related models
                    return array_pluck($data, 'id');
                }
                'expanded' => true,
                'multiple' => true
            ])
            // Renders all fieds from song form and wraps names for better handling
            // <input type="text" name="song-title"> becomes <input type="text" name="song[song-title]">
            ->add('song', 'form', [
                'class' => $this->formBuilder->create('App\Forms\SongForm')
            ])
            ->add('policy-agree', 'checkbox', [
                'default_value' => 1,    //  <input type="checkbox" value="1">
                'label' => 'I agree to policy',
                'checked' => false    // This is the default.
            ])
            // Creates 2 inputs. These are the defaults
            ->add('password', 'repeated', [
                'type' => 'password'    // can be anything that fits <input type="type-here">
                'second_name' => 'password_confirmation', // defaults to name_confirmation
                'first_options' => [],   // Same options available as for text type
                'second_options' => [],   // Same options available as for text type
            ])
            ->add('save', 'submit', [
                'attr' => ['class' => 'btn btn-primary']
            ])
            ->add('clear', 'reset', [
                'label' => 'Clear the form',
                'attr' => ['class' => 'btn btn-danger']
            ]);
    }
}
```

You can also remove fields from the form when neccessary. For example you don't want to show `clear` button and `subscription` fields on the example above on edit page:

``` php
<?php namespace App/Http/Controllers;

use App\Forms\PostForm;

class PostsController extends BaseController {

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $form = \FormBuilder::create(PostForm::class, [
            'method' => 'PUT',
            'url' => route('posts.update', $id),
            'model' => $post
        ])
        ->remove('clear')
        ->remove('subscription');

        return view('posts.edit', compact('form'));
    }

    public function update($id)
    {
    }
}
```

Or you can modify it in the similar way (options passed will be merged with options from old field,
if you want to overwrite it pass 4th parameter as `true`)

``` php
    // ...
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $form = \FormBuilder::create(PostForm::class, [
            'method' => 'PUT',
            'url' => route('posts.update', $id),
            'model' => $post,
        ])
        // If passed name does not exist, add() method will be called with provided params
        ->modify('gender', 'select', [
            'attr' => ['class' => 'form-select']
        ], false)   // If this is set to true, options will be overwritten - default: false

        return view('posts.edit', compact('form'));
    }
```

In a case when `choice` type has `expanded` set to `true` and/or `multiple` also set to true, you get a list of
radios/checkboxes:

``` html
<div class="form-group">
    <label for="languages" class="control-label">languages</label>

    <label for="France_fr">France</label>
    <input id="France_fr" name="languages[]" type="checkbox" value="fr">

    <label for="English_en">English</label>
    <input id="English_en" name="languages[]" type="checkbox" value="en">

    <label for="German_de">German</label>
    <input id="German_de" name="languages[]" type="checkbox" value="de">
</div>
```

If you maybe want to customize how each radio/checkbox is rendered, maybe wrap it in some container, you can loop over children on `languages` choice field:

``` php
    // ...

    <?php foreach($form->languages->getChildren() as $child): ?>
        <div class="checkbox-wrapper">
            <?= form_row($child, ['checked' => true]) ?>
        </div>
    <?php endforeach; ?>
    // ...
```

Here is a categorized list of all available field types:
* Simple
  * text
  * textarea
  * select
  * choice
  * checkbox
  * radio
  * password
  * hidden
  * file
  * static
* Date and Time
  * date
  * datetime-local
  * month
  * time
  * week
* Special Purpose
  * color
  * search
  * image
  * email
  * url
  * tel
  * number
  * range
* Buttons
  * submit
  * reset
  * button
* Form Builder Extensions
  * repeated
  * [form](#child-form)
  * [collection](#collection)

You can also bind the model to the class and add other options with setters

``` php
<?php namespace App/Http/Controllers;

use Illuminate\Routing\Controller;

class PostsController extends BaseController {

    public function edit($id)
    {
        $model = Post::findOrFail($id);

        $form = \FormBuilder::create('App\Forms\PostForm')
            ->setMethod('PUT')
            ->setUrl(route('post.update'))
            ->setModel($model)   // This will automatically do Form::model($model) in the form
            ->setData('post_choices', [ 'y' => 'yes', 'n' => 'no']) // This can be used in form like $this->getData('post_choices')
            ->addData([   // Add multiple data values at once
                'name' => 'some_name',
                'some_other_data' => 'some other data'
            ]);

        // Code above is similar to this:

        $form = \FormBuilder::create('App\Forms\PostForm', [
            'method' => 'PUT',
            'url' => route('post.update'),
            'model' => $model,
            'data' => [ 'post_choices' => [ 'y' => 'yes', 'n' => 'no'] ]
        ]);

        or this:

        $form = \FormBuilder::create('App\Forms\PostForm')->setFormOptions([
            'method' => 'PUT',
            'url' => route('post.update'),
            'model' => $model,
            'data' => [ 'post_choices' => [ 'y' => 'yes', 'n' => 'no'] ]
        ]);

        // Any options passed like this except 'model' and 'data' will be passed to the view for form options
        // So if you need to pass any data to form class, and use it only there, use setData() method or 'data' key
        // and pass what you need

        return view('posts.edit', compact('form'));
    }

    public function update()
    {
    }
}
```

And in form, you can use that model to populate some fields like this

``` php
<?php namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class PostForm extends Form
{
    public function buildForm()
    {
        // Request can be acessed if needed
        $someRequestData = $this->getRequest()->all();

        $this
            ->add('title', 'text')
            ->add('body', 'textearea')
            ->add('some_choices', 'choices', [
                'choices' => $this->getData('post_choices')     // When form is created passed as ->setData('post_choices', ['some' => 'array'])
            ])
            ->add('category', 'select', [
                'choices' => $this->model->categories()->lists('id', 'name')
            ]);
    }
}
```

### Changing configuration and templates

As mentioned above, bootstrap 3 form classes are used. If you want to change the defaults you can override it by running

```sh
php artisan vendor:publish
```

This will create config file `config/laravel-form-builder.php` and folder with views in `resources/views/vendor/laravel-form-builder`.

Structure of the config needs to be like this:
[config.php](https://github.com/kristijanhusak/laravel-form-builder/blob/master/src/config/config.php) file.

change values in `defaults` key as you wish.

If you would like to avoid typing in full namespace of the form class when creating, you can add default namespace to the config that was just published, and it will prepend it every time you want to create form:

``` php
<?php

// config/app/laravel-form-builder.php

return [
    'default_namespace' => 'App\Forms'
]

// app/Http/Controllers/HomeController

public function indexAction()
{
    \FormBuilder::create('SongForm');
}
```

It is empty by default.

All views for fields and forms needs to be similar to this: [views](https://github.com/kristijanhusak/laravel-form-builder/tree/master/src/views)

Other way is to change path to the templates in the
[config.php](https://github.com/kristijanhusak/laravel-form-builder/blob/master/src/config/config.php) file.

``` php
return [
    // ...
    'checkbox' => 'posts.my-custom-checkbox'    // resources/views/posts/my-custom-checkbox.blade.php
];
```


One more way to change template is directly from Form class:

``` php
<?php namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class PostForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('title', 'text')
            ->add('body', 'textearea', [
                'template' => 'posts.textarea'    // resources/views/posts/textarea.blade.php
            ]);
    }
}
```

**When you are adding custom templates make sure they inherit functionality from defaults to prevent breaking.**

### Custom fields

If you want to create your own custom field, you can do it like this:

``` php
<?php namespace App\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

class DatetimeType extends FormField {

    protected function getTemplate()
    {
        // At first it tries to load config variable,
        // and if fails falls back to loading view
        // resources/views/fields/datetime.blade.php
        return 'fields.datetime';
    }

    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true)
    {
        $options['somedata'] = 'This is some data for view';

        return parent::render($options, $showLabel, $showField, $showError);
    }
}
```

And then in view you can use what you need:

``` php
// ...

<?= $options['somedata'] ?>

// ...
```

**Notice:** Package templates uses plain PHP for printing because of plans for supporting version 4 (prevent conflict with tags), but you can use blade for custom fields, just make sure to use tags that are not escaping html (`{!! !!}`)

And then add it to published config file(`config/packages/kris/laravel-form-builder/config.php`) in key `custom-fields` key this:

``` php
// ...
    'custom_fields' => [
        'datetime' => 'App\Forms\Fields\DatetimeType'
    ]
// ...
```

Or if you want to load it only for a single form, you can do it directly in BuildForm method:

``` php
<?php namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class PostForm extends Form
{
    public function buildForm()
    {
        $this->addCustomField('datetime', 'App\Forms\Fields\DatetimeType');

        $this
            ->add('title', 'text')
            ->add('created_at', 'datetime')
    }
}
```

### Contributing
Project follows [PSR-2](http://www.php-fig.org/psr/psr-2/) standard and it's covered with PHPUnit tests.
Pull requests should include tests and pass [Travis CI](https://travis-ci.org/kristijanhusak/laravel-form-builder) build.

To run tests first install dependencies with `composer install`.

After that tests can be run with `vendor/bin/phpunit`

### Todo
* Add possibility to disable showing validation errors under fields - **DONE**
* Add event dispatcher ?
