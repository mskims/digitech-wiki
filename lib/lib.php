<?php
session_start();
date_default_timezone_set("Asia/Seoul");
// DEFINE
define("_ROOT", $_SERVER["DOCUMENT_ROOT"]);

$lenLimit = 30;

// URL
$get = isset($_GET["param"]) ? explode("/", $_GET["param"]) : NULL;
$dir = isset($get[0]) && $get[0] != "" ? $get[0] : NULL;
$page = isset($get[1]) ? $get[1] : NULL;


$ip = $_SERVER["REMOTE_ADDR"];
// DB
$db = new PDO("mysql: host=127.0.0.1;dbname=minseok_dwiki;charset=utf8", "minseok", "1234");
function sql($sql){
	global $db;
	return $db->query($sql);
}
function sqlf($sql){
	global $db;
	return sql($sql)->fetch();
}
// FCS
function indexChk(){
	global $dir;
	return !$dir;
}
function alert($msg){
	echo "<script>alert('{$msg}');</script>";
}
function move($url, $w=NULL){
	$url = !is_null($w) ? "/w/{$url}" : $url;
	echo "<script>location.href='{$url}';</script>";
}
function wChk(){
	global $dir; 
	return !indexChk() && $dir == "w";
}
function loginChk(){
	return isset($_SESSION["member"]);
}
function adminChk(){
//	return loginChk() && $_SESSION["member"]["idx"] == "1";
	return $_SERVER["REMOTE_ADDR"] == "118.36.189.171" || $_SERVER["REMOTE_ADDR"] == "39.115.229.173" || $_SERVER["REMOTE_ADDR"] == "127.0.0.1";
}
function getTitle($col, $val){
	$res = sqlf("select title from wiki where {$col}='{$val}'");
	return $res["title"];
}
function redirectChk($idx){
	$res = sql("select * from wiki where redirect={$idx}");
	return $res;
}
function getLastHistory($idx){
	$res = sqlf("select * from history where wiki={$idx} order by idx desc limit 1");
	return $res;
}



// Hash Chk
function hashRedirectChk(){
	
}



// ReplaceMent
function getReplaceMentWords(){
	return sql("select * from replacement order by idx desc");
}
// Wiki gra
function replaceEqualToH1Tag($content){
	$content = preg_replace("/===(.+?)===/is", "<h1>$1</h1>", $content);
	return $content;
}
function replaceEqualToH2Tag($content){
	$content = preg_replace("/==(.+?)==/is", "<h2>$1</h2>", $content);
	return $content;
}
function replaceEqualToH3Tag($content){
	$content = preg_replace("/\<br \/\>=(.+?)=/is", "<br /><h3>$1</h3>", $content);
	return $content;
}
function replaceUrlToImgTag($content){
    if (preg_match('#((http|https)://[^\s]+(?=\.(jpe?g|png|gif)))#i', $content)){
        $content = preg_replace('#((http|https)://[^\s]+(?=\.(jpe?g|png|gif)))(\.(jpe?g|png|gif))#i', '<img src="$1.$3" alt="$1.$3" onerror="this.alt=\'오류 : 이미지 문서 참조\'"/>', $content);
    } else {
        // $content = preg_replace('#(http://[^\s]+(?!\.(jpe?g|png|gif)))#i', '<a href="$1" target="_blank" title="$1" class="w-link">$1</a>', $content);
    }
    return $content;
}
function replaceUrlToYoutube($content){
	$content = preg_replace('/\<br \/\>(?:(http|https)?:\/\/)?(?:www\.)?(?:(?:(?:youtube.com\/watch\?[^?]*v=|youtu.be\/)([\w\-]+))(?:[^\s?]+)?)/', '<br /><iframe width="420" height="345" src="http://www.youtube.com/embed/$2" frameborder="0" allowfullscreen></iframe>', $content);
	return $content;
}
function replaceBracketToATag($content){
	$content = preg_replace("/\[\[(.+?)\]\]/is", "<a class='w-link' href='/w/$1'>$1</a>", $content);
	return $content;
}
function replaceQuoteToDivTag($content){
	$content = preg_replace("/\&lt;\"(.+?)\"\&gt;/is", "<div class='quote'>$1</div>", $content);
	return $content;
}
function replaecDelLine($content){
	$content = preg_replace("/~~(.+?)~~/is", "<del>$1</del>", $content);
	return $content;
}
function replaceCurlyBracketToCautionDiv($content, $idx, $type){
	if($type=="title"){
		$content = preg_replace("/\{(.+?)\}/is", "<div class='caution caution-title caution-{$idx}'>$1</div>", $content);
	}else{
		$content = preg_replace("/\{\{(.+?)\}\}/is", "<div class='caution caution-content'>$1</div>", $content);
	}
	return $content;
}
function replaceHashCautionToCautionDiv($content){
//	$content = preg_replace("/#틀 (.+?)\<br \/\>/is", "<div class='caution'>$1</div>", $content);
	return $content; 
}
function replaceThreeQuoteToBoldTag($content){
	$content = preg_replace("/\&\#39\;\&\#39\;\&\#39\;(.+?)\&\#39\;\&\#39\;\&\#39\;/is", "<strong>$1</strong>", $content);
	return $content;
}
function replaceTwoQuoteToItalicTag($content){
	$content = preg_replace("/\&\#39\;\&\#39\;(.+?)\&\#39\;\&\#39\;/is", "<em>$1</em>", $content);
	return $content;
}
function replaceTwoUnderlineToUnderlineTag($content){
	$content = preg_replace("/__(.+?)__/is", "<u>$1</u>", $content);
	return $content;
}


