<html>
<head>
<title><?=$subject;?></title>
<style>
	.mess{
	padding-bottom:20px;
	border-bottom:1px solid #ddd;
	}
	tr,td,th{
	text-align:left;padding:8px;border: 1px solid #cccccc;
	}
	table{
	width:99%;
	}
	td.ltd{
	width:15%;
	}
	td.rtd{
	width:85%;
	}
</style>
</head>
<body>
	<table class="b1 cpa">
		<tbody>
			<tr>
				<td style="width:366px;">
					<p align="center"><img alt="logo.png" src="logo.png" width="197" /></p>
				</td>
				<td style="width:366px;">
					contact info
				</td>
			</tr>
		</tbody>
	</table>
	<div class="mess"><?=$message;?></div>
</body>
</html>