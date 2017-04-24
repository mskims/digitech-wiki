<div class="w-content-wrap">
<?php
tagBlock($page);
nbspBlock($page);
lengthBlock($page, $lenLimit);


$title = "";
$content = "";
// $sql = "select wiki.* from wiki where title='{$page}'";
$search_key = $page;
$search_key = str_replace(" ", "", $search_key);

	$sql = "select wiki.* from wiki where upper(replace(title, ' ', '')) = '{$search_key}'";
// $sql = "select wiki.*, count(recommend.idx) as cnt FROM wiki join recommend on wiki.idx=recommend.wiki WHERE upper(replace(wiki.title, ' ', '')) = '{$search_key}'";
$wData = sqlf($sql);



$isset = false;
if(count($wData) > 1){
// 문서 존재
if($page!=$wData["title"]){
	move("/w/{$wData['title']}");
}
$page = $wData["title"];

$isset = true;
$title = $wData["title"];
if(adminChk()){
	$title .= "(".$wData["idx"].")";
}

if($wData["parent"] != 0){
	// Parent가 0이 아닐시 Parent의 Title을 가져와서 title앞에 붙임
	$ptitle = getTitle("idx", $wData["parent"]);
	echo "부모 위키가 존재합니다:<a href='/w/{$ptitle}'>{$ptitle}</a><br />";
//	$title = $ptitle.":".$title;
}else if($wData["redirect"] != 0){
	// redirect가 0이 아닐시 redirect의 title을 가져와서 move함수ㄱ
	$rtitle = getTitle("idx", $wData["redirect"]);
	move($rtitle."?r=".$wData["title"], "w");
}


$isR = false;
if(isset($_GET["r"])){
	$isR = true;
	echo "<div>이문서는 <a href='/w/{$_GET['r']}'>".$_GET["r"]."</a>(으)로 검색해도 들어올 수 있습니다.</div>";
}

$rRes = redirectChk($wData["idx"]);
while($rData=$rRes->fetch()){
	if($isR){
		if($_GET["r"] == $rData["title"]){
			continue;
		}
	}
	echo "<div>이문서는 <a href='{$rData['title']}'>".$rData["title"]."</a>(으)로 검색해도 들어올 수 있습니다.</div>";
}

/*
sql
대문 title 검색 -> parent가 0 이 아니면 parent값을 idx로 가진 row의 title값 join, 
else redirect가 0이 아니면 redirect값을 idx로 가진 row의 title값 join
*/




/*
특수문자
&equal;
&lbracket;
&rbracket;
*/


// Content 제어
$content = $wData["content"];

// 틀
preg_match_all("/#틀 (.+?)\<br \/\>/is", $content, $cautionArr);
foreach($cautionArr[1] as $caution){
	$cautionTitle = trim($caution);
	$cautionData = sqlf("select * from wiki where title='틀:{$cautionTitle}'");

	if(count($cautionData) == 1){
		$content = str_replace("#틀 {$cautionTitle}", "{경고. {$cautionTitle} 틀이 존재하지 않습니다.}{{틀 이름을 확인해주세요.}}", $content);
	}else{
		$content = str_replace("#틀 {$cautionTitle}", $cautionData["content"]."", $content);
	}
	$cautionIdx = count($cautionData)!=1 ? $cautionData["idx"] : 1;
	$content = replaceCurlyBracketToCautionDiv($content, $cautionIdx, "content");
	$content = replaceCurlyBracketToCautionDiv($content, $cautionIdx, "title");
}
if(titleCautionChk($page)){
	$content = replaceCurlyBracketToCautionDiv($content, $wData["idx"], "content");
	$content = replaceCurlyBracketToCautionDiv($content, $wData["idx"], "title");
}




// 해시태그
$hashRes = sql("select * from hashtag where wiki={$wData['idx']}");
while($hashData=$hashRes->fetch()){
	$html = "";
	if($hashData["tag"]=="redirect"){
		move("/w/{$hashData['query']}?r={$wData['title']}");
		exit();
	}
	if($hashData["lim"]!=0){
		$hashData["query"] .= " limit {$hashData['lim']}";
	}
	$hashData["query"] = str_replace("#ymd", date("Y-m-d"), $hashData["query"]);
	$isRes = sql($hashData["query"]);
	while($isData=$isRes->fetch()){
		$html .= "[[{$isData['title']}]]<br />";
	}
	$content = str_replace("#".$hashData["tag"], $html, $content);
}

// 문서 링크 (| 포함, js로 처리)
$content  = replaceBracketToATag($content);

// h1,h2,h3
$content = replaceEqualToH1Tag($content);
$content = replaceEqualToH2Tag($content);
$content = replaceEqualToH3Tag($content);

// 인용구
$content = replaceQuoteToDivTag($content);

// 이미지
$content = replaceUrlToImgTag($content);

// 취소선
$content = replaecDelLine($content);

// 유튜브
$content = replaceUrlToYoutube($content);

// 굵게
$content = replaceThreeQuoteToBoldTag($content);

// 기울이기
$content = replaceTwoQuoteToItalicTag($content);

// 밑줄 
$content = replaceTwoUnderlineToUnderlineTag($content);

// 치환
$repRes = getReplaceMentWords();
while($repData=$repRes->fetch()){
	$content = str_replace($repData["str1"], $repData["str2"], $content);
}





$lhData = getLastHistory($wData["idx"]);
if(count($lhData)>1){
	if(titlePoemChk($page)){
		$lhData["ip"] = "익명";
	}
	echo "<div>{$lhData['regdate']} 에 {$lhData['ip']} 에 의해 마지막으로 수정됨.</div>";	
}
sql("update wiki set hit=hit+1 where idx={$wData['idx']}");


}else{
// 문서 없음
$title = "문서가 없습니다";
$content = "<button type='button' onclick='location.href=\"/edit/{$page}\"'>".$page." 문서 만들기</button>";
}



