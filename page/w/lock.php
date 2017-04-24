<?php

$recovery = isset($get[2]) && $get[2] == "recovery" ? true : false;
if(isset($_POST["action"])){
	if($_POST["code"]=="5286a"){
		sql("update wiki set locked=".($recovery?"0":"1")." where title='{$_POST['title']}'");
		$wData = sqlf("select idx from wiki where title='{$_POST['title']}'");
		sql("insert into history set type=".($recovery?"3":"2").", wiki={$wData['idx']}, ip='{$_SERVER['REMOTE_ADDR']}', content='#잠금 ".($recovery?"해제":"설정")."됨 || IP : {$_SERVER['REMOTE_ADDR']}'");
		alert("처리되었습니다");
		move("/w/".$_POST["title"]);
	}else{
		alert("관리자 전용입니다.");
		move("/w/".$_POST["title"]);
	}
}
?>

<form action="#" method="post">
	<input type="hidden" name="action" value="lock" />
	<input type="hidden" name="title" value="<?=$page?>" />
	<input type="password" name="code" placeholder="관리자 암호 입력" />
	<button type="submit"><?=$recovery?"잠금해제":"잠금"?></button>
</form>