<div class="w-content-wrap">
<div class="wtitle" style="float: none; ">연결되지 않은 문서</div>

<div class="wcontent">
<?php
	$res = sql("select * from wiki order by title asc");
	while($data=$res->fetch()){ 
		$cnt = sqlf("select count(*) as cnt from wiki where content like '%[[{$data['title']}]]%'");
		if($cnt["cnt"] == 0){
			echo "<a class='w-link' href='/w/{$data['title']}'>{$data['title']}</a><br/>";
		}
	}
?>
</div>
</div>