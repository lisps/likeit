<?php
/**
 * DokuWiki Plugin likeit
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  lisps    
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

require_once(DOKU_INC.'inc/auth.php');

class helper_plugin_likeit extends DokuWiki_Plugin {
	
	protected $_user = array();
	
	/*
	 * generate html code
	 * @param $ids string|array ids separated by space
	 * @return string html code
	 */
	public function setUser($users) {
		global $auth;
		$this->_user = array();
		if(!is_array($users)) { 
			if($users == '') return '';
			$users_r = explode(" ",trim($users));
			
		} else {
			if(empty($users)) return '';
			$users_r = $users;
		}
		
		$userdata = array();
		foreach($users_r as $user) {
			$data =  $auth->getUserData($user);
			if($data) {
				$userdata[$user] = $data;
			}
		}


		$this->_user = $userdata;
		
		return count($users_r);
	}
	
	public function getUserCount() {
		return count($this->_user);
	}
	
	
	public function getUserList() {
		$user = array_map(function($value){return $value['name'];},$this->_user);
		return $user;
		
		
	}
	
	public function renderUserList() {
		$return = '';
		foreach($this->getUserList() as $user) {
			$return .= "<span class='listitem'>" . hsc($user) . "</span>";
		}
		
		return $return;
	}
		


}

// vim:ts=4:sw=4:et:
