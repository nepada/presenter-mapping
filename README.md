Presenter Mapping
=================


Installation
------------

Via Composer:

```sh
$ composer require nepada/presenter-mapping
```

Register the extension in `config.neon`:

```yaml
extensions:
    - Nepada\Bridges\PresenterMappingDI\PresenterMappingExtension
```


Usage
-----

The extension replaces standard `PresenterFactory` provided by `ApplicationExtension` and adds extended support for presenter mapping. The new presenter mapping system is fully compatible with simple mapping from Nette.

### Submodule mapping

Nette supports mapping based only on the root module name, this package adds support for separate mapping for submodules. You can define different mapping for submodule `Foo:Bar` and it will take precendence over the mapping defined for `Foo` and `*` respectively.

Example:
```yaml
application:
    mapping:
        'Foo': Foo\*Module\*Presenter
        'Foo:Bar': Bar\*Module\*Presenter
```

### Presenter class mapping

You can define mapping between presenter name and class directly for individual presenters. This is especially useful when you need to override a single presenter from a module provided for example by external composer package.

Example:
```yaml
application:
    mapping:
        'Foo:Bar:Baz': FooBar\BazPresenter
```

### Configuration from another `CompilerExtension`

Some extensions may need to setup their own presenter mappings, this can be done in `beforeCompile()` phase by customizing setup of `PresenterMapper`.

```php
$presenterMapper = $containerBuilder->getByType(Nepada\PresenterMapping\PresenterMapper::class);
$containerBuilder->getDefinition($presenterMapper)
    ->addSetup('setPresenterMapping', ['Foo:Bar:Baz', FooBar\BazPresenter::class])
    ->addSetup('setModuleMapping', ['Foo:Bar', 'Bar\*Module\*Presenter'])
    ->addSetup('setModuleMapping', ['Foo', 'Foo\*Module\*Presenter']);
```
