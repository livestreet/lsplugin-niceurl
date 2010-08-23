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


$aRouterUri=array();
//$aRouterUri=Config::Get('router.uri'); // раскомментировать если необходимо сохранить ранее определенные реврайты
$aRouterUri['~^([\w_\-]+)\.html~i']="error/\\1.html";
Config::Set('router.uri',$aRouterUri);

return $config;
?>