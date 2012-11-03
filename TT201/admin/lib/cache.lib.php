<?php
	
class cache {
	
	var $file;
	var $cacheDIR = "cache/";
	var $cacheFILE;
	
	public $add;
	public $get;
	
	function __construct($name) {
		
		$name = md5(APP_PATH);
		
		$this->file = $name;
		$this->cacheFILE = $this->cacheDIR.$this->file;
		
		file_put_contents($this->cacheFILE,"",FILE_APPEND);
		
		$temp = explode(",",file_get_contents($this->cacheFILE));
		
		foreach($temp as $elem) {
			if(empty($elem)) continue;
			$p = explode(":",$elem);
			$this->get[trim($p[0])] = trim($p[1]);
		}
	}
		
	public function save() {
		foreach($this->add as $key => $value) {
			file_put_contents($this->cacheFILE,$key.":".$value.",\n",FILE_APPEND);
		}
	}
}
?>