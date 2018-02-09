<?php
		// Start a session if it is not all ready started
		if (!isset($_SESSION)) session_start();

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
		
		$activeAccount = '';
		$nowActive = '';
		$msgBox = '';
		
		if((isset($_GET['userEmail']) && !empty($_GET['userEmail'])) && (isset($_GET['hash']) && !empty($_GET['hash']))) {
			// Set some variables
			$theEmail = $mysqli->real_escape_string($_GET['userEmail']);
			$hash = $mysqli->real_escape_string($_GET['hash']);

			// Check to see if there is an account that matches the link
			$check1 = $mysqli->query("SELECT
										userEmail,
										hash,
										isActive
									FROM
										users
									WHERE
										userEmail = '".$theEmail."' AND
										hash = '".$hash."' AND
										isActive = 0
			");
			$match = mysqli_num_rows($check1);
			
			// Check if account has all ready been activated
			$check2 = $mysqli->query("SELECT 'X' FROM users WHERE userEmail = '".$theEmail."' AND hash = '".$hash."' AND isActive = 1");
			if ($check2->num_rows) {
				$activeAccount = 'true';
			}

			// Match found, update the User's account to active
			if ($match > 0) {
				$isActive = '1';

				$stmt = $mysqli->prepare("
									UPDATE
										users
									SET
										isActive = ?
									WHERE
										userEmail = ?");
				$stmt->bind_param('ss',
								   $isActive,
								   $theEmail);
				$stmt->execute();
				$nowActive = 'true';
				$stmt->close();
			}
		}
		
		include('includes/header.php');
?>
		<section id="main-container">
			<div class="container">
				<h3><?php echo $activatePageHeader; ?></h3>
				<?php if ($msgBox) { echo $msgBox; } ?>
				
				<?php
					// The account has been activated
					if ($nowActive != '') {
				?>
						<p class="lead"><?php echo $activateText1; ?></p>
						<div class="alertMsg success">
							<i class="fa fa-check-square-o"></i> <?php echo $activateText2; ?>
						</div>
				<?php
					// An account match was found and has all ready been activated
					} else if ($activeAccount != '') {
				?>
						<p class="lead"><?php echo $activateText3; ?></p>
						<div class="alertMsg success">
							<i class="fa fa-check-square-o"></i> <?php echo $activateText4; ?>
						</div>
				<?php
					// An account match was not found/or the
					// user tried to directly access this page
					} else {
				?>
						<p class="lead"><?php echo $activateText5; ?></p>
						<div class="alertMsg danger">
							<i class="fa fa-minus-circle"></i> <?php echo $activateText6; ?>
						</div>
				<?php
					}
				?>
			</div>
		</section>
<?php
	include('includes/footer.php');
	
?>