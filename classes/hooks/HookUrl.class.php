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
    		$sEvent=Router::GetActionEvent();    		
    		if (preg_match("@^([\w_\-]+)\.html$@",$sEvent,$aMatch) ) {
    			/**
    			 * Получаем топик
    			 */
    			if (is_numeric($aMatch[1])) {
    				$oTopic=$this->Topic_GetTopicById($aMatch[1]);
    			} else {
    				$oTopic=$this->PluginNiceurl_Niceurl_GetTopicByTitleLat($aMatch[1]);
    			}
    			if ($oTopic) {    				
    				if ($oTopic->getBlog()->getType()=='personal') {    					
    					Router::Action('blog',$oTopic->getId().'.html',array());
    				} else {
    					Router::Action('blog',$oTopic->getBlog()->getUrl(),array($oTopic->getId().'.html'));
    				}
    			}
    		}    		
    	}    	
    }
    
    public function UpdateTopic($aParams) {
    	$this->PluginNiceurl_Niceurl_UpdateTopicUrl($aParams[0]);
    }
    
    public function AddTopic($aParams) {
    	if ($oTopic=$aParams['result']) {
    		$this->PluginNiceurl_Niceurl_UpdateTopicUrl($oTopic);
    	}    	
    }
}
?>