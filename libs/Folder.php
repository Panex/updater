<?php
/**
 * @ide         PhpStorm.
 * @author:     Pnsme
 * @datetime:   2017-7-15 23:12
 * @version:    0.1
 * @description:   文件操作类
 */

class Folder{

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
     * 将一个目录下所有的的文件和文件夹进行分类，存储到一个数组内并返回
     * @param $dir
     * @param $exclude_dir
     * @return array
     */
    public function dir_analyse($dir, $exclude_dir){
        $dir_arr = scandir($dir);
        $dirs = array();
        $files = array();
        foreach($dir_arr as $item){
            if($item == '.' || $item == '..'){
                continue;
            }

            $full_dir = $dir.DIRECTORY_SEPARATOR.$item;
            if(is_dir($full_dir) && !$this->exclude_dir($full_dir, $exclude_dir)){
                $dirs[] = $item;
            }else{
                $files[] = $item;
            }
        }

        return array('dirs' => $dirs, 'files' => $files);
    }

    /**
     * 判断$dir是否属于排除的目录，如果是，这返回true,否则返回false
     * @param $dir
     * @param $exclude_dir
     * @return bool
     */
    public function exclude_dir($dir, $exclude_dir){
        foreach($exclude_dir as $item){
            $escaped_item = '';
            $item = str_split($item);
            foreach($item as $c){
                $escaped_item .= $this->char_escape($c);
            }
            $dir = ltrim($dir, '.'.DIRECTORY_SEPARATOR);
            if(preg_match("/^{$escaped_item}/", $dir)){
                return true;
            }
        }
        return false;
    }

    /**
     * 将正则表达式的特殊字符进行转义
     * @param $char
     * @return mixed
     */
    public function char_escape($char){
        $map = array(
            '.'  => '\.',
            '['  => '\[',
            ']'  => '\]',
            '('  => '\(',
            ')'  => '\)',
            '|'  => '\|',
            '/'  => '\\'.DIRECTORY_SEPARATOR,
            '\\' => '\\'.DIRECTORY_SEPARATOR,
        );
        if(array_key_exists($char, $map)){
            return $map[$char];
        }else{
            return $char;
        }
    }


    /**
     * 递归删除一个目录和该目录下的所有文件、文件夹
     * @param $dir
     */
    public function clean_dir($dir){
        $handler = opendir($dir);
        while(($filename = readdir($handler)) !== false){
            if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..’，不要对他们进行操作
                $path = $dir.DIRECTORY_SEPARATOR.$filename;
                if(is_dir($path)){// 如果读取的某个对象是文件夹，则递归
                    if(count(scandir($path)) == 2){//目录为空,=2是因为.和..存在
                        rmdir($path);// 删除空目录
                    }else{
                        $this->clean_dir($path);
                        rmdir($path);
                    }
                }else{ //删除文件
                    unlink($path);
                }
            }
        }
    }
}