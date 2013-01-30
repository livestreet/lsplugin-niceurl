<?php

if (!function_exists('func_translit')) {

function func_translit($sText,$bLower=true) {
    $aConverter=array(  
        'а' => 'a',   'б' => 'b',   'в' => 'v',  
        'г' => 'g',   'д' => 'd',   'е' => 'e',  
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',  
        'и' => 'i',   'й' => 'y',   'к' => 'k',  
        'л' => 'l',   'м' => 'm',   'н' => 'n',  
        'о' => 'o',   'п' => 'p',   'р' => 'r',  
        'с' => 's',   'т' => 't',   'у' => 'u',  
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',  
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',  
        'ь' => "'",  'ы' => 'y',   'ъ' => "'",  
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',  
  
        'А' => 'A',   'Б' => 'B',   'В' => 'V',  
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',  
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',  
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',  
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',  
        'О' => 'O',   'П' => 'P',   'Р' => 'R',  
        'С' => 'S',   'Т' => 'T',   'У' => 'U',  
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',  
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',  
        'Ь' => "'",  'Ы' => 'Y',   'Ъ' => "'",  
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya', 
        
        " "=> "-", "."=> "", "/"=> "-" 
    );  
    $sRes=strtr($sText,$aConverter);
    if ($sResIconv=@iconv("UTF-8", "ISO-8859-1//IGNORE//TRANSLIT", $sRes)) {
    	$sRes=$sResIconv;
    }
    if (preg_match('/[^A-Za-z0-9_\-]/', $sRes)) {    	
    	$sRes = preg_replace('/[^A-Za-z0-9_\-]/', '', $sRes);
    	$sRes = preg_replace('/\-+/', '-', $sRes);
    }
    if ($bLower) {
    	$sRes=strtolower($sRes);
    }
    return $sRes;
}

}

if (!function_exists('func_niceurl_url_to_preg')) {
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
			'%title%' => '([\w_\-а-яё]+)',
		);

		$sUrl=trim($sUrl,'/ ');
		$sUrlEscape=preg_quote($sUrl);
		$sUrlEscapeReplace=$sUrl;

		if (preg_match_all('#%\w+%#',$sUrlEscape,$aMatch)) {
			foreach ($aMatch[0] as $k=>$sFind) {
				$sReplace='\\'.($k+1);
				$sUrlEscapeReplace=str_replace($sFind,$sReplace,$sUrlEscapeReplace);
			}
		}

		$sUrlEscape=strtr($sUrlEscape,$aPreg);
		return array('search'=>$sUrlEscape,'replace'=>$sUrlEscapeReplace);
	}
}
?>