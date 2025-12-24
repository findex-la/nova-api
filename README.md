## Support us

At Opscale, we’re passionate about contributing to the open-source community by providing solutions that help businesses scale efficiently. If you’ve found our tools helpful, here are a few ways you can show your support:

⭐ **Star this repository** to help others discover our work and be part of our growing community. Every star makes a difference!

💬 **Share your experience** by leaving a review on [Trustpilot](https://www.trustpilot.com/review/opscale.co) or sharing your thoughts on social media. Your feedback helps us improve and grow!

📧 **Send us feedback** on what we can improve at [feedback@opscale.co](mailto:feedback@opscale.co). We value your input to make our tools even better for everyone.

🙏 **Get involved** by actively contributing to our open-source repositories. Your participation benefits the entire community and helps push the boundaries of what’s possible.

💼 **Hire us** if you need custom dashboards, admin panels, internal tools or MVPs tailored to your business. With our expertise, we can help you systematize operations or enhance your existing product. Contact us at hire@opscale.co to discuss your project needs.

Thanks for helping Opscale continue to scale! 🚀



## Description

Add default secured CRUD API endpoints for your Nova resources.

Integrations are everywhere, even managing your operations in your Nova app, external systems will need to communicate with your app for consuming data or keep records up to date. APIs are the best solution for that!

![API demo](https://raw.githubusercontent.com/opscale-co/nova-api/refs/heads/main/screenshots/nova-api.gif)

## Installation

[![Latest Version on Packagist](https://img.shields.io/packagist/v/opscale-co/nova-api.svg?style=flat-square)](https://packagist.org/packages/opscale-co/nova-api)

You can install the package in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash

composer require opscale-co/nova-api

```

Next up, you must register the tool with Nova. This is typically done in the `tools` method of the `NovaServiceProvider`.

```php

// in app/Providers/NovaServiceProvider.php
// ...
public function tools()
{
    return [
        // ...
        new \Opscale\NovaAPI\Tool(),
    ];
}

```

After registering the tool, you need to run two important commands:

1. **Publish the configuration file:**
   ```bash
   php artisan nova-api:install
   ```
   This command publishes the `nova-api.php` configuration file to your `config` directory.

2. **Sync your Nova resources:**
   ```bash
   php artisan nova-api:sync-resources
   ```
   This command scans your Nova resources and writes them to the configuration file, making them available as API endpoints. Run this command whenever you add new Nova resources that you want to expose via API.

This package uses [Orion](https://orion.tailflow.org/) and [Laravel Sanctum](https://laravel.com/docs/master/sanctum) internally to automatically create controllers for serving, requests for validating and policies for securing API for your Nova resources. Any further configuration can be done publishing the configuration file using:

`php artisan vendor:publish --tag=orion-config`

## Usage

You will see a "API Tokens" item in your menu by default. You can create your API Tokens here and you cab use them for any CRUD operation. 

## Testing

``` bash

npm run test

```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/opscale-co/.github/blob/main/CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email development@opscale.co instead of using the issue tracker.

## Credits

- [Opscale](https://github.com/opscale-co)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.