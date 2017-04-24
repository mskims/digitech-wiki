<?php

include_once $_SERVER["DOCUMENT_ROOT"]."/lib/lib.php"; 
$ip2 =  $_SERVER["REMOTE_ADDR"];

/*if(!adminChk()){
	exit();
}*/

$ip = $_SERVER["REMOTE_ADDR"];
if(count(sqlf("select * from ban where ip='{$ip}'")) != 1){
	// Ban Action
	echo '<div class="notice" style="width: 100%; height: 50px; line-height: 50px">
		<div class="container">
			<h1>BLOCKED</h1>
		</div>
	</div>';
	exit();	
}

//if($_SERVER["SERVER_NAME"]!="digitech.wiki" && !adminChk()){
	if(false){
	$url = "http://digitech.wiki";
	if(strlen($_GET["param"])>1){
		$url .= "/".$_GET["param"];
	}
	echo "<h1><a href='{$url}'>2초후 digitech.wiki 로 이동합니다</a></h1><meta http-equiv='refresh' content='2; url={$url}'></meta>";
	exit();
}

if(false){
	echo '<div class="notice" style="width: 100%; height: 50px; line-height: 50px">
		<div class="container">
			<h1>admin@kimminseok.info</h1>
		</div>
	</div>';
	exit();	
}
?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="naver-site-verification" content="64ce58d0c6db37879c60bca3c64df95cba4f9fad"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="Keywords" content="학생, 고등학교, 디지텍, 디지텍고, 서울디지텍, 서울디지텍고, 서울디지텍고등학교, 디지텍위키, 서울디지텍위키, 위키피디아, 위키">
<meta name="Description" content="서울디지텍고등학교의 위키피디아입니다. 문의사항 http://facebook.com/digitech.wiki">

<title><?=$dir=="w"&&$page!="대문"?$page." - ":""?>디지텍 위키</title>
<link rel="stylesheet" href="/common/css/main.css" />
<link rel="stylesheet" href="/common/css/wiki.css" />
<link rel="stylesheet" href="/common/css/responsive.css"/>
<link rel="shortcut icon" type="image/x-icon" href="/static/favi.ico" />
<link rel="apple-touch-icon" href="/static/apple.png"/>

<script src="/common/js/jquery.js"></script>
<script src="/common/js/wiki.js"></script>
<script src="https://www.google.com/recaptcha/api.js"></script>
</head>
<!-- 애널리틱스 시작 -->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-74090106-1', 'auto');
  ga('send', 'pageview');

</script>
<!-- 애널리틱스 종료 -->

<body>
<!-- 페이스북 SDK -->
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/ko_KR/sdk.js#xfbml=1&version=v2.5";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<!-- 페이스북 SDK-->

