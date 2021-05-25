# DiContainer
### Caution!
This is still on an experimental level.
Extensive testing needs to be done also.

# Purpose

This package provides a simple dependecy injection container.
It runs on the basis of injected data (PHP-array).
The syntax that is used for interpreting the wiring can be manipulated by the injection of the instance of a syntax class.


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
