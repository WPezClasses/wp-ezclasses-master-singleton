<?php
/**
 * Base / parent boilerplate class for WP ezClasses. Can also be used as a base for WP plugins.
 *
 */
 
/*
 * ==== CHANGE LOG ====
 *
 */
 
// No WP? Die! Now!!
if (!defined('ABSPATH')) {
	header( 'HTTP/1.0 403 Forbidden' );
    die();
}

if ( ! class_exists('Class_WP_ezClasses_Master_Singleton') ){
	abstract class Class_WP_ezClasses_Master_Singleton {
			
		// this is where we store the instances
		private static $arr_instance = array();
		
		protected $_str_get_called_class;
		protected $_int_get_current_blog_id;
		
		protected function __construct() {
	
			$this->_str_get_called_class = get_called_class();
			$this->_int_get_current_blog_id = get_current_blog_id();
		}
		
		/*
		 * More info: http://scotty-t.com/2012/07/09/wp-you-oop/
		 */
		public static function ezc_get_instance($mix_args = NULL) {
		
			$str_get_called_class = get_called_class();
				
			if ( !isset( self::$arr_instance[$str_get_called_class] ) ) {
				self::$arr_instance[$str_get_called_class] = new $str_get_called_class();
				
				// note: the mix_args passed in are passed again
				self::$arr_instance[$str_get_called_class]->ezc_init($mix_args);  
			}
			
			return self::$arr_instance[$str_get_called_class];
		} 

		
		/*
		 * Note: Only called the first time the class/object is instantiated. (Duh?)
		 */
		abstract public function ezc_init();  


		/*
		 * Fact: Traditional GLOBALS are subject to abuse. The ezCONFIGS method allows you to maintain safe, read-only "GLOBALS" for any class that extends this one. 
		 *
		 * You can pass in a string, or an array of values that will be implode'd into the ezCONFIG's key. For example, array(some_key, blog_id, called_class)
		 *
		 * IMPORTANT >> This capability it useful but you might also be better off using WP ezGlobals, especially if you're running WP MS and many of the sites
		 * within your network share a significant number of properties and/or ("global") methods. 
		 */
		protected function ezCONFIGS($mix_keys=NULL){
		
			// quick validate - let's make sure we should continue
			if ( is_null($mix_keys) || ( ! is_string($mix_keys) && ! is_array($mix_keys) ) || ( is_array($mix_keys) && empty($mix_keys) ) ){
				return $this->ez_configs_validate_false_default();
			}
			
			$arr_ez_configs = $this->ez_configs();
		
			/*
			 * If a string was passed in, we set up an array using the current blog_id and then the called class. This enables you to setup
			 * ezCONFIGS keys by key + blog id + called class, or key + blog id, or just key. 
			 *
			 * See logic below for how the array is used to build fallback keys
			 */
			$arr_keys = $mix_keys;
			if ( is_string($mix_keys) ){
				$arr_keys = array($mix_keys, $this->_int_get_current_blog_id, $this->_str_get_called_class);

			} 
			
			/*
			 * Once we have an array, we start with a key of all array value build with implode and then check that key against the ez_globals(). If the
			 * isset() is false, we unset the last element in the array and try again, and so on until the isset() is true, and then we return that value.
			 * If the isset() is never true we will eventually return ez_globals_isset_false_default()
			 *
			 * You can define the arrays / keys anyway you want for anything you want. It's that flexible. 
			 *
			 * IMPORTANT >> However, DO NOT REMOVE 'log', 'validate' or 'filters' since those are used in other classes in the WP ezClasses framework. 
			 */
			$arr_y = array();
			$arr_keys_copy = $arr_keys;
			foreach ( $arr_keys as $key => $value ){
			
				$arr_y[$key] = implode('+' , $arr_keys_copy);
				
				if (isset($arr_ez_configs[$arr_y[$key]]) ){
					return $arr_ez_configs[$arr_y[$key]];
				}
				
				$int_unset = (count($arr_keys) - 1) - $key;
				unset($arr_keys_copy[$int_unset]);
			}
			return $this->ez_configs_isset_false_default($mix_keys);
		}

		/*
		 *
		 */
		private function ez_configs(){
				
			$arr_ez_configs = array(
								'log'			=> true,
								'validate'		=> true,
								'filters'		=> false,
								);
								
			return array_merge($arr_ez_configs, $this->ez_configs_custom());
		}
		
		/*
		 *
		 */
		private function ez_configs_custom(){
		
			$arr_ez_configs_custom = array();
								
			return $arr_ez_configs_custom;
		}
		
		/*
		 *
		 */
		private function ez_configs_validate_false_default(){
		
			$mix_default_value = NULL;
		
			return $mix_default_value;
		}
		
		/*
		 *
		 */
		private function ez_configs_isset_false_default(){
		
			$mix_default_value = false;
		
			return $mix_default_value;
		}
				
	}
}
?>