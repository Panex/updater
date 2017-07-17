<?php
/**
 * @ide         PhpStorm.
 * @author:     Pnsme
 * @datetime:   2017-7-18 0:15
 * @version:    0.0
 * @description:   [description]
 */

require '../../libs/Folder.php';
$folder = Folder::getInstance();

$test = array(
    '/path1/path2/'  => DIRECTORY_SEPARATOR.'path1'.DIRECTORY_SEPARATOR.'path2'.DIRECTORY_SEPARATOR,
    '/path1/file'    => DIRECTORY_SEPARATOR.'path1'.DIRECTORY_SEPARATOR.'file',
    './path1/path2/' => '.'.DIRECTORY_SEPARATOR.'path1'.DIRECTORY_SEPARATOR.'path2'.DIRECTORY_SEPARATOR,
    './path1/file'   => '.'.DIRECTORY_SEPARATOR.'path1'.DIRECTORY_SEPARATOR.'file',
    'path1/path2/'   => '.'.DIRECTORY_SEPARATOR.'path1'.DIRECTORY_SEPARATOR.'path2'.DIRECTORY_SEPARATOR,
    'path1/file'     => '.'.DIRECTORY_SEPARATOR.'path1'.DIRECTORY_SEPARATOR.'file',

    '\\path1\\path2\\'  => DIRECTORY_SEPARATOR.'path1'.DIRECTORY_SEPARATOR.'path2'.DIRECTORY_SEPARATOR,
    '\\path1\\file'    => DIRECTORY_SEPARATOR.'path1'.DIRECTORY_SEPARATOR.'file',
    '.\\path1\\path2\\' => '.'.DIRECTORY_SEPARATOR.'path1'.DIRECTORY_SEPARATOR.'path2'.DIRECTORY_SEPARATOR,
    '.\\path1\\file'   => '.'.DIRECTORY_SEPARATOR.'path1'.DIRECTORY_SEPARATOR.'file',
    'path1\\path2\\'   => '.'.DIRECTORY_SEPARATOR.'path1'.DIRECTORY_SEPARATOR.'path2'.DIRECTORY_SEPARATOR,
    'path1\\file'     => '.'.DIRECTORY_SEPARATOR.'path1'.DIRECTORY_SEPARATOR.'file',

    '/path1\\path2\\'  => DIRECTORY_SEPARATOR.'path1'.DIRECTORY_SEPARATOR.'path2'.DIRECTORY_SEPARATOR,
    '\\path1/file'    => DIRECTORY_SEPARATOR.'path1'.DIRECTORY_SEPARATOR.'file',
    '.\\path1\\path2/' => '.'.DIRECTORY_SEPARATOR.'path1'.DIRECTORY_SEPARATOR.'path2'.DIRECTORY_SEPARATOR,
    './path1\\file'   => '.'.DIRECTORY_SEPARATOR.'path1'.DIRECTORY_SEPARATOR.'file',
    'path1\\path2/'   => '.'.DIRECTORY_SEPARATOR.'path1'.DIRECTORY_SEPARATOR.'path2'.DIRECTORY_SEPARATOR,
);
$flag = true;
foreach($test as $index => $item){
    $result = $folder->path_format($index);
    if($result['path'] != $item){
        $flag = false;
        echo $index.'=>'.$item.'     :wrong to "'.$result['path'].'"';
        echo "<br>";
    }
}

if($flag){
    echo "Everything is fine !";
}