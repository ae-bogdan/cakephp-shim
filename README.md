# Shim plugin for CakePHP
[![Build Status](https://api.travis-ci.org/dereuromark/cakephp-shim.svg)](https://travis-ci.org/dereuromark/cakephp-shim)
[![License](https://poser.pugx.org/dereuromark/cakephp-shim/license.svg)](https://packagist.org/packages/dereuromark/cakephp-shim)
[![Minimum PHP Version](http://img.shields.io/badge/php-%3E%3D%205.4-8892BF.svg)](https://php.net/)
[![Coding Standards](https://img.shields.io/badge/cs-PSR--2--R-yellow.svg)](https://github.com/php-fig-rectified/fig-rectified-standards)

Shim plugin to "shim" functionality up and down for CakePHP major versions.
It also provides some small fixes.

## This branch is for backporting 3.x to 2.x
or to shim certain 3.x functionality/features in 2.x.

This is super-useful if you plan on upgrading to the next major framework version
in the (near) future. Using the 3.x shims, you can already use the new functionality and even more
importantly the new syntax.
This way you save yourself quite some time, because that code will need almost zero refactoring when
finally upgrading. So be smart and prepare your 2.x application already for 3.x.
All new code ideally is as close to 3.x as it gets, making the future upgrade as smooth as possible.

**This plugin requires CakePHP 2.5+** (ideally you always use the current stable version).

## Installation
Please see [SETUP.md](docs/SETUP.md)

## Usage
Please see [Docs](/docs)

## Main shims

### ModernPasswordHasher / FallbackPasswordHasher
Already use the PHP5.5+ password functionality with the ModernPasswordHasher class and the Passwordable behavior. Easily upgradable to 3.x in minutes then.
That includes auto-conversation (on-the-fly upon login) of old hashs to the new ones via `Fallback` password hasher class.

### More shims
- PasswordHasherFactory to easily load your hasher classes.
- Model::updateAllJoinless() - since 3.x won't join updateAll() anymore.
- Model::deleteAllJoinless() - since 3.x won't join updateAll() anymore.
- Model::get() the new 3.x way.
- IntegrationTestCase (replaces the deprecated ControllerTestCase)
- Shimmed ControllerTestCase (defaults to GET by default) including referrer reset
for WebTestRunner and additional debugging tools.
- Assert query strings (and report usage of deprecated named params) if desired using `Configure::write('App.warnAboutNamedParams', true)`
- Assert contain (and report wrong recursive level) if desired using `Configure::write('App.warnAboutMissingContain', true)`
- Controller::paginate() loading config defaults and providing query string pagination by default.
- Bootstrap configuration for out-of-the-box query string functionality for CakeDC Search plugin.
- Header monitor via `App.monitorHeaders` to assert no output is done before the response class.
- FormShim and HtmlShim helpers for detection of view deprecations.
- Auto-301-redirects (or 404s) for named params => query strings.

## Main fixes
- Controller::disableCache() to help to write that directive to the browser for all (even IE).
- Correct auto-aliasing for models' `$order` property.

## TODO
- SEO dispatching filter to prevent duplicate content via multiple ways of accessing an action.
- Add warnings about deprecated `$this->data` access in Controller, about request `$this->...` usage in a controller or component.
- (Maybe backport CakePHP 3.0 core FlashComponent and FlashHelper - will probably be a 2.7 feature soon)

## More shims
can be found in my [Tools plugin](https://github.com/dereuromark/cakephp-tools) directly:

### FlashComponent and FlashHelper
A 3.x branch-off that allows stackable flash messages. It uses the same syntax as in 3.x, so you can
flawlessly upgrade without touching the flash functionality.

### RssView
Use RssView (and view-less action) instead of the akward and limited helper approach.

### TestConsoleOutput
TestConsoleOutput() for stdout and stderr instead of mocks. Less fiddling around.

... and [more](https://github.com/dereuromark/cakephp-tools/blob/master/docs/Shims.md)


## Stay up to date
It is wise to always use the current master (latest minor framework version).
This can usually be one by replacing the core folder (or composer config) and a few tweaks here and there.

Read the migration guides of the official documentation on that one.
You can also leverage the [Upgrade plugin](https://github.com/dereuromark/cakephp-upgrade) to automate some if it.

Also see [these tips](https://github.com/dereuromark/cakephp-upgrade/wiki/Tips-Upgrading-to-CakePHP-2.x).