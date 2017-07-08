<?php

/**
 * @ide             PhpStorm.
 * @author:         Pnsme
 * @datetime:       2017-7-8 0:02
 * @version:        0.0
 * @description:    进行文件升级的脚本
 */
class updater
{
    private static $instance = null;
    private $config = array();
    private function __construct(){
        $this->config = $this->read_config_file();
    }
    private function __clone(){}

    public static function getInstance(){
        if(! self::$instance instanceof self){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function option($options){
        $opt_map = array(
            'config' => 'set_config_file',
            'new-config' => 'generate_new_config',
            'hash' => 'show_hash',
            'pack' => 'pack',
            'release' => 'release'
        );

        $opt = '';
        foreach ($options as $key => $val){
            $key = trim($key, ':');
            if(array_key_exists($key, $opt_map)){
                $opt = $key;
                break;
            }
        }

        if($opt === ''){
            echo error(0);
            return;
        }

        $opt_map[$opt]($options);

    }

    private function set_config_file($options){
        var_dump($options);
        echo '设置新的配置文件';
    }

    private function error($flag){
        $error = array(
            0 => '未找到操作',
        );
        return $error[$flag];
    }

    private function read_config_file(){

        return array();
    }

}