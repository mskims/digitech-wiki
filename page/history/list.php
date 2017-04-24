<?php
$title = $get[2];
tagBlock($title);
nbspBlock($title);
lengthBlock($title, $lenLimit);


$sql = "select idx,locked from wiki where title='{$title}'";
$wData = sqlf($sql);
$sql = "select *, CHAR_LENGTH(content) as length from history where wiki={$wData['idx']} order by idx desc";
$hRes = sql($sql);

if($wData["locked"] && !adminChk()){
	alert("잠긴 문서입니다. 관리자만 확인할수있습니다");
	echo "<script>history.back();</script>";
	exit();
}
?>
<div class="w-content-wrap">
<h1 style="margin-bottom: 30px; "><?=$title?> 의 수정 기록</h1>
<a href="#" onclick="history.back();">뒤로가기</a> <br /><br />
<ul>
<?php
	while($hData=$hRes->fetch()){
		if(titlePoemChk($title)){
			$hData["ip"] = "익명";
		}
		$type = "수정본";
		switch($hData["type"]){
			case 1:
				$type = "생성됨";
				break;
			case 2:
				$type = "잠금 설정됨";
				break;
			case 3:
				$type = "잠금 해제됨";
		}
	?>
	<li><a href="/history/view/<?=$title?>/<?=$hData['idx']?>"><?=$hData["regdate"]?> <?=$type?> || <?=$hData["ip"]?> (<?=$hData["length"]?>)</a></li>
	<?php } ?>
</ul>
</div>