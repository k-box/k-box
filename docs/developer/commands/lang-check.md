# `lang:check` command

Verify that all the strings in english language files can be found in the other languages.

The command reads all the language files in `resources/lang/en` and compare all the translation
strings in the other languages to find untranslated strings.

## Usage

```
$ php artisan lang-check [--report=file]
```

When the command executes, at the top of the output, you will be able to see language statistics, like 
how many strings are translated in a language and the available languages

```
Checking language files...
Checking de translations...
  6.21% [60 / 966]
Checking ru translations...
  98.24% [949 / 966]

languages: 2
translation: 52.23%
```

By default the command will output the language report on the console output. The report consists in 
the list of files that contains untranslated strings, like in the code block below

```
ru

administration 146/150
settings.analytics_section
settings.analytics_section_help
settings.analytics_token_field
settings.analytics_save_btn

dashboard 1/2
project_edition
```

for all the files the untranslated strings are outputted in the same form as the Laravel translation 
string used as parameter to the `trans` helper.

The option `--report` can be used to redirect the output to a text file. The file name must be always 
specified.
