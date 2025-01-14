<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://framework.tech
 * @since             1.0.0
 * @package           VE Dev Functions
 *
 * @wordpress-plugin
 * Plugin Name:       VE Debugging Output Functions
 * Plugin URI:        https://ve-dev-functions.com
 * Description:       Various simple functions for debugging while developing a plugin 
 *                    (error_log to a file, for example)
 * Version:           2.0.0
 * Author:            Vladimir Eric
 * Author URI:        https://framework.tech
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ve-dev-functions
 * Domain Path:       /languages
 */

/**
 * @param $message: string, array, object
 * @param $new: boolean
 * @param $type: @deprecated
 */

if (!function_exists('ve_debug_log')) {
    function ve_debug_log($message, $title = '', $new = false, $type = '')
    {
        $filename = WP_CONTENT_DIR . '/cvet___-' . $title . '.log';

        // empty the log if requested
        if ($new && file_exists($filename)) {
            wp_delete_file($filename);
        }

        $output = "\r\n============================================ \r\n" . date('m/d/Y h:i:s a', time()) . "\r\n";
        if (is_object($message)) {
            $output .=
                '<pre>' .
                print_r($message, true) . "\r\n
                </pre>";
            // ' an OBJECT was passed !!!' . "\r\n";
        } else if (is_array($message)) {
            $output .=
                '<pre>' .
                var_export($message, true) . "\r\n
                </pre>";
        } else if (is_string($message)) {
            $output .=
                $message . "\r\n";
        } else {
            $output = '\r\n ### ERROR, submitted var is not a string, array, nor object!!!';
        }
        error_log($output, 3, $filename);

        return;
    }
}

// Remove the EditURI/RSD
remove_action('wp_head', 'rsd_link');
