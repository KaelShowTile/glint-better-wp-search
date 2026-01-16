# Glint Better WP Search

A WordPress plugin that enhances the default search functionality to support all post types, including custom post types, and provides customizable search result templates.

## Features

- **Multi-Post Type Search**: Search across all post types, including custom post types
- **Title and Content Search**: Searches both post titles and content
- **Customizable Templates**: Use custom search result templates in your child theme
- **Admin Settings**: Easy-to-use backend settings page to configure search options
- **Responsive Design**: Mobile-friendly search results layout
- **Default Template**: Includes a default search results template if no custom template is provided

## Installation

1. Download the plugin files
2. Upload the `glint-better-wp-search` folder to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Configure the settings via **Settings > Glint Search**

## Configuration

### Post Types Selection
In the admin settings page, select which post types should be included in search results. By default, posts and pages are included.

### Custom Template
Specify the filename of your custom search results template. Place this file in your child theme directory to override the default template.

Example: `search-results.php`

## Usage

### Default Behavior
The plugin automatically modifies WordPress search queries to include selected post types and uses the custom template for displaying results.

### Search Form Function
Use the `glint_search_form()` function to display a search form anywhere in your theme:

```php
// Basic usage
glint_search_form();

// With custom parameters
glint_search_form(array(
    'placeholder' => 'Search our site...',
    'button_text' => 'Go',
    'class' => 'my-custom-search-form'
));

// Return the form HTML instead of echoing
$form_html = glint_search_form(array('echo' => false));
```

### Custom Template
To create a custom search results template:

1. Create a file named `search-results.php` (or your chosen filename) in your child theme directory
2. Copy the structure from `templates/search-results.php` in the plugin
3. Customize the HTML and CSS as needed

### Template Hierarchy
The plugin follows this template hierarchy:
1. Child theme template (if exists)
2. Parent theme template (if exists)
3. Plugin default template

## Files Structure

```
glint-better-wp-search/
├── glint-better-wp-search.php     # Main plugin file
├── includes/
│   ├── class-glint-search.php     # Search functionality
│   └── class-glint-search-settings.php # Admin settings
├── templates/
│   └── search-results.php         # Default search template
├── assets/
│   └── css/
│       └── glint-search.css       # Plugin styles
└── README.md                      # This file
```

## Hooks and Filters

The plugin uses standard WordPress hooks:
- `pre_get_posts` to modify search queries
- `template_include` to load custom templates
- `wp_enqueue_scripts` to load styles

## Requirements

- WordPress 4.0 or higher
- PHP 5.6 or higher

## Changelog

### 1.0.0
- Initial release
- Multi-post type search support
- Customizable templates
- Admin settings page
- Responsive design

## License

This plugin is licensed under the GPL v2 or later.

## Support

For support, please check the WordPress plugin repository or contact the developer.
