<?php
use WHMCS\Database\Capsule;
 
define("CLIENTAREA", true);
//define("FORCESSL", true); // Uncomment to force the page to use https://
 
require("init.php");
 
$ca = new WHMCS_ClientArea();
 
$ca->initPage();
 
# To assign variables to the template system use the following syntax.
# These can then be referenced using {$variablename} in the template.
 
# Check login status
if ($ca->isLoggedIn()) {
 
  # User is logged in - put any code you like here

  # Here's an example to get the currently logged in clients first name

  $clientName = Capsule::table('tblclients')
      ->where('id', '=', $ca->getUserID())->pluck('firstname');
 
  $ca->assign('clientname', $clientName);
 
} else {
 
  # User is not logged in

}

$url = $_REQUEST['page'];

$pages = Capsule::table('mod_custom_page')->where('url', $url)->first();

if ( !empty( $pages ) ) {
	
	$ca->addToBreadCrumb('index.php', Lang::trans('globalsystemname'));
	$ca->addToBreadCrumb('pages.php?page='.$pages->url, $pages->title);

	//设置模板文件
	$ca->setTemplate('pages/'.$pages->url);
	
	//设置页面名称
	$ca->setPageTitle($pages->title);
	
	if ( $pages->islogin ) {
		$ca->requireLogin(); // Uncomment this line to require a login to access this page
	}
	
	$value['title']			= $pages->title;
	$value['url']			= $pages->url;
	$value['keywords']		= $pages->keywords;
	$value['description']	= $pages->description;
	$value['content']		= $pages->content;
	$value['frontend']		= $pages->isfrontend;
} else {
	
	$ca->addToBreadCrumb('index.php', Lang::trans('globalsystemname'));
	$ca->addToBreadCrumb( '', Lang::trans('errorPage.404.title'));
	
	//设置模板文件
	$ca->setTemplate('error/page-not-found');
	//设置页面名称
	$ca->setPageTitle(Lang::trans('errorPage.404.title'));
	$value['frontend'] = '0';
}

$ca->assign('value', $value);
$ca->output();