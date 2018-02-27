## Overview

Simple plugin for Ecomail API integration.

## Installation
Import routing in your routing.yml file:

```yml
czende_ecomail_plugin:
    resource: "@EcomailPlugin/Resources/config/routing.yml"
    prefix: /
```

Add plugin dependencies to your AppKernel.php

```php
public function registerBundles()
{
    return array_merge(parent::registerBundles(), [
        ...
        
        new \Czende\EcomailPlugin\EcomailPlugin(),
    ]);
}
```

## Usage

Add Ecomail API key and desired list ID to your parameters.yml file

```yml
parameters:
    ...
    
    ecomail_api_key: YOUR_API_KEY
    ecomail_list_id: LIST_ID
 ```

In your twig template include 

```twig
{% include '@EcomailPlugin/_subscribe.html.twig' %}
```

In case you'd like to submit the form with AJAX

1. Install assets  

```bash
$ bin/console assets:install --symlink
```

2. Override default sylius javascript template

```twig
{% block javascripts %}
    {{ parent() }}
    <script src="{{ asset('bundles/ecomailplugin/czende-ecomail-plugin.js') }}"></script>
    <script>
        $('#newsletter-form').joinNewsletter();
    </script>
{% endblock %}
```
