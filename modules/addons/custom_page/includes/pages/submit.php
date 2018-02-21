<?php
use CustomPage\Tools;
use WHMCS\Database\Capsule;

if (!defined('WHMCS')) {
	die('This file cannot be accessed directly');
}

if ( isset( $_REQUEST ) ) {
    $pageID 				= intval($_REQUEST['id']);
    $pagecID 				= intval($_REQUEST['cid']);
    $pageType 				= intval($_REQUEST['type']);
    $pageTitle 				= trim($_REQUEST['title']);
    $pageURL 				= trim($_REQUEST['url']);
    $pageIsLogin 			= intval($_REQUEST['islogin']);
    $pageIsFrontend 		= intval($_REQUEST['isfrontend']);
    $pageKeyWords 			= $_REQUEST['keywords'];
    $pageDescription 		= $_REQUEST['description'];
    $pageContent 			= html_entity_decode($_REQUEST['content']);
        
	if ( $_REQUEST['acc'] == 'new' ) {
    	
		$checkURL = Capsule::table('mod_custom_page')->where('url', $pageURL)->first();
		
		//print_r($checkURL);die();
        if ( !empty ( $checkURL ) ) {
            $alert = error('URL 已存在');
            return NULL;
        }
        
        $action = Capsule::table('mod_custom_page')->insert([
        	'cid'			=> $pagecID,
        	'type'			=> $pageType,
        	'title'			=> $pageTitle,
        	'url'			=> $pageURL,
        	'islogin'		=> $pageIsLogin,
        	'isfrontend'	=> $pageIsFrontend,
        	'keywords'		=> $pageKeyWords,
        	'description'	=> $pageDescription,
        	//'content'		=> $pageContent,
        ]);
        Tools::fileSet($pageURL . '.tpl', $pageContent);
            
    } else {
    	
		$checkURL = Capsule::select("SELECT * FROM mod_custom_page WHERE url = '$pageURL' AND id!='$pageID'");
		
        if ( !empty ( $checkURL ) ) {
            $alert = error('URL 不能重复');
            return NULL;
        }
        
        $action = Capsule::table('mod_custom_page')->where('id', $pageID)->update([
        	'cid'			=> $pagecID,
        	'type'			=> $pageType,
        	'title'			=> $pageTitle,
        	'url'			=> $pageURL,
        	'islogin'		=> $pageIsLogin,
        	'isfrontend'	=> $pageIsFrontend,
        	'keywords'		=> $pageKeyWords,
        	'description'	=> $pageDescription,
        	//'content'		=> $pageContent,
        ]);
        Tools::fileSet($pageURL . '.tpl', $pageContent);
    }
            
    if ( !empty ( $action ) ) {
        $alert = success('操作成功');
    } else {
        $alert = error('操作失败');
    }
    
} else {
    $alert = error('未定义操作');
}