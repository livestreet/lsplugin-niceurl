<?php
/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/

class PluginNiceurl_HookUrl extends Hook {

    public function RegisterHook() {
        $this->AddHook('init_action', 'InitAction');
        $this->AddHook('module_topic_updatetopic_before', 'UpdateTopic');
        $this->AddHook('module_topic_addtopic_after', 'AddTopic');
    }

    public function InitAction() {
		/**
		 * Подхватываем обработку URL вида /title_topic.html
		 */
    	if (Router::GetAction()=='error') {
    		$sEvent=$sActionReal=Router::GetActionEvent();

    		
    		$aParamsNew=$aParamsReal=Router::GetParams();
			$sEventReal=array_shift($aParamsReal);
			$aParamsNew = array_pad($aParamsNew, -(count($aParamsNew)+1), $sEvent);
			$sUrlRequest=implode('/',$aParamsNew);
    		
    	
    		
    		if (preg_match(Config::Get('plugin.niceurl.url_preg'),$sUrlRequest,$aMatch)) {
    			
    			/**
    			 * Проверяем корректность
    			 */
    			
    			$sUrlEscape=preg_quote(Config::Get('plugin.niceurl.url'));

    			
    			$bError=true;
    			$aRule=array();
    			$aRuleRequire=array();
    			if (preg_match_all('#%(\w+)%#',$sUrlEscape,$aMatch2)) {    				
    				foreach ($aMatch2[1] as $k=>$sFind) {
    					if (in_array($sFind,array('id','title'))) {
    						if (isset($aMatch[$k+1])) {
    							$aRuleRequire[$sFind]=$aMatch[$k+1];
    						}
    						$bError=false;
    					}
    					$aRule[$k+1]=$sFind;    					
    				}
    			}
				if ($bError) {
					Router::Action($sActionReal,$sEventReal,$aParamsReal);
    				return ;
    			}
    			
    			/**
    			 * Получаем топик
    			 */
    			$oTopic=null;
    			if (isset($aRuleRequire['id'])) {
    				$oTopic=$this->Topic_GetTopicById($aRuleRequire['id']);
    			} elseif (isset($aRuleRequire['title'])) {
    				$oTopic=$this->PluginNiceurl_Niceurl_GetTopicByTitleLat($aRuleRequire['title']);
    			}
    			if (!$oTopic) {
					Router::Action($sActionReal,$sEventReal,$aParamsReal);
    				return ;
    			}
    			
    			$sUrlForRedirect=Config::Get('plugin.niceurl.url').Config::Get('plugin.niceurl.url_postfix');
    			$this->bNeedRedirect=false;
    			foreach ($aMatch as $k=>$v) {
    				if ($k>0) {
    					$this->CheckRule($aRule[$k],$v,$oTopic);
    					$sUrlForRedirect=str_replace('%'.$aRule[$k].'%',$v,$sUrlForRedirect);
    				}
    			}
    			
    			/**
    			 * Редирект на правльный URL
    			 */
    			if ($this->bNeedRedirect) {
    				Router::Location(Config::Get('path.root.web').$sUrlForRedirect);    				
    			}
    			
    			$sActionRewrite='blog';
    			if (LS_VERSION=='0.4.2') { // в след версиях этого делать не нужно, т.к. Router::Action() сделает это сам
    				$aConfigRoute = Config::Get('router');
    				$sActionRewrite = (isset($aConfigRoute['rewrite'][$sActionRewrite])) ? $aConfigRoute['rewrite'][$sActionRewrite] : $sActionRewrite;
    			}
    			/**
    			 * Прогружаем блоки
    			 */
    			$this->AddBlocks();
    			
    			if ($oTopic->getBlog()->getType()=='personal') {
    				Router::Action($sActionRewrite,$oTopic->getId().'.html',array());
    			} else {
    				Router::Action($sActionRewrite,$oTopic->getBlog()->getUrl(),array($oTopic->getId().'.html'));
    			}
    		}    		
    	}    	
    }
    
