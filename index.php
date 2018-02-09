<?php
		if(!isset($_SESSION)) session_start();

		// Logout
		if (isset($_GET['action'])) {
			$action = $_GET['action'];
			if ($action == 'logout') {
				session_destroy();
				header('Location: index.php');
			}
		}

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

		$msgBox = '';

		include('includes/header.php');

		// Get the Page URL
		$pageURL = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

		$homePage = 'true';

		// Get Ad Data
		$ads  = "SELECT
					adId, adType, adImage,
					adTitle, adText, adUrl,
					adStartDate, adEndDate, isActive
				FROM
					ads
				WHERE
					(isActive = 1 OR
					adStartDate <= DATE_SUB(CURDATE(),INTERVAL 0 DAY) AND
					adEndDate >= DATE_SUB(CURDATE(),INTERVAL 0 DAY)) AND
					adType = 1
				ORDER BY RAND()
				LIMIT 1";
		$adres = mysqli_query($mysqli, $ads) or die('-8' . mysqli_error());
?>
		<section id="main-container">
			<div class="container">
				<?php if ($msgBox) { echo $msgBox; } ?>

				<?php
					if(mysqli_num_rows($adres) > 0) {
						while ($ad = mysqli_fetch_assoc($adres)) {
				?>
							<div class="adText">
								<h3><a href="<?php echo clean($ad['adUrl']); ?>"><?php echo clean($ad['adTitle']); ?> <i class="fa fa-external-link pull-right"></i></a></h3>
								<p><a href="<?php echo clean($ad['adUrl']); ?>"><?php echo nl2br(clean($ad['adText'])); ?></a></p>
								<span class="label label-default"><?php echo $advertisementText; ?></span>
								<div class="clearfix"></div>
							</div>
				<?php
						}
					}
				?>

			</div>
		</div>
<?php
		include('includes/footer.php');
	
?>