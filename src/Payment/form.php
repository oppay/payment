<!doctype html>
<html>
<head>
<title></title>
<meta charset="UTF-8">
<style type="text/css">
body{
}

form{
	padding: 0;
	margin: 0;
	height: 0;
}

.spinner{
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	margin: auto;
}

.spinner{
	width: 40px;
	height: 40px;
}

.spinner .text{
	position: absolute;
	top: 120px;
	left: 50%;
	margin-left: -125px;
	width: 250px;
	text-align: center;
}

.spinner .text button{
	padding: 9px 25px 5px 25px;
	margin: 0;
	border: 0;
	border-bottom: 2px solid #097438;
	color: #FFF;
	direction: rtl;
	background-color: #2ECD71;
	text-shadow: 0 1px 0 rgba(0,0,0,.3);
	font: bold 16px/1.3em arial;
	border-radius: 4px;
	cursor: pointer;
}

.cube1, .cube2{
	background-color: #1ABC9C;
	width: 10px;
	height: 10px;
	position: absolute;
	top: 0;
	left: 0;
	
	-webkit-animation: cubemove 1.8s infinite ease-in-out;
	animation: cubemove 1.8s infinite ease-in-out;
}

.cube2{
	-webkit-animation-delay: -0.9s;
	animation-delay: -0.9s;
}

@-webkit-keyframes cubemove{
	25% { -webkit-transform: translateX(30px) rotate(-90deg) scale(0.5) }
	50% { -webkit-transform: translateX(30px) translateY(30px) rotate(-180deg) }
	75% { -webkit-transform: translateX(0px) translateY(30px) rotate(-270deg) scale(0.5) }
	100% { -webkit-transform: rotate(-360deg) }
}

@keyframes cubemove{
	25% { 
		transform: translateX(30px) rotate(-90deg) scale(0.5);
		-webkit-transform: translateX(30px) rotate(-90deg) scale(0.5);
	} 50% { 
		transform: translateX(30px) translateY(30px) rotate(-179deg);
		-webkit-transform: translateX(30px) translateY(30px) rotate(-179deg);
	} 50.1% { 
		transform: translateX(30px) translateY(30px) rotate(-180deg);
		-webkit-transform: translateX(30px) translateY(30px) rotate(-180deg);
	} 75% { 
		transform: translateX(0px) translateY(30px) rotate(-270deg) scale(0.5);
		-webkit-transform: translateX(0px) translateY(30px) rotate(-270deg) scale(0.5);
	} 100% { 
		transform: rotate(-360deg);
		-webkit-transform: rotate(-360deg);
	}
}

</style>
</head>
<body>

<form action="<?php echo $url ?>" method="<?php echo $method ?>">

	<div class="spinner">
		<div class="cube1"></div>
		<div class="cube2"></div>
		<div class="text">
			<noscript>
				<button type="submit">اتصال به درگاه پرداخت</button>
			</noscript>
		</div>
	</div>

	<?php echo $elements ?>
</form>

<script type="text/javascript">
	setTimeout(function()
	{
		document.getElementsByTagName('form')[0].submit();
	}, 1000);
</script>

</body>
</html>
