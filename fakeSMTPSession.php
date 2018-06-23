<?php
namespace techdada;

class fakeSMTPSession {
        public $data = array();
        public $socket;
        public $id;
        public $ip;
        public $port;
		protected $expectwhat = false;
		protected $authenticated = false;
		protected $keep = false;

        public function __construct(&$socket) {
                socket_getpeername($socket, $ip, $port);
                $this->id=$ip.'.'.$port;
                $this->ip = $ip;
                $this->port = $port;
                $this->socket = $socket;
        }

        public function addData($key,$data,$mode = 'a') {
                if ($mode == 'a') {
                        if (!isset($this->data[$key])) $this->data[$key] = $data;
                        else $this->data[$key].=$data;
                }
                if (!$mode)       $this->data[$key] = $data;
                return true;
        }
        public function getData($key,$flush = false) {
                //print_r($this->data);
                if (array_key_exists($key, $this->data)) {
			if ($flush) { 
				$r = $this->data[$key];
				unset($this->data[$key]);
				return $r;
			} else return $this->data[$key];
                }
                return false;
        }

	public function new_line(&$data) {
		if (!$this->keep) return;
		$this->addData('keep_data',$data."\n");
	}
	
	public function expect($what,$do = null) {
		if ($do == null) {
			return ($this->expectwhat == $what);
		}
		if ($do) $this->expectwhat = $what;
		else $this->expectwhat = false;
	}
	
	public function parseUser($b64_user) {
		if ($this->expect('user')) {
			if ($user = base64_decode($b64_user)) {
				$this->user = $user;
				return true;
			}
		}
		return false;
	}
	
	public function parsePass($b64_pass,&$userlist) {
		if ($this->expect('pass')) {
			if ($pass = base64_decode($b64_pass)) {
				$this->pass = $pass;
				if (!is_array($userlist)) {
					return true;
				} elseif (isset($userlist[$this->user]) && ($userlist[$this->user] == $this->pass) ) {
					$this->authenticated = true;
					return true;
				} else {
					return false;
				}
			}
		}
		return false;
	}

	public function flushLines() {
		return $this->getData('keep_data');
	}

	public function keeping() {
		return $this->keep;
	}
	
	public function keepLines($fetch=true) {
		$this->keep = $fetch;
	}
}

