<?php defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>404 Page Not Found</title>
<style type="text/css">

::selection { background-color: #E13300; color: white; }
::-moz-selection { background-color: #E13300; color: white; }

body {

	margin: 0;
	font: 13px/20px normal Helvetica, Arial, sans-serif;
	color: #4F5155;
}

a {
	color: #003399;
	background-color: transparent;
	font-weight: normal;
}

h1 {
	color: #444;
	background-color: transparent;
	border-bottom: 1px solid #D0D0D0;
	font-size: 19px;
	font-weight: normal;
	margin: 0 0 14px 0;
	padding: 14px 15px 10px 15px;
}

code {
	font-family: Consolas, Monaco, Courier New, Courier, monospace;
	font-size: 12px;
	background-color: #f9f9f9;
	border: 1px solid #D0D0D0;
	color: #002166;
	display: block;
	margin: 14px 0 14px 0;
	padding: 12px 10px 12px 10px;
}



p {
	margin: 12px 15px 12px 15px;
}

body, html, .error_page{height:100%;}
.error_page {background: #f8f8f8;}
.error_div {position: absolute;width: 500px;background: #fff;left: 50%;top: 50%;transform: translate(-50%,-50%);border-top: 5px solid #01b9e6;padding: 30px 20px 40px 20px;-webkit-box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);-moz-box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);}
.error_div h1 {font-size: 24px;color: #666;padding: 0;margin: 30px 0;text-align: center;display: inline-block;width: 100%;clear: both;border: none;}
.error_div h1 span {display: block;margin: 0 0 40px 0;font-size: 72px;color: #333;}
.error_div p {font-size: 16px;text-align: center;margin: 0;color: #888;letter-spacing: 0.5px;}

</style>
</head>
<body>

	<div class="error_page">
		<div id="container">
			<div  class="error_div">
			<!--<h1><?php //echo $heading; ?></h1>  -->
			<h1><span>404</span>Page Not Found</h1>
			<p><?php echo $message; ?></p>
			</dov>
		</div>
	</div>
</body>
</html>