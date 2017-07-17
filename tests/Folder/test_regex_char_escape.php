<?php
/**
 * @ide         PhpStorm.
 * @author:     Pnsme
 * @datetime:   2017-7-18 0:35
 * @version:    0.0
 * @description:   [description]
 */

require '../../libs/Folder.php';
$folder = Folder::getInstance();

$test = array(
    '.'  => '\.',
    '['  => '\[',
    ']'  => '\]',
    '('  => '\(',
    ')'  => '\)',
    '|'  => '\|',
    '/'  => '\\'.DIRECTORY_SEPARATOR,
    '\\' => '\\'.DIRECTORY_SEPARATOR,
);
$flag = true;
foreach($test as $index => $item){
    $result = $folder->regex_char_escape($index);
    if($result != $item){
        $flag  = false;
        echo $index.' => '.$item."       wrong to : {$result}";
        echo "<br>";
    }
}

if($flag){
    echo "Everything is fine";
}