<?php
$raData = sqlf("select title from wiki order by RAND() limit 1");
move("/w/{$raData['title']}");
?>