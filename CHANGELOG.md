## 1.6.30
- Add client side validation (Thanks to [@barryvdh](https://github.com/barryvdh))
- Extract some part of html in views to own partial
- Fix select with `multiple` option naming ([#150](https://github.com/kristijanhusak/laravel-form-builder/issues/150))
- Fix model binding on existing instances of child form([#157](https://github.com/kristijanhusak/laravel-form-builder/issues/157))
- Fix duplicate `help_block` on select/choice ([#153](https://github.com/kristijanhusak/laravel-form-builder/issues/153))

## 1.6.20
- Add Validation to the Form class ([#135](https://github.com/kristijanhusak/laravel-form-builder/issues/135))
- Add getters for value and default_value (getValue() and getDefaultValue())
- Add option to pass template to form class through options
- Make setValue method public
- Make unspecified labels to use ucfirst instead of ucwords

## 1.6.12
- Add `path` and `namespace` options to the form class generator command.

## 1.6.11
- Fix checkables checked state not working with model
- Fix adding `for` attribute twice to label
- Add some tests

## 1.6.10
- Replace [Illuminate/Html](https://github.com/illuminate/html) with [LaravelCollective/Html](https://github.com/LaravelCollective/html)
- add `disableFields` and `enableFields` functions to Form class to allow disabling all fields in the form
- Fix collection type model value binding

## 1.6.0
- **Minor BC Break** - Rename `default_value` to `value`, and use `default_value` as fallback value if no `value` or model data available

    If You published views update all templates and set `$options['default_value']` to `$options['value']`

- Add form composition (Add fields from another form with `compose()` method) - Thanks to [@theshaunwalker](https://github.com/theshaunwalker)
- Add trait for controller that allows shorter sintax (`$this->form()` and `$this->plain()`)
- Fix `renderUntil` to check the name by real name instead of namespaced name
- Fix collection of child forms not rendering when there is no data
- Fix collection prototype to return proper `prototype_name` for nested collections
- Return `$this` from `rebuildForm()` method to allow regenerating form in loops


## 1.5.10
- Fix collection of forms not rendering when there is no model or any data passed to collection.

## 1.5.1
- Add `entity` field type that allows fetching specific Model data

## 1.5.0
- Bind all fields values manually without Laravel's form builder `Form::model` (Check note below for possible BC break)
- Add possibility to use Closure as default value for fields which solves issues like in [#98](https://github.com/kristijanhusak/laravel-form-builder/issues/98#issuecomment-103893235)
- Fix passing model to child forms
- Set FormBuilder class properties to protected to allow extending
- Optmization and other minor fixes
gg
**Note**: If You published views before, they need to be updated to prevent possible breaking.
Since value binding is now done in package, and `Form::model` is removed, views needs to be republished (or updated) to remove `Form::model` from [form.php](https://github.com/kristijanhusak/laravel-form-builder/blob/master/src/views/form.php). Also [choice.php](https://github.com/kristijanhusak/laravel-form-builder/blob/master/src/views/choice.php) needs to be updated to pass `selected` value.

## 1.4.26
- Fix expanded/multiple choice fields id by prefixing it with properly formatted name

## 1.4.25
- Add `addBefore` and `addAfter` methods to Form class to allow adding fields at specific location
- Add `required` option for all field types, that adds class `required` (configurable in config) to label, and `required` attribute to field.

## 1.4.22
- Fix choice field type not adding `[]` on regular forms

## 1.4.21
- Add `wrapper` option for button type, defaults to false
- Fix `help_block` rendering twice on repeated field type
- Fix choice field type adding additional `[]` to the name in child forms/collections

## 1.4.20
- Add `help_block` option for fields which renders note under the field (http://getbootstrap.com/css/#forms)
- Fix repeated type not closing tags properly

## 1.4.13
- Fix default_value for child forms ([#77](https://github.com/kristijanhusak/laravel-form-builder/issues/80))
- Pass form data to child forms.

## 1.4.12
- Fix issue with showing validation errors for nested forms ([#78](https://github.com/kristijanhusak/laravel-form-builder/issues/78). Thanks to [@theshaunwalker](https://github.com/theshaunwalker))

## 1.4.11
- Add ability to exclude some fields from rendering ([PR-77](https://github.com/kristijanhusak/laravel-form-builder/pull/77). Thanks to [@theshaunwalker](https://github.com/theshaunwalker))

## 1.4.10
- Use old input for collection field type. (Example: after failed validation, redirect back withInput).
- Add `static` type field.
- Add `form_until` function that renders fields until specified field(Thanks to [@hackel](https://github.com/hackel))
- using `getData` without param returns all data

## 1.4.06
- Bind data to child form every time to allow setting null.

## 1.4.05
- Fix id and name collision.

## 1.4.04
- Fix collection type form model binding.

## 1.4.03
- Fix custom template per field.

## 1.4.02
- Fix adding enctype multipart/form-data to form when field is of type file.

## 1.4.01
- Fix setting field id on child forms.

## 1.4.0
- Allow calling form methods directly from child form
- Update views to print all errors for single field
- Fix custom field template for child forms
- Fix disabling errors for fields in form class

## 1.3.8
- Fix form model binding for named forms
- Collection type now pulls data from model if data not passed in `data` option

## 1.3.71
- Fix choices to show only one error message

## 1.3.7
- Update `repeated` type to work with child forms
- Add possibility to create named forms (fields get name[fieldname])

## 1.3.6
- Add support for some HTML5 input types(By @bishopb)

## 1.3.5
- Add `choice_options` property to `choice` field type that handles each radio/checkbox options when `expanded` is true.
- Allow disabling wrapper for fields by setting it to `false`

## 1.3.4
- Fix child form rebuild bug.

## 1.3.3
- Don't override existing aliases for Form and Html (Fixed by @squigg)

## 1.3.2
- Allow passing any type of model to form class (array, object, eloquent model, Collection).

## 1.3.1
- Fix bug where wrapper was not showing if label was false.

## 1.3.0
- Add [Collection](https://github.com/kristijanhusak/laravel-form-builder#collection) type
- Minor fixes

## 1.2.0
- Allow adding child forms with full class name which will be instantiated automatically
- Add FormBuilder class instance to the Form class
- Setting label explicitly to false in the field options disables printing label for that field
- Minor fixes

## 1.1.11
- Add `default_namespace` configuration to allow typing only class name when creating form (Same functionality like for routes)

## 1.1.10
- Fix `loadViewsFrom` in the service provider and add publishes paths.

## 1.1.9
- Fix service provider.

## 1.1.8
- Dynamically access children in parent form types.

## 1.1.7
- Fix empty select value to be empty string instead of `0`

## 1.1.6
- Append `[]` to field name automatically if multiple attribute is set to true.

## 1.1.5
- Fix `child form validation errors not showing` bug.

## 1.1.4
- Added `repeated` type field (https://github.com/kristijanhusak/laravel-form-builder#field-customization)
- Minor fixes

## 1.1.3
- Added [Child form](https://github.com/kristijanhusak/laravel-form-builder#child-form) type
- Added `rebuildForm` method
- Added `getRequest` method
- Added `setData` and `getData` options to form
- Minor fixes

## 1.1.2
- `form_end()` function now also renders rest of the form fields - can be changed with 2nd parameter as false(`form_end($form, false)`)
- Minor fixes

## 1.1.1
- Added `remove()` and `modify()` methods to Form class
- Added 'empty_value' option for select
- `choice` and `select` types now needs `selected` option to pass key instead of value of choice

## 1.1
- Added [Laravel 4](https://github.com/kristijanhusak/laravel-form-builder/tree/laravel-4) support (Tags 0.*)
- Config loaded outside of class and injected as array
- Changed command from `laravel-form-builder:make` to `form:make`

## 1.0
- Initial version
