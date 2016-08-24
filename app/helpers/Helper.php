<?php

class Helper {

    // other functions
    public static function oldRadio($name, $value, $default = false) {
        return null !== Input::old($name) ? (Input::old($name) == $value ? 'checked' : '') : ($default ? 'checked' : '');
    }

    public static function print_r($array){
    	echo '<pre>';
		print_r($array);
		echo '</pre>';
		dd(1);
    }

    public static function sanitize($string, $force_lowercase = true, $anal = false) {
	    $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
	                   "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
	                   "â€”", "â€“", ",", "<", ".", ">", "/", "?","’");
	    $clean = trim(str_replace($strip, "", strip_tags($string)));
	    $clean = preg_replace('/\s+/', "-", $clean);
	    $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean ;
	    return ($force_lowercase) ?
	        (function_exists('mb_strtolower')) ?
	            mb_strtolower($clean, 'UTF-8') :
	            strtolower($clean) :
	        $clean;
	}

	public static function replace_content_inside_delimiters($start, $end, $new, $source) {
		return preg_replace('#('.preg_quote($start).')(.*?)('.preg_quote($end).')#si', '$1'.$new.'$3', $source);
	}

	public static function getDay($int){
		switch ($int) {
			case '1':
				return 'MON';
				break;
			case '2':
				return 'TUE';
				break;
			case '3':
				return 'WED';
				break;
			case '4':
				return 'THU';
				break;
			case '5':
				return 'FRI';
				break;
			case '6':
				return 'SAT';
				break;
			case '7':
				return 'SUN';
				break;
			default:
				# code...
				break;
		}
	}
}