---
layout: page
title: "Basic input types"
category: field
order: 2
date: 2015-05-24 21:06:38
---

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
<?php
$this->add('name', 'text', [
    'default_value' => 'John'
]);
```

`default_value` (String) (Default: null) - Used for setting default value.

If not provided, Form class will search for `name` property on the passed `model`.

In case you want to modify the value pulled from model before it's passed to view, you can use Closure to manipulate data:

```php
<?php
$this->add('name', 'text', [
    'default_value' => function ($name) {
        return 'This is name: ' + $name;
    }
]);
```
