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

class PluginNiceurl_ModuleNiceurl_MapperNiceurl extends Mapper {	
		
		
	public function GetTopicByTitleLat($sTitle) {
		$sql = "SELECT id FROM ".Config::Get('plugin.niceurl.table.topic')." WHERE title_lat = ? limit 0,1";
		if ($aRow=$this->oDb->selectRow($sql,$sTitle)) {
			return $aRow['id'];
		}
		return null;
	}
	
	public function UpdateTopic(PluginNiceurl_ModuleNiceurl_EntityTopic $oNiceurlTopic) {
		$sql = "REPLACE INTO  ".Config::Get('plugin.niceurl.table.topic')." 
			SET title_lat = ?, id = ?d ";
		if ($aRow=$this->oDb->query($sql,$oNiceurlTopic->getTitleLat(),$oNiceurlTopic->getId())) {
			return true;
		}
		return false;
	}
	
	public function DeleteTopicById($sId) {
		$sql = "DELETE FROM ".Config::Get('plugin.niceurl.table.topic')." WHERE id = ?d ";
		if ($aRow=$this->oDb->query($sql,$sId)) {
			return true;
		}
		return false;
	}
	
	public function GetTopicsByArrayId($aArrayId) {
		if (!is_array($aArrayId) or count($aArrayId)==0) {
			return array();
		}
				
		$sql = "SELECT 
					*							 
				FROM 
					".Config::Get('plugin.niceurl.table.topic')."
				WHERE 
					id IN(?a) 									
				ORDER BY FIELD(id,?a) ";
		$aTopics=array();
		if ($aRows=$this->oDb->select($sql,$aArrayId,$aArrayId)) {
			foreach ($aRows as $aTopic) {
				$aTopics[]=Engine::GetEntity('PluginNiceurl_Niceurl_Topic',$aTopic);
			}
		}		
		return $aTopics;
	}
	
	
	public function GetTopicsHeadAll() {				
		$sql = "SELECT 
					t.*,
					tc.*							 
				FROM 
					".Config::Get('db.table.topic')." as t	
					JOIN  ".Config::Get('db.table.topic_content')." AS tc ON t.topic_id=tc.topic_id				
				";
		$aTopics=array();
		if ($aRows=$this->oDb->select($sql)) {
			foreach ($aRows as $aTopic) {
				$aTopics[]=Engine::GetEntity('Topic',$aTopic);
			}
		}		
		return $aTopics;
	}
}
?>