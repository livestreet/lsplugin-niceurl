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

/**
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('Plugin')) {
	die('Hacking attempt!');
}

class PluginNiceurl extends Plugin {
	
	protected $aInherits=array(       
       'module'  =>array('ModuleTopic'),       
       'entity'  =>array('ModuleTopic_EntityTopic'),       
	);

	
	/**
	 * Активация плагина	 
	 */
	public function Activate() {		
		if (!$this->isTableExists('prefix_niceurl_topic')) {
			/**
			 * При активации выполняем SQL дамп
			 */
			$this->ExportSQL(dirname(__FILE__).'/dump.sql');
		}
		set_time_limit(0);
		/**
		 * Жесткий костыль по загрузке конфига ДО активации плагина
		 */
		require_once(Config::Get('path.root.server').'/plugins/niceurl/include/function.php');
		$aConfig = include(Config::Get('path.root.server').'/plugins/niceurl/config/config.php');
		if(!empty($aConfig) && is_array($aConfig)) {			
			$sKey = "plugin.niceurl";
			if(!Config::isExist($sKey)) {
				Config::Set($sKey,$aConfig);
			} else {				
				Config::Set(
				$sKey,
				func_array_merge_assoc(Config::Get($sKey), $aConfig)
				);
			}
		}
		/**
		 * Пересохраняем все топики
		 * Получаем топики порциями
		 */
		$iCurrPage=1;
		while ($aTopics=$this->PluginNiceurl_Niceurl_GetTopicsHeadAll($iCurrPage,20)) {
			foreach ($aTopics as $oTopic) {
				$this->PluginNiceurl_Niceurl_UpdateTopicUrl($oTopic);
			}
			$iCurrPage++;
		}
		return true;
	}
	
	/**
	 * Инициализация плагина
	 */
	public function Init() {
	}
}
?>