?>



<div class="wtitle"><?=$title?></div>
<?php if($isset){ ?>
<div class="wtools">
	<button type="button" onclick="location.href='/edit/<?=$page?>';">편집</button>
	<button type="button" onclick="location.href='/history/list/<?=$page?>';">기록</button>

	<?php if($wData["locked"]){ ?>
	<button type="button" class="btn-danger" onclick="location.href='/lock/<?=$page?>/recovery';">잠겨있음</button>
	<?php } else { ?>
	<button type="button" onclick="location.href='/lock/<?=$page?>';">잠금</button>
	<?php } ?>

	<button type="button" class="recommend-action" data-title="<?=$wData['title']?>">추천 <?=getRecommendNum($wData['idx'])?></button>


	<?php if(adminChk()){ ?>
	<button type="button" onclick="location.href='/hide/<?=$page?>';">숨김</button>
	<?php } ?>
</div>
<?php } ?>
<div class="ad ad1">

<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- 디지텍위키_반응형 -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-4193503801718208"
     data-ad-slot="3816563974"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>


</div>


<div class="toc" id="toc">
	<div class="toc-indent toc-cont"></div>
</div>
<div class="wcontent">
<?=$content?>
</div>

</div>

<?php 
// Comment
if($isset){

//$test1 = sqlf("select * from comment where idx=22");
//$test2 = sqlf("select max(ob) as mx from comment where parent=1 and step={$test1['step']}+1 and ob>{$test1['ob']}");
// $mx = count($test2["mx"])!=0?$test2["mx"]:$test1["ob"];
// sql("update comment set ob=ob+1 where ob>{$mx}");
// echo "<br />";
// sql("insert into comment set wiki=16261, content='댓글1의 답글의 답글2의 답글5의 답글1', parent={$test1['parent']}, ob={$mx}+1, step={$test1['step']}+1, ip='192.168.0.1'");

$cRes = sql("select * from comment where wiki={$wData['idx']} order by parent asc,ob asc, idx asc");
?>
<div class="w-comment-wrap">
	<div class="wcomment" id="comments">
	
	<?php while($cData=$cRes->fetch()){ $isReply = $cData["step"]!=0?$cData["step"]:false; ?>
		<div class="comment<?=$isReply?" reply":""?>" id="comment_<?=$cData["idx"]?>">
			<?=$isReply?"<div class='reply-inner' style='margin-left: ".($isReply*15)."px; '>":""?>
			<div class="owner"><?=$cData["ip"]?></div>
			<div class="coption">
				<button>수정</button>
				<button>삭제</button>
				<button>답글</button>
			</div>
			<div class="regdate"><?=$cData["regdate"]?></div>
			<div class="clear" style="clear: both"></div>
			<div class="ccontent"><?=$cData["content"]?></div>
		</div>
		<?=$isReply?"</div>":""?>
	<?php } ?>
		<form id="commentmaineditor" action="#" method="post" data-articleid="<?=$wData["idx"]?>">
			<div class="ceditor">
				<textarea id="comment-editor" placeholder="댓글을 작성해보세요"></textarea>
			</div>
			<div class="caction">
				<button type="submit">등록</button>
			</div>
		</form>

	</div>
</div>

<?php } ?>
<script>

// 문서 없을때
<?php if(!$isset){ ?>
$(".toc").hide();
<?php } ?>

// 목차 생성
$(function(){
	var h1=1, h2=1, h3=1;
	$(".article h1, .article h2, .article h3").each(function(idx){
		switch($(this)[0].tagName){
			case "H1":
				numbering($(this), h1);
				h1++;
				h2=1,h3=0;
				break;
			case "H2":
				numbering($(this), (h1-1)+"."+h2);
				h2++;
				h3=1;
				break;
			case "H3":
				numbering($(this), (h1-1)+"."+(h2-1)+"."+h3);
				h3++;
				break;
		}
	});
	if(h1==1&&h2==1&&h3==1){
		$(".toc").hide();
	}
});
function numbering(ele, num){
	var $span = $("<span/>").addClass("toc-item").html(atagging(num, "reverse") + " " +$(ele).text());

	var commaCnt = (num+"").split(".").length-1;
	if(commaCnt>0){
		$span.addClass("toc-indent"+(commaCnt));
	}
	$span.appendTo(".toc-cont");

	$(ele).html(atagging(num) + " " +$(ele).text());
}
function atagging(html, reverse){
	if(typeof reverse != "undefined"){
		html = "<a href='#s-" + (html+"").replace(/\./gi, "-") + "'>" + html + ".</a>";
	}else{
		html = "<a href='#toc' id='s-" + (html+"").replace(/\./gi, "-") + "'>" + html + ".</a>";
	}
	return html;
}
// 목차 생성




// 문법 수정
// 문법 수정

// 브라켓 문법
addBracketLink();
function addBracketLink(){
	$(".wcontent a.w-link").each(function(){
		if($(this).text().indexOf("|") > -1 ){
			// | 문자 포함시
			$(this).attr({
				"href": $(this).text().split("|")[0],
				"target": "_blank"
			});
			$(this).addClass("w-link-out").text("#"+$(this).text().split("|")[1]);
		}else{
			$(this).attr("href", "/w/"+$(this).text());	
		}
	});	
}
</script>