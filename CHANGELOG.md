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