<div class="wrap" id="main">
	<?php if(false){//if($_SERVER["SERVER_NAME"]!="digitech.wiki"){ ?>			
	<div class="container">
			<h2><a href="#" onclick="top.location = 'http://digitech.wiki'">digitech.wiki</a>로 접속해주세요.</h2>
		</div>
	</div>
	<?php } ?>
	<div class="notice mobile" style="width: 100%; height: 50px; line-height: 50px; text-align: center; ">
		<div class="container">
			<h1><a class="w-link" href="#banners">배너 모음</a></h1>
		</div>
	</div>
	<header id="header">
		<div class="container">
			<a href="/w/대문">디지텍위키</a>
			<div class="search">
				<input class="search_key" type="text" placeholder="검색: 엔터" maxlength="50"/>
			</div>
		</div>
	</header>
	<div class="content" id="content">
		<div class="container">
			<div class="left">
				<div class="article">
				<?php
				
				if(indexChk()){
					move("/w/대문");
				}else{
					$path = _ROOT;
					if($dir=="w"){
						$path .= "/page/w/article.php";
					}else if($dir=="edit"){
						$path .= "/page/w/edit.php";
					}else if($dir=="lock"){
						$path .= "/page/w/lock.php";
					}else if($dir=="hide"){
						$path .= "/page/w/hide.php";
					}else if($page!=NULL){
						$path .= "/page/{$dir}/{$page}.php";					
					}else{
						$path .= "/page/{$dir}/{$dir}.php";
					}
					if(file_exists($path)){
						include_once $path;
					}else{
						echo "<h1>404 에러</h1>";
					}
					
				}

				?>
				</div>
			</div>

			<div class="right" id="banners">
				<div class="banner mobile">
					<h2><a href="#main">맨위로</a></h2>
				</div>
				<div class="banner">
					<h2><a href="/random">랜덤 위키</a></h2>
				</div>
				<div class="banner">
					<h2><a href="/w/대문#s-2-1">주요문서</a></h2>
					<ul>
						<li><a href="/w/모든 문서">모든 문서</a></li>
						<li><a href="/disconnected">연결되지 않은 문서</a></li>
						<li><a href="/w/오늘 생성된 문서">오늘 생성된 문서</a></li>
					</ul>
				</div>
				<div class="banner">
					<h2>연결된 문서</h2>
					<ul>
						<?php 
						$cnt = 0;
						if((strpos($page, "사건") > -1 || strpos($page, "사고") > -1) && $wData["idx"]!=15899){
							echo "<li><a href='/w/사건 및 사고'>사건 및 사고</a></li>";
							$cnt = 1;
						}
						$reRes = getConnectedWiki($page);
						while($reData=$reRes->fetch()){ $cnt++; ?>
						<li><a href="/w/<?=$reData["title"]?>"><?=$reData["title"]?></a></li>
						<?php } if($cnt==0){echo "<li>없습니다</li>";}?>

					</ul>
				</div>
				<div class="banner ilbe">
					<h2><a href="/w/추천 문서">추천 문서</a></h2>
					<ul>
						<?php 
						$reRes = getIlbe(5);
						while($reData=$reRes->fetch()){ ?>
						<li><a href="/w/<?=$reData["title"]?>"><?=$reData["title"]?></a></li>
						<?php } ?>

					</ul>
				</div>
				<div class="banner">
					<h2>최근 생성</h2>
					<?php $todayCount = getTodayWikiCount(); if($todayCount != 0){ ?>
					<h2 style="font-size:.9em; "><a href="/w/오늘 생성된 문서">오늘 <?=$todayCount?>개의 문서가 생성되었군요!</a></h2>
					<?php } ?>
					<ul>
						<?php 
						$reRes = getRecentWiki();
						while($reData=$reRes->fetch()){ ?>
						<li><a href="/w/<?=$reData["title"]?>"><?=$reData["title"]?></a></li>
						<?php } ?>

					</ul>
				</div>
				<div class="banner">
					<h2><a href="/w/최근 수정 문서">최근 수정</a></h2>
					<ul>
						<?php 
						$reRes = getRecentEditedWiki();
						while($reData=$reRes->fetch()){ ?>
						<li><a href="/w/<?=$reData["title"]?>"><?=$reData["title"]?></a></li>
						<?php } ?>

					</ul>
				</div>
				<div class="banner">
					<h2>최근 댓글</h2>
					<ul>
						<?php 
						$reRes = getRecentComments();
						while($reData=$reRes->fetch()){ ?>
						<li><a href="/w/<?=$reData["title"]?>#comment_<?=$reData["idx"]?>"><?=$reData["content"]?></a></li>
						<?php } ?>

					</ul>
				</div>
				<!--
				<div class="banner">
					<h2>인기 문서</h2>
					<ul>
						<?php 
						$reRes = getHotWiki();
						while($reData=$reRes->fetch()){ ?>
						<li><a href="/w/<?=$reData["title"]?>"><?=$reData["title"]?></a></li>
						<?php } ?>

					</ul>
				</div>
				-->
				<div class="banner">
					<h2>문서 생성 TOP5</h2>
					<ul>
						<?php 
						$reRes = getHotCreaterUser(5);
						while($reData=$reRes->fetch()){ ?>
						<li><?=$reData["ip"]?> (<?=$reData["cnt"]?>회)</li>
						<?php } ?>

					</ul>
				</div>
				<div class="banner">
					<h2>문서 수정 TOP5</h2>
					<ul>
						<?php 
						$reRes = getHotEditerUser(5);
						while($reData=$reRes->fetch()){ ?>
						<li><?=$reData["ip"]?> (<?=$reData["cnt"]?>회)</li>
						<?php } ?>

					</ul>
				</div>
				<div class="banner">
					<h2>댓글 작성 TOP5</h2>
					<ul>
						<?php 
						$reRes = getHotCommentCreaterUser(5);
						while($reData=$reRes->fetch()){ ?>
						<li><?=$reData["ip"]?> (<?=$reData["cnt"]?>회)</li>
						<?php } ?>

					</ul>
				</div>
				<div class="banner mobile">
					<h2><a href="#main">맨위로</a></h2>
				</div>
			</div>
		</div>
	</div>
	<footer>
		Copyright (C) <a href="http://digitech.wiki">digitech.wiki</a> All Rights Reserved.
	</footer>
</div>
</body>
</html>