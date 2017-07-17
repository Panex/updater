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
     * @param $filter
     * @return array
     */
    public function dir_analyse($dir, $filter){
        $dir_arr = scandir($dir);
        $dirs = array();
        $files = array();
        foreach($dir_arr as $item){
            if($item == '.' || $item == '..'){
                continue;
            }

            $path = $dir.DIRECTORY_SEPARATOR.$item;
            if($this->path_filter($path, $filter)){
                continue;
            }

            if(is_dir($path)){
                $dirs[] = $item;
            }else{
                $files[] = $item;
            }
        }

        return array('dirs' => $dirs, 'files' => $files);
    }

    /**
     * 判断$dir是否属于需要过滤的目录，如果是，这返回true,否则返回false
     * @param $path
     * @param $filter
     * @return bool
     * @throws Exception
     */
    public function path_filter($path, $filter){
        /* 转换过滤条件 */
        if(is_string($filter)){
            $filter = explode('|', $filter);
        }elseif(!is_array($filter)){
            throw new Exception('错误的过滤条件');
        }

        /* 格式化路径 */
        $format = $this->path_format($path);
        $path = $format['path'];
        $is_dir = $format['is_dir'];

        foreach($filter as $item){
            $escaped_item = '';
            $item = str_split($item);
            foreach($item as $c){
                $escaped_item .= $this->regex_char_escape($c);
            }


            //todo 后面有问题，无法正确过滤
            if($is_dir){  //如果需要判断的路径是文件夹时，需要判断在排除条件处于路径中间等部分的情况
                $rgx_dir_sep = $this->regex_char_escape(DIRECTORY_SEPARATOR);
                $escaped_item = trim($escaped_item, DIRECTORY_SEPARATOR);
                $regex = "/{$rgx_dir_sep}{$escaped_item}{$rgx_dir_sep}/";
                if(preg_match($regex, $path)){
                    return true;
                }
            }else{ //若判断为文件，则只判断处在结尾的情况
                if(preg_match("/{$escaped_item}$/", $path)){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 将正则表达式的特殊字符进行转义
     * @param $char
     * @return mixed
     */
    public function regex_char_escape($char){
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
     * 将一个路径转换成“./path1/path2/filename.suffix”或“./path1/path2/”的标准格式
     * @param string $path
     * @param bool|mixed $is_dir
     * @return array
     */
    public function path_format($path, $is_dir = ''){
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        /* 将路径的开头规范化 */
        if(strpos($path, DIRECTORY_SEPARATOR) !== 0 && strpos($path, '.'.DIRECTORY_SEPARATOR) !== 0){
            $path = '.'.DIRECTORY_SEPARATOR.$path;
        }
        /* 根据路径末尾是否有分隔符判断是否为文件夹 */
        $path_rev = strrev($path);
        if(strpos($path_rev, DIRECTORY_SEPARATOR) === 0){
            $is_dir = true;
        }
        $path = rtrim($path, DIRECTORY_SEPARATOR);
        if($is_dir === true){ //如果明确指定该路径为文件夹，则给路径末尾新增一个分隔符
            $path = $path.DIRECTORY_SEPARATOR;
        }elseif($is_dir !== false){ //如果未明确指定该路径为何种类型，则判断在本地是否有对应的文件或文件夹，然后进行转化
            if(is_dir($path)){
                $path = $path.DIRECTORY_SEPARATOR;
                $is_dir = true;
            }else{
                $is_dir = false;
            }

        }

        return array('is_dir' => $is_dir, 'path' => $path);
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