<?php
include_once $_SERVER["DOCUMENT_ROOT"]."/lib/lib.php"; 

function test($content){
	$content = preg_replace("/\&\#39\;\&\#39\;\&\#39\;(.+?)\&\#39\;\&\#39\;\&\#39\;/is", "<strong>$1</strong>", $content);
	return $content;
}
echo test("&#39;&#39;&#39;굵게&#39;&#39;&#39;");
?>