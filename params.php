<?php
/**
 * @ide             PhpStorm.
 * @author:         Pnsme
 * @datetime:       2017-7-8 0:02
 * @version:        0.1
 * @description:    一些参数
 */
return array(
    'short_options' => 'v',
    'long_options'  => array(
        'init::',               //初始化，若带上true参数，则会强制初始化，即清空当前所有的缓存内容

        'clear-config',         //清空配置文件

        'keep',                 //操作后保留原文件
        'hash',                 //输出操作hash
        'version:',             //操作指定版本
        'clear',                //清理项目中的配置内容

        'pack',                 //打包
        'pack-include-dir:',    //指定打包程序扫描包含目录
        'pack-exclude-dir:',    //指定打包程序扫描排除目录
        'file-new-suffix:',     //新增文件后缀标记
        'file-update-suffix:',  //更新文件后缀标记
        'folder-new-tag:',      //新增目录标记
        'folder-update-tag:',   //更新目录（名）标记

        'release',               //更新
        'release-include-dir:',  //指定升级程序包含的目录
        'release-exclude-dir:',  //指定升级程序排除的目录
    ),

    'option_map' => array(
        'init'         => 'init',
        'hash'         => 'show_hash',
        'pack'         => 'pack',
        'release'      => 'release',
        'clear-config' => 'clear_config',
    ),
    'config'     => array(
        'root'               => '.',
        'keep'               => false,
        'version'            => 0,
        'file-new-suffix'    => 'nf',     //新增文件后缀标记，通过使用‘|’来标记多种配置
        'file-update-suffix' => 'uf',  //更新文件后缀标记，通过使用‘|’来标记多种配置
        'folder-new-tag'     => 'nt',      //新增目录标记，通过使用‘|’来标记多种配置
        'folder-update-tag'  => 'ut',   //更新目录（名）标记，通过使用‘|’来标记多种配置
        'config-segment'     => '.update',  //更新配置片段，通过使用‘|’来标记多种配置
        'clear-config'       => false,        //是否在操作后清理所有的配置项
    ),
    'init_item'  => array(
        'dir'  => array(
            'base'   => '.update',
            'backup' => '.update'.DIRECTORY_SEPARATOR.'backup',
            'caches' => '.update'.DIRECTORY_SEPARATOR.'caches',
            'info'   => '.update'.DIRECTORY_SEPARATOR.'info',
            'logs'   => '.update'.DIRECTORY_SEPARATOR.'logs',
        ),
        'file' => array(
            'config' => '.update/config.json',
        ),
    ),
);