<?php
/**
 * DokuWiki Plugin likeit
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  lisps    
 */

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

/*
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_likeit extends DokuWiki_Syntax_Plugin {

	private $_itemPos = array();
    function incItemPos() {
        global $ID;
        if(array_key_exists($ID,$this->_itemPos)) {
            return $this->_itemPos[$ID]++;
        } else {
            $this->_itemPos[$ID] = 1;
            return 0;
        }
    }
    function getItemPos(){
        global $ID;
        if(array_key_exists($ID,$this->_itemPos)) {
            $this->_itemPos[$ID];
        } else {
            return 0;
        }
    }

    /*
     * What kind of syntax are we?
     */
    function getType() {
	    return 'substition';
	}

    /*
     * Where to sort in?
     */
    function getSort() {
		return 155;
	}

    /*
     * Paragraph Type
     */
    function getPType() {
		return 'normal';
	}

    /*
     * Connect pattern to lexer
     */
    function connectTo($mode) {
		$this->Lexer->addSpecialPattern("<likeit[^>]*>",$mode,'plugin_likeit');
	}

    /*
     * Handle the matches
     */
    function handle($match, $state, $pos, &$handler){
    	global $ID;
    	
    	$opts = array(
    		$this->incItemPos(),
			$ID,
			trim(substr($match,8,strlen($match)-8-1))
		);
		
		return ($opts);
    }
        
    /*
     * Create output
     */
    function render($mode, &$renderer, $opts)
	{
		if($mode == 'metadata') return false;
		
		global $INFO;
		$H =  $this->loadHelper('likeit');
		list($index,$pageid,$users) = $opts;
		
		$H->setUser($users);
		
		if($mode == 'xhtml') {
			$Hajax = $this->loadHelper('ajaxedit');
			
			$doAction = $Hajax && $pageid == $INFO['id'];
		

			$htmlid = hsc($pageid).'_'.$index;

			$renderer->doc .= "<span class='plugin_likeit container ".($doAction?'':'disabled')."' id='plugin_likeit_".$htmlid."'>";
			$renderer->doc .= "<span class='plugin_likeit button'>";
			$renderer->doc .= "<span class='plugin_likeit image'>";
			$renderer->doc .= "</span>";
			$renderer->doc .= "<span class='plugin_likeit label'>".hsc($this->getLang('likeit'));
			
			$renderer->doc .= "</span>";
			$renderer->doc .= "<span class='plugin_likeit count'>";
			$renderer->doc .= $H->getUserCount();
			$renderer->doc .= "</span>";
			$renderer->doc .= "</span>";
			$renderer->doc .= "<span class='plugin_likeit list'>";
			$renderer->doc .= $H->renderUserList();
			$renderer->doc .= "</span>";
			$renderer->doc .= "</span>";
					
		}
		else {
			$renderer->doc .= hsc($this->getLang('likeit')) ." (". $H->getUserCount() .")" ;
		}
		return true;
	}
	


}

