<div class="w-content-wrap">
<?php
/*
if(!adminChk()){
	echo "<h1>읽기만 가능합니다</h1><br /><a href='#' onclick='history.back();'>뒤로가기</a>";
	exit();
}*/
if(isset($_POST["action"])){
	// reCaptcha
/*	$captcha;
	if(isset($_POST["g-recaptcha-response"])){
		$captcha = $_POST["g-recaptcha-response"];
	}
	if(!$captcha){
		alert("로봇이 아닙니다. 를 눌러주세요");
		echo "<script>history.back();</script>";
		exit();
	}*/

	
	$_POST["wcontent"] = str_replace(">", "&gt;", str_replace("<", "&lt;", $_POST["wcontent"]));
	$_POST["wcontent"] = str_replace("\n", "<br />", $_POST["wcontent"]);
	$_POST["wcontent"] = str_replace("'", "&#39;", $_POST["wcontent"]);
	$originalWContent = $_POST["wcontent"];

	$page = str_replace(">", "&gt", str_replace("<", "&lt;", $page));
	if($_POST["isnew"]=="true"){
		if(newArticleLimit()){
			alert("너무 빨리 작성하실 수 없습니다. 기다려주세요");
			echo "<script>history.back();</script>";
			exit();
		}else{
			newArticleautoBanChk();
			$parent = 0;
			$sqlip = $ip;
			if(titleCautionChk($page)){
				$parent = 16217;
			}
			if(titlePoemChk($page)){
				$parent = 16298;
			}
			if(sameArticleChk($page)){
				alert("같은 이름의 문서가 있습니다. 작성중이신 문서는 클립보드에 복사되었습니다. Ctrl v 로 붙여넣으세요.");
				echo "<script>window.clipboardData.setData('{$originalWContent}','copy');history.back();</script>";
				exit();
			}
			sql("insert into wiki set content='{$_POST['wcontent']}', parent={$parent}, owner=1, title='{$page}', redirect=0, ip='{$sqlip}'");
			$_POST["idx"] = $db->lastInsertId();
			sql("insert into history set type=1, wiki={$_POST['idx']}, ip='{$sqlip}', content='{$_POST['wcontent']}'");
			move("/w/{$page}");
		}
	}else{
		if(editArticleLimit()){
			alert("너무 자주 수정하실 수 없습니다. 기다려주세요");
			// echo "<script>history.back();</script>";
		}else{
			editArticleAutoBanChk();
			sql("update wiki set content='{$_POST['wcontent']}' where idx={$_POST['idx']} and title='{$page}' ");
			sql("insert into history set wiki={$_POST['idx']}, ip='{$_SERVER['REMOTE_ADDR']}', content='{$_POST['wcontent']}'");
			move("/w/{$page}");
		}
	}
}
nbspBlock($page);

$title = $page;
$content = "";
$sql = "select wiki.* from wiki where title='{$page}'";
$wData = $db->query($sql)->fetch();
$isNew = false;
$title = $wData["title"];

if($wData["locked"] == "1"){
	if(!adminChk()){
		alert("잠긴 문서입니다. 관리자만 수정할수있습니다");
		echo "<script>history.back();</script>";
		exit();
	}
}
if($title==""){
	$title=$page."(작성)";
	$isNew = true;
}

if($wData["parent"] != 0){
	// Parent가 0이 아닐시 Parent의 Title을 가져와서 title앞에 붙임
	$ptitle = getTitle("idx", $wData["parent"]);
	echo "부모 위키가 존재합니다:{$ptitle}<br />";
	$title = $ptitle.":".$title;
}
$content = str_replace("<br />", "\n", $wData["content"]);
?>

<form action="#" method="post">
	<input type="hidden" name="isnew" value="<?=$isNew?"true":"false"?>" />
	<input type="hidden" name="action" value="edit" />
	<input type="hidden" name="idx" value="<?=$wData["idx"]?>" />
	<div class="wtitle" style="width: 100%; float: none; "><a href="/w/<?=$page?>"><?=$title?></a></div>
	<div class="wcontent edit">
	<textarea name="wcontent"><?php
	if($isNew){
		if(titleCautionChk($page)){
			echo "{타이틀}\n{{상세내용}}";
		}else{
			echo "=== 개요 ===\n";
		}
	}else{
		echo $content;
	}
	?></textarea>
	</div>
	<div class="etools">
		
		<button type="submit">저장</button>
	</div>
</form>
</div>