<?php
	$msgBox = '';
	
	// Contact Us
	if (isset($_POST['submit']) && $_POST['submit'] == 'cantactUs') {
		// Validation
		if($_POST['contactSub'] == '') {
			$msgBox = alertBox($contactSubReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['contactName'] == '') {
			$msgBox = alertBox($contactNameReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['contactEmail'] == '') {
			$msgBox = alertBox($contactEmailReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['contactCom'] == '') {
			$msgBox = alertBox($contactCommentsReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['contactHole'] != '') {
			$msgBox = alertBox($contactErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
			$_POST['contactSub'] = $_POST['contactName'] = $_POST['contactEmail'] = $_POST['contactPhone'] = $_POST['contactCom'] = $_POST['contactAns'] = '';
		} else {
			if(strtolower($_POST['contactAns']) == $_SESSION['thecode']) {
				$contactSub = $mysqli->real_escape_string($_POST['contactSub']);
				$contactName = $mysqli->real_escape_string($_POST['contactName']);
				$contactEmail = $mysqli->real_escape_string($_POST['contactEmail']);
				$contactPhone = $mysqli->real_escape_string($_POST['contactPhone']);
				$contactCom = htmlentities($_POST['contactCom']);

				// Send out the email in HTML
				$installUrl = $set['installUrl'];
				$siteName = $set['siteName'];
				$siteEmail = $set['siteEmail'];

				$subject = $contactSub;
				$message = '<html><body>';
				$message .= '<h3>'.$subject.'</h3>';
				$message .= '<hr>';
				$message .= '<p>'.$contactUsEmail1.' '.$contactName.'<br />'.$contactUsEmail2.' '.$contactEmail.'<br />'.$contactUsEmail3.' '.$contactPhone.'</p>';
				$message .= '<p>'.nl2br($contactSub).'</p>';
				$message .= '<hr>';
				$message .= '<p>'.$subscribeEmail3.'<br>'.$siteName.'</p>';
				$message .= '</body></html>';
				$headers = "From: ".$contactName." <".$contactEmail.">\r\n";
				$headers .= "Reply-To: ".$contactEmail."\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

				if (mail($siteEmail, $subject, $message, $headers)) {
					$msgBox = alertBox($contactUsMsgSent, "<i class='fa fa-check-square'></i>", "success");
					// Clear the Form of values
					$_POST['contactSub'] = $_POST['contactName'] = $_POST['contactEmail'] = $_POST['contactPhone'] = $_POST['contactCom'] = $_POST['contactAns'] = '';
				}
			} else {
				$msgBox = alertBox($captchaErrorMsg, "<i class='fa fa-warning'></i>", "warning");
			}
		}
	}

	$confsql = "SELECT 'X' FROM confessions WHERE isActive = 1";
	$conftotal = mysqli_query($mysqli, $confsql) or die('-1'.mysqli_error());
	$totConf = mysqli_num_rows($conftotal);
	
	$comssql = "SELECT 'X' FROM comments WHERE isActive = 1";
	$commentstotal = mysqli_query($mysqli, $comssql) or die('-1'.mysqli_error());
	$totComments = mysqli_num_rows($commentstotal);
	
	$likessql = "SELECT 'X' FROM likes";
	$likestotal = mysqli_query($mysqli, $likessql) or die('-1'.mysqli_error());
	$totLikes = mysqli_num_rows($likestotal);
	
	$dislikessql = "SELECT 'X' FROM dislikes";
	$dislikestotal = mysqli_query($mysqli, $dislikessql) or die('-1'.mysqli_error());
	$totDislikes = mysqli_num_rows($dislikestotal);
	
	$viewssql = "SELECT 'X' FROM views";
	$viewstotal = mysqli_query($mysqli, $viewssql) or die('-1'.mysqli_error());
	$totViews = mysqli_num_rows($viewstotal);

	include('includes/header.php');
?>
	<section id="main-container">
		<div class="container">
			<?php if ($msgBox) { echo $msgBox; } ?>
			
			<div class="row">
				<div class="col-md-9">
					<?php echo htmlspecialchars_decode($set['aboutUs']); ?>
					
					<form action="" method="post">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="contactSub"><?php echo $subjectField; ?></label>
									<input type="text" class="form-control" name="contactSub" required="" value="<?php echo isset($_POST['contactSub']) ? $_POST['contactSub'] : ''; ?>" />
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="contactName"><?php echo $yourNameField; ?></label>
									<input type="text" class="form-control" name="contactName" required="" value="<?php echo isset($_POST['contactName']) ? $_POST['contactName'] : ''; ?>" />
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="contactEmail"><?php echo $emailAddyField; ?></label>
									<input type="email" class="form-control" name="contactEmail" required="" value="<?php echo isset($_POST['contactEmail']) ? $_POST['contactEmail'] : ''; ?>" />
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="contactPhone"><?php echo $phoneField; ?></label>
									<input type="text" class="form-control" name="contactPhone" value="<?php echo isset($_POST['contactPhone']) ? $_POST['contactPhone'] : ''; ?>" />
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="contactCom"><?php echo $commentsField; ?></label>
							<textarea class="form-control" name="contactCom" required="" rows="6"><?php echo isset($_POST['contactCom']) ? $_POST['contactCom'] : ''; ?></textarea>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="row">
									<div class="col-md-4">
										<img src="includes/captcha.php" id="captcha" data-toggle="tooltip" data-placement="left" title="<?php echo $captchaCodeTooltip; ?>" />
									</div>
									<div class="col-md-8">
										<div class="form-group">
											<input type="text" class="form-control" name="contactAns" required="" maxlength="6" placeholder="<?php echo $captchaCodeTooltip; ?>">
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<input type="hidden" name="contactHole" id="contactHole" />
								<button type="input" name="submit" value="cantactUs" class="btn btn-success btn-lg pull-right btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $sendContactBtn; ?></button>
							</div>
						</div>
					</form>
				</div>
				<div class="col-md-3">
					<ul class="list-group">
						<li class="list-group-item">Medicamente introduse<span class="badge"><?php echo $totConf; ?></span></li>
						<li class="list-group-item"><?php echo $totalCommentsText; ?> <span class="badge"><?php echo $totComments; ?></span></li>
						<li class="list-group-item"><?php echo $totalLikesText; ?> <span class="badge"><?php echo $totLikes; ?></span></li>
					</ul>
					
					<?php
						$ad3 = "SELECT
									adImage, adTitle, adUrl,
									adStartDate, adEndDate, isActive
								FROM
									ads
								WHERE
									(isActive = 1 OR
									adStartDate <= DATE_SUB(CURDATE(),INTERVAL 0 DAY) AND
									adEndDate >= DATE_SUB(CURDATE(),INTERVAL 0 DAY)) AND
									adType = 2
								ORDER BY RAND()
								LIMIT 1";
						$adres3 = mysqli_query($mysqli, $ad3) or die('-87' . mysqli_error());

						if(mysqli_num_rows($adres3) > 0) {
							while ($ad3 = mysqli_fetch_assoc($adres3)) {
								echo '
										<a href="'.clean($ad3['adUrl']).'" data-toggle="tooltip" data-placement="bottom" title="'.$advertisementText.'">
											<img alt="'.clean($ad3['adTitle']).'" src="'.$adsPath.clean($ad3['adImage']).'" class="img-responsive mt-20" />
										</a>
									';
							}
						}
					?>
				</div>
			</div>
			
		</div>
	</div>