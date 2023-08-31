# Statamic Autograph

> Statamic Autograph is a Statamic addon that lets your team generate HTML code on the fly to add 
> custom e-mail signatures in their favourite e-mail client.  

## Features

With this addon, you can:

- Generate HTML code to set up your e-mail signature in your favorite e-mail client.
- Preview what the e-mail signature will look like for each team member.
- Get user data from control panel accounts or any collection.
- Fully customizable design with support for multiple Antlers template files.


## How to Install

You can search for this addon in the `Tools > Addons` section of the Statamic control panel and click **install**, or run the following command from your project root:

``` bash
composer require j-jalving/statamic-autograph
```

## How to Use

Using Statamic Autograph is really simple:

1. Make sure at least one template file is present in the templates folder (default: `resources/views/autograph`). A simple default template can be published as a starting point (see instructions below).
2. Open the Autograph page, select a user and click the Generate button.
3. An HTML code block will appear, copy this code to your prefered e-mail client. 
  
> Note: I don't have instructions how to do this for each client, but a quick Google search should do the trick.

## Creating a template

By default, the addon looks for `antlers.html` template files in the `resources/views/autograph` folder (though this path can be changed in the config). You can do everything in your template that you can do in any other Antlers view, but on top of that an `autograph` variable is avaiable containing all data for the selected user.


## Publishables

You can publish all of the publishables with:

```sh
php artisan vendor:publish --provider="JJalving\Autograph\ServiceProvider"
```

Or publish them individually by using tags:

```sh
php artisan vendor:publish --provider="JJalving\Autograph\ServiceProvider" --tag="config"
php artisan vendor:publish --provider="JJalving\Autograph\ServiceProvider" --tag="templates"
```
