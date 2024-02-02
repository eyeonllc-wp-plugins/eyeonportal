<?php
$api_call_url = MCP_API_LINKS.'?center='.$mcd_settings['center_id'];
$links_data = mcd_api_data($api_call_url);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, user-scalable=no, maximum-scale=1.0">
	<link rel="shortcut icon" href="https://mycenterportal.com/assets/img/favicon.ico" type="image/x-icon" >
	<title><?= $links_data['center']['name'] ?> - Links</title>

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Karla:wght@700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="<?= mcd_version_url('assets/css/links.min.css') ?>">

	<style type="text/css">
	#main .links ul li a {
		background-color: <?= $links_data['settings']['bg_color'] ?>;
		border-color: <?= $links_data['settings']['bg_color'] ?>;
		color: <?= $links_data['settings']['text_color'] ?>;
	}
	#main .links ul li a:hover {
    background-color: #FFF;
		color: <?= $links_data['settings']['bg_color'] ?>;
	}
	</style>
</head>
<body>

<div id="main">
	<div class="wrapper">
		<div class="center">
			<div class="logo">
				<img src="<?= $links_data['center']['media']['light']['url'] ?>" alt="<?= $links_data['center']['name'] ?>" />
			</div>
			<div class="name"><?= $links_data['center']['name'] ?></div>
		</div>
		<div class="links">
			<ul>
				<?php foreach( $links_data['items'] as $link ) : ?>
					<li>
						<a href="<?= $link['link'] ?>" target="_blank"><?= $link['title'] ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>

</body>
</html>