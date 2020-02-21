[![Build Status](https://travis-ci.org/kristijanhusak/laravel-form-builder.svg)](https://travis-ci.org/kristijanhusak/laravel-form-builder)
[![Coverage Status](http://img.shields.io/scrutinizer/coverage/g/kristijanhusak/laravel-form-builder.svg?style=flat)](https://scrutinizer-ci.com/g/kristijanhusak/laravel-form-builder/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/kris/laravel-form-builder.svg?style=flat)](https://packagist.org/packages/kris/laravel-form-builder)
[![Latest Stable Version](https://img.shields.io/packagist/v/kris/laravel-form-builder.svg?style=flat)](https://packagist.org/packages/kris/laravel-form-builder)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE)

# Laravel 5 form builder

[![Join the chat at https://gitter.im/kristijanhusak/laravel-form-builder](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/kristijanhusak/laravel-form-builder?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Form builder for Laravel 5 inspired by Symfony's form builder. With help of Laravels FormBuilder class creates forms that can be easy modified and reused.
By default it supports Bootstrap 3.

## Laravel 4
For Laravel 4 version check [laravel4-form-builder](https://github.com/kristijanhusak/laravel4-form-builder).

## Bootstrap 4 support
To use bootstrap 4 instead of bootstrap 3, install [laravel-form-builder-bs4](https://github.com/ycs77/laravel-form-builder-bs4).

## Upgrade to 1.6
If you upgraded to `>1.6.*` from `1.5.*` or earlier, and having problems with form value binding, rename `default_value` to `value`.

More info in [changelog](https://github.com/kristijanhusak/laravel-form-builder/blob/master/CHANGELOG.md).

## Documentation
For detailed documentation refer to https://kristijanhusak.github.io/laravel-form-builder/.

## Changelog
Changelog can be found [here](https://github.com/kristijanhusak/laravel-form-builder/blob/master/CHANGELOG.md).

## Installation

### Using Composer

```sh
composer require kris/laravel-form-builder
```

Or manually by modifying `composer.json` file:

``` json
{
    "require": {
        "kris/laravel-form-builder": "1.*"
    }
}
```

And run `composer install`

Then add Service provider to `config/app.php`

``` php
    'providers' => [
        // ...
        Kris\LaravelFormBuilder\FormBuilderServiceProvider::class
    ]
```

And Facade (also in `config/app.php`)

``` php
    'aliases' => [
        // ...
        'FormBuilder' => Kris\LaravelFormBuilder\Facades\FormBuilder::class
    ]

```

**Notice**: This package will add `laravelcollective/html` package and load aliases (Form, Html) if they do not exist in the IoC container.


## Quick start

Creating form classes is easy. With a simple artisan command:

```sh
php artisan make:form Forms/SongForm --fields="name:text, lyrics:textarea, publish:checkbox"
```

Form is created in path `app/Forms/SongForm.php` with content:

```php
<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\Field;

class SongForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('name', Field::TEXT, [
                'rules' => 'required|min:5'
            ])
            ->add('lyrics', Field::TEXTAREA, [
                'rules' => 'max:5000'
            ])
            ->add('publish', Field::CHECKBOX);
    }
}
```

If you want to instantiate empty form without any fields, just skip passing `--fields` parameter:

```sh
php artisan make:form Forms/PostForm
```

Gives:

```php
<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class PostForm extends Form
{
    public function buildForm()
    {
        // Add fields here...
    }
}
```

After that instantiate the class in the controller and pass it to view:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Kris\LaravelFormBuilder\FormBuilder;

class SongsController extends BaseController {

    public function create(FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(\App\Forms\SongForm::class, [
            'method' => 'POST',
            'url' => route('song.store')
        ]);

        return view('song.create', compact('form'));
    }

    public function store(FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(\App\Forms\SongForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        // Do saving and other things...
    }
}
```

Alternative example:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Kris\LaravelFormBuilder\FormBuilder;
use App\Forms\SongForm;

class SongsController extends BaseController {

    public function create(FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(SongForm::class, [
            'method' => 'POST',
            'url' => route('song.store')
        ]);

        return view('song.create', compact('form'));
    }

    public function store(FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(SongForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        // Do saving and other things...
    }
}
```


If you want to store a model after a form submit considerating all fields are model properties:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Kris\LaravelFormBuilder\FormBuilder;
use App\SongForm;

class SongFormController extends Controller
{
    public function store(FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(\App\Forms\SongForm::class);
        $form->redirectIfNotValid();
        
        SongForm::create($form->getFieldValues());

        // Do redirecting...
    }
```

You can only save properties you need:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Kris\LaravelFormBuilder\FormBuilder;
use App\SongForm;

class SongFormController extends Controller
{
    public function store(FormBuilder $formBuilder, Request $request)
    {
        $form = $formBuilder->create(\App\Forms\SongForm::class);
        $form->redirectIfNotValid();
        
        $songForm = new SongForm();
        $songForm->fill($request->only(['name', 'artist'])->save();

        // Do redirecting...
    }
```

Or you can update any model after form submit:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Kris\LaravelFormBuilder\FormBuilder;
use App\SongForm;

class SongFormController extends Controller
{
    public function update(int $id, Request $request)
    {
        $songForm = SongForm::findOrFail($id);

        $form = $this->getForm($songForm);
        $form->redirectIfNotValid();

        $songForm->update($form->getFieldValues());

        // Do redirecting...
    }
```

Create the routes

```php
// app/Http/routes.php
Route::get('songs/create', [
    'uses' => 'SongsController@create',
    'as' => 'song.create'
]);

Route::post('songs', [
    'uses' => 'SongsController@store',
    'as' => 'song.store'
]);
```

Print the form in view with `form()` helper function:

```html
<!-- resources/views/song/create.blade.php -->

@extends('app')

@section('content')
    {!! form($form) !!}
@endsection
```

Go to `/songs/create`; above code will generate this html:

```html
<form method="POST" action="http://example.dev/songs">
    <input name="_token" type="hidden" value="FaHZmwcnaOeaJzVdyp4Ml8B6l1N1DLUDsZmsjRFL">
    <div class="form-group">
        <label for="name" class="control-label">Name</label>
        <input type="text" class="form-control" id="name">
    </div>
    <div class="form-group">
        <label for="lyrics" class="control-label">Lyrics</label>
        <textarea name="lyrics" class="form-control" id="lyrics"></textarea>
    </div>
    <div class="form-group">
        <label for="publish" class="control-label">Publish</label>
        <input type="checkbox" name="publish" id="publish">
    </div>
</form>
```

Or you can generate forms easier by using simple array
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\Field;
use App\Forms\SongForm;

class SongsController extends BaseController {

    public function create(FormBuilder $formBuilder)
    {
        $form = $formBuilder->createByArray([
                        [
                            'name' => 'name',
                            'type' => Field::TEXT,
                        ],
                        [
                            'name' => 'lyrics',
                            'type' => Field::TEXTAREA,
                        ],
                        [
                            'name' => 'publish',
                            'type' => Field::CHECKBOX
                        ],
                    ]
            ,[
            'method' => 'POST',
            'url' => route('song.store')
        ]);

        return view('song.create', compact('form'));
    }
}
```


## Contributing

Project follows [PSR-2](http://www.php-fig.org/psr/psr-2/) standard and it's covered with PHPUnit tests.
Pull requests should include tests and pass [Travis CI](https://travis-ci.org/kristijanhusak/laravel-form-builder) build.

To run tests first install dependencies with `composer install`.

After that tests can be run with `vendor/bin/phpunit`
