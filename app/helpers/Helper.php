<?php

class Helper {

    // other functions
    public static function oldRadio($name, $value, $default = false) {
        return null !== Input::old($name) ? (Input::old($name) == $value ? 'checked' : '') : ($default ? 'checked' : '');
    }

    public static function print_array($array){
    	echo '<pre>';
		print_r($array);
		echo '</pre>';
    }
}