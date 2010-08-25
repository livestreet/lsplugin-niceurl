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
$config=array();
/**
 * Таблицы БД
 */
$config['table']['topic'] = '___db.table.prefix___niceurl_topic';

/**
 * Настройка URL
 * Допустимы шаблоны:
 * %year% - год топика (2010)
 * %month% - месяц (08)
 * %day% - день (24)
 * %hour% - час (17)
 * %minute% - минуты (06)
 * %second% - секунды (54)
 * %login% - логин автора топика (admin)
 * %blog% - url коллективного блога (report), если топик в личном блоге, то этот параметр заменится на $config['url_personal_blog']
 * %id% - id топика (325)
 * %title% - заголовок топика в транслите (title_topic)
 * 
 * В шаблоне обязательно должен быть %id% или %title%
 */
$config['url'] = '/%blog%/%year%/%month%/%day%/%title%';
$config['url_postfix'] = '.html'; // добавка в конец урла, не рекомендуется её убирать, т.к. могут перестать работать стандартные страницы - они будут перехвачены плагином и отданы как 404 ошибка
$config['url_personal_blog'] = '%login%'; // URL для персонального блога, нельзя задавать пустым. Из шаблонов допустимо значение только '%login%'



/**
 * Настройка блоков для отображения на странице топиков
 */
$aBlocks=array(
	array(
		'group' => 'right',
		'name' => 'stream',
		'params' => array(),
		'priority' => 100,
	),
	array(
		'group' => 'right',
		'name' => 'tags',
		'params' => array(),
		'priority' => 50,
	),
	array(
		'group' => 'right',
		'name' => 'blogs',
		'params' => array(),
		'priority' => 1,
	),
);

$config['topic_blocks']=$aBlocks;


/**
 * **************************************** НИЖЕ НЕ ТРОГАТЬ! **********************************
 * **************************************** НИЖЕ НЕ ТРОГАТЬ! **********************************
 * **************************************** НИЖЕ НЕ ТРОГАТЬ! **********************************
 * **************************************** НИЖЕ НЕ ТРОГАТЬ! **********************************
 * **************************************** НИЖЕ НЕ ТРОГАТЬ! **********************************
 */
/**
 * Роутинг
 */
$aRouterUri=Config::Get('router.uri');
unset($aRouterUri['~^(\d+)\.html~i']);

$aUrlPreg=func_niceurl_url_to_preg($config['url']);
$config['url_preg']='~^'.$aUrlPreg['search'].preg_quote($config['url_postfix']).'~i';
$aRouterUri[$config['url_preg']]="error/".$aUrlPreg['replace'].$config['url_postfix'];
Config::Set('router.uri',$aRouterUri);


function func_niceurl_url_to_preg($sUrl) {
	$aPreg=array(
		'%year%' => '(\d{4})',
		'%month%' => '(\d{2})',
		'%day%' => '(\d{2})',
		'%hour%' => '(\d{2})',
		'%minute%' => '(\d{2})',
		'%second%' => '(\d{2})',
		'%login%' => '([\da-z\_\-]+)',
		'%blog%' => '([\da-z\_\-]+)',
		'%id%' => '(\d+)',
		'%title%' => '([\w_\-]+)',
	);
	
	$sUrl=trim($sUrl,'/ ');	
	$sUrlEscape=$sUrlEscapeReplace=preg_quote($sUrl);
	
	if (preg_match_all('#%\w+%#',$sUrlEscape,$aMatch)) {
		foreach ($aMatch[0] as $k=>$sFind) {
			$sReplace='\\'.($k+1);
			$sUrlEscapeReplace=str_replace($sFind,$sReplace,$sUrlEscapeReplace);
		}		
	}
	
	$sUrlEscape=strtr($sUrlEscape,$aPreg);
	return array('search'=>$sUrlEscape,'replace'=>$sUrlEscapeReplace);
}

return $config;
?>