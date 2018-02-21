<?php
use CustomPage\Tools;
use WHMCS\Database\Capsule;

if (!defined('WHMCS')) {
	die('This file cannot be accessed directly');
}

if ( !empty( $_REQUEST['id'] ) ) {
	$getPages = Capsule::table('mod_custom_page')->where('id', $_REQUEST['id'])->first();
	if ( $getPages->islogin ) {
		$loginchecked = 'checked';
	}
	if ( $getPages->isfrontend ) {
		$isfrontendchecked = 'checked';
	}
}
	
if ( file_exists ( Tools::filePath() . $getPages->url . '.tpl' ) ) {
	$getFile = Tools::fileGet($getPages->url . '.tpl');
} else {
	$getFile = $getPages->content;
}
	
$filelist = Tools::fileList();

if ( $_REQUEST['action'] == 'new' ) {
    $action = '新建页面';
} else {
    $action = '编辑 ' . $getPages->title;
}

$editor = '
<!-- include summernote css/js-->
<link rel="stylesheet" type="text/css" href="'.$systemurl.'/modules/addons/custom_page/assets/css/select2.min.css" />
<script type="text/javascript" src="'.$systemurl.'/modules/addons/custom_page/assets/js/select2.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdnjs.neworld.org/ajax/libs/codemirror/5.33.0/codemirror.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdnjs.neworld.org/ajax/libs/codemirror/5.33.0/theme/base16-light.min.css" />
<script src="https://cdnjs.neworld.org/ajax/libs/codemirror/5.33.0/codemirror.min.js"></script>
<script src="https://cdnjs.neworld.org/ajax/libs/codemirror/5.33.0/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.neworld.org/ajax/libs/codemirror/5.33.0/mode/css/css.min.js"></script>';
$editor .= "<script>
	jQuery(function() {
		$('#url').change(function(){
			var filename = jQuery('#url').val();
			$.get('{$modulelink}',{action:'writable' , filename:filename},function(data){
				console.log(data.status);
				$('#alert').removeClass('hide');
				$('#alert .alert').addClass('alert-' + data.status).text(data.msg);
			},'json');
		});
		$('#file').change(function(){
			var filename = jQuery('#file').find('option:selected').val();
			$.get('{$modulelink}',{action:'getfile' , file:filename},function(data){
				$('#content').empty();
				$('#content').text(data);
				$('.CodeMirror').remove();
				editor = CodeMirror.fromTextArea(document.getElementById(\"content\"), {
					mode: 'text/html',
					lineWrapping: true,
					lineNumbers: true,
					foldGutter: true,
					gutters: ['CodeMirror-linenumbers', 'CodeMirror-foldgutter'],
				});
			});
		});
		jQuery('.select2').select2({
			minimumResultsForSearch: Infinity
		});
		var editor = CodeMirror.fromTextArea(document.getElementById('content'), {
			mode: 'text/html',
			lineWrapping: true,
			lineNumbers: true,
			foldGutter: true,
			gutters: ['CodeMirror-linenumbers', 'CodeMirror-foldgutter'],
		});
	});
	</script>";
$editor .= '
<form action="'.$modulelink.'" method="post" class="form-horizontal">
<input type="hidden" name="action" value="submit" />
<input type="hidden" name="acc" value="'.$_REQUEST['action'].'" />
<input type="hidden" name="id" value="'.$_REQUEST['id'].'" />
<div class="row">
	<div class="col-sm-9">
	<div class="forms-group">'.$action.'</div>
		<div class="forms-group">
		  <input type="text" name="title" class="form-control" id="title" placeholder="标题" value="'.$getPages->title.'" />
		</div>
		<div class="forms-group">
			<div class="mono url-slug">
				'.$rewrite.'
				<div style="display: inline-block;">
					<input type="text" name="url" id="url" value="'.$getPages->url.'" />
				</div>
				/
			</div>
		</div>
		<div class="forms-group">
			<label for="file">文件路径</label>
			<select name="file" id="file" class="form-control select2">
				<optgroup label="File List">';
				foreach ( $filelist as $key => $file ) {
					$filename = explode('.', $file)[0];
					if ( $filename != $getPages->url ) {
						$editor .= '<option value="'.$file.'">'.Tools::fileCurrentPath().$file.'</option>';
					} else {
						$editor .= '<option value="'.$file.'" selected>'.Tools::fileCurrentPath().$file.'</option>';
					}
				}
				
			$editor .= '
			</optgroup>
			</select>
		</div>
		<div class="forms-group hide" id="alert">
			<div class="alert" role="alert"></div>
		</div>
		<div class="forms-group">
			<label for="content">文件内容</label>
			<textarea name="content" id="content" class="form-control" rows="5">'.$getFile.'</textarea>
		</div>
		<div class="forms-group">
			<label for="description">自定义CSS</label>
			<textarea name="description" id="description" class="form-control" rows="5">'.$getPages->description.'</textarea>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="forms-group">所属分类</div>
		<div class="forms-group">
			<select name="cid" class="form-control select2">
				<option value="0">顶级分类</option>';
				$cats = Capsule::table('mod_custom_page')->where('cid', '0')->get();
				foreach ( $cats as $key => $cat ) {
					if ( !empty( $getPages->cid ) ) {
						if ( $getPages->cid == $cat->cid ) continue;
						$catschecked = 'selected';
					}
					$value[$key]['title'] 	= $cat->title;
					$value[$key]['id'] 		= $cat->id;
					$value[$key]['cid'] 	= $cat->cid;
				    $editor .= '<option value="'.$value[$key]['id'].'" '.$catschecked.'>'.$value[$key]['title'].'</option>';
				}
				
			$editor .= '
			</select>
		</div>
		<div class="forms-group hide">页面类型</div>
		<div class="forms-group hide">
			<select name="type" class="form-control select2">
				<option value="top">显示于顶部</option>
				<option value="btm">显示于尾部</option>
				<option value="none">不显示</option>
			</select>
		</div>
		<div class="forms-group">关键词</div>
		<div class="forms-group">
			<input type="text" name="keywords" class="form-control select2-tags" id="keywords" value="'.$getPages->keywords.'" />
			<span class="help-block">以半角逗号 <code>,</code> 分隔</span>
		</div>
		<div class="forms-group">高级选项</div>
		<div class="forms-group">
			<div class="checkbox">
				<label>
			        <input type="checkbox" name="islogin" value="1" '.$loginchecked.'/> 勾选需要登录方可查看
			    </label>
			</div>
			<div class="checkbox">
				<label>
			        <input type="checkbox" name="isfrontend" value="1" '.$isfrontendchecked.' /> 勾选用户中心显示
			    </label>
			</div>
		</div>
		<div class="forms-group">
			<button type="submit" class="btn btn-block btn-success">保存修改</button>
		</div>
	</div>
</div>
</form>';