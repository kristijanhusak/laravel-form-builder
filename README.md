[![Build Status](https://img.shields.io/travis/kristijanhusak/laravel-form-builder/master.svg?style=flat)](https://travis-ci.org/kristijanhusak/laravel-form-builder)
[![Coverage Status](http://img.shields.io/scrutinizer/coverage/g/kristijanhusak/laravel-form-builder.svg?style=flat)](https://scrutinizer-ci.com/g/kristijanhusak/laravel-form-builder/?branch=master)
[![Quality Score](http://img.shields.io/scrutinizer/g/kristijanhusak/laravel-form-builder.svg?style=flat)](https://scrutinizer-ci.com/g/kristijanhusak/laravel-form-builder)
[![Total Downloads](https://img.shields.io/packagist/dt/kris/laravel-form-builder.svg?style=flat)](https://packagist.org/packages/kris/laravel-form-builder)
[![Latest Stable Version](https://img.shields.io/packagist/v/kris/laravel-form-builder.svg?style=flat)](https://packagist.org/packages/kris/laravel-form-builder)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE)

# Laravel 5 form builder

Form builder for Laravel 5 inspired by Symfony's form builder. With help of Laravels FormBuilder class creates forms that can be easy modified and reused.
By default it supports Bootstrap 3.

##### Note: Laravel 5 is still in development, so it can break functionality of this package. If you want a stable version use Laravel 4 and [laravel-4](http://github.com/kristijanhusak/laravel-form-builder/tree/laravel-4) branch.

## Laravel 4
For laravel 4 version check branch [laravel-4](https://github.com/kristijanhusak/laravel-form-builder/tree/laravel-4)


## Changelog
Changelog can be found [here](https://github.com/kristijanhusak/laravel-form-builder/blob/master/CHANGELOG.md)

## Table of contents
1. [Installation](#installation)
2. [Basic usage](#usage)
  1. [Usage in controllers](#usage-in-controllers)
  2. [Usage in views](#usage-in-views)
3. [Plain form](#plain-form)
4. [Child form](#child-form)
5. [Field customization](#field-customization)
6. [Changing configuration and templates](#changing-configuration-and-templates)
7. [Custom fields](#custom-fields)
8. [Contributing](#contributing)
9. [Issues and bug reporting](#issues-and-bug-reporting)

###Installation

``` json
{
    "require": {
        "kris/laravel-form-builder": "1.*"
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

**Notice**: This package will add `illuminate/html` package and load Aliases (Form, Html) if they does not exist in the IoC container

### Basic usage

Creating form classes is easy. With a simple artisan command:

``` sh
    php artisan form:make Forms/PostForm
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
php artisan form:make Forms/SongForm --fields="name:text, lyrics:textarea, publish:checkbox"
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

class SongsController extends BaseController {

    /**
     * @Get("/songs/create", as="song.create")
     */
    public function index()
    {
        $form = \FormBuilder::create('App\Forms\SongForm', [
            'method' => 'POST',
            'url' => route('song.store')
        ]);

        return view('song.create', compact('form'));
    }

    /**
     * @Post("/songs", as="song.store")
     */
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

class SongsController extends BaseController {

    /**
     * @Get("/login", as="login-page")
     */
    public function index()
    {
        $form = \FormBuilder::plain([
            'method' => 'POST',
            'url' => route('login')
        ])->add('username', 'text')->add('password', 'password')->add('login', 'submit');

        return view('auth.login', compact('form'));
    }

    /**
     * @Post("/login", as="login")
     */
    public function login()
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

class SongForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('name', 'text')
            ->add('song', 'form', [
                'class' => \FormBuilder::create('App\Forms\PostForm')
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
                'wrapper' => 'name-input-container',
                'attr' => ['class' => 'input-name', 'placeholder' => 'Enter name here...'],
                'label' => 'Full name'
            ])
            ->add('bio', 'textarea')
            // This creates a select field
            ->add('subscription', 'choice', [
                'choices' => ['monthly' => 'Monthly', 'yearly' => 'Yearly'],
                'empty_value' => '==== Select subscription ===',
                'multiple' => false // This is default. If set to true, it creates select with multiple select posibility
            ])
            // This creates radio buttons
            ->add('gender', 'choice', [
                'choices' => ['m' => 'Male', 'f' => 'Female'],
                'selected' => 'm',
                'expanded' => true
            ])
            // Automatically adds enctype="multipart/form-data" to form
            ->add('image', 'file', [
                'label' => 'Upload your image'
            ])
            // This creates a checkbox list
            ->add('languages', 'choice', [
                'choices' => ['en' => 'English', 'de' => 'German', 'fr' => 'France'],
                'selected' => ['en', 'de']
                'expanded' => true,
                'multiple' => true
            ])
            // Renders all fieds from song form and wraps names for better handling
            // <input type="text" name="song-title"> becomes <input type="text" name="song[song-title]">
            ->add('song', 'form', [
                'class' => \FormBuilder::create('App\Forms\SongForm')
            ])
            ->add('policy-agree', 'checkbox', [
                'default_value' => 1,    //  <input type="checkbox" value="1">
                'label' => 'I agree to policy',
                'checked' => false    // This is the default.
            ])
            ->add('save', 'submit', [
                'attr' = ['class' => 'btn btn-primary']
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

    /**
     * @Get("/posts/{id}/edit", as="posts.edit")
     */
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

    /**
     * @Post("/posts/{id}", as="post.update")
     */
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

Here is the list of all available field types:
* text
* email
* url
* tel
* search
* password
* hidden
* number
* date
* textarea
* submit
* reset
* button
* file
* image
* select
* checkbox
* radio
* choice
* form

You can also bind the model to the class and add other options with setters

``` php
<?php namespace App/Http/Controllers;

use Illuminate\Routing\Controller;

class PostsController extends BaseController {

    /**
     * @Get("/posts/{id}/edit", as="posts.edit")
     */
    public function edit($id)
    {
        $model = Post::findOrFail($id);

        $form = \FormBuilder::create('App\Forms\PostForm')
            ->setMethod('PUT')
            ->setUrl(route('post.update'))
            ->setModel($model)   // This will automatically do Form::model($model) in the form
            ->setData('post_choices', [ 'y' => 'yes', 'n' => 'no']); // This can be used in form like $this->getData('post_choices')

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

    /**
     * @Post("/posts/{id}", as="posts.update")
     */
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

As mentioned above, bootstrap 3 form classes are used. If you want to change the defaults you need to publish the config like this:
``` sh
php artisan publish:config kris/laravel-form-builder
```
This will create folder `kris` in `config/packages` folder which will contain
[config.php](https://github.com/kristijanhusak/laravel-form-builder/blob/master/src/config/config.php) file.

change values in `defaults` key as you wish.

If you want to customize the views for fields and forms you can publish the views like this:
``` sh
php artisan publish:views kris/laravel-form-builder
```

This will create folder with all files in `resources/views/packages/kris/laravel-form-builder`

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
Project follows [PSR-2](http://www.php-fig.org/psr/psr-2/) standard and it's 100% covered with PHPUnit tests.
Pull requests should include tests and pass [Travis CI](https://travis-ci.org/kristijanhusak/laravel-form-builder) build.

### Issues and bug reporting
When creating an issue, please mark it with label **Laravel 4** or **Laravel 5** so it can be easier to handle.

To run tests first install dependencies with `composer install`.

After that tests can be run with `vendor/bin/phpunit`

### Todo
* Add possibility to disable showing validation errors under fields - **DONE**
* Add event dispatcher ?
