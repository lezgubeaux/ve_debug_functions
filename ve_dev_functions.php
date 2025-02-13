<?php

/** *
 * @link              https://erich.biz
 * @since             1.0.0
 * @package           VE Dev Functions
 *
 * @wordpress-plugin
 * Plugin Name:       VE Debugging Output Functions
 * Plugin URI:        https://ve-dev-functions.com
 * Description:       Various simple functions for debugging PHP and JS code
 *                    (error_log to a file / div as js_log.log)
 * Version:           1.2.0
 * Author:            Vladimir Eric
 * Author URI:        https://framework.tech
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ve-dev-functions
 * Domain Path:       /languages
 */

/** 
 * Instructions:
 * 
 * PHP debug log:  
 * $ve_debug->log(
 *      *$message, 
 *      $title = '',        // file title sufix
 *      $new = false,  // override file content
 * )
 * 
 * JS debug log:
 * veDebug.log{
 *      *const message,      // object, array, string, boolean, int
 *      string div id,          // custom ID of a div js_log
 *      bool newCnt = false,               // override or append content
 *      bool fixed = true             // fix the height of div and show the last output line
 *      int x,                      // viewport left offset
 *      int y,                      // viewport top offset
 * }
 * 
 * !!! Use with caution. The function will ouput user-visible data (for logged admins)
 */

define('VE_DEBUG_VER', '1.2.0');

define('VE_DEBUG_URL', plugin_dir_url(__FILE__));
define('VE_DEBUG_DIR', plugin_dir_path(__FILE__));

if (!class_exists('VeDebug')) {

    class VeDebug
    {

        private $div_id;
        private $js_debug;

        public function __construct()
        {

            // id of the div to output JS log in
            $this->div_id = 've_debug__';

            // prepare HTML and assets for JS log
            $this->js_log_init();
        }

        /**
         * PHP debug log (to file)
         */
        public function log($message, $title = '', $new = false)
        {

            $filename = WP_CONTENT_DIR . '/ve_debug__-' . $title . '.log';

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
            } else if (is_bool($message)) {
                $output .= $message ? 'TRUE' : 'FALSE';
            } else if (is_integer($message)) {
                $output .= strval($message);
            } else {
                $type = gettype($message);
                $output .= '\r\n ### ERROR, submitted var is not a string, array, nor object!!!' . PHP_EOL . 'It. is an ' . $type;
            }

            error_log($output, 3, $filename);

            return;
        }



        /**
         * prepare html and assets for JS log
         */
        public function js_log_init()
        {

            // print div before the content
            add_filter('the_content', array($this, 've_add_debug_div'), 10, 1);
            add_action('edit_form_after_title', array($this, 've_add_debug_div'), 10);

            // add the JS that prints JS log to the div
            add_action('wp_enqueue_scripts', array($this, 'add_ve_debug_script'), 1);
        }



        // load js
        public function add_ve_debug_script()
        {
            $f_path = VE_DEBUG_DIR . '/ve_dev_functions/assets/style.css';
            $f_url = VE_DEBUG_URL . '/ve_dev_functions/assets/style.css';

            // add plugin's stylesheet
            wp_enqueue_style('ve_debug_style', $f_url,  [], filemtime($f_path));

            $f_path = VE_DEBUG_DIR . '/ve_dev_functions/assets/scripts.js';
            $f_url = VE_DEBUG_URL . '/ve_dev_functions/assets/scripts.js';

            wp_enqueue_script(
                've_debug_script',
                $f_url,
                [],
                filemtime($f_path)
            );

            // pass debug div id prefix to js
            wp_localize_script('ve_debug_script', 'veDebugDiv', ['divId' => $this->div_id, 'logged' => current_user_can('manage_options')]);
        }



        // hook the log div on top of the page content
        public function ve_add_debug_div($content = '')
        {
            // Define the HTML to be added before the content
            //  It is a template div that will remain hidden, and cloned to another instances for actual debugging
            $custom_html = '
                <div id="' . $this->div_id . '">
                    <div class="content">
                    </div>
                </div>';

            if ($content === '') {
                // if called by an action, PRINT the cnt
                echo $custom_html;
            }

            // return the cnt to the filter
            return $custom_html . $content;
        }
    }
}

global $ve_debug;
add_action('init', function () use (&$ve_debug) {
    $ve_debug = new VeDebug();
});