    protected function CheckRule($sRule,&$sValue,$oTopic) {
    	switch ($sRule) {
    		case 'year':
    			if ($sValue==date("Y",strtotime($oTopic->GetDateAdd()))) {
    				return true;
    			} else {
    				$sValue=date("Y",strtotime($oTopic->GetDateAdd()));
    				$this->bNeedRedirect=true;
    				return false;
    			}
    			break;
    	
    		case 'month':
    			if ($sValue==date("m",strtotime($oTopic->GetDateAdd()))) {
    				return true;
    			} else {
    				$sValue=date("m",strtotime($oTopic->GetDateAdd()));
    				$this->bNeedRedirect=true;
    				return false;
    			}
    			break;
    	
    		case 'day':
    			if ($sValue==date("d",strtotime($oTopic->GetDateAdd()))) {
    				return true;
    			} else {
    				$sValue=date("d",strtotime($oTopic->GetDateAdd()));
    				$this->bNeedRedirect=true;
    				return false;
    			}
    			break;
    	
    		case 'hour':
    			if ($sValue==date("H",strtotime($oTopic->GetDateAdd()))) {
    				return true;
    			} else {
    				$sValue=date("H",strtotime($oTopic->GetDateAdd()));
    				$this->bNeedRedirect=true;
    				return false;
    			}
    			break;
    	
    		case 'minute':
    			if ($sValue==date("i",strtotime($oTopic->GetDateAdd()))) {
    				return true;
    			} else {
    				$sValue=date("i",strtotime($oTopic->GetDateAdd()));
    				$this->bNeedRedirect=true;
    				return false;
    			}
    			break;
    	
    		case 'second':
    			if ($sValue==date("s",strtotime($oTopic->GetDateAdd()))) {
    				return true;
    			} else {
    				$sValue=date("s",strtotime($oTopic->GetDateAdd()));
    				$this->bNeedRedirect=true;
    				return false;
    			}
    			break;
    	
    		case 'login':
    			if ($sValue==$oTopic->GetUser()->getLogin()) {
    				return true;
    			} else {
    				$sValue=$oTopic->GetUser()->getLogin();
    				$this->bNeedRedirect=true;
    				return false;
    			}
    			break;
    	
    		case 'blog':
    			$sBlogUrl=$oTopic->GetBlog()->getUrl();
    			if ($oTopic->GetBlog()->getType()=='personal') {
    				$sBlogUrl=Config::Get('plugin.niceurl.url_personal_blog');
    				// проверка на логин
    				if ($sBlogUrl=='%login%') {
    					$sBlogUrl=$oTopic->GetUser()->getLogin();
    				}
    			}
    			
    			if ($sValue==$sBlogUrl) {
    				return true;
    			} else {
    				$sValue=$sBlogUrl;
    				$this->bNeedRedirect=true;
    				return false;
    			}
    			break;
    	
    		case 'id':
    			if ($sValue==$oTopic->GetId()) {
    				return true;
    			} else {
    				$sValue=$oTopic->GetId();
    				$this->bNeedRedirect=true;
    				return false;
    			}
    			break;
    	
    		case 'title':    			
    			if ($sValue==$oTopic->GetTitleLat()) {
    				return true;
    			} else {
    				$sValue=$oTopic->GetTitleLat();
    				$this->bNeedRedirect=true;
    				return false;
    			}
    			break;
    			
    		default:
    			break;
    	}
    	return true;
    }
    
    public function UpdateTopic($aParams) {
    	$this->PluginNiceurl_Niceurl_UpdateTopicUrl($aParams[0]);
    }
    
    public function AddTopic($aParams) {
    	if ($oTopic=$aParams['result']) {
    		$this->PluginNiceurl_Niceurl_UpdateTopicUrl($oTopic);
    	}    	
    }
    
    protected function AddBlocks() {
    	$aBlocks=Config::Get('plugin.niceurl.topic_blocks');
    	if ($aBlocks) {
    		foreach ($aBlocks as $aBlock) {
    			$this->Viewer_AddBlock($aBlock['group'],$aBlock['name'],$aBlock['params'],$aBlock['priority']);
    		}
    	}
    }
}
?>