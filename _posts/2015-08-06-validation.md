---
layout: page
title: "Validation"
category: overview
order: 4
date: 2015-08-06 11:30:30
---

* [Basics](#basics)
* [Overriding rules](#overriding-rules)
* [Custom messages](#custom-messages)
* [Getting the rules from Form](#getting-the-rules-from-form)
* [Different request](#different-request)

### Basics
Since version 1.6.20 validation is added to the Form class. It automatically gets the rules
that are specified through field options, and validates the form. Here's a short example:

```php
// app/Forms/PostForm.php

<?php namespace App\Forms;
use Kris\LaravelFormBuilder\Form;

class PostForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('title', 'text', [
                'label' => 'Post title',
                'rules' => 'required|min:5'
            ])
            ->add('body', 'textarea', [
                'label' => 'Post body',
                'rules' => 'max:5000'
            ]);
    }
}
```

```php
// app/Http/Controllers/PostController.php
<?php
namespace App\Http\Controllers;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\PostForm;

class PostController extends Controller
{
    use FormBuilderTrait;

    public function create()
    {
        $form = $this->form(PostForm::class, [
            'method' => 'POST',
            'route' => 'PostController@store'
        ]);

        return view('posts.create', compact('form'));
    }

    public function store(Request $request)
    {
        $form = $this->form(PostForm::class);

        // It will automatically use current request, get the rules, and do the validation
        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        Post::create($request->all());
        return redirect()->route('posts');
    }
}
```

**NOTE: Form class will automatically use labels as attributes in error messages: For example when required validation fails:**

```
* The Post title is required.
* The Post body is required.
```

### Overriding rules

By default when you call `isValid` method on the Form class, it will call `validate` method,
and use rules provided in form options. If you want to override some rules, you can call `validate` by yourself and change what you need.
It will override rules only for fields that you pass, and for other will use default ones provided in buildForm.

Let's take example from [basics](#basics) part:

```php
// app/Http/Controllers/PostController.php
<?php
namespace App\Http\Controllers;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\PostForm;

class PostController extends Controller
{
    use FormBuilderTrait;

    public function store(Request $request)
    {
        $form = $this->form(PostForm::class);

        $form->validate(['title' => 'required|alpha_num']);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        Post::create($request->all());
        return redirect()->route('posts');
    }
}
```

### Custom messages

In some situations there is a need for a custom message per field.
Custom messages can be provided as **2nd argument to `validate` method**.
Let's use the example from above:

```php
// app/Http/Controllers/PostController.php
<?php
namespace App\Http\Controllers;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\PostForm;

class PostController extends Controller
{
    use FormBuilderTrait;

    public function store(Request $request)
    {
        $form = $this->form(PostForm::class);
        $form->validate(['title' => 'required|alpha_num'], [
            'title.required' => 'Please provide valid title for this post.'
        ]);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        Post::create($request->all());
        return redirect()->route('posts');
    }
}
```

For more informations how to override messages, check Laravel docs http://laravel.com/docs/5.1/validation#custom-error-messages


### Getting the rules from Form

Some people like to use Exceptions when validating the form. That can be easily done in Laravel 5.1 with [ValidatesRequests](https://github.com/laravel/framework/blob/5.1/src/Illuminate/Foundation/Validation/ValidatesRequests.php) trait.

The problem is that `validate` method from this trait requires 2 parameters:
Request and rules.

If you want to use this method, you can just call `getRules` from Form class, and it will give you back properly formatted rules in array.

```php
// app/Http/Controllers/PostController.php
<?php
namespace App\Http\Controllers;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\PostForm;
use Illuminate\Foundation\Validation\ValidatesRequests;

class PostController extends Controller
{
    use FormBuilderTrait;
    use ValidatesRequests;

    public function store(Request $request)
    {
        $form = $this->form(PostForm::class);

        $rulesToOverride = [
            'body' => 'max:10000'
        ];

        $this->validate($request, $form->getRules($rulesToOverride));

        Post::create($request->all());
        return redirect()->route('posts');
    }
}
```

### Different request

By default, Form class will use latest request that it got when form was created.

That request is always the same as the one that use use in the controller method,
so there is no need to override it with the same request in those situations.

If for some reason request needs to be changed, it can be done with `setRequest`


```php
// app/Http/Controllers/PostController.php
<?php
namespace App\Http\Controllers;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\PostForm;

class PostController extends Controller
{
    use FormBuilderTrait;

    public function store(Request $request)
    {
        $form = $this->form(PostForm::class);
        $somePreviousRequest = $this->getPreviousRequest();

        // DO NOT DO THIS, SAME REQUEST IS ALREADY AVAILABLE IN FORM CLASS
        $form->setRequest($request);

        $form->setRequest($somePreviousRequest);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        Post::create($request->all());
        return redirect()->route('posts');
    }
}
```
