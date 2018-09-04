# Core

This library is the primary dependency of other `seothemes/core` components, and the foundation of our config-driven WordPress themes.

It contains two key classes:
* `Core` to be extended to build other components, and:
* `Theme` which is responsible for instantiating components and injecting the correct configuration. 

## Installation

This library will be automatically installed as a dependency when you use other `seothemes/core` components, you won't need to install it separately.

## Usage

Components should be loaded in your theme `functions.php` file, using the `Theme::setup` static method. Code should run on the `after_setup_theme` hook (or `genesis_setup` if you use Genesis Framework). 

For example:

```php
add_action( 'after_setup_theme', function() {
    $config = include_once __DIR__ . '/config/defaults.php';
    \D2\Core\Theme::setup( $config );
} );
```
_See specific components for configuration file formatting and examples._

## Available Core Components

* [Asset Loader](https://github.com/seothemes/core/src/asset-loader): Load theme scripts and stylesheets.
* [Breadcrumbs](https://github.com/seothemes/core/src/Breadcrumbs): Change Genesis breadcrumb arguments.
* [Constants](https://github.com/seothemes/core/src/Constants): Define child theme constants.
* [Custom Colors](https://github.com/seothemes/core/src/CustomColors): Add Customizer color settings.
* [Demo Content Import](https://github.com/seothemes/core/src/DemoImport): Set One Click Demo Import plugin settings.
* [Genesis Settings](https://github.com/seothemes/core/src/GenesisSettings): Set or override default Genesis theme settings.
* [Google Fonts](https://github.com/seothemes/core/src/GoogleFonts): Enqueue Google Fonts.
* [Hooks](https://github.com/seothemes/core/src/Hooks): Add or remove action and filter hooks.
* [Image Sizes](https://github.com/seothemes/core/src/ImageSizes): Add or remove image sizes through configuration.
* [Page Layouts](https://github.com/seothemes/core/src/PageLayouts): Register and unregister Genesis layouts.
* [Page Templates](https://github.com/seothemes/core/src/PageTemplates): Unregister page templates.
* [Plugin Activation](https://github.com/seothemes/core/src/PluginActivation): Add recommended plugins with TGM Plugin Activation.
* [TextDomain](https://github.com/seothemes/core/src/Textdomain): Load a WordPress theme’s translated strings.
* [Theme Support](https://github.com/seothemes/core/src/ThemeSupport): Add or remove support for theme features.
* [Simple Social Icons](https://github.com/seothemes/core/src/SimpleSocialIcons): Set default Simple Social Icons plugin values.
* [Widgets](https://github.com/seothemes/core/src/Widgets): Register and unregister widgets.
* [Widget Areas](https://github.com/seothemes/core/src/WidgetAreas): Register or unregister widget areas.