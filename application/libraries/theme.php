<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Theme {
	protected $ci = null;
	protected $config = array();
	protected $data = array();
	
	private $module = null;
	private $contoller = null;
	private $method = null;
	
	private $template_locations = array();
	
	public function __construct() {
		$this->ci =& get_instance();
		$this->config = config_item('theme');
		
		if (method_exists($this->ci->router, 'fetch_module')) {
            $this->module 	= $this->ci->router->fetch_module();
        }
        
        // What controllers or methods are in use
        $this->controller	= $this->ci->router->fetch_class();
        $this->method 		= $this->ci->router->fetch_method();
        
        $this->set_theme($this->config['theme']);
	}
	
	public function set_theme($theme = 'default') {
		$this->config['theme'] = $theme;
		
		$functions = $this->config('path').$this->config('theme').'/functions.php';
    	if (file_exists($functions)) {
    		include_once($functions);
    	}

        $this->template_locations = array($this->config('path') . $this->config('theme') . '/views/modules/' . $this->module .'/',
                                          $this->config('path') . $this->config('theme') . '/views/',
                                          $this->config('path') . 'default/views/modules/' . $this->module .'/',
                                          $this->config('path') . 'default/views/',
                                          APPPATH . 'modules/' . $this->module . '/views/'
                                     );
	}
	
	public function set_layout($layout = 'index') {
		$path = $this->config('path').$this->config('theme').'/'.$layout.'.php';
		if (!file_exists($path)) $layout = 'index';
		$this->config['layout'] = $layout;
	}
	
	public function template_part($view=null, $return=false) {
		$path = $this->config('path') . $this->config('theme') . '/' . $view . EXT;
		if (file_exists($path)) {
			$this->ci->load->vars($this->data);
			return $this->ci->load->file($path, $return);
		}
	}
	
	public function element($element=null, $data=null) {
		$path = $this->config('path') . $this->config('theme') . '/views/elements/' . $element;
		$data = !is_null($data) ? $data : $this->data;
		if (file_exists($path . EXT)) {
			$this->ci->load->vars($data);
			return $this->ci->load->file($path . EXT, true);
		} else
			return $this->ci->load->view('elements/' . $element, $data, true);
	}
	
	public function set($key, $value) {
		$this->data[$key] = $value;
	}
	
	public function get($key=null, $default=false) {
		return isset($this->data[$key]) ? $this->data[$key] : $default;
	}
	
	public function config($name, $default = false) {
        return isset($this->config[$name]) ? $this->config[$name] : $default;
    }
	
	public function render($view, $data=null) {
		if (is_array($data)) {
			$this->data = array_merge($this->data, $data);
		}
		
		$theme = $this->config('path') . $this->config('theme') . '/' . $this->config('layout');
		if (!file_exists($theme . EXT)) {
			show_error('Missing theme layout');
		}
		
		$path = null;
		foreach($this->template_locations as $location) {
			if(file_exists($location.$view.EXT) && is_null($path)) {
				$path = $location.$view.EXT;
				$this->ci->load->vars($this->data);
				$this->data['layout_content'] = $this->ci->load->file($path, true);
			}
		}
		
		if (is_null($path)) {
			$this->data['layout_content'] = $this->ci->load->view($view, $this->data, true);
		}
		
		if ($this->ci->input->is_ajax_request()) {
			$format = $this->ci->input->get_post('format');
			if (!empty($format) && $format == 'json') {
				echo json_encode(array('html' => $this->data['layout_content']));
			} else {
				echo $this->data['layout_content'];
			}
			return;
		}
		
		$this->ci->load->vars($this->data);
		$this->ci->load->file($theme . EXT);
	}
}
?>
