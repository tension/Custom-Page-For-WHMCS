<?php
use WHMCS\Database\Capsule;
use WHMCS\Config\Setting;

if (!defined('WHMCS')) {
	die('This file cannot be accessed directly');
}

$editor .= '
<div class="panel panel-default">
	<div class="panel-heading">使用说明</div>
	<div class="panel-body">';
		if (Setting::getValue('Template') != 'NeWorld-New') {
$editor .= '<div class="alert alert-danger">
请将 <code>/templates/pages/</code> 目录移动至 <code>/templates/'.Setting::getValue('Template').'/</code> 目录，编辑文件夹内的文件以适应您的模板。
			</div>';
		} else {
			$editor .= '<div class="alert alert-success">
			当前主题 <code>'.Setting::getValue('Template').'</code> 可直接添加或编辑页面。
		</div>';
		}
$editor .= '<p>前台模板调用文件 <code>{$isfrontend eq \'0\'}</code> 判断是否前台显示，例如：</p>';
$editor .= '<pre class="html">
{if $frontend eq "0"}
    前台可以显示的内容
{/if}</pre>';
$editor .= '<p>Apache 伪静态规则
<pre class="bash">
RewriteEngine on
RewriteBase /
RewriteRule ^([^/]*)?/$ ./pages.php?page=$1 [QSA,L]
</pre></p>';
$editor .= '<p>Nginx 伪静态规则
<pre class="bash">
rewrite ^/([^/]*)?/$ /./pages.php?page=$1 last;
</pre></p>';
$editor .= '</div></div>';