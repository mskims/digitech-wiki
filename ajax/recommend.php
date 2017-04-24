<?php
include_once $_SERVER["DOCUMENT_ROOT"]."/lib/lib.php"; 

$title = $_POST["title"];

$wData = sqlf("select idx from wiki where title='{$title}'");

// 중복체크
$ts = date("Y-m-d");
$rChk = sqlf("select count(*) as cnt from recommend where ip='{$ip}' and wiki={$wData['idx']} and regdate >= '{$ts}'");
if($rChk["cnt"]>=1){
	echo "#01";
}else{
	sql("insert into recommend set wiki={$wData['idx']}, ip='{$ip}'");
	$cnt = sqlf("select count(*) as cnt from recommend where wiki={$wData['idx']}");
	echo $cnt["cnt"];
}
exit();

?>