<?php

/**
 * @ide         PhpStorm.
 * @author:     Pnsme
 * @datetime:   2017-7-15 21:35
 * @version:    0.1
 * @description:   将一个文件中的文件夹压缩
 */
class Zipper{
    private static $instance = null;

    private function __construct(){

    }

    private function __clone(){
    }

    public static function getInstance(){
        if(!self::$instance instanceof self){
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 压缩文件夹内的文件
     * @param  string $path         需要压缩的文件夹路径
     * @param  string $zip_filename 压缩后的文件名
     * @param  string $zip_dir      压缩文件内的路径
     * @return bool                 成功返回true，失败将会抛出错误
     * @throws Exception 错误的文件夹或创建压缩文件失败
     */
    public function zip($path, $zip_filename, $zip_dir = ''){
        $zip_dir = trim($zip_dir, DIRECTORY_SEPARATOR);
        $path = rtrim($path, DIRECTORY_SEPARATOR);
        if(!is_file($path) && !is_dir($path)){
            throw new Exception('file or directory "'.$path.'" not exists');
        }
        $zip = new ZipArchive();
        $this->createDir($zip_filename);
        $open_zip = $zip->open($zip_filename, ZipArchive::CREATE);
        if($open_zip === TRUE){
            if(is_file($path)){
                $zipped_filename = $zip_dir == '' ? basename($path) : $zip_dir.DIRECTORY_SEPARATOR.basename($path);
                return $zip->addFile($path, $zipped_filename);
            }
            $this->addFileToZip($path, $zip, $path, $zip_dir); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
            $zip->close(); //关闭处理的zip文件
        }else{
            throw new Exception('create file "'.$zip_filename.'" failed');
        }
        return true;
    }

    /**
     * 递归添加$path目录中的文件和文件夹到zip压缩包内
     * @param  string      $path    要压缩的文件夹路径
     * @param  ZipArchive  $zip     ZipArchive对象
     * @param  string      $dir     递归中的最外层目录，等于第一次递归时的$path
     * @param  string      $zip_dir 在压缩文件夹内的路径
     * @throws Exception
     */
    private function addFileToZip($path, ZipArchive $zip, $dir, $zip_dir = ''){
        $handler = opendir($path); //打开当前文件夹由$path指定。
        while(($filename = readdir($handler)) !== false){
            if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..’，不要对他们进行操作
                $full_filename = $path.DIRECTORY_SEPARATOR.$filename;
                if(is_dir($full_filename)){// 如果读取的某个对象是文件夹，则递归
                    $dir_name = $this->filter_dir($full_filename, $dir);
                    $dir_name = $zip_dir == '' ? $dir : $zip_dir.DIRECTORY_SEPARATOR.$dir_name;
                    $zip->addEmptyDir($dir_name);   //添加空目录
                    $this->addFileToZip($full_filename, $zip, $dir, $zip_dir);
                }else{ //将文件加入zip对象
                    $file_name = $this->filter_dir($full_filename, $dir);
                    $file_name = $zip_dir == '' ? $file_name : $zip_dir.DIRECTORY_SEPARATOR.$file_name;
                    if(!$zip->addFile($full_filename, $file_name)){
                        throw new Exception('add file "'.$full_filename.'" to zip failed');
                    }
                }
            }
        }
        closedir($handler);
    }

    /**
     * 将全部路径中的非升级目录路径删除
     * @param $full_filename
     * @param $dir
     * @return string
     */
    private function filter_dir($full_filename, $dir){
        $dir_len = strlen($dir);
        return ltrim(substr($full_filename, $dir_len), DIRECTORY_SEPARATOR);
    }

    private function createDir($filename){
        $dir = dirname($filename);
        echo $dir;
        if(!is_dir($dir)){
            mkdir($dir, 0755, true);
        }
    }
}
