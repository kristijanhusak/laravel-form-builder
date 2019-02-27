## 1.20.0
- Add field rules event [#491](https://github.com/kristijanhusak/laravel-form-builder/pull/491)(Thanks to [@rudiedirkx](https://github.com/rudiedirkx))
- Escape html with `e()` to respect Htmlable [#473](https://github.com/kristijanhusak/laravel-form-builder/pull/473)(Thanks to [@rudiedirkx](https://github.com/rudiedirkx))
- Fix `datetime_local` to `datetime-local` field constant [#483](https://github.com/kristijanhusak/laravel-form-builder/pull/483)(Thanks to [@nea](https://github.com/nea))
- Add missing `entity` field to constants [#484](https://github.com/kristijanhusak/laravel-form-builder/pull/484)(Thanks to [@nea](https://github.com/nea))
- Fix compatibility with Laravel 5.8 by using EventDispatcher `dispatch` method instead of `fire`
## 1.16.0
- Add option for form specific config. [#406](https://github.com/kristijanhusak/laravel-form-builder/pull/406) (Thanks to [@beghelli](https://github.com/beghelli))
- Add class enum that contains all field types [#455](https://github.com/kristijanhusak/laravel-form-builder/pull/455) (Thanks to [@tresa02](https://github.com/tresa02))
## 1.15.1
- Fix issue [#441](https://github.com/kristijanhusak/laravel-form-builder/issues/441)
- Fix issue [#442](https://github.com/kristijanhusak/laravel-form-builder/issues/442)
## 1.15.0
- Add translation template [#399](https://github.com/kristijanhusak/laravel-form-builder/pull/399) (Thanks to [@koenvu](https://github.com/koenvu))
- Add field error class [#411](https://github.com/kristijanhusak/laravel-form-builder/pull/411) (Thanks to [@n7olkachev](https://github.com/n7olkachev))
- Allow using different error bag per form [#414](https://github.com/kristijanhusak/laravel-form-builder/pull/414) (Thanks to [@Fellner96](https://github.com/Fellner96))
- Get PSR-4 namespace from composer [#424](https://github.com/kristijanhusak/laravel-form-builder/pull/424) (Thanks to [@icfr](https://github.com/icfr))
- Escape static field value [#407](https://github.com/kristijanhusak/laravel-form-builder/pull/407) (Thanks to [@beghelli](https://github.com/beghelli))
- Fix missing field name for rule closure [#403](https://github.com/kristijanhusak/laravel-form-builder/pull/403) (Thanks to [@yemenifree](https://github.com/yemenifree))
- Fix checking trueness of empty array in collection type [#412](https://github.com/kristijanhusak/laravel-form-builder/pull/412) (Thanks to [@kiperz](https://github.com/kiperz))
- Fix parent type not pushing options to children [#356](https://github.com/kristijanhusak/laravel-form-builder/pull/356) (Thanks to [@pimlie](https://github.com/pimlie))
- Use request as model when validating to properly validate collection types
- Setup named model after attaching model to form
- Fix custom closure interpreted as string when using html5 validation rules [#435](https://github.com/kristijanhusak/laravel-form-builder/pull/435) (Thanks to [@yarbsemaj](https://github.com/yarbsemaj))
- Fix radio and checkbox help block position [#440](https://github.com/kristijanhusak/laravel-form-builder/pull/440) (Thanks to [@sagarnasit](https://github.com/sagarnasit))
## 1.14.0
- Fix php7.2 compatibility
## 1.13.0
- Add Laravel 5.5 support [#377](https://github.com/kristijanhusak/laravel-form-builder/pull/377) (Thanks to [@wuwx](https://github.com/wuwx))
- Add field filters [#376](https://github.com/kristijanhusak/laravel-form-builder/pull/376) (Thanks to [@unckleg](https://github.com/unckleg))
- Add `data_override` closure for choice type fields [#383](https://github.com/kristijanhusak/laravel-form-builder/pull/383) (Thanks to [@yemenifree](https://github.com/yemenifree))
- Fix adding client validation attributes to non required fields [#379](https://github.com/kristijanhusak/laravel-form-builder/pull/379) (Thanks to [@koichirose](https://github.com/koichirose))

## 1.12.1
- Fix issue #354

## 1.12.0
- Add `createByArray` to Form builder form building forms with simple array - #316 (Thanks to [@saeidraei](https://github.com/saeidraei))
- Add ability to automatically validate form classes when they are instantiated by adding ValidatesWhenResolved trait - #345 (Thanks to [@mpociot](https://github.com/mpociot))
- Allow configuring plain form class - #319 (Thanks to [@rudiedirkx](https://github.com/rudiedirkx))
- Allow creating custom validation rules parser - #345 (Thanks to [@rudiedirkx](https://github.com/rudiedirkx))
- Use primary key as default property_key for EntityType field - #334 (Thanks to Thanks to [@pimlie](https://github.com/pimlie))
- Check if custom field already defined on rebuild form - #348 (Thanks to [@alamcordeiro](https://github.com/alamcordeiro))
- Fix child models not being bound correctly in collection forms - #325 (Thanks to [@njbarrett](https://github.com/njbarrett))
- Fix passing `choice_options` from view - #336 - (Thanks to Thanks to [@schursin](https://github.com/schursin))
- Fix ButtonGroupType having wrong template - #344 (Thanks to [@jayjfletcher](https://github.com/jayjfletcher))
- Fix CollectionType using request's `get()` instead of `input()` method - #346 (Thanks to [@unfalln](https://github.com/unfalln))

## 1.10.0
- Add `buttongroup` field type - #298 (Thanks to [@noxify](https://github.com/noxify))
- Allow custom `id` and `for` attributes for a field - #285
- Fix accessing fields from twig by adding `__isset` magic method - #301
- Use custom Form macro for labels in views

## 1.9.0
- Bump minimum php version to 5.6 - #276 (Thanks to [@max-kovpak](https://github.com/max-kovpak))
- Add support for Laravel 5.3 and fix EntityType lists method - #276 (Thanks to [@max-kovpak](https://github.com/max-kovpak))
- Add `alterFieldValues` and `alterValid` methods to Form class - #272 (Thanks to [@rudiedirkx](https://github.com/rudiedirkx))
- Fix collection type to use current request data if old input is not available - issue #268
- Fix automatic class append functionality that was added in PR #220 - use `class_append` option instead.


## 1.8.0
- Add default classes per field - #220 (Thanks to @jvdlaar)
- Set up ServiceProvider to be compatible with Laravel 5.3 - #236 (Thanks to @marcoraddatz)
- Added `getFieldValues` method to form which returns all field values from request - #248 (Thanks to @rudiedirkx)
- Added events after form and field creation/validation - #254 (Thanks to @rudiedirkx)
- Allow nested field names without usage of any child fields - #251 (Thanks to @rudiedirkx)
- Add `redirectIfNotValid` method to Form - #258 (Thanks to @koenvu)
- Add `class_append` option for `label_attr`, `attr` and `wrapper` options which appends classes to the default ones - #257 (Thanks to @koenvu)
- Add `form_rows` helper method, and pass `child_form` to view in ChildFormType, which contains internal Form instance - #262 (Thanks to @rudiedirkx)
- Fix adding `required` class to label even without client validation enabled - #261 (Thanks to @koenvu)

## 1.7.10
- Fix bug where `error_messages` were not namespaced properly in child forms ([#225](https://github.com/kristijanhusak/laravel-form-builder/issues/225))
- Add check for field name and forbid reserved words ([#232](https://github.com/kristijanhusak/laravel-form-builder/issues/232))
- Use Symfony's `TranslatorInterface` instead of Laravel's Translator implementation([#231](https://github.com/kristijanhusak/laravel-form-builder/issues/231))

## 1.7.0
- Add check for nested translations (Thanks to [@paultela](https://github.com/paultela))
- Add `label_show` property for field to allow hiding the label without setting it to false (Fixes issue with validation where error message requires label)
- Add `error_messages` property for field to allow defining error messages in `buildForm`.

## 1.6.50
- Translate form field labels if translation exist, and add `language_name` option to Form class in order to allow translating fields from specifing file (Thanks to [@koenvu](https://github.com/koenvu))
- Add required attribute if validation rules contains `required` option (Thanks to [@koenvu](https://github.com/koenvu))
- Fix issue #211 - Multiple select name wrong in subform
- Fix issue #212 - ChoiceType ignoring own validation rules
- Fix issue #213 - required attribute applied even if client validation is disabled

## 1.6.42
- Fix issue #191 - clientValidationEnabled for child forms not working
- Fix issue #202 - 'Field already exists' exception when using `compose()` method
- Fix issue #204 - 'default_value' not handled properly for CheckableType
- Fix issue #205 - Data not being passed properly to child form

## 1.6.41
- Fix LaravelCollective compatibility with older versions of Laravel (Thanks to [@marcoraddatz](https://github.com/marcoraddatz))
- Do not throw exception when trying to remove non existing field - [#149](https://github.com/kristijanhusak/laravel-form-builder/issues/149) (Thanks to [@marcoraddatz](https://github.com/marcoraddatz))
- Fix README to match php 5.5+ syntax (Thanks to [@marcoraddatz](https://github.com/marcoraddatz))

## 1.6.40
- Setup compatibility with Laravel 5.2 and LaravelCollective
- Bump php requirement to version 5.5
- Fix bug where different request was used in tests
- Fix `setModel` method
- Add some deprecation warnings

## 1.6.31
- Add `template_prefix` option form form and fields (Thanks to [@koenvu](https://github.com/koenvu))
- Add `empty_row` option for Collection type to remove initial empty row when there is no data for it
- Add `removeChild` option for ParentType fields to allow removing child
- Fix child form value binding when parent form has a name.
- Fix `renderUntil` to throw exception when field does not exist (Thanks to [@pnoonan](https://github.com/pnoonan))
- Fix form stub to satisfy PSR-2 standard (Thanks to [@koenvu](https://github.com/koenvu))


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
