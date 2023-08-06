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
 * Version:           1.1.0
 * Author:            Vladimir Eric
 * Author URI:        https://framework.tech
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ve-dev-functions
 * Domain Path:       /languages
 */

/**
 * log output to a file in /wp-content
 * params: log content, empty(0,1), filename sufix
 */
if (!function_exists('ve_debug_log')) {
    function ve_debug_log($message, $title = '', $new = false)
    {
        $filename = WP_CONTENT_DIR . '/woo_cprlm__l_debug-' . $title . '.log';

        // empty the log if requested
        if ($new && file_exists($filename)) {
            wp_delete_file($filename);
        }

        error_log("\r\n" . date('m/d/Y h:i:s a', time()) . " v" . "\r\n" .
            $message . "\r\n", 3, $filename);

        return;
    }
}

/**
 * list all hooks of the site
 * (use ve_list_hooks() to output on any page)
 */
if (!function_exists('ve_dump_hook')) {
    function ve_dump_hook($tag, $hook)
    {
        $hook = json_decode(json_encode($hook), true);

        ksort($hook);

        echo "
        <style>
        .ve_dev_hooklist{
            background-bottom:1px solid dotted;
            position:relative;
        }
        .ve_dev_hooklist .ve_dev_popup{
            display:none;
            position:absolute;
                left:0;
                top:100px;
                width:80%;
                height:300px;
                max-height:300px;
                overflow-y:scroll;
        }
        .ve_dev_hooklist:hover{
            background-color: lightgrey;
        }
        .ve_dev_hooklist:hover .ve_dev_popup{
            background-color: white;
            border:2px spolid #666;
            display:block;
        }
        </style>
        <pre class='ve_dev_hooklist'>>>>>>\t$tag<br>";

        foreach ($hook as $priority => $functions) {

            echo $priority;

            foreach ($functions as $function)
                echo "
                <div class='ve_dev_popup'>";
            if ($function['function'] != 'list_hook_details') {

                echo "\t";

                if (is_string($function['function']))
                    echo $function['function'];

                elseif (is_string($function['function'][0]))
                    echo $function['function'][0] . ' -> ' . $function['function'][1];

                elseif (is_object($function['function'][0]))
                    echo "(object) " . get_class($function['function'][0]) . ' -> ' . $function['function'][1];

                else
                    print_r($function);

                echo " (" . $function['accepted_args'] . ") <br>";
            }
            echo "
                </div>";
        }

        echo '</pre>';
    }
}

if (!function_exists('ve_list_hooks')) {
    function ve_list_hooks($filter = false)
    {
        global $wp_filter;

        $hooks = $wp_filter;
        ksort($hooks);

        foreach ($hooks as $tag => $hook)
            if (false === $filter || false !== strpos($tag, $filter))
                ve_dump_hook($tag, $hook);
    }
}
