---
layout: page
title: "Checkbox and radio"
category: field
order: 4
date: 2015-05-24 20:52:29
---

##### Checkbox
Checkbox by itself is not commonly used. Some use case can be `remember me` checkbox on login:

```php
<?php
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
<?php
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
