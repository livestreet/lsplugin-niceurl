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

class PluginNiceurl_ModuleNiceurl extends Module {
	protected $oMapper;
	protected $oUserCurrent=null;
	
	public function Init() {		
		$this->oMapper=Engine::GetMapper(__CLASS__);
		$this->oUserCurrent=$this->User_GetUserCurrent();
	}
	
	
	/**
	 * Получает топик по его латинсокму названию
	 *	
	 * @param string $sTitle
	 * @return ModuleTopic_EntityTopic
	 */
	public function GetTopicByTitleLat($sTitle) {
		if (false === ($data = $this->Cache_Get("topic_by_titlelat_{$sTitle}"))) {			
			$data = $this->oMapper->GetTopicByTitleLat($sTitle);
			$this->Cache_Set($data, "topic_by_titlelat_{$sTitle}", array('niceurl_topic_update'), 60*60*24*5);
		}		
		return $this->Topic_GetTopicById($data);
	}
	/**
	 * Обновление доп. информации о топике
	 *
	 * @param PluginNiceurl_ModuleNiceurl_EntityTopic $oWpTopic
	 * @return unknown
	 */
	public function UpdateTopic(PluginNiceurl_ModuleNiceurl_EntityTopic $oNiceurlTopic) {
		$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array('niceurl_topic_update'));
		return $this->oMapper->UpdateTopic($oNiceurlTopic);
	}
	/**
	 * Удаляет доп. инфу о топике
	 *
	 * @param unknown_type $sId
	 * @return unknown
	 */
	public function DeleteTopicById($sId) {
		$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array('niceurl_topic_update'));
		return $this->oMapper->DeleteTopicById($sId);
	}
	/**
	 * Обновление URL топика
	 *
	 * @param unknown_type $oTopic
	 */
	public function UpdateTopicUrl($oTopic) {
		$oNiceurlTopic=Engine::GetEntity('PluginNiceurl_ModuleNiceurl_EntityTopic');
    	$oNiceurlTopic->setId($oTopic->getId());
    	    	
    	$i=2;
    	$sTitle=$sTitleSold=func_translit($oTopic->getTitle());    	
    	while (($oNiceurlTopicOld=$this->PluginNiceurl_Niceurl_GetTopicByTitleLat($sTitle)) and $oNiceurlTopicOld->getId()!=$oNiceurlTopic->getId()) {
    		$sTitle=$sTitleSold.'_'.$i;
    		$i++;
    	}
    	$oNiceurlTopic->setTitleLat($sTitle);
    	$oTopic->setTitleLat($sTitle);
    	$this->PluginNiceurl_Niceurl_UpdateTopic($oNiceurlTopic);
	}
	/**
	 * Получает список доп. данных топика по массиву ID
	 *
	 * @param unknown_type $aTopicId
	 * @return unknown
	 */
	public function GetTopicsByArrayId($aTopicId) {
		if (!is_array($aTopicId)) {
			$aTopicId=array($aTopicId);
		}
		$aTopicId=array_unique($aTopicId);	
		$aTopics=array();	
		$s=join(',',$aTopicId);
		if (false === ($data = $this->Cache_Get("niceurl_topic_id_{$s}"))) {			
			$data = $this->oMapper->GetTopicsByArrayId($aTopicId);
			foreach ($data as $oTopic) {
				$aTopics[$oTopic->getId()]=$oTopic;
			}
			$this->Cache_Set($aTopics, "niceurl_topic_id_{$s}", array("niceurl_topic_update"), 60*60*24*1);
			return $aTopics;
		}		
		return $data;
	}
	
	
	public function GetTopicsHeadAll($iCurrPage,$iPerPage) {
		return $this->oMapper->GetTopicsHeadAll($iCurrPage,$iPerPage);
	}
	
	public function BuildUrlForTopic($oTopic) {		
		$sUrlSource=Config::Get('plugin.niceurl.url').Config::Get('plugin.niceurl.url_postfix');
		
		$aPreg=array(
			'%year%' => date("Y",strtotime($oTopic->GetDateAdd())),
			'%month%' => date("m",strtotime($oTopic->GetDateAdd())),
			'%day%' => date("d",strtotime($oTopic->GetDateAdd())),
			'%hour%' => date("H",strtotime($oTopic->GetDateAdd())),
			'%minute%' => date("i",strtotime($oTopic->GetDateAdd())),
			'%second%' => date("s",strtotime($oTopic->GetDateAdd())),
			//'%login%' => $oTopic->GetUser()->getLogin(),
			//'%blog%' => $oTopic->GetBlog()->getUrl(),
			'%id%' => $oTopic->GetId(),
			'%title%' => $oTopic->GetTitleLat(),
		);
		
		$sBlogUrl=$oTopic->GetBlog()->getUrl();
		if ($oTopic->GetBlog()->getType()=='personal') {
			$sBlogUrl=Config::Get('plugin.niceurl.url_personal_blog');
			$sUrlSource=str_replace('%blog%',Config::Get('plugin.niceurl.url_personal_blog'),$sUrlSource);
		}
		$aPreg['%blog%']=$sBlogUrl;
		
		if (strpos($sUrlSource,'%login%')!==false) {
			if (!($oUser=$oTopic->GetUser())) {
				$oUser=$this->User_GetUserById($oTopic->getUserId());
			}
			$aPreg['%login%']=$oUser->getLogin();
		}
		
		$sUrl=strtr($sUrlSource,$aPreg);		
		return Config::Get('path.root.web').$sUrl;
	}
}
?>