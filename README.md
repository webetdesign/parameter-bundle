# webetdesign/parameter-bundle

Bundle for add parameter management.

## Requirement
- PHP ^7.4
- symfony ^4

## Installation
Add the repo to your composer.json

```json
"repositories": [
	 {
	   "type": "git",
	   "url": "https://github.com/webetdesign/parameter-bundle.git"
	 }
],
```

 And 

```
composer require webetdesign/parameter-bundle
```

## Configuration

```yaml
#config/packages/webetdesign_parameter.yaml
web_et_design_parameter:
  types:
      - text
      - textarea
      - number
      - list
      - boolean
      - file
      - media
  fixtures:
    fixture1:
      type: list #default text
      label: my parameter
      default_value: 
         - val1
         - val2
    fixture2:
      label: parameter text
      default_value: Hello

```

## Sonata admin
Sonata admin is not required for using this bundle but it's recommanded for management parameters.
Add this template in the twig configuration.

```yaml
#config/packages/twig.yaml
twig:
  ...
  form_themes:
    - ...
    - '@WebEtDesignParameter/Form/parameters.html.twig

```

## Fixtures
For add required parameters in database use this command in CI or composer post-update
```
php bin/console parameter:fixture
```

It's automatically add parameter define in the configuration "fixtures" section

## File parameter
For file parameters you will have to add the path in the parameter of your application like that:

```yaml
#config/services.yaml
parameters:
    product_catalogue_file_directory: '%kernel.project_dir%/public/upload/catalogue'

```

The key must be build with the following pattern: **[code of the parameter]_directory**

## Media type
To use the media parameter type, the webetdesign/wd-media-bundle must be enable.

````yaml
#config/packages/webetdesign/wd_media.yaml
categories:
  media_parameter:
      label: Media des paramètres
      formats:
        default:
          xl:
            #[...]
````
