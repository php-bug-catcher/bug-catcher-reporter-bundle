# Catch every bug in your Symfony application

## Installation

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
$ composer require php-sentinel/bug-catcher-reporter-bundle:dev-main
```

### Applications that don't use Symfony Flex

#### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require php-sentinel/bug-catcher-reporter-bundle
```

#### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    BugCatcher\Reporter\BugCatcherReporterBundle::class => ['all' => true],
];
```

### Configuration
**if you want sent caught errors via http request**

```yaml
# config/packages/bug_catcher.yaml
services:
    app.chain_uri_catcher:
        class: BugCatcher\Reporter\UrlCatcher\ChainUriCatcher
        arguments:
            $uriCatchers:
                - '@bug_catcher.uri_catcher.http_catcher'
                - '@bug_catcher.uri_catcher.console_catcher'
framework:
    http_client:
        scoped_clients:
            # only requests matching scope will use these options
            bug_catcher.client:
                base_uri: 'https://your-bug-catcher-instance:8000'
bug_catcher:
    project: 'dev'
    http_client: 'bug_catcher.client'
    uri_cather: 'app.chain_uri_catcher'
```

**Configure monolog**

```
composer require symfony/monolog-bundle
```
```yaml
# config/packages/monolog.yaml
monolog:
    handlers:
        bug_catcher:
            type: service
            id: bug_catcher.handler
            level: 500
```