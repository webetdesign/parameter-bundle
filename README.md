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
