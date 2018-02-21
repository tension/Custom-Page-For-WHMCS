<?php
namespace CustomPage;

// 引入初始化类
require_once dirname(__DIR__).'/init.php';

use WHMCS\Config\Setting;

class Tools {
    // 将构造方法添加 private 修饰符可防止实例化
    private function __construct() {
	
	}
	
	// 判断目录是否可写入
	public static function writeAble($file) {
	    if ( is_dir($file) ) {
	        $dir = Tools::filePath($file);
	        if ($fp = @fopen($file, 'w')) {
	            @fclose($fp);
	            @unlink($file);
	            $writeable = 1;
	        } else {
	            $writeable = 0;
	        }
	    } else {
	        if ($fp = @fopen($file, 'a+')) {
	            @fclose($fp);
	            $writeable = 1;
	        } else {
	            $writeable = 0;
	        }
	    }
	 
	    return $writeable;
	}
	
	// 获取文件列表
	public static function fileList() {
		$templatename = Setting::getValue('Template');
		$file_path = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/templates/'.$templatename.'/pages/';
		$filelist = array_diff( scandir( $file_path ), ['..','.']);
		return $filelist;
	}
	
	// 获取模板绝对路径
	public static function filePath() {
		$templatename = Setting::getValue('Template');
		$file_path = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/templates/'.$templatename.'/pages/';
		return $file_path;
	}
	
	// 获取模板路径
	public static function fileCurrentPath() {
		$templatename = Setting::getValue('Template');
		$file_path = '/templates/'.$templatename.'/pages/';
		return $file_path;
	}
	
	// 获取文件
	public static function fileGet( $filename ) {
		$file_path = Tools::filePath() . $filename;
		if ( file_exists ( $file_path ) ){
			$filevalue = file_get_contents( $file_path ); //将整个文件内容读取
			return $filevalue;
		}
	}
	
	//写入文件
	public static function fileSet( $filename, $fileValue ) {
		$file_path = Tools::filePath() . $filename;
		file_put_contents($file_path, htmlspecialchars_decode($fileValue)); //将整个文件内容写入
	}
}