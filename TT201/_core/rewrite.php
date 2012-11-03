<?php
	//Default rewrites
	//ALTER TABLE  `tentego_settings` ADD  `rewrite` INT( 1 ) NOT NULL DEFAULT  '0'
	if(!$kernel->settings->load('rewrite')) {
	
		class rewrite {
			var $index;
			var $waiting;
			var $random;
			var $add;
			var $login;
			var $register;
			var $contact;
			var $inbox;
			var $search;
			var $rules;
			var $profile;
			var $favorites;
			
			function __construct() {
				$this->index = kernel::host();
				$this->waiting = $this->index.'/waiting.php';
				$this->random = $this->index.'/random.php';
				$this->add = $this->index.'/add.php';
				$this->login = $this->index.'/login.php';
				$this->register = $this->index.'/register.php';
				$this->contact = $this->index.'/contact.php';
				$this->inbox = $this->index.'/inbox.php';
				$this->search = $this->index.'/search.php';
				$this->rules = $this->index.'/rules.php';
				$this->profile = $this->index.'/profile.php';
				$this->favorites = $this->index.'/favorites.php';
			}
			
			public function changeSigns($text) {
				$replace = array(  
				 'ą' => 'a', 'Ą' => 'A', 'ę' => 'ę', 'Ę' => 'E',  
				 'ć' => 'c', 'Ć' => 'C', 'ń' => 'n', 'Ń' => 'N', 'ł' => 'l',  
				 'Ł' => 'L', 'ś' => 's', 'Ś' => 'S', 'ż' => 'z',  
				 'Ż' => 'Z', 'ź' => 'z', 'Ź' => 'Z', 'ó' => 'o', 'Ó' => 'o',
				 ' ' => '-');  
				$text = str_replace(array_keys($replace), array_values($replace), $text);
				$text = strtolower($text);
				$text = preg_replace('/[^0-9a-z\-]+/', '', $text);
				$text = preg_replace('/[\-]+/', '-', $text); 
				return $text;
			}
			
			public function img($id, $name = NULL) {
				return $this->index.'/img.php?id='.$id;
			}
			public function categories($id, $name = NULL, $page = NULL) {
				if($page) return $this->index.'/categories.php?id='.$id.'&page='.$page;
				else return $this->index.'/categories.php?id='.$id;
			}
			public function user($id, $name = NULL) {
				return $this->index.'/user.php?id='.$id;
			}
		}
	}
	else {

		class rewrite {
			var $index;
			var $waiting;
			var $random;
			var $add;
			var $login;
			var $register;
			var $contact;
			var $inbox;
			var $search;
			var $rules;
			var $profile;
			var $favorites;
			
			function __construct() {
				$this->index = kernel::host();
				$this->waiting = $this->index.'/waiting/';
				$this->random = $this->index.'/random/';
				$this->add = $this->index.'/add/';
				$this->login = $this->index.'/login/';
				$this->register = $this->index.'/register/';
				$this->contact = $this->index.'/contact/';
				$this->inbox = $this->index.'/inbox/';
				$this->search = $this->index.'/search/';
				$this->rules = $this->index.'/rules/';
				$this->profile = $this->index.'/profile/';
				$this->favorites = $this->index.'/favorites/';
			}
			
			public function changeSigns($text) {
				$replace = array(  
				 'ą' => 'a', 'Ą' => 'A', 'ę' => 'ę', 'Ę' => 'E',  
				 'ć' => 'c', 'Ć' => 'C', 'ń' => 'n', 'Ń' => 'N', 'ł' => 'l',  
				 'Ł' => 'L', 'ś' => 's', 'Ś' => 'S', 'ż' => 'z',  
				 'Ż' => 'Z', 'ź' => 'z', 'Ź' => 'Z', 'ó' => 'o', 'Ó' => 'o',
				 ' ' => '-');  
				$text = str_replace(array_keys($replace), array_values($replace), $text);
				$text = strtolower($text);
				$text = preg_replace('/[^0-9a-z\-]+/', '', $text);
				$text = preg_replace('/[\-]+/', '-', $text); 
				return $text;
			}
			
			public function img($id, $name = NULL) {
				return $this->index.'/img/'.$id.'/'.$name.'/';
			}
			public function categories($id, $name = NULL, $page = NULL) {
				if($page) return $this->index.'/category/'.$id.'/'.$name.'/'.$page.'/';
				else return $this->index.'/category/'.$id.'/'.$name.'/';
			}
			public function user($id, $name = NULL) {
				return $this->index.'/user/'.$id.'/'.$name.'/';
			}
		}
	}

	$rewrite = new rewrite();
?>