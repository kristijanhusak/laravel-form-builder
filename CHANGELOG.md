**Note:** [Laravel 4](https://github.com/kristijanhusak/laravel-form-builder/tree/laravel-4) branch is also affected by changes from version **1.1** and above (tagged as 0.* instead of 1.*)

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