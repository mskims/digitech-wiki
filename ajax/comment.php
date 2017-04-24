<?php
include_once $_SERVER["DOCUMENT_ROOT"]."/lib/lib.php"; 

function ajaxError($code){
	echo "#E".$code;
	exit();
}
if(strlen($_POST["content"]) <= 0){
	ajaxError(1);
}
// 15초 댓글 체크 (추가예정)
if(newCommentLimit()){
	ajaxError(2);
}

$_POST["content"] = tagBlockReturn($_POST["content"]);
$parent = sqlf("select max(parent) as mx from comment");
$parent = $parent["mx"];
sql("insert into comment set wiki={$_POST['wiki']}, content='{$_POST['content']}', parent={$parent}+1, ob=0, step=0, ip='{$ip}'");
echo "#99";
?>