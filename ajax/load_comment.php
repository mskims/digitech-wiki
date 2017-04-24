<?php
include_once $_SERVER["DOCUMENT_ROOT"]."/lib/lib.php"; 
function jsonError($code){
	global $arr;
	$arr["error"] = $code;
}
$arr = [];
$wiki = $_POST["wiki"];

$cRes = sql("select * from comment where wiki={$wiki} order by parent asc,ob asc, idx asc");
while($cData=$cRes->fetch()){
	$data;

	$data["idx"] = $cData["idx"];
	$data["owner"] = $cData["ip"];
	$data["regdate"] = $cData["regdate"];
	$data["ccontent"] = $cData["content"];
	$data["step"] = $cData["step"];
	$data["isReply"] = $cData["step"]!=0?$cData["step"]:"false";

	array_push($arr, $data);
}
echo json_encode($arr,JSON_UNESCAPED_UNICODE);
?>