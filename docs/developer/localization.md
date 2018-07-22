# Localization

Localization of the User Interface and messages in other languages is a key topic.

Localization is part of a broather topic, called internationalization. A great reference article 
is [Design for internationalization](https://medium.com/dropbox-design/design-for-internationalization-24c12ea6b38f#.k6f86wehj) 
by John Saito.

> The focus is for localization of static text, not text inserted by users, like abstracts or titles.

Current language supported are:

- English
- Russian
- Tajik
- French

The language showed to the user is chosen according to the 
[Accept-Language HTTP header (RFC2616)](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4) 
sent by the browser.

If the browser request a language not available, the system will show the English version, as a fallback.
Once logged in, the user, can select its preferred language for the User Interface.

## Language files

Language files an localization rules follows the [Laravel Localization](http://laravel.com/docs/5.5/localization). 

The files are stored in `language` folders, represented by the two-letter language code (ISO 639-1).

The main language files can be found under [`resources/lang`](../../resources/lang).

In some cases there are specific packages that include additional language files:

- The content processing package has language files located in [`/packages/contentprocessing/lang`](../../packages/contentprocessing/lang)
- The license package has language files located in [`/packages/licenses/lang`](../../packages/licenses/lang)


### Translation of licenses

License description and names are localizable. 

The list of supported license is stored in the [licenses.json](../../packages/licenses/assets/licenses.json) file under [`/packages/licenses/assets`](../../packages/licenses/assets).

For the title and the license URL the localization happens directly in the JSON file.
The `title` and `license` property is an object whose keys are the language code. Adding a new language means adding a new key to that object.

> Make sure that the resulting [JSON object is valid](https://jsonformatter.curiousconcept.com/)

```json
{
    "id": "CC-BY-4.0",
    "name": "Creative Commons Attribution 4.0",
    "license": {
        "en": "http://creativecommons.org/licenses/by/4.0/legalcode"
    },
    "icon": "by.svg",
    "short_title": "CC BY 4.0",
    "title": {
        "en": "Creative Commons Attribution 4.0 International",
        "ru": "Creative Commons С указанием авторства 4.0 Всемирная"
    },
    "description": "cc-by-4.0.md"
}
```

The license description is located in a separate file under the [`descriptions/{language}`](../../packages/licenses/assets/descriptions/) folder. 

The files are named accordingly to the license id in lower case and must be in markdown format.

> For Creative Commons please consider to pull in the official translation of the license name and deeds. Non official translations might be rejected.

## Localizing strings in PHP

Laravel offer some helper methods to write localized strings in the PHP source code and in blade templates.

- [`trans`](https://laravel.com/docs/5.2/helpers#method-trans)
- [`trans_choice`](https://laravel.com/docs/5.2/helpers#method-trans-choice)


### Date and time localization

#### `LocalizableDateFields` Trait

The trait `KBox\Traits\LocalizableDateFields` has been added to offer localized date and time output 
for Eloquent Models. Using the `LocalizableDateFields` trait the created_at, updated_at and deleted_at model 
dates can be easily localized.

This trait offers the following methods:

- `getCreatedAt` returns the localized creation date string
- `getUpdatedAt` returns the localized update date string
- `getDeletedAt` returns the localized deletion date string, if defined on the model
- `getCreatedAtHumanDiff` outputs the human diff between the current date and the creation date
- `getUpdatedAtHumanDiff` outputs the human diff between the current date and the update date
- `getDeletedAtHumanDiff` outputs the human diff between the current date and the deletion date,
  if defined on the model

Localization is performed according the pattern defined in `units.date_format_full` and `units.date_format` 
(file `units.php` in each language folder).

#### Helper methods

If you want to offer date and time localization use `Jenssegers\Date\Date` as the datetime class instead of Carbon.
The class `Jenssegers\Date\Date` is an extension of `Carbon`.

You can convert a `Carbon` instance to `Jenssegers\Date\Date` by using `Jenssegers\Date\Date::instance( /* Carbon */ $carbon_date)`
or by using the `localized_date(DateTime $dt)` helper, that takes a php `DateTime` instance 
(also a `Carbon` instance) and converts it to a localizable Date instance

Other helper methods available:

- `localized_date_human_diff(DateTime $dt)` that outputs a human diff between the current date and 
   the specified date if the difference in days is less than 2
- `localized_date_full(DateTime $dt)` outputs a localized long date/time (according to the 
   translation `units.date_format_full`)
- `localized_date_short(DateTime $dt)` outputs a localized short date (according to the 
   translation `units.date_format`)

## Localizing strings in Javascript

The javascript localization is handled with the help of the RequireJS [i18n plugin](https://github.com/requirejs/i18n).

Language files are constructed at build time and copied in the `/public/js/nls/` folder (please do not edit those files 
directly).

The system will load `/public/js/nls/lang.js` which contains the english fallback and then will try to load the specific 
locales, if available. The current locale is configured by the `require-config.blade.php` file in the `i18n` section of the 
RequireJS configuration.

### Building javascript language files

Construction of the language files is performed reading the `config/localization.php` file, which stores all the strings 
that should be made available to Javascript. In this file all the strings should be specified like they are the key of the 
Laravel `trans` helper.

To generate the javascript language files launch

```bash
php artisan dms:lang-publish 
```

You have to generate the language files everytime a translation string or the `config/localization.php` file is edited.

### Language module

Access to the localization is offered through the `language` module (RequireJS module). This code block shows how to 
reference the `language` module in a page module.

```js
define(['language'], function(Lang) {
    
	// Use Lang.trans( ...) 

});
```

#### Available module API

##### `trans`

```
trans ( messageKey : string, replacements : ReplacementObject ) : string
```

_Parameters:_

- `messageKey`: the key of the translation string to retrieve
- `replacements`: if the translation string contains placeholder, like `:attribute`, you could 
  substitute them with something more useful, see [ReplacementObject](#replacementobject) 

_Returns:_

the localized string, with applied replacements or messageKey if the corresponding localized 
string cannot be found


##### `choice`

```
choice ( messageKey : string, count : number, replacements : ReplacementObject ) : boolean
```
_Parameters:_

- `messageKey`: the key of the translation string to retrieve
- `count`: the number of elements used for the selection of the proper translation string
- `replacements`: if the translation string contains placeholder, like `:attribute`, you could 
  substitute them with something more useful, see [ReplacementObject](#replacementobject)

_Returns:_

the localized string, with applied replacements or messageKey if the corresponding localized string cannot be found

**Important Notice**: currently only english pluralization rules are supported.

##### `alternate`

```
trans ( messageKey : string, alternateMessageKey : string, basedOn : string, replacements : ReplacementObject ) : string
```

Choose the translation string from messageKey or alternateMessageKey based on the existence of basedOn key in replacements.

_Parameters:_

- `messageKey`: the key of the translation string to retrieve
- `alternateMessageKey`: the key of the translation string to retrieve as a fallback
- `basedOn`: the property name to check inside replacements to decide if use `messageKey` or `alternateMessageKey`
- `replacements`: if the translation string contains placeholder, like `:attribute`, you could substitute them with 
  something more useful, see [ReplacementObject](#replacementobject) 

_Returns:_

the localized string, with applied replacements, using messageKey if the property basedOn exists and not null in 
replacements, using alternateMessageKey otherwise


##### `has`

```
has ( messageKey : string ) : boolean
```

Tests if the given key is defined in the translation set.

_Parameters:_

- `messageKey`: the key of the translation string to retrieve

_Returns:_

`true` if the localized string exists, `false` otherwise.


###### ReplacementObject 

The replacement object is a plain Javascript object in which keys must be named parameters contained in the 
translation string and values could be any string. Values will be substituted to placeholders in the translation string.

For example, consider the following translation string

```
":page cannot be found"
```

the replacement object that can be used is

```json
{ "page": "value" }
```


