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
require_once(dirname(dirname(__FILE__)).'/include/function.php');
/**
 * Таблицы БД
 */
$config['table']['topic'] = '___db.table.prefix___niceurl_topic';

$config['manual_topic_url']=true; // Использовать или нет возможность ручного указания URL для топика
$config['manual_topic_url_only_admin']=true; // Возможность ручного указания URL только для админа
$config['manual_topic_url_users']=array(); // Возможность ручного указания URL для указанных пользователей (прописывать нужно список ID пользователей)
$config['translit_topic_url']=true; // Транслитерировать URL топика. Внимани! Значение false работает в тестовом режиме и до конца не протестированно.
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
$config['url_strict'] = false; // Строгое совпадение URL, при false не учитывается окончание URL, которое может не совпадать с правилом

/**
 * Настройка списка редиректов старых адресов на новые
 * По дефолту два редиректа для стандартных урлов топиков в LS
 */
$config['redirect']=array(
	'/blog/%id%.html',
	'/blog/%blog%/%id%.html',
);

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
$config['url_preg']='~^'.$aUrlPreg['search'].preg_quote($config['url_postfix']).($config['url_strict'] ? '$' : '').'~ui';
$aRouterUri[$config['url_preg']]="error/".$aUrlPreg['replace'].$config['url_postfix'];

$config['redirect_preg']=array();
foreach($config['redirect'] as $sUrl) {
	$aUrlPreg=func_niceurl_url_to_preg($sUrl);
	$sPreg='~^'.$aUrlPreg['search'].''.'~ui';
	$config['redirect_preg'][$sPreg]=$sUrl;
	$aRouterUri[$sPreg]="error/".$aUrlPreg['replace'].'';
}

Config::Set('router.uri',$aRouterUri);

return $config;
?>