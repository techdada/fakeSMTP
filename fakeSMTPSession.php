<?php
namespace techdada;

class fakeSMTPSession {
        public $data = array();
        public $socket;
        public $id;
        public $ip;
        public $port;
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

