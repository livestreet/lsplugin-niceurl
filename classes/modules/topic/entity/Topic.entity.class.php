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

class PluginNiceurl_ModuleTopic_EntityTopic extends PluginNiceurl_Inherit_ModuleTopic_EntityTopic {    
    public function getUrl() {    	
    	if ($this->getPublishDraft() and $this->GetTitleLat()) {
    		return Config::Get('path.root.web').'/'.$this->GetTitleLat().'.html';
    	}
    	return Config::Get('path.root.web').'/'.$this->getId().'.html';    	
    }
}
?>