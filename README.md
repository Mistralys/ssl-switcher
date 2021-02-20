# SSL Switcher

PHP class to enforce an SSL connection in a website.

## Installation

Simply require the package in your `composer.json` file.

```
composer require "mistralys/ssl-switcher"
```

## Quick start

The simplest way to use the script is to use the static `autoSwitch()` method:

```php
use Mistralys\SSLSwitcher;

SSLSwitcher::autoSwitch('https://domain.extension');
```

This will check if the current connection is SSL or not, and redirect to the target URL as needed. Otherwise, the script continues as usual.

NOTE: Any existing request parameters are appended to the target URL, to stay on the same page.

## SSL switching conditions

The script will redirect to the https version of the website only if the following conditions are met:

1) The script is not run from the command line
2) The script is not running from localhost
3) SSL is not enabled

## Using class methods

When instantiating the class, you get access to its methods - for example to run some checks
in your application before doing the switch to https.

```php
use Mistralys\SSLSwitcher;

$switcher = new SSLSwitcher('https://domain.extension');

// Is a switch to https required?
if($switcher->isSwitchRequired())
{
    // Do something before the redirect
}

// Redirect
$switcher->switch();
```

## Utility methods

The class has a few utility methods that can come in handy.

- `isCLI()` - Is the script running from the command line?
- `isLocalhost()` - Is the server run as localhost / 127.0.0.1?
- `isSSLActive()` - Is SSL enabled?
- `getTargetURL()` - The URL the script would redirect to.

## Disabling the exit call

By default, the class will call `exit()` after the redirect. If your application needs to handle this instead, the exit can be turned off:

```php
use Mistralys\SSLSwitcher;

$switcher = new SSLSwitcher('https://domain.extension');
$switcher->setExitEnabled(false);
$switcher->switch();

// Handle the exit in the script instead
if($switcher->isSwitchRequired())
{
    exit();
}
```