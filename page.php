<?php
		if(!isset($_SESSION)) session_start();
		$msgBox = '';

		// Access DB Info
		include('config.php');

		// Get Settings Data
		include ('includes/settings.php');
		$set = mysqli_fetch_assoc($setRes);

		// Set Localization
		$local = $set['localization'];
		switch ($local) {
			case 'en':		include ('language/en.php');		break;
			case 'en-ca':	include ('language/en-ca.php');		break;
			case 'en-gb':	include ('language/en-gb.php');		break;
			case 'ro':		include ('language/ro.php');		break;
		}

		// Include Functions
		include('includes/functions.php');
		
		// Link to the Page
		if (isset($_GET['page']) && $_GET['page'] == $pageNavLinkAbout) {
			$page = $pageNavLinkAbout;
		} else if (isset($_GET['page']) && $_GET['page'] == $pageNavLinkRules) {
			$page = $pageNavLinkRules;
		} else if (isset($_GET['page']) && $_GET['page'] == 'view') {
			$page = 'view';
		} else if (isset($_GET['page']) && $_GET['page'] == 'confessions') {
			$page = 'confessions';
		} else if (isset($_GET['page']) && $_GET['page'] == 'viewConfession') {
			$page = 'viewConfession';
			$addCss = '<link rel="stylesheet" type="text/css" href="css/datetimepicker.css" />';
		} else if (isset($_GET['page']) && $_GET['page'] == 'comments') {
			$page = 'comments';
		} else if (isset($_GET['page']) && $_GET['page'] == 'subscriptions') {
			$page = 'subscriptions';
		} else if (isset($_GET['page']) && $_GET['page'] == 'export') {
			$page = 'export';
		} else if (isset($_GET['page']) && $_GET['page'] == 'users') {
			$page = 'users';
		} else if (isset($_GET['page']) && $_GET['page'] == 'viewUser') {
			$page = 'viewUser';
		} else if (isset($_GET['page']) && $_GET['page'] == 'advertising') {
			$page = 'advertising';
			$addCss = '<link rel="stylesheet" type="text/css" href="css/datetimepicker.css" />';
		} else if (isset($_GET['page']) && $_GET['page'] == 'viewAd') {
			$page = 'viewAd';
			$addCss = '<link rel="stylesheet" type="text/css" href="css/datetimepicker.css" />';
		} else if (isset($_GET['page']) && $_GET['page'] == 'siteSettings') {
			$page = 'siteSettings';
		} else {
			$page = 'myProfile';
		}
		
		if (file_exists('pages/'.$page.'.php')) {
			// Load the Page
			include('pages/'.$page.'.php');
		} else {
			// Else Display an Error
			echo '
					<section id="main-container">
						<div class="container">
							<h2>'.$pageNotFoundHeader.'</h2>
							<div class="alertMsg warning">
								<i class="fa fa-warning"></i> '.$pageNotFoundQuip.'
							</div>
						</div>
					</div>
				';
		}

		include('includes/footer.php');
	
?>