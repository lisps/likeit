<?php
/**
 * DokuWiki Plugin likeit
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  lisps
 */

if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
require_once (DOKU_PLUGIN . 'action.php');

class action_plugin_likeit extends DokuWiki_Action_Plugin {

	/**
	 * Register the eventhandlers
	 */
	function register(Doku_Event_Handler $controller) {
		$controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE',  $this, '_ajax_call');
	}
	
	
	function _ajax_call(&$event,$param) {
		if ($event->data !== 'plugin_likeit') {
			return;
		}
		//no other ajax call handlers needed
		$event->stopPropagation();
		$event->preventDefault();
		
		/* @var $INPUT \Input */
		global $INPUT;
		
		$input_index = $INPUT->int('id'); //input index on the server
		
		$user = $_SERVER['REMOTE_USER'];

		
		/* @var $Hajax \helper_plugin_ajaxedit */
		$Hajax = $this->loadHelper('ajaxedit');
		
		/* @var $Hfsinput \helper_plugin_flysprayinput */
		$Hlikeit = $this->loadHelper('likeit');
		
		$likewithread = $this->getConf('likewithread')?AUTH_READ:AUTH_EDIT;
		$data=$Hajax->getWikiPage(true, $likewithread); //
		
		//find "our" fsinput fields
		$found=explode("<likeit",$data);
				
		if ($input_index < count($found)) {
		
			$found[$input_index+1] = ltrim($found[$input_index+1]);
			$stop=strpos($found[$input_index+1],">");
			if ($stop === FALSE) {
				$Hajax->error('Cannot find object, please contact your admin!');
			}
			else {
				$olduserlist = substr($found[$input_index+1],0,$stop);
				
				$newuserlist_r=explode(" ",trim($olduserlist));
				if(in_array($user,$newuserlist_r)) {
					$Hajax->success(array(
						'msg'=>$this->getLang('already_liked')
							
					));
				}
				
				$newuserlist_r[] = $user;
				sort($newuserlist_r);
				$newuserlist_r=array_unique($newuserlist_r);
				$newuserlist=implode(" ",$newuserlist_r);
				
				
				if($stop == 0){
					$found[$input_index+1]= " ".$newuserlist." ".$found[$input_index+1];
				}
				else {
					$found[$input_index+1]=str_replace($olduserlist," ".$newuserlist." ",$found[$input_index+1]);
				}
			}
			
			$Hlikeit->setUser($newuserlist);
			
			$data=implode("<likeit",$found);
			$param = array(
					'msg' => $this->getLang('added'),
					'count' => $Hlikeit->getUserCount(),
					'list'  => $Hlikeit->renderUserList()
			);
			$summary = "Likeit ".$input_index." liked";
			$Hajax->saveWikiPage($data,$summary,true,$param);

		}
	}

}
