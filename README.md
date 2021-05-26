# DiContainer
### Caution!
This is still on an experimental level.
Extensive testing needs to be done also.

# Purpose

This package provides a simple dependecy injection container.
It runs on the basis of injected data (PHP-array).
The syntax that is used for interpreting the wiring can be manipulated by the injection of the instance of a syntax class.




<div>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;</div>

# Installation
### Using Composer

```shell script
composer require miniature/di_container
```
### Downloading Package

Unzip to a directory named **`Miniature`**.
Add to your autoloading something like the following:

```PHP
<?php

function miniature_autoload($class)
{
    $fileName = str_replace('\\', '/', realpath(__DIR__) . '/' . $class ) . '.php';
    if (preg_match('/^(.*\/Miniature)\/(\w+)\/((\w+\/)*)(\w+)\.php/', $fileName)) {
        $newFileName = preg_replace(
            '/^(.*\/Miniature)\/(\w+)\/((\w+\/)*)(\w+)\.php/',
            '$1/$2/src/$3$5.php',
            $fileName
        );
        if (is_file($newFileName)) {
            require $newFileName;
        }
    }
}
spl_autoload_register('miniature_autoload');

```

Can be that you must adjust the file path concatenation for `filePath`
by setting the relative path in the `filepath()` statement.






## Instantiating
```PHP
$myDiContainer = (new \Miniature\DiContainer\DiContainer())
    ->readMappings($configArray);
```

### The syntax
Find information about the syntax
[here](https://github.com/guidoerfen/miniature-component#the-di-mapping).


<a name="syntax-overrides"></a>
#### Overriding the syntax

The syntax can be overridden by incting an implementation of
`\Miniature\DiContainer\Syntax\MapperAbstract`.
The class
`\Miniature\DiContainer\Syntax\MapperSymfonyStyle`
is an example of how it works.

```PHP
$myDiContainer = (
    new \Miniature\DiContainer\DiContainer(
            new \Miniature\DiContainer\Syntax\MapperSymfonyStyle()
        )
    )->readMappings($configArray);
```
### Providing the mapping

This is absolutely your choice where the mapping comes from.

We don't recomment using the reader class `Miniature\Component\Reader\Config`
out of context since it might be moved int the future.

If you want the advantages of the
[Reader](https://github.com/guidoerfen/miniature-component#reading-the-configuration-directory)
and the
[environment based overrides](https://github.com/guidoerfen/miniature-component#environment-based-overrides)
You might consider the use of the
[`public` key](https://github.com/guidoerfen/miniature-component#key-public)
 in combination of
[the availability in environments](https://github.com/guidoerfen/miniature-component#setenvallowingpublicaccess)
in order to create a grey-box/white-box behaviour
[component](https://github.com/guidoerfen/miniature-component#the-instance-of-the-component)
as a provider for your DI-Mapping.

