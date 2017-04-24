<?php
if(!adminChk()){
	alert("관리자 전용입니다");
	echo "<script>history.back();</script>";
	exit();
}

sql("update wiki set hidden=1 where title='{$page}'");
alert("숨김처리 되었습니다.");
echo "<script>history.back();</script>";
?>