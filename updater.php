<?php

/**
 * @ide             PhpStorm.
 * @author:         Pnsme
 * @datetime:       2017-7-8 0:02
 * @version:        0.1
 * @description:    进行文件升级的脚本
 */
class updater{
    const ERROR_OPTIONS_NOT_FOUND = 0;
    const ERROR_DID_NOT_INIT = 11;
    const ERROR_INIT_FAIL = 12;
    const ERROR_CONFIG_FILE_FORMAT = 21;
    const ERROR_ROOT_NOT_EXISTS = 31;

    private static $instance = null;
    private $short_options;
    private $long_options;
    private $option_map;
    private $config;
    private $init_item;
    private $config_path;

    private function __construct(){
        $params = require 'params.php';
        $this->short_options = $params['short_options'];
        $this->long_options = $params['long_options'];
        $this->option_map = $params['option_map'];
        $this->config = $params['config'];
        $this->init_item = $params['init_item'];
        $this->config_path = $params['init_item']['file']['config'];
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
     * 入口方法，处理参数，调用相应的处理
     * @param array $input
     */
    public function handle($input){
        $opt = '';
        foreach($input as $key => $val){
            $key = trim($key, ':');
            if(array_key_exists($key, $this->option_map)){
                $opt = $key;
                break;
            }
        }

        if($opt != 'init' && !$this->check_init()){
            $this->error(self::ERROR_DID_NOT_INIT);
            exit;
        }
        if($opt === ''){
            $this->error(self::ERROR_OPTIONS_NOT_FOUND);

            return;
        }
        /*  不进行配置文件相关操作时才读取配置文件 */
        if($opt != 'clear-config'){
            $this->read_config_file();
        }
        $fn = $this->option_map[$opt];
        $this->$fn($input, $opt);

    }

    private function init($input, $opt){
        $flag = $input[$opt];
        if($flag){ //重新初始化升级器
            $this->_init(true);
        }else{ //检查是否存在升级器配置，若不存在，则初始化升级器
            if(!$this->check_init()){
                $this->_init();
            }
        }
    }

    /**
     * 初始化升级器
     * @param bool $flag
     */
    private function _init($flag = false){ //todo 当--init=true时，删除原有的目录，建立新的目录
        foreach($this->init_item['dir'] as $item){
            if(!is_dir($item)){
                mkdir($item);
            }
        }
        foreach($this->init_item['file'] as $item){
            if(!is_file($item)){
                if($item == $this->init_item['file']['config']){
                    $this->new_config($item);
                    continue;
                }
                $handle = fopen($item, 'w');
                fclose($handle);
            }
        }
    }

    private function check_init(){
        foreach($this->init_item['dir'] as $item){
            if(!is_dir($item)){
                return false;
            }
        }
        foreach($this->init_item['file'] as $item){
            if(!is_file($item)){
                return false;
            }
        }

        return true;
    }

    private function read_config_file(){
        if(!is_file($this->config_path)){
            return;
        }

        try{
            $file_config = json_decode(file_get_contents($this->config_path), true);
        }catch(Exception $e){
            $file_config = array();
        }

        if(!is_array($file_config)){
            $this->error(self::ERROR_CONFIG_FILE_FORMAT);
            exit;
        }
        $this->config = array_merge($this->config, $file_config);

    }

    private function clear_config(){
        $this->new_config();
    }

    private function new_config($path = ''){
        if($path === ''){
            $path = $this->init_item['file']['config'];
        }
        $handle = fopen($path, 'w');
        $config = json_encode($this->config);
        $config = str_replace(",", ",\n", $config);
        $config = str_replace("{", "{\n", $config);
        $config = str_replace("}", "\n}", $config);
        $config = str_replace(":", ": ", $config);
        $config = str_replace("\n\"", "\n    \"", $config);
        fwrite($handle, $config);
        fclose($handle);
    }


    private function pack(){
        $root = $this->config['root'];
        if(!is_dir($root)){
            $this->error(self::ERROR_ROOT_NOT_EXISTS);
            exit;
        }

        $result = $this->item_finder($root);
        var_dump($result);

    }

    public function get_options(){
        return array('short' => $this->short_options, 'long' => $this->long_options);
    }

    private function error($error_mark){
        $error = array(
            self::ERROR_OPTIONS_NOT_FOUND  => '未找到操作',
            self::ERROR_INIT_FAIL          => '初始化失败',
            self::ERROR_DID_NOT_INIT       => '未初始化',
            self::ERROR_CONFIG_FILE_FORMAT => '配置文件读取错误',
        );

        echo $error[$error_mark];
    }

    private function item_finder($dir){
        static $file_list = array();
        $items = $this->dir_analyse($dir);
        $folder_new_tag = $this->config['folder-new-tag'];
        $folder_update_tag = $this->config['folder-update-tag'];
        $file_new_suffix = $this->config['file-new-suffix'];
        $file_update_suffix = $this->config['file-update-suffix'];
        foreach($items['dirs'] as $item){
            if(preg_match("/.*\.({$folder_new_tag}|{$folder_update_tag})$/", $item)){
                $file_list['dirs'][] = $dir.DIRECTORY_SEPARATOR.$item;
            }
            $this->item_finder($dir.DIRECTORY_SEPARATOR.$item);
        }

        foreach($items['files'] as $item){
            if(preg_match("/.*\.({$file_new_suffix}|{$file_update_suffix})$/", $item)){
                $file_list['files'][] = $dir.DIRECTORY_SEPARATOR.$item;
            }
        }

        return $file_list;
    }

    private function dir_analyse($dir){
        $dir_arr = scandir($dir);
        $dirs = array();
        $files = array();
        foreach($dir_arr as $item){
            if($item == '.' || $item == '..'){
                continue;
            }

            if(is_dir($dir.'/'.$item)){
                $dirs[] = $item;
            }else{
                $files[] = $item;
            }
        }

        return array('dirs' => $dirs, 'files' => $files);
    }

}
