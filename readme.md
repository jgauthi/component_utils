# Component Utilities
My PHP functions and several utility methods of different types combined with the [nette/utils](https://doc.nette.org/en/utils) library. Some methods have been [archived](https://github.com/jgauthi/component_utils/tree/v2.1) and are no longer used since the usage of nette/utils.

## Prerequisite

* PHP 5.6+ (v1) or 7.4 (v2), or 8.2 (v2.5+)

## Install
Edit your [composer.json](https://getcomposer.org) (launch `composer update` after edit):
```json
{
  "repositories": [
    { "type": "git", "url": "git@github.com:jgauthi/component_utils.git" }
  ],
  "require": {
    "jgauthi/component_utils": "2.*"
  }
}
```


## Documentation
You can look at [folder example](example).

Documentation links for nette/utils _(used by this utils pack)_:
1. [Arrays](https://doc.nette.org/en/utils/arrays), must be used with `Jgauthi\Component\Utils\Arrays`
2. [Callback](https://doc.nette.org/en/utils/callback)
3. [Date and Time](https://doc.nette.org/en/utils/datetime), must be used with `Jgauthi\Component\Utils\Date`
4. [Filesystem](https://doc.nette.org/en/utils/filesystem)
5. [Helper Functions](https://doc.nette.org/en/utils/helpers)
6. [HTML Elements](https://doc.nette.org/en/utils/html-elements), must be used with `Jgauthi\Component\Utils\Html`
7. [Images](https://doc.nette.org/en/utils/images), must be used with `Jgauthi\Component\Utils\Image`
8. [~~JSON~~](https://doc.nette.org/en/utils/json) use instead: `Jgauthi\Component\Utils\Json`
9. [Paginator](https://doc.nette.org/en/utils/paginator)
10. [Random Strings](https://doc.nette.org/en/utils/random)
11. [SmartObject](https://doc.nette.org/en/utils/smartobject)
12. [PHP Reflection](https://doc.nette.org/en/utils/reflection)
13. [Strings](https://doc.nette.org/en/utils/strings), must be used with `Jgauthi\Component\Utils\Strings`
14. [Floats](https://doc.nette.org/en/utils/floats)
15. [PHP Types](https://doc.nette.org/en/utils/type)
16. [Validators](https://doc.nette.org/en/utils/validators)
