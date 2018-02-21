<?php
require_once dirname(__FILE__) . '/includes/init.php';

use CustomPage\Tools;
use WHMCS\Config\Setting;
use WHMCS\Database\Capsule;

function custom_page_config() {
	$configarray = array(
		'name' 			=> 'Custom Page',
		'description' 	=> 'This module allows you to create custom page on the WHMCS.',
		'version' 		=> '1.3',
		'author' 		=> '<a href="http://neworld.org" target="_blank">NeWorld</a>',
		'fields' 		=> []
	);
	
	$configarray['fields']['ReWrite'] = [
		"FriendlyName" => "启用URL重写",
		"Type" => "yesno",
		"Size" => "50",
		"Description" => "勾选后开启 URL 重写规则，请按照说明设置。",
	];
	
	return $configarray;
}

function custom_page_activate() {
	try {
		if (!Capsule::schema()->hasTable('mod_custom_page')) {
			Capsule::schema()->create('mod_custom_page', function ($table) {
				$table->increments('id');
				$table->unsignedInteger('cid');
				$table->unsignedInteger('type');
				$table->text('title');
				$table->text('url');
				$table->text('keywords')->nullable();
				$table->text('description')->nullable();
				$table->text('content')->nullable();
				$table->boolean('islogin')->nullable()->default('0');
				$table->boolean('isfrontend')->nullable()->default('0');
			});
		}
	} catch (Exception $e) {
		return [
			'status' => 'error',
			'description' => '不能创建表 mod_custom_page: ' . $e->getMessage()
		];
	}
	return [
		'status' => 'success',
		'description' => '模块激活成功. 点击 配置 对模块进行设置。'
	];
}

function custom_page_deactivate() {
	try {
		Capsule::schema()->dropIfExists('mod_custom_page');
		return [
			'status' => 'success',
			'description' => '模块卸载成功'
		];
	}
	catch (Exception $e) {
		return [
			'status' => 'error',
			'description' => 'Unable to drop tables: ' . $e->getMessage()
		];
	}
}

function custom_page_output($vars) {
    $systemurl = Setting::getValue('SystemURL');
    $modulelink = $vars['modulelink'];
    if ( $vars['ReWrite'] == 'on' ) {
	    $rewrite = $systemurl.'/';
    } else {
	    $rewrite = $systemurl.'/pages.php?page=';
    }
    $result = "<link rel=\"stylesheet\" href=\"{$systemurl}/modules/addons/custom_page/assets/css/style.css?3\">";
    
    if (isset($_REQUEST['action'])) {
        switch ($_REQUEST['action']) {
            case 'new':
            case 'edit':
            	require dirname(__FILE__) . '/includes/pages/new-edit.php';
            	break;
            case 'info':
            	require dirname(__FILE__) . '/includes/pages/info.php';
            	break;
            case 'writable':
            	if ( !empty( $_REQUEST['filename'] ) ) {
	            	$file = $_REQUEST['filename'];
	            	$file = Tools::filePath($file);
	            	$fileName = $file.$_REQUEST['filename'].'.tpl';
	            	$filename = Tools::writeAble($file.$_REQUEST['filename'].'.tpl');
					if ( $filename ) {
						$result = [
							'status' 	=> 'success',
							'msg' 		=> $fileName.' 文件可正常读写！',
						];
					} else {
						$result = [
							'status' 	=> 'warning',
							'msg' 		=> $fileName.' 文件或 '.$file.' 目录没有写入权限或所有权不正确。',
						];
					}
					die(json_encode($result));
					//print_r($result);die();
	            }
            	break;
            case 'del':
            	if ( !empty( $_REQUEST['id'] ) ) {
                	$delePages = Capsule::table('mod_custom_page')->where('id', $_REQUEST['id'])->delete();
                	if ( $delePages ) {
                        $alert = success('删除成功');
                	} else {
                        $alert = error('删除失败');
                	}
            	}
            	break;
            case 'getfile':
            	if ( !empty( $_REQUEST['file'] ) ) {
	            	$File = Tools::fileGet($_REQUEST['file']);
	            	die($File);
	            }
            	break;
            case 'submit':
            	require dirname(__FILE__) . '/includes/pages/submit.php';
                break;
            default:
                break;
        }
    }
    if ( $editor ) {
        
    	$result .= $editor;
    	
    } else {
        
        require dirname(__FILE__) . '/includes/pages/home.php';
        
	}
    echo '<a href="'.$modulelink.'" class="btn btn-default btn-xs pull-right"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i> 返回</a><a href="'.$modulelink.'&action=info" class="btn btn-info btn-xs pull-right" style="margin-right: 10px;"><i class="fa fa-info-circle" aria-hidden="true"></i> 安装说明</a><div class="clearfix"></div>';
    echo $alert . $result;
    echo '<div class="text-center foot">Copyright © NeWorld Cloud Ltd. All Rights Reserved.</div>';
}

if ( !function_exists('success') ) {
	function success($str) {
	    return '<script>jQuery.growl.notice({ title: "成功", message: "'.$str.'" });</script>';
	}
}

if ( !function_exists('error') ) {
	function error($str) {
	    return '<script>jQuery.growl.error({ title: "失败", message: "'.$str.'" });</script>';
	}
}
