## View

### JsonView
The JsonShimView aims to backport 3.x to 2.x.
In 3.x there will be 4 flags set as default (as they require PHP 5.3+): `JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT`.
This is to comply with RFC4627.

Use this class and those will be then already set by default for your new 2.x app, as well.
This eases migration of 2.x to 3.x, since the output will not suddenly change.

#### Passing custom options
You can pass custom options easily (available since 2.6.5, but with this shim class already for any 2.x version!):
```php
// We want JSON_PRETTY_PRINT to always be on (not just in debug mode):
$options = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PRETTY_PRINT;
$this->set('_jsonOptions', $options);
```

Note: Passing `false` will get you the former 2.x behavior again.

#### DRY Configure options
You can also declare your options side-wide using Configure key 'Shim.jsonOptions'.

### FormHelper::input() and inputs()
`FormHelper::input()` and `FormHelper::inputs()` are deprecated since 3.4.0.
If the project is going to be migrated directly to CakePHP 3.4.0 or higher then 
'Shim.warnAboutFormInputs' shim can be used to warn about these deprecated 
methods. `FormHelper::control()` and `FormHelper::controls()` are supposed 
to be used instead.

The support of 'div', 'before', 'after', 'between' and 'errorMessage' options was removed in 3.x.

In order to migrate the code that uses `input()` method with these deprecated options
use specific input type methods `text()`, `select()`, `radio()` etc., 
`label()` method for the label and place the code from 'div', 'before', 'after', 
'between' and 'errorMessage' to the corresponding places around the input 
and the label in HTML.

### FormHelper::inputDefaults() and 'inputDefaults' option
The 'inputDefaults' option has been removed from `create()` and 
`FormHelper::inputDefaults()` method has been removed in 3.x. 
'Shim.formInputDefaults' shim can be used to warn about this deprecated method 
and the option. The code from `inputDefaults` can be moved to form inputs.