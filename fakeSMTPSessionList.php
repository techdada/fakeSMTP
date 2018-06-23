<?php
namespace techdada;

class fakeSMTPSessionList {
        protected $sessions = array();
        protected $listener;

        public function __construct(&$listener) {
                $this->listener = $listener;
        }

        /**
         * 
         * @param socket $socket
         * @return fakeSMTPSession
         */
        public function &contains(&$socket) {
                $session = null;
                foreach ($this->sessions as &$session) {
                        if ($session->socket == $socket)
                                return $session;
                }
                return $session;
        }
        public function addData(&$socket,$key,$data,$mode='a') {
                if ($s = $this->contains($socket)) {
                        return $s->addData($key,$data,$mode);
                }
                return false;
        }
        public function add(&$socket) {

                if (!$this->contains($socket)) {
                        $this->sessions[]=new fakeSMTPSession($socket);
                        return true;
                }
                return false;
        }

        public function remove(&$socket) {
                foreach ($this->sessions as $k=>$s) {
                        if ($s->socket == $socket) {
                                unset ($this->sessions[$k]);
                                //socket_close($socket);
                        }
                }
        }

        public function getSockList() {
                $r = array($this->listener);
                foreach ($this->sessions as $s) {
                        $r[] = $s->socket;
                }
                return $r;
        }

        public function full() {
                if (sizeof($this->sessions) >= 10)
                        return true;
                return false;
        }
        public function size() {
                return sizeof($this->sessions);
        }
}

