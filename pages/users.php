<?php
	$jsFile = 'users';
	$msgBox = '';
	
	// Add New User Account
	if (isset($_POST['submit']) && $_POST['submit'] == 'newUser') {
        // Validation
        if($_POST['userEmail'] == "") {
            $msgBox = alertBox($userEmailReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['userFirst'] == "") {
            $msgBox = alertBox($userFirstNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['userLast'] == "") {
            $msgBox = alertBox($userLastNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['password1'] == "") {
            $msgBox = alertBox($newAccPassReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password1'] != $_POST['password2']) {
			$msgBox = alertBox($passNotMatchMsg, "<i class='fa fa-warning'></i>", "warning");
        } else {
			// Set some variables
			$userEmail = $mysqli->real_escape_string($_POST['userEmail']);
			$userFirst = $mysqli->real_escape_string($_POST['userFirst']);
			$userLast = $mysqli->real_escape_string($_POST['userLast']);
			$setActive = $mysqli->real_escape_string($_POST['setActive']);
			$password = encryptIt($_POST['password1']);
			$hash = md5(rand(0,1000));
			$joinDate = date("Y-m-d H:i:s");
			$signupIp = $_SERVER['REMOTE_ADDR'];
			$dupEmail = '';

			// Check for Duplicate email
			$check = $mysqli->query("SELECT 'X' FROM users WHERE userEmail = '".$userEmail."'");
			if ($check->num_rows) {
				$dupEmail = 'true';
			}

			// If duplicates are found
			if ($dupEmail != '') {
				$msgBox = alertBox($dupAccRegMsg, "<i class='fa fa-warning'></i>", "warning");
				$_POST['userEmail'] = $_POST['userFirst'] = $_POST['userLast'] = '';
			} else {
				if ($setActive == '1') {
					// Create the new account
					$stmt = $mysqli->prepare("
										INSERT INTO
											users(
												userEmail,
												password,
												userFirst,
												userLast,
												joinDate,
												isActive,
												hash
											) VALUES (
												?,
												?,
												?,
												?,
												?,
												?,
												?
											)");
					$stmt->bind_param('sssssss',
						$userEmail,
						$password,
						$userFirst,
						$userLast,
						$joinDate,
						$setActive,
						$hash
					);
					$stmt->execute();
					$msgBox = alertBox($newUserAccCreatedMsg1, "<i class='fa fa-check-square'></i>", "success");
					// Clear the form of Values
					$_POST['userEmail'] = $_POST['userFirst'] = $_POST['userLast'] = '';
					$stmt->close();
				} else {
					// Create the new account and send an email
					$stmt = $mysqli->prepare("
										INSERT INTO
											users(
												userEmail,
												password,
												userFirst,
												userLast,
												joinDate,
												isActive,
												hash
											) VALUES (
												?,
												?,
												?,
												?,
												?,
												?,
												?
											)");
					$stmt->bind_param('sssssss',
						$userEmail,
						$password,
						$userFirst,
						$userLast,
						$joinDate,
						$setActive,
						$hash
					);
					$stmt->execute();
					
					// Send out the email in HTML
					$installUrl = $set['installUrl'];
					$siteName = $set['siteName'];
					$siteEmail = $set['siteEmail'];
					$newPass = $mysqli->real_escape_string($_POST['password1']);

					$subject = $newAccCreatedSubject;
					$message = '<html><body>';
					$message .= '<h3>'.$subject.'</h3>';
					$message .= '<p>'.$newAccCreated1.'</p>';
					$message .= '<hr>';
					$message .= '<p>'.$newAccCreated2.' '.$newPass.'</p>';
					$message .= '<p>'.$newAccCreated3.$installUrl.'activate.php?userEmail='.$userEmail.'&hash='.$hash.'</p>';
					$message .= '<hr>';
					$message .= '<p>'.$newAccCreated4.'</p>';
					$message .= '<p>'.$newAccCreated5.'</p>';
					$message .= '<p>'.$subscribeEmail3.'<br>'.$siteName.'</p>';
					$message .= '</body></html>';
					$headers = "From: ".$siteName." <".$siteEmail.">\r\n";
					$headers .= "Reply-To: ".$siteEmail."\r\n";
					$headers .= "MIME-Version: 1.0\r\n";
					$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

					if (mail($userEmail, $subject, $message, $headers)) {
						$msgBox = alertBox($newUserAccCreatedMsg2, "<i class='fa fa-check-square'></i>", "success");
						// Clear the form of Values
						$_POST['userEmail'] = $_POST['userFirst'] = $_POST['userLast'] = '';
					}
					$stmt->close();
				}
			}
		}
	}
	
	// Activate
	if (isset($_POST['submit']) && $_POST['submit'] == 'activateAcc') {
		$userId = $mysqli->real_escape_string($_POST['userId']);
		$isActive = '1';
		$stmt = $mysqli->prepare("UPDATE users SET isActive = ? WHERE userId = ?");
		$stmt->bind_param('ss', $isActive, $userId);
		$stmt->execute();
		$msgBox = alertBox($userAccActiveMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
	}
	
	// Deactivate
	if (isset($_POST['submit']) && $_POST['submit'] == 'deactivateAcc') {
		$userId = $mysqli->real_escape_string($_POST['userId']);
		$isActive = '0';
		$stmt = $mysqli->prepare("UPDATE users SET isActive = ? WHERE userId = ?");
		$stmt->bind_param('ss', $isActive, $userId);
		$stmt->execute();
		$msgBox = alertBox($userAccInactiveMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
	}
	
	// Delete
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteAcc') {
		$deleteId = $mysqli->real_escape_string($_POST['deleteId']);
		$stmt = $mysqli->prepare("DELETE FROM users WHERE userId = ?");
		$stmt->bind_param('s', $deleteId);
		$stmt->execute();
		$msgBox = alertBox($userAccDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
	}
	
	$select = "SELECT
				userId,
				isAdmin,
				userEmail,
				userFirst,
				userLast,
				DATE_FORMAT(joinDate,'%b %d, %Y at %h:%i %p') AS joinDate,
				isActive,
				DATE_FORMAT(lastVisited,'%b %d, %Y at %h:%i %p') AS lastVisited
			FROM
				users";
	$res = mysqli_query($mysqli, $select) or die('-1' . mysqli_error());

	include('includes/header.php');

	if ($admin != '1') {
?>
	<section id="main-container">
		<div class="container">
			<h3><?php echo $accessErrorHeader; ?></h3>
			<div class="alertMsg danger no-margin">
				<i class="fa fa-warning"></i> <?php echo $permissionDenied; ?>
			</div>
		</div>
	</div>
<?php } else { ?>
	<section id="main-container">
		<div class="container">	
			<div class="row">
				<div class="col-md-6">
					<h3><?php echo $usersPageHead; ?></h3>
				</div>
				<div class="col-md-6">
					<a data-toggle="modal" href="#newUser" class="btn btn-success btn-icon pull-right mt-20"><i class="fa fa-user"></i> <?php echo $newUserLink; ?></a>
				</div>
			</div>
			<?php if ($msgBox) { echo $msgBox; } ?>
			
			<div class="modal fade" id="newUser" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
							<h4 class="modal-title"><?php echo $newUserLink; ?></h4>
						</div>
						<form action="" method="post">
							<div class="modal-body">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="setActive"><?php echo $setUserAccActiveField; ?></label>
											<select class="form-control" name="setActive">
												<option value="0" selected><?php echo $noBtn; ?></option>
												<option value="1"><?php echo $yesBtn; ?></option>
											</select>
											<span class="help-block"><?php echo $setUserAccActiveFieldHelp; ?></span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="userEmail"><?php echo $userEmailAddyField; ?></label>
											<input type="text" class="form-control" required="" name="userEmail" value="" />
											<span class="help-block"><?php echo $userEmailAddyFieldHelp; ?></span>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="userFirst"><?php echo $usersFirstNameField; ?></label>
											<input class="form-control" required="" name="userFirst" value="<?php echo isset($_POST['userFirst']) ? $_POST['userFirst'] : ''; ?>" type="text">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="userLast"><?php echo $usersLastNameField; ?></label>
											<input class="form-control" required="" name="userLast" value="<?php echo isset($_POST['userLast']) ? $_POST['userLast'] : ''; ?>" type="text">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="password1"><?php echo $passwordField; ?></label>
											<div class="input-group">
												<input type="password" class="form-control" required="" name="password1" id="password1" value="" />
												<span class="input-group-addon"><a href="" id="generate" data-toggle="tooltip" data-placement="top" title="<?php echo $genPasswordTooltip; ?>"><i class="fa fa-key"></i></a></span>
											</div>
											<span class="help-block">
												<a href="" id="showIt" class="btn btn-warning btn-xs"><?php echo $showPlainText; ?></a>
												<a href="" id="hideIt" class="btn btn-info btn-xs"><?php echo $hidePlainText; ?></a>
											</span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="password2"><?php echo $repeatPasswordField; ?></label>
											<input type="password" class="form-control" required="" name="password2" id="password2" value="" />
											<span class="help-block"><?php echo $repeatPasswordField; ?></span>
										</div>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="input" name="submit" value="newUser" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
								<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
							</div>
						</form>
					</div>
				</div>
			</div>
			
			<table id="userData" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th><?php echo $usersNameTab; ?></th>
						<th class="text-center"><?php echo $emailAddressField; ?></th>
						<th class="text-center"><?php echo $accTypeTab; ?></th>
						<th class="text-center"><?php echo $activeText; ?></th>
						<th class="text-center"><?php echo $joinDateTab; ?></th>
						<th class="text-center"><?php echo $lastVisitedTab; ?></th>
						<th><?php echo $actionsText; ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
						while ($row = mysqli_fetch_assoc($res)) {
							if ($row['isAdmin'] == '1') { $type = '<strong class="text-success">'.$administratorText.'</strong>'; } else { $type = '<strong class="text-info">'.$userText.'</strong>'; }
							if ($row['isActive'] == '1') {
								$activeUser = '<span class="text-success">'.$yesBtn.'</span>';
								$action = '
											<form action="" method="post" class="tableForm">
												<input type="hidden" name="userId" value="'.$row['userId'].'" />
												<span data-toggle="tooltip" data-placement="left" title="'.$deactivateAccTooltip.'">
													<button type="input" name="submit" value="deactivateAcc" class="label label-warning"><i class="fa fa-ban"></i></button>
												</span>
											</form>
										  ';
							} else {
								$activeUser = '<strong class="text-danger">'.$noBtn.'</strong>';
								$action = '
											<form action="" method="post" class="tableForm">
												<input type="hidden" name="userId" value="'.$row['userId'].'" />
												<span data-toggle="tooltip" data-placement="left" title="'.$activateAccTooltip.'">
													<button type="input" name="submit" value="activateAcc" class="label label-success"><i class="fa fa-check"></i></button>
												</span>
											</form>
										  ';
							}
					?>
							<tr>
								<td>
									<a href="page.php?page=viewUser&userId=<?php echo $row['userId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewUserTooltip; ?>">
										<?php echo clean($row['userFirst']).' '.clean($row['userLast']); ?>
									</a>
								</td>
								<td class="text-center"><?php echo clean($row['userEmail']); ?></td>
								<td class="text-center"><?php echo $type; ?></td>
								<td class="text-center"><?php echo $activeUser; ?></td>
								<td class="text-center"><?php echo $row['joinDate']; ?></td>
								<td class="text-center"><?php echo $row['lastVisited']; ?></td>
								<td>
									<?php echo $action; ?>
									<span data-toggle="tooltip" data-placement="left" title="<?php echo $viewUserTooltip; ?>">
										<a href="page.php?page=viewUser&userId=<?php echo $row['userId']; ?>" class="label label-info"><i class="fa fa-edit"></i></a>
									</span>
									<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteAccTootip; ?>">
										<a data-toggle="modal" href="#delete<?php echo $row['userId']; ?>" class="label label-danger"><i class="fa fa-times"></i></a>
									</span>
								</td>
							</tr>
							
							<div class="modal fade" id="delete<?php echo $row['userId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<form action="" method="post">
											<div class="modal-body">
												<p class="lead"><?php echo $deleteAccConf; ?></p>
											</div>
											<div class="modal-footer">
												<input name="deleteId" type="hidden" value="<?php echo $row['userId']; ?>" />
												<button type="input" name="submit" value="deleteAcc" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $deleteAccBtn; ?></button>
												<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
											</div>
										</form>
									</div>
								</div>
							</div>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</section>
<?php } ?>