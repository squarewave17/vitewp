<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Vite_Testing
 *
 * @wordpress-plugin
 * Plugin Name:       Vite testing
 * Plugin URI:        http://example.com/plugin-name-uri/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Your Name or Your Company
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       vite-testing
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('M_VERSION', '0.1.1');
define('M_PATH', dirname(__FILE__));
define('M_URI', home_url(str_replace(ABSPATH, '', M_PATH)));
define('M_HMR_HOST', 'http://vite-testing.test/');
define('M_ASSETS_PATH', M_PATH . '/dist');
define('M_ASSETS_URI', M_URI . '/dist');
define('M_RESOURCES_PATH', M_PATH . '/resources');
define('M_RESOURCES_URI', M_URI . '/resources');

add_action('admin_menu', function () {
    add_menu_page(
        'Molecular',
        'Molecular',
        'manage_options',
        'oxyframe',
        function () {
            echo '<div id="app"></div>';
        },
        'dashicons-admin-generic',
    );
});

class AssetResolver
{
    private $manifest = [];

    public function __construct()
    {
        // get the manifest
        $path = M_ASSETS_PATH . '/manifest.json';

        if (empty($path) || !file_exists($path)) {
            wp_die(__('Run <code>npm run build</code> in your application root!', 'fm'));
        }

        $this->manifest = json_decode(file_get_contents($path), true);
        // var_dump($this->manifest);
    }

    public function resolve(string $path)
    {
        $url = '';

        if (!empty($this->manifest["{$path}"])) {
            // $url = M_ASSETS_URI . "/{$this->manifest["{$path}"]['file']}";
            $url = M_PATH . "/{$this->manifest["{$path}"]['file']}";
        }

        return $url;
    }
}

function mol_enqueue_styles()
{
    $ar = new AssetResolver();
    wp_enqueue_style('mol-vue-css',  $ar->resolve('src/index.scss'), [], 'all');
    wp_enqueue_script('mol-vue-js', $ar->resolve('src/index.js'), [], 'all');
}

add_action('admin_enqueue_scripts', 'mol_enqueue_styles');
