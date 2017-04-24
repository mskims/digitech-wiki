$(function(){	
	$(".search_key").on("keyup", function(e){
		if(e.keyCode == 13){
			var search_key = ($(this).val()+"").replace(/\./gi, "");;
			location.href= '/w/' + search_key;
		}
	});


	// 외부링크 클릭
	$(".w-link-out").click(function(){
		if($(this).attr("href").indexOf("http") != 0 && $(this).attr("href").indexOf("data") != 0){
			$(this).attr("href", "http://"+$(this).attr("href"));
		}
	});

	// 추천
	$(".recommend-action").click(function(){
		var $ele = $(this);
		title = $(this).attr("data-title");	
		$.post("/ajax/recommend.php", {"title": title}, function(data){
		console.log(data);
		if(data[0]=="#"){
			alert("이미 추천하셨습니다");
		}else{
			$ele.text("추천 "+data);
		}
	});
	});


	// 댓글
	$("#commentmaineditor").submit(function(e){
		e.preventDefault();
		if($(".caction button").hasClass("disabled")){
			alert("처리중입니다. 기다려보세요");
			return false;
		}
		var ccontent = $("#comment-editor").val();
		var wikiidx= $(this).data("articleid");
		$(".caction button").addClass("disabled");
		$.ajax({
			url: "/ajax/comment.php",
			type: "POST",
			data:  {
				type: "new", // new, reply
				wiki: wikiidx,
				content: ccontent
			},
			async: false,
			success: function(data){
				if(data[1] == "E"){
					msg = "오류 : ";
					switch(data[2]){
						case "1":
							msg = "내용을 입력해주세요";
							break;
						case "2":
							msg = "댓글 작성은 30초에 한개로 제한됩니다.";
							break;
					}
					alert(msg);
					$(".caction button").removeClass("disabled");
					return false;
				}
				loadComment(wikiidx);
			}
		});
	});


	// Load Comment
	function loadComment(wikiidx){
		console.log(wikiidx);
		$.post("/ajax/load_comment.php", {
			wiki: wikiidx
		}, function(data){
			$(".comment").remove();
			data = JSON.parse(data);
			console.log(data);

			var html = "";
			for(i in data){
				var comment = data[i];
				console.log(comment);
				html += "<div class='comment'>";
				for(j in comment){
					var ele = comment[j];
					switch(j){
						case "owner":
							html += "<div class='"+j+"'>"+comment[j]+"</div>";
							break;
						case "regdate":
							html += "<div class='coption'><button>수정</button> <button>삭제</button> <button>답글</button></div>";
							html += "<div class='"+j+"'>"+comment[j]+"</div>";
							break;
						case "ccontent":
							html += "<div class='clear'></div>";
							html += "<div class='"+j+"'>"+comment[j]+"</div>";
							break;
					}
				}
				html += "</div>";
			}
			$(".wcomment").prepend(html);
			$("#comment-editor").val("");
			$(".caction button").removeClass("disabled");
		});
	}


	// 댓글 액션
	$(document).on("click", ".coption button", function(){
		alert("준비중입니다");
		return false;
	});
});