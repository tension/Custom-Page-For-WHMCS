<?php
use WHMCS\Database\Capsule;

if (!defined('WHMCS')) {
	die('This file cannot be accessed directly');
}

$result .= '
<div class="row">
	<div class="col-sm-9">
		<div class="forms-group">
			<h5>页面名称</h5>
		</div>
		<ul class="page-list">';
$pages = Capsule::table('mod_custom_page')->where('cid', '!=', '0')->orderBy('id','DESC')->get();
foreach ( $pages as $key => $page ) {
	$value[$key]['title'] 	= $page->title;
	$value[$key]['url'] 	= $page->url;
	$value[$key]['id'] 		= $page->id;
	$value[$key]['cid'] 	= $page->cid;
	if ( empty( $value[$key]['cid'] ) ) {
		$value[$key]['cats'] = ' <span class="label label-primary">顶级分类</span>';
	} else {
		$catName = Capsule::table('mod_custom_page')->where('id', $page->cid)->first()->title;
		$value[$key]['cats'] = ' <span class="label label-primary">'.$catName.'</span>';
	}
	if ( $vars['ReWrite'] == 'on' ) {
		$url = $systemurl.'/';
	} else {
		$url = $systemurl.'/pages.php?page=';
	}
    $result .= '
	    <li>
    		<h4>'.$value[$key]['title'].'</h4>
    		<div class="cats">'.$value[$key]['cats'].'</div>
			<div class="mono url-slug">
				'.$systemurl.'/<code data-toggle="tooltip" data-placement="bottom" title="访问 '.$value[$key]['title'].'"><a href="'.$url.$value[$key]['url'].'/" target="_Blank">'.$value[$key]['url'].'</a></code>/
			</div>
			<div class="tool">
				<a class="btn btn-xs btn-info" href="'.$modulelink.'&action=edit&id='.$value[$key]['id'].'"><i class="fa fa-check-circle" aria-hidden="true"></i> 编辑</a>
				<a class="btn btn-xs btn-danger" href="'.$modulelink.'&action=del&id='.$value[$key]['id'].'"><i class="fa fa-times-circle" aria-hidden="true"></i> 删除</a>
			</div>
		</li>';
}
$result .= '
    	</ul>
	</div>
	<div class="col-sm-3">
		<div class="forms-group">
			<h5>分类名称</h5>
		</div>
		<ul class="page-list">';
$cats = Capsule::table('mod_custom_page')->where('cid', '0')->orderBy('id','DESC')->get();
foreach ( $cats as $key => $cat ) {
	$value[$key]['title'] 	= $cat->title;
	$value[$key]['url'] 	= $cat->url;
	$value[$key]['id'] 		= $cat->id;
	$value[$key]['cid'] 	= $cat->cid;
    $result .= '
	    <li>
    		<h4 style="width: 49%;">'.$value[$key]['title'].'</h4>
			<div class="tool" style="width: 49%;">
				<a class="btn btn-xs btn-info" href="'.$modulelink.'&action=edit&id='.$value[$key]['id'].'"><i class="fa fa-check-circle" aria-hidden="true"></i> 编辑</a>
				<a class="btn btn-xs btn-danger" href="'.$modulelink.'&action=del&id='.$value[$key]['id'].'"><i class="fa fa-times-circle" aria-hidden="true"></i> 删除</a>
			</div>
		</li>';
}
$result .= '
    	</ul>
        <a href="'.$modulelink.'&action=new" class="btn btn-block btn-info" style="margin-top: 30px;">
        	<i class="fa fa-file-text-o" aria-hidden="true"></i> 添加页面</h4>
		</a>
	</div>
</div>';