// Limit
function newArticleLimit(){
	global $ip;

	if(adminChk())	return false;
	$timestamp = date("Y-m-d H:i:s", strtotime("-30 seconds"));
	$cnt = sqlf("select count(*) as cnt from wiki where ip='{$ip}' and regdate >= '{$timestamp}' ");
	$cnt = $cnt["cnt"];
	if($cnt >= 1 ){
		return true;
	}
	return false;
}
function editArticleLimit(){
	global $ip;

	if(adminChk())	return false;
	$timestamp = date("Y-m-d H:i:s", strtotime("-15 seconds"));
	$cnt = sqlf("select count(*) as cnt from history where ip='{$ip}' and regdate >= '{$timestamp}' and type=0");
	$cnt = $cnt["cnt"];
	if($cnt >= 1 ){
		return true;
	}
	return false;
}
function newCommentLimit(){
	global $ip;

//	if(adminChk())	return false;
	$timestamp = date("Y-m-d H:i:s", strtotime("-30 seconds"));
	$cnt = sqlf("select count(*) as cnt from comment where ip='{$ip}' and regdate >= '{$timestamp}' ");
	$cnt = $cnt["cnt"];
	if($cnt >= 1 ){
		return true;
	}
	return false;
}

// Auto ban
function newArticleautoBanChk(){
	global $db, $ip;
	if(adminChk())	return false;

	$timestamp = date("Y-m-d H:i:s", strtotime("-30 seconds"));
	$cnt = sqlf("select count(*) as cnt from wiki where ip='{$ip}' and regdate >= '{$timestamp}' ");
	$cnt = $cnt["cnt"];

	if($cnt >= 2){
		ban("30초 내에 문서 2개 작성, 문서 숨김처리됨");
		sql("update wiki set hidden=1 where ip='{$ip}'");
	}
}
function editArticleAutoBanChk(){
	global $db, $ip;

	if(adminChk())	return false;
	$timestamp = date("Y-m-d H:i:s", strtotime("-15 seconds"));
	$cnt = sqlf("select count(*) as cnt from history where ip='{$ip}' and regdate >= '{$timestamp}' and type=0");
	$cnt = $cnt["cnt"];

	if($cnt >= 2){
		ban("15초 내에 수정 2회");
//		sql("update wiki set hidden=1 where ip='{$ip}'");
	}
}

