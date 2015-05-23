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
2. [Quick start](#quick-start)
3. [Form types](#form-types)
    1. [Regular form](#regular-form)
    2. [Named form](#named-form)
    3. [Plain form](#plain-form)
4. [Field types](#field-types)
    1. [Basic input types](#basic-input-types)
    2. [Buttons](#buttons)
    3. [Checkables and dropdowns](#checkables-and-dropdowns)
    4. [Complex types](#complex-types)
    5. [Other types](#other-types)
5. [View helper functions](#view-helper-functions)
6. [Customization](#customization)
    1. [Field customization](#field-customization)
    2. [Changing configuration and templates](#changing-configuration-and-templates)
    3. [Custom fields](#custom-fields)
7. [Contributing](#contributing)

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

### Quick start

Creating form classes is easy. With a simple artisan command:

``` sh
php artisan make:form Forms/SongForm --fields="name:text, lyrics:textarea, publish:checkbox"
```

Form is created in path `app/Forms/SongForm.php` with content:

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

If you want to instantiate empty form withoutl any fields, just skip passing `--fields` parameter:

``` sh
    php artisan make:form Forms/PostForm
```

Gives:

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

After that instantiate the class in the controller and pass it to view:

``` php
<?php namespace App/Http/Controllers;

use Illuminate\Routing\Controller as BaseController;
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
}
```

Print the form in view with `form()` helper function:

``` html
<!-- resources/views/song/create.blade.php -->

@extend('layouts.master')

@section('content')
    {!! form($form) !!}
@endsection
```

Above code will generate this html:

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


### Form types
There are 3 types of forms that can be used:

1. [Regular form](#regular-form)
2. [Named form](#named-form)
3. [Plain form](#plain-form)

#### Regular form
This type is most frequently used, and it is basis of all form types.

By default, it should contain `buildForm` method that will be called on form instantiation through `FormBuilder`.

Here is simple example of a LoginForm:

```php
<?php namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class LoginForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('username', 'text')
            ->add('password', 'password')
            ->add('remember_me', 'checkbox');
    }
}
````

Instantiating this form is done with `FormBuilder` class or Facade that is provided:

```php
<?php namespace App\Http\Controllers;

    class AuthController extends Controller
    {
        public function login()
        {
            $form = \FormBuilder::create('App\Forms\LoginForm', [
                'method' => 'POST',
                'route' => action('AuthController@postLogin')
            ]);
        }

        public function postLogin()
        {
            // Code for logging in user...
        }
    }
```

`FormBuilder::create` accepts 3 parameters:

|No.| Parameter       | Type   |
|---|-----------------|--------|
| 1 | Form class path | String |
| 2 | Form options    | Array  |
| 3 | Form data       | Array  |

1. This is the full class name of the Form class that we want to create. Namespace can be skipped if configuration has `default_namespace` option set.
2. These are the options that will be used in the form.
    If you are familiar with Laravel's `Form`, it is the thing that is passed as second argument to `Form::open`, and also some additional things.
    So it can contain something like this:
    ```php
    <?php
    $model = User::find(1);
    $formOptions = [
        'method' => 'POST',
        'url' => action('AuthController@postLogin'),
        'class' => 'form-horizontal',
        'model' => $model,                              // Not passed to view, just used in form class
        'name' => 'users',                              // Not passed to view, just used in form class
        'data' => ['some_dummy_data' => 'some text'],   // Not passed to view, just used in form class
    ];
    ```
    All options except `model`, `name` and `data` are passed to view. These two are pulled out of options and used for another things.

    * `model` is Eloquent model that will be used to bind values to form. It will be done automatically!
    Another way to set the model for form is with `setModel` method.

    * `name` is used for creating [Named form](#named-form).

    * `data` is just another way to pass some static data that can be used in the form (Check below 3.).
    Another way to set data is with `addData` and `setData` methods on Form class. `addData` just adds one entry to `data` array,
    and `setData` overrides it with passed array.

    Several of these options can be set on the Form class as a property to avoid duplicating code. For example:
    ```php
        <?php

        class LoginForm extends Form
        {
            property $formOptions = [
                'method' => 'POST',
                'class' => 'form-horizontal'
            ];
        }
    ```

3. This is the data that can be passed to Form class so it can be used with `getData()` method.
    Do not confuse this with `model`, this is only used as static data in the form class, kind of helper.

Here are some useful methods and properties on form class:

Methods:

* `add($fieldName, $type, $options)` - adds a field to form class.
* `addAfter($name, $fieldName, $type, $options)` - add a field after another field ($name).
* `addBefore($name, $fieldName, $type, $options)` - add a field before another field ($name).
* `remove($fieldName)` - Remove existing field from form.
* `modify($fieldName, $type, $options, $overwriteOpts = false)` - modify existing field. if $overwriteOpts is true, options of the field that is being modified are overwritten. Otherwise it's merged.
* `getModel()`(Eloquent|array) - Get the model that was passed when creating form. Also available in child forms.
* `getRequest()`(Illuminate/Http/Request) - Get the current request
* `getData($name = null, $default = null)`(mixed) - Get element from $data passed to form class. If null, returns all data.
* `getFields()`(array)- Get all fields for this form class
* `getField($name)`(Field instance) - Get a single field instance from form class

Properties:

* `protected $showFieldErrors = true` - Should validation errors appear under the fields.
* `protected $name = null` - Name for the (Named form)(#named-form)


#### Named form
Named form is basically a regular form with only one addition. It has name that is used to namespace fields in the form.
There are 3 ways to set up a named form:

1. Passing it as form option. This is **recommended** method
    ```php
        <?php

        FormBuilder::create('App\Forms\LoginForm', [
            'name' => 'users';
        ])
    ```

2. Setting as property on the Form class. Useful when namespacing is always needed
    ```php
        <?php
        Class LoginForm extends Form
        {
            protected $name = 'users';
        }
    ```
3. Using `setName` method on form class after instantiation.
    **Avoid** using this method because it requires rebuilding fields to set proper names.
    ```php
        <?php
        FormBuilder::create('App\Forms\LoginForm')->setName('users');
    ```

#### Plain form
Plain form is useful when you want quickly to create a form, for which you do not need separate Form class.
It basicallly instantiates `Kris\LaravelFormBuilder\Form` class. It's same as creating regular form, only the
first parameter is skipped.

```php
    $formOptions = [
        'url' => route('password.reset'),
        'method' => 'POST'
    ];

    $form = \FormBuilder::plain($formOptions)->add('email', 'email', [
        'label' => 'Email address'
    ]);
```


### Field types
This package provides many field types out of the box. Here's the complete list:

1. [Basic input types](#basic-input-types)
    * text
    * email
    * password
    * hidden
    * etc...(Check section for basic input types)
2. [Buttons](#buttons)
    * submit
    * reset
    * button
3. [Checkables and dropdowns](#checkables-and-dropdowns)
    * select
    * checkbox
    * radio
    * choice
    * entity
4. [Complex types](#complex-types)
    * form
    * collection
5. [Other types](#other-types)
    * repeated
    * static

They all share these default options (Most of these are configurable through config.php file. Check [customization](#customization)):
```php
$options = [
    'wrapper' => ['class' => 'form-group'],
    'attr' => ['class' => 'form-control'],
    'help_block' => [
        'text' => null,
        'tag' => 'p',
        'attr' => ['class' => 'help-block']
    ],
    'label' => $this->name, // Field name used
    'label_attr' => ['class' => 'control-label', 'for' => $this->name],
    'errors' => ['class' => 'text-danger']
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
    $this->add('field_name', 'text', [
        'attr' => ['class' => 'form-control field-input'],
        'label' => 'This is label for field name'
    ]);
```
Or when rendered in the view with `form_*` helper functions:
```html
    form_row($form->field_name, ['label' => 'This label will be used']);
```

#### Basic input types
These are types that are used most of the time in forms.
All fields that match this pattern are in this category:
```html
    <input type="TYPE_HERE">
```
Here's the full list:
* text
* email
* password
* hidden
* textarea
* number
* file
* image
* url
* tel
* search
* color
* date
* datetime-local
* month
* range
* time
* week

Beside inherited options, it contains option that is used to set value for the field:

```php
    $this->add('name', 'text', [
        'default_value' => 'John'
    ]);
```

`default_value`(String)(Default: null) - Used for setting default value.

If not provided, Form class will search for `name` property on the passed `model`.

In case you want to modify the value pulled from model before it's passed to view, you can use Closure to manipulate data:
```php
    $this->add('name', 'text', [
        'default_value' => function ($name) {
            return 'This is name: ' + $name;
        }
    ]);
```

#### Buttons
Classic form buttons are available:
* submit
* reset
* button

It is added like any other field in form class:
```php
    $this
        ->add('save', 'submit', ['label' => 'Save form'])
        ->add('clear', 'reset', ['label' => 'Clear form']);
```

and it creates this html:

```html
<button type="submit">Save form</button>
<button type="reset">Clear form</button>
```

#### Checkables and dropdowns
Field types that belongs to this category are:

* [select](#select)
* [checkbox](#checkbox)
* [radio](#radio)
* [choice](#choice)
* [entity](#entity)

Even if not totally similar, they are in same category for several reasons:

1. choice field can be:
    * Select (single and multiple select)
    * Radio buttons
    * Checkboxes
2. Entity is choice type with `choices` pulled from some kind of entity (Model)

##### Select
This is classic select dropdown that is added like this:

```php
    $this->add('languages', 'select', [
        'choices' => ['en' => 'English', 'fr' => 'French'],
        'selected' => 'en',
        'empty_value' => '=== Select language ==='
    ]);
```

Beside inherited it have 3 additional options:

1. `choices`(Array)(Default: `[]`) - key value pairs used for options in the select
2. `empty_value`(String)(Default: `null`) - If provided, added to the start of select as empty value
3. `selected`(String|Array|Closure)(Default: `null`) - Option that needs to be selected. If not provided, Form class will try to fetch it from passed model.
    * array is used when 'multiple' attribute is set
    * Closure is used for modifying model data before passed to view. Useful when fetching relationship data to pluck only data that is needed.

```php
    $this->add('languages', 'select', [
        'choices' => ['en' => 'English', 'fr' => 'French'],
        'selected' => function ($data) {
            // Returns the array of short names from model relationship data
            return array_pluck($data, 'short_lang_name');
        }
        'empty_value' => '=== Select language ==='
    ])
```

##### Checkbox
Checkbox by itself is not commonly used. Some use case can be `remember me` checkbox on login:
```php
    $this->add('remember_me', 'checkbox', [
        'default_value' => 1,
        'checked' => true
    ]);
```
Beside inherited, there are 2 more options:

1. `default_value`(String)(Default: `null`) - value that will be used for the value attribute
2. `checked`(Boolean)(Default: `false`) - If true, checkbox will be checked
```html
    <div class="form-group">
        <input type="checkbox" value="1" checked id="remember_me" name="remember_me">
        <label for="remember_me">Remember me</label>
    </div>
```

##### Radio
Radio is also not commonly used in this way, even less than checkbox, because it creates only single radio button.
```php
    $this->add('remember_me', 'radio', [
        'default_value' => 1,
        'checked' => false
    ]);
```
Beside inherited, there are 2 more options:

1. `default_value` - value that will be used for the value attribute
2. `checked` - If true, checkbox will be checked. Default: `false`
```html
    <div class="form-group">
        <input type="radio" value="1" checked id="remember_me" name="remember_me">
        <label for="remember_me">Remember me</label>
    </div>
```

##### Choice
Choice field is hybrid type, because it can be:
* Regular select
* Select with multiple choices
* List of radio buttons
* List of checkboxes

```php
    // This creates list of checkboxes
    $this->add('languages', 'choice', [
        'choices' => ['en' => 'English', 'fr' => 'French'],
        'selected' => ['en', 'fr'],
        'expanded' => true,
        'multiple' => true
    ]);
```

Beside inherited, there are some additional options available:

1. `choices`(Array)(Default: `[]`) - key value pairs that will be used in the list
2. `selected`(String|Array|Closure)(Default: `null`) - Item that needs to be selected/checked, if not provided fetched from Model
3. `expanded`(Boolean)(Default: `false`) - If true, list will be of type radios or checkboxes(depending on multiple option)
4. `multiple`(Boolean)(Default: `false`) - If true, allows multiple select or list of checkboxes (depending on expanded option)

| expanded | multiple | Field type                     |
|----------|----------|--------------------------------|
| false    | false    | Select                         |
| false    | true     | Select with multiple attribute |
| true     | false    | List of radio buttons          |
| true     | true     | List of checkboxes             |

`selected` can be modified with Closure the same way like in [select field type](#select)

##### Entity
Entity inherits all functionality from [choice type](#choice), with additional options:

1. `class`(String)(Default: `null`) - Full path to the Model class that will be used to fetch choices.
2. `query_builder`(Closure)(Default: `null`) - If provided, used to filter data before setting as choices. If null, gets all data.
3. `property`(String)(Default: `name`) - Property from model that will be used as label for options in choices
4. `property_key`(String)(Default: `id`) - Property from model that will be used as value for options in choices

**Note**: If `choices` are provided in options, `class` option is ignored, and passed data for `choices` is used.

```php
    $this->add('languages', 'entity', [
        'class' => 'App\Language',
        'property' => 'short_name',
        'query_builder' => function (App\Language $lang) {
            // If query builder option is not provided, all data is fetched
            return $lang->where('active', 1);
        }
    ]);
```
