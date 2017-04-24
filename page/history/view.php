<?php
$title = $get[2];
$idx = $get[3];

$sql = "select * from wiki where title='{$title}'";
$wData = sqlf($sql);
$sql = "select * from history where wiki={$wData['idx']} and idx={$idx}";
$hiData = sqlf($sql);

if($wData["locked"] && !adminChk()){
	alert("잠긴 문서입니다. 관리자만 확인할수있습니다");
	echo "<script>history.back();</script>";
	exit();
}


$content = tagBlockReturn($hiData["content"]);
sql("update history set hit=hit+1 where idx={$idx}");

?>
<div class="w-content-wrap">
<h1 style="margin-bottom: 30px; "><?=$hiData["regdate"]?> 의 <?=$title?> 정보 입니다</h1>
<a href="#" onclick="history.back();">뒤로가기</a><br /><br />
<?=$content?>
<br /><br />
<a href="#" onclick="history.back();">뒤로가기</a>
</div>