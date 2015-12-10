---
layout: page
title: "Methods and properties"
category: form
order: 2
date: 2015-05-24 17:57:24
---

Here are some useful methods and properties on form class:

### Methods

* [add($name, $type, $options)](#add) - adds a field to form class.
* [addBefore($beforeFieldName, $name, $type, $options)](#addbefore) - add a field before another field.
* [addAfter($afterFieldName, $name, $type, $options)](#addafter) - add a field after another field.
* [compose($form, $name, $type, $options)](#compose) - Add fields from passed `$form` to this form.
* [remove($name)](#remove) - Remove existing field from form.
* [modify($name, $type, $options, $overwriteOpts = false)](#modify) - modify existing field.
* [setModel()(**DEPRECATED**)](#setmodel-deprecated)- Set the model for the form class that will be used to bind values to the form.
* [getModel()](#getmodel)- Get the model that was passed when creating form. Also available in child forms.
* [getRequest()](#getrequest) - Get the current request.
* [getData($name = null, $default = null)](#getdata) - Get element from $data passed to form class. If null, returns all data.
* [getFields()](#getfields)- Get all fields for this form class
* [getField($name)](#getfield) - Get a single field instance from form class
* [disableFields](#disablefields)- Disable all fields in a form
* [enableFields](#enablefields) - Enable all fields in the form

### Properties

* [protected $formOptions = ['method' => 'GET', url => null]`](#formoptions) - Form options passed to view.
* [protected $showFieldErrors = true](#showfielderrors) - Should validation errors appear under the fields.
* [protected $name = null]({{ site.baseurl }}{% post_url 2015-05-24-named-form %}) - Name for the [Named form]({{ site.baseurl }}{% post_url 2015-05-24-named-form %})
* [protected $clientValidationEnabled = true](#clientvalidationenabled) - Enable/disable client side validation

#### add

This method is used to add a field to the form. It is used in Form class `buildForm` method,
but it can be also used after form instantiation.

It accepts 3 arguments:

| Description   | Type   | Required | Default |
|---------------|--------|----------|---------|
| Field name    | String | true     | -       |
| Field type    | String | false    | text    |
| Field options | Array  | false    | []      |

```php
<?php
$this
    ->add('first_name', 'text', [
            'label' => 'First name'
    ]);
```

#### addBefore

This method is used to add a field before some other field.
It's mostly useful after the form instantiation, if You want to add new field at specific location.

It accepts 4 arguments:

| Description       | Type   | Required | Default |
|-------------------|--------|----------|---------|
| Before field name | String | true     | -       |
| Field name        | String | true     | -       |
| Field type        | String | false    | text    |
| Field options     | Array  | false    | []      |

```php
<?php
$form = FormBuilder::plain()
    ->add('username', 'text')
    ->add('password', 'password')
    ->add('save', 'submit');

$form->addBefore('password', 'full_name', 'text');
```

#### addAfter

Similar to `addBefore`, this method is used to add a field before some other field.

It's mostly useful after the form instantiation, if You want to add new field at specific location.

It accepts 4 arguments:

| Description       | Type   | Required | Default |
|-------------------|--------|----------|---------|
| Before field name | String | true     | -       |
| Field name        | String | true     | -       |
| Field type        | String | false    | text    |
| Field options     | Array  | false    | []      |

```php
<?php
$form = FormBuilder::plain()
    ->add('username', 'text')
    ->add('password', 'password')
    ->add('save', 'submit');

$form->addAfter('password', 'address', 'text');
```

#### compose

This method is used when you want to add fields from one form to another form.

It accepts 3 arguments:

| Description                     | Type                      | Required | Default |
|---------------------------------|---------------------------|----------|---------|
| Form which fields will be added | Form|ChildFormType|String | true     | -       |
| $options                        | String                    | false    | []      |
| $modify                         | String                    | false    | false   |

```php
<?php namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class AddressForm extends Form
{
    public function buildForm()
    {
        $this->add('street', 'text')
            ->add('city', 'text');
    }
}

class UserForm extends Form
{
    public function buildForm()
    {
        $this->add('name', 'text')
            ->add('email', 'email')
            ->compose('App\Forms\AddressForm');
    }
}

class CompanyForm extends Form
{
    public function buildForm()
    {
        $this->add('name', 'text')
            ->add('phone', 'tel')
            ->compose('App\Forms\AddressForm');
    }
}
```

#### modify

In situations when there is need for modifying existing field on the form class, we can use this method.

It accepts 4 arguments:

| Description                           | Type    | Required | Default |
|---------------------------------------|---------|----------|---------|
| Field name to modify                  | String  | true     | -       |
| New field type for the field          | String  | false    | text    |
| Field options to add/overwrite        | Array   | false    | []      |
| Should options be totally overwritten | Boolean | false    | false   |

```php
<?php
$form = FormBuilder::plain()
    ->add('username', 'text')
    ->add('password')
    ->add('save', 'submit');

$form->modify('username', 'text', [
    'attr' => ['disabled' => true]
]);
```


#### remove

Since forms can be reused, sometimes certain fields are not needed in different places.
Using this method we can remove those fields.

It accepts 1 argument:

| Description          | Type    | Required | Default |
|----------------------|---------|----------|---------|
| Field name to remove | String  | true     | -       |

```php
<?php
$form = FormBuilder::plain()
    ->add('username', 'text')
    ->add('email', 'email')
    ->add('password')
    ->add('save', 'submit');

$form->remove('email');
```

#### setModel **(DEPRECATED)**
**Note**: This method is deprecated and will be removed in 1.7 version.
Pass model through options when creating a form. More info in [Form options]({{ site.baseurl }}{% post_url 2015-05-24-basics %}#form-options) section

After form instantiation, we can set the model for the form, that will be used to bind values for the fields in the form.

```php
<?php
$model = User::find(1);

$form = FormBuilder::plain()
    ->add('username', 'text')
    ->add('email', 'email')
    ->add('password')
    ->setModel($model);
```

Another way to pass the model is through formOptions parameter:

```php
<?php
$model = User::find(1);

$form = FormBuilder::plain(['model' => $model])
    ->add('username', 'text')
    ->add('email', 'email')
    ->add('password');
```

#### getModel

Get the model that was passed to the form class.
Can be used in `buildForm` for referencing some relationship or data from the model,
or after the instantiation.

```php
<?php namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class CommentForm extends Form
{
    public function buildForm()
    {
        $this->add('body', 'textarea');

        if ($this->getModel()->owner === \Auth::id()) {
            $this->add('publish', 'checkbox');
        }
    }
}
```

#### getRequest

Get the current request.

returns `Illuminiate\Http\Request` instance.

#### getFields

Get all fields of the single Form class.

returns `Array` of fields.

#### getField

Get single field from the Form class.

It accepts 1 argument:

| Description       | Type    | Required | Default |
|-------------------|---------|----------|---------|
| Name of the field | String  | true     | -       |

```php
<?php
$model = User::find(1);

$form = FormBuilder::plain(['model' => $model])
    ->add('username', 'text')
    ->add('email', 'email')
    ->add('password');

$usernameField = $form->get('username');
```


**Note:** Another way to get single field from the Form class is getting it by property.
Form class has magic methods that handle finding fields.

Using above form:
```
    $usernameField = $form->username;
```


#### disableFields
Disable all fields in a form, by adding `disabled` attribute to the fields. Useful when you only want to use form as read only.

```php
$form = FormBuilder::create('App\Forms\SearchForm');

$form->disableFields();
```

#### enableFields

Enable all fields in a form, by removing `disabled` attribute to the fields.

```php
$form = FormBuilder::create('App\Forms\SearchForm');

$form->enableFields();
```

#### formOptions
These options are passed to the view into the `Form::open` method.

If Form class will always have same method or url during it's usage, it's good pratice to set this property on the form class to avooid repeating.

Type: `Array`
Default: `['method' => 'GET', 'url' => null]`


```php
<?php namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class SearchForm extends Form
{
    protected $formOptions = [
        'method' => 'GET',
        'url' => '/search'
    ];

    public function buildForm()
    {
        $this->add('term', 'text');
    }
}
```

And then it's enough to call this:

```php
$form = FormBuilder::create('App\Forms\SearchForm');
```

#### showFieldErrors
This property is used to determine if we want to show validation errors in the form under each field.

Type: `Boolean`

Default: `true`

#### clientValidationEnabled
Since version 1.6.30 form automatically generates html5 validation properties in html.
Setting this to `false` will remove those properties.

Type: `Boolean`

Default: `true`
