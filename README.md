# Dravencms Map module

This is a simple Map module for dravencms

## Instalation

The best way to install dravencms/map is using  [Composer](http://getcomposer.org/):


```sh
$ composer require dravencms/map:@dev
```

Then you have to register extension in `config.neon`.

```yaml
extensions:
	map: Dravencms\Map\DI\MapExtension
```

## Admin authorization and mutations

Administration uses User 3.1, Admin 3.2 and Application 0.3 guards. Screens and write callbacks enforce the existing provider-defined permissions server-side, including direct URLs and bulk callbacks. Mutating signals require POST and a session-bound action token; synchronize the matching admin assets when upgrading. See the User package's `user:security:migrate` instructions for the password-reset schema change.
