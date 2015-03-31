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
