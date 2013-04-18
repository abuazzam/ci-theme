<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('_ci')) {
	function _ci() {
		$ci = &get_instance();
		return $ci;
	}
}

if (!function_exists('theme_url')) {
	function theme_url($url=null) {
		return base_url('/themes/'._ci()->theme->config('theme').'/'.ltrim($url, '/'));
	}
}

if (!function_exists('element')) {
	function element($view=null, $data=null) {
		return _ci()->theme->element($view, $data);
	}
}

if (!function_exists('template_part')) {
	function template_part($view=null, $return=false) {
		return _ci()->theme->template_part($view, $return);
	}
}

if (!function_exists('get_header')) {
	function get_header() {
		_ci()->theme->template_part('header');
	}
}

if (!function_exists('get_footer')) {
	function get_footer() {
		_ci()->theme->template_part('footer');
	}
}

if (!function_exists('get_sidebar')) {
	function get_sidebar($view='sidebar') {
		_ci()->theme->template_part($view);
	}
}

if (!function_exists('menu_active')) {
	function menu_active($page='home', $class='active') {
		if ($page == _ci()->get('menu_active')) {
			return $class;
		}
		return false;
	}
}

?>
