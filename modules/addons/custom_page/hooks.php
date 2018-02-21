<?php
use WHMCS\Database\Capsule;

function GetPageURL() {
	$url = explode("=", $_SERVER['QUERY_STRING'])[1];
	$url = explode("&", $url)[0];
	return $url;
}

add_hook('ClientAreaHeadOutput', 1, function($vars) {
	$getCustomPages = Capsule::table('mod_custom_page')->where('url', GetPageURL())->first();
	$keywords 		= $getCustomPages->keywords;
	$css 			= $getCustomPages->description;
	if ( $keywords ) {
		$keywords = "<meta name=\"keywords\" content=\"{$keywords}\" />\r\n";
	}
	if ( $css ) {
		$css = "<style type=\"text/css\"> \r\n{$css}\r\n</style>\r\n";
	}
	return $keywords.$css;
});

add_hook('ClientAreaPage', 1, function ($vars){
    $frontend = Capsule::table('mod_custom_page')->where('url', GetPageURL())->first();
	$isfrontend = $frontend->isfrontend;

    return [
        'frontend' => $isfrontend,
    ];
});