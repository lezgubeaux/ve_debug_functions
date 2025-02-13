# ve_debug_functions
Custom debug log and other debugging functions

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
