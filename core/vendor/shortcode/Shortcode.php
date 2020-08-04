<?php

defined("ACCESS") or die("Script access inactive");

/**
 * @author      Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright   2016 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version     0.0.1
 */

/*

// Add {foo} content inside {/foo} router
Shortcode::add('foo', function($attributes, $content) {
    // Extract attributes
    extract($attributes);
    // text
    if (isset($color)) $color = $color; else $color = 'black';
    // return
    return '<span style="color:'.$color.'">'.Router::FilterApply('content', $content).'</span>';
});


// Add Shortcode {Name var1="Hello" var2="World"}
Shortcode::add('Name', function($attributes) {
    // Extract attributes
    extract($attributes);
    // name
    if (isset($var1)) $var1 = $var1; else $var1 = 'Hello';
    if (isset($var2)) $var2 = $var2; else $var2 = 'World';
    // return
    return '<section>
                <!-- your code here -->
                <p>'.$var1.'</p>
                <p>'.$var2.'</p>
            </section>';
});
*/

class Shortcode
{
    protected static $shortcode_tags = array();
    /**
     * Funcion para los codigos cortos.
     */
    public static function add($shortcode, $callback_function)
    {
        $shortcode = (string) $shortcode;
        if (is_callable($callback_function)) {
            self::$shortcode_tags[$shortcode] = $callback_function;
        }
    }
    /**
     * Parsear shortcode.
     */
    public static function parse($content)
    {
        if (!self::$shortcode_tags) {
            return $content;
        }
        $shortcodes = implode('|', array_map('preg_quote', array_keys(self::$shortcode_tags)));
        $pattern = "/(.?)\\{([{$shortcodes}]+)(.*?)(\\/)?\\}(?(4)|(?:(.+?)\\{\\/\\s*\\2\\s*\\}))?(.?)/s";

        return preg_replace_callback($pattern, 'self::handle', $content);
    }
    /**
     * maneja shortcode.
     */
    protected static function handle($matches)
    {
        $prefix = $matches[1];
        $suffix = $matches[6];
        $shortcode = $matches[2];
        if ($prefix == '{' && $suffix == '}') {
            return substr($matches[0], 1, -1);
        }
        $attributes = array();
        if (preg_match_all('/(\\w+) *= *(?:([\'"])(.*?)\\2|([^ "\'>]+))/', $matches[3], $match, PREG_SET_ORDER)) {
            foreach ($match as $attribute) {
                if (!empty($attribute[4])) {
                    $attributes[strtolower($attribute[1])] = $attribute[4];
                } elseif (!empty($attribute[3])) {
                    $attributes[strtolower($attribute[1])] = $attribute[3];
                }
            }
        }

        return isset(self::$shortcode_tags[$shortcode]) ? $prefix.call_user_func(self::$shortcode_tags[$shortcode], $attributes, $matches[5], $shortcode).$suffix : '<div style="background:red;color:white;padding:16px;">Shortcode '.$shortcode.' not found</div>';
    }
}
