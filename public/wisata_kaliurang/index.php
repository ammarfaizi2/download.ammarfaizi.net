<?php

$curpath = "/wisata_kaliurang";

define("INIT", true);

require __DIR__."/helpers.php";

?><!DOCTYPE html>
<html>
<head>
	<title>Wisata Kaliurang</title>
	<link rel="stylesheet" type="text/css" href="<?php print $curpath."/style.css"; ?>"/>
</head>
<body>
	<center>
		<div id="main_cage"></div>
		<h1 id="loading">Loading...</h1>
		<a style="margin-bottom: 50px;" id="load_more" href="javascript:void(0);">Load More</a>
	</center>
	<script type="text/javascript" src="<?php print $curpath."/loader.js"; ?>"></script>
</body>
</html>