// Ban
function ban($info){
	if(count(sqlf("select * from ban where ip='{$_SERVER['REMOTE_ADDR']}'")) == 1 ){
		sql("insert into ban set ip='{$_SERVER['REMOTE_ADDR']}', type=0, location='/{$_GET['param']}', info='{$info}'");
	}
	exit();
}

// Block
function nbspBlock($str){
	if(strlen($str)==0 || strpos($str, "　") > -1){
		echo "<h1>BLOCKED</h1>";
		exit();
	}
}
function lengthBlock($str, $len){
	if(mb_strlen($str, "UTF-8") > $len){
		echo "<h1>BLOCKED</h1>";
		exit();
	}
}
function tagBlock($str){
	if(strpos($str, "'") > -1 || strpos($str, '"') > -1 || strpos($str, "<") > -1 || strpos($str, ">") > -1){
		echo "<h1>BLOCKED</h1>";
		exit();
	}
}
function tagBlockReturn($str){
	$str = str_replace(">", "&gt;", str_replace("<", "&lt;", $str));
	$str = str_replace(">", "&gt", str_replace("<", "&lt;", $str));
	$str= str_replace("&lt;br /&gt;", "<br />", $str);
	return $str;
}

// Get Wikis
function getRecentEditedWiki(){
	return sql("select max(history.idx) as idx, wiki.title from history join wiki on history.wiki=wiki.idx where wiki.hidden=0 and wiki.redirect=0 and history.type=0 group by history.wiki order by idx desc limit 7");
}
function getRecentWiki(){
	return sql("select * from wiki where hidden = 0 and redirect = 0 and ( select count(hashtag.idx) from hashtag where hashtag.wiki = wiki.idx and hashtag.tag = 'redirect' )= 0 order by idx desc limit 7");
}
function getHotWiki(){
	return sql("select * from wiki where idx<>2 && idx<>121 order by hit desc limit 7");
}
function getConnectedWiki($str){
	return sql("SELECT * FROM wiki WHERE content LIKE '%[[{$str}]]%'");
}
function getIlbe($num){
	$ts = date("Y-m-d");
	return sql("select wiki.title, count(recommend.idx) as cnt FROM wiki join recommend on wiki.idx=recommend.wiki where recommend.regdate >= '{$ts}' group by recommend.wiki order by rand() limit {$num}");
}
// get Comments 
function getRecentComments(){
	return sql("select comment.content, comment.idx, wiki.title from comment join wiki on comment.wiki=wiki.idx order by comment.idx desc limit 7");
}

// get User
function getHotCreaterUser($num){
	return sql("select ip, count(*) as cnt from wiki where redirect=0 group by ip order by cnt desc limit {$num}");
}
function getHotEditerUser($num){
	return sql("SELECT count(*) as cnt, ip from history group by ip order by cnt desc limit {$num}");
}
function getHotCommentCreaterUser($num){
	return sql("SELECT count(*) as cnt, ip from comment group by ip order by cnt desc limit {$num}");
}

// Get Count
function getTodayWikiCount(){
	$res = sqlf("select count(*) as cnt from wiki where regdate>='".date("Y-m-d")."'");
	return $res["cnt"];
}
function getRecommendNum($idx){
	$res = sqlf("select count(*) as cnt from recommend where wiki={$idx}");
	return $res["cnt"];
}



// Chk
function sameArticleChk($str){
	$data = sqlf("select count(*) as cnt from wiki where upper(replace(title, ' ', '')) = '{$str}'");
	if($data["cnt"]==0){
		return false;
	}else{
		return true;
	}
}
function titleCautionChk($str){
	return strpos($str, "틀:") > -1;
}
function titlePoemChk($str){
	return strpos($str, "시:") > -1;
}
?>