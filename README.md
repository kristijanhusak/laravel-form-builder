[![Build Status](https://img.shields.io/travis/kristijanhusak/laravel-form-builder/master.svg?style=flat)](https://travis-ci.org/kristijanhusak/laravel-form-builder)
[![Coverage Status](http://img.shields.io/scrutinizer/coverage/g/kristijanhusak/laravel-form-builder.svg?style=flat)](https://scrutinizer-ci.com/g/kristijanhusak/laravel-form-builder/?branch=master)
[![Quality Score](http://img.shields.io/scrutinizer/g/kristijanhusak/laravel-form-builder.svg?style=flat)](https://scrutinizer-ci.com/g/kris/laravel-form-builder)
[![Total Downloads](https://img.shields.io/packagist/dt/kris/laravel-form-builder.svg?style=flat)](https://packagist.org/packages/kris/laravel-form-builder)
[![Latest Stable Version](https://img.shields.io/packagist/v/kris/laravel-form-builder.svg?style=flat)](https://packagist.org/packages/kris/laravel-form-builder)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE)

# Laravel 5 form builder

Form builder for Laravel 5 inspired by Symfony's form builder. With help of Laravels FormBuilder class creates forms that can be easy modified and reused.
By default it supports Bootstrap 3.

###Installation

``` json
{
    "require": {
        "kris/laravel-form-builder": "1.0"
    }
}
```

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

----
###Usage
----

Creating form classes is easy. With a simple artisan command:

``` sh
    php artisan laravel-form-builder:make Forms/PostForm
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
php artisan laravel-form-builder:make Forms/SongForm --fields="name:text, lyrics:textarea, publish:checkbox"
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

It can be used in controller like this:

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
        
        return view('song.create', compact('form');
    }
    
    /**
     * @Post("/songs", as="song.store")
     */
    public function store()
    {
    }
}
```

and then in view add this:

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
<label for="name" class="control-label">name</label>
    
{!! form_widget($form->publish, ['checked' => true]) !!}
<input type="checkbox" name="publish" checked="checked">

{!! form_errors($form->publish) !!}
<div class="text-danger">This field is required.</div> <!-- Rendered only if validation errors occur. -->

{!! form_rest($form) !!}
<div class="form-group">
    <label for="name" class="control-label">name</label>
    <input type="text" class="form-control" id="name">
</div>
<div class="form-group">
    <label for="publish" class="control-label">publish</label>
    <input type="text" name="publish" id="publish">
</div>

{!! form_end($form) !!}
</form>

```


List of all available field types:
* text
* email
* url
* tel
* password
* hidden
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


