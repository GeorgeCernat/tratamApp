<?php
	$userId = $_GET['userId'];
	$jsFile = 'viewUser';
	$msgBox = '';
	
	// Update User
	if (isset($_POST['submit']) && $_POST['submit'] == 'updateUser') {
		// Validation
		if($_POST['userEmail'] == "") {
            $msgBox = alertBox($userEmailReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['userFirst'] == "") {
            $msgBox = alertBox($userFirstNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['userLast'] == "") {
            $msgBox = alertBox($userLastNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$userEmail = $mysqli->real_escape_string($_POST['userEmail']);
			$userFirst = $mysqli->real_escape_string($_POST['userFirst']);
			$userLast = $mysqli->real_escape_string($_POST['userLast']);

			$stmt = $mysqli->prepare("UPDATE
										users
									SET
										userEmail = ?,
										userFirst = ?,
										userLast = ?
									WHERE
										userId = ?"
			);
			$stmt->bind_param('ssss',
									$userEmail,
									$userFirst,
									$userLast,
									$userId
			);
			$stmt->execute();
			$msgBox = alertBox($userAccUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
    }
	
	// Update Account Password
	if (isset($_POST['submit']) && $_POST['submit'] == 'updatePassword') {
		// Validation
		if($_POST['password'] == '') {
			$msgBox = alertBox($newAccPassReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['passwordr'] == '') {
			$msgBox = alertBox($repeatNewPassReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password'] != $_POST['passwordr']) {
            $msgBox = alertBox($passNotMatchMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			if(isset($_POST['password']) && $_POST['password'] != "") {
				$password = encryptIt($_POST['password']);
			} else {
				$password = $_POST['passwordOld'];
			}

			$stmt = $mysqli->prepare("UPDATE
										users
									SET
										password = ?
									WHERE
										userId = ?"
			);
			$stmt->bind_param('ss', $password, $userId);
			$stmt->execute();
			$msgBox = alertBox($userPasswordChangedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
    }
	
	// Update User Account
	if (isset($_POST['submit']) && $_POST['submit'] == 'updateStatus') {
		$activeAcc = $mysqli->real_escape_string($_POST['isActive']);
		$accountType = $mysqli->real_escape_string($_POST['accountType']);

		$stmt = $mysqli->prepare("UPDATE
									users
								SET
									isAdmin = ?,
									isActive = ?
								WHERE
									userId = ?"
		);
		$stmt->bind_param('sss',
								$accountType,
								$activeAcc,
								$userId
		);
		$stmt->execute();
		$msgBox = alertBox($userAccStatusUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }
	
	// Email User
	if (isset($_POST['submit']) && $_POST['submit'] == 'sendEmail') {
		// Validation
		if($_POST['emailText'] == '') {
			$msgBox = alertBox($emailMsgReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else {
			$emailText = htmlentities($_POST['emailText']);
			$userEmail = $mysqli->real_escape_string($_POST['userEmail']);

			// Send out the email in HTML
			$installUrl = $set['installUrl'];
			$siteName = $set['siteName'];
			$siteEmail = $set['siteEmail'];

			$subject = $emailUserSubject;
			$message = '<html><body>';
			$message .= '<h3>'.$subject.'</h3>';
			$message .= '<hr>';
			$message .= '<p>'.nl2br($emailText).'</p>';
			$message .= '<hr>';
			$message .= '<p>'.$subscribeEmail3.'<br>'.$siteName.'</p>';
			$message .= '</body></html>';
			$headers = "From: ".$siteName." <".$siteEmail.">\r\n";
			$headers .= "Reply-To: ".$siteEmail."\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			if (mail($userEmail, $subject, $message, $headers)) {
				$msgBox = alertBox($emailSentMsg, "<i class='fa fa-check-square'></i>", "success");
				// Clear the Form of values
				$_POST['emailText'] = '';
			}
		}
	}
	
	// Approve Confession
	if (isset($_POST['submit']) && $_POST['submit'] == 'approve') {
		$confessId = $mysqli->real_escape_string($_POST['confessId']);
		$isActive = '1';
		$stmt = $mysqli->prepare("UPDATE confessions SET isActive = ? WHERE confessId = ?");
		$stmt->bind_param('ss', $isActive, $confessId);
		$stmt->execute();
		$msgBox = alertBox($confApprovedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
	}
	
	// Disable Confession
	if (isset($_POST['submit']) && $_POST['submit'] == 'disable') {
		$confessId = $mysqli->real_escape_string($_POST['confessId']);
		$isActive = '0';
		$stmt = $mysqli->prepare("UPDATE confessions SET isActive = ? WHERE confessId = ?");
		$stmt->bind_param('ss', $isActive, $confessId);
		$stmt->execute();
		$msgBox = alertBox($confDisabledMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
	}
	
	// Delete Confession
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteConf') {
		$deleteId = $mysqli->real_escape_string($_POST['deleteId']);

		// Delete Confession Entry
		$stmt = $mysqli->prepare("DELETE FROM confessions WHERE confessId = ?");
		$stmt->bind_param('s', $deleteId);
		$stmt->execute();
		$stmt->close();
		
		// Delete Confession Likes
		$stmt = $mysqli->prepare("DELETE FROM likes WHERE confessId = ?");
		$stmt->bind_param('s', $deleteId);
		$stmt->execute();
		$stmt->close();
		
		// Delete Confession Dislikes
		$stmt = $mysqli->prepare("DELETE FROM dislikes WHERE confessId = ?");
		$stmt->bind_param('s', $deleteId);
		$stmt->execute();
		$stmt->close();
		
		// Delete Confession Comments
		$stmt = $mysqli->prepare("DELETE FROM comments WHERE confessId = ?");
		$stmt->bind_param('s', $deleteId);
		$stmt->execute();
		$stmt->close();
		
		$msgBox = alertBox($confDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
	}
	
	// Approve Comment
    if (isset($_POST['submit']) && $_POST['submit'] == 'approveComment') {
		$commentId = $mysqli->real_escape_string($_POST['commentId']);
		$isActive = '1';
		$stmt = $mysqli->prepare("UPDATE comments SET isActive = ? WHERE commentId = ?");
		$stmt->bind_param('ss', $isActive, $commentId);
		$stmt->execute();
		$msgBox = alertBox($commentApprovedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
	}

	// Disable Comment
    if (isset($_POST['submit']) && $_POST['submit'] == 'disableComment') {
		$commentId = $mysqli->real_escape_string($_POST['commentId']);
		$isActive = '0';
		$stmt = $mysqli->prepare("UPDATE comments SET isActive = ? WHERE commentId = ?");
		$stmt->bind_param('ss', $isActive, $commentId);
		$stmt->execute();
		$msgBox = alertBox($commentDisabledMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
	}
	
	// Delete Comment
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteComment') {
		$deleteId = $mysqli->real_escape_string($_POST['deleteId']);
		$stmt = $mysqli->prepare("DELETE FROM comments WHERE commentId = ?");
		$stmt->bind_param('s', $deleteId);
		$stmt->execute();
		$msgBox = alertBox($commentDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
	}

	// Edit Comment
    if (isset($_POST['submit']) && $_POST['submit'] == 'editComment') {
		$commentId = $mysqli->real_escape_string($_POST['commentId']);
		$comments = htmlspecialchars($_POST['comments']);

		$stmt = $mysqli->prepare("UPDATE
									comments
								SET
									comments = ?
								WHERE
									commentId = ?"
		);
		$stmt->bind_param('ss',
							$comments,
							$commentId
		);
		$stmt->execute();
		$msgBox = alertBox($myCommentUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
	}

	
	$select = "SELECT
				userId,
				isAdmin,
				userEmail,
				password,
				userFirst,
				userLast,
				DATE_FORMAT(joinDate,'%b %d, %Y at %h:%i %p') AS joinDate,
				isActive,
				hash,
				DATE_FORMAT(lastVisited,'%b %d, %Y at %h:%i %p') AS lastVisited
			FROM
				users
			WHERE
				userId = ".$userId;
	$res = mysqli_query($mysqli, $select) or die('-1' . mysqli_error());
	$row = mysqli_fetch_assoc($res);
	if ($row['isAdmin'] == '1') {
		$type = '<strong class="text-success">'.$administratorText.'</strong>';
		$isaAdmin = 'selected';
	} else {
		$type = '<strong class="text-info">'.$userText.'</strong>';
		$isaAdmin = '';
	}
	if ($row['isActive'] == '1') {
		$status = '<strong class="text-success">'.$activeAccText.'</strong>';
		$selActive = '';
	} else {
		$status = '<strong class="text-warning">'.$inactiveAccText.'</strong>';
		$selActive = 'selected';
	}
	
	// Get Confessions
	$query = "SELECT
				confessId,
				(IFNULL(firstName, '')) AS firstName,
				confessText,
				DATE_FORMAT(postDate,'%b %d %Y') AS postDate,
				isActive,
				postIp
			FROM
				confessions
			WHERE userId = ".$userId;
	$results = mysqli_query($mysqli, $query) or die('-2' . mysqli_error());
	
	// Get Comments
	$qry = "SELECT
				commentId,
				confessId,
				(IFNULL(firstName, '')) AS fName,
				comments,
				DATE_FORMAT(commentDate,'%b %d %Y') AS commentDate,
				isActive,
				commentIp
			FROM
				comments
			WHERE userId = ".$userId;
	$result = mysqli_query($mysqli, $qry) or die('-3'.mysqli_error());

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
			<h3 class="mb-20"><?php echo $viewUserPageHead; ?></h3>
			<?php if ($msgBox) { echo $msgBox; } ?>
			
			<div class="row">
				<div class="col-md-4">
					<ul class="list-group mb-20">
						<li class="list-group-item"><?php echo $usersNameText; ?> <span class="pull-right"><?php echo clean($row['userFirst']).' '.clean($row['userLast']); ?></span></li>
						<li class="list-group-item"><?php echo $usersEmailText; ?> <span class="pull-right"><?php echo clean($row['userEmail']); ?></span></li>
						<li class="list-group-item"><?php echo $accTypeText; ?> <span class="pull-right"><?php echo $type; ?></span></li>
						<li class="list-group-item"><?php echo $accStatusText; ?> <span class="pull-right"><?php echo $status; ?></span></li>
						<li class="list-group-item"><?php echo $userJoinDateText; ?> <span class="pull-right"><?php echo $row['joinDate']; ?></span></li>
						<li class="list-group-item"><?php echo $lastSignInText; ?> <span class="pull-right"><?php echo $row['lastVisited']; ?></span></li>
					</ul>
				</div>
				<div class="col-md-8">
					<ul class="nav nav-tabs" role="tablist">
						<li class="active"><a href="#update" role="tab" data-toggle="tab"><i class="fa fa-user"></i> <?php echo $updateUserLink; ?></a></li>
						<li><a href="#password" role="tab" data-toggle="tab"><i class="fa fa-lock"></i> <?php echo $usersPasswordLink; ?></a></li>
						<li><a href="#type" role="tab" data-toggle="tab"><i class="fa fa-cogs"></i> <?php echo $usersAccLink; ?></a></li>
						<li><a href="#email" role="tab" data-toggle="tab"><i class="fa fa-envelope"></i> <?php echo $emailUserLink; ?></a></li>
					</ul>
					
					<div class="tab-content">
						<div class="tab-pane fade in active" id="update">
							<form action="" method="post">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="userFirst"><?php echo $usersFirstNameField; ?></label>
											<input type="text" class="form-control" required="" name="userFirst" value="<?php echo clean($row['userFirst']); ?>" />
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="userLast"><?php echo $usersLastNameField; ?></label>
											<input type="text" class="form-control" required="" name="userLast" value="<?php echo clean($row['userLast']); ?>" />
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="userEmail"><?php echo $userEmailAddyField; ?></label>
									<input type="text" class="form-control" required="" name="userEmail" value="<?php echo clean($row['userEmail']); ?>" />
								</div>
								<button type="input" name="submit" value="updateUser" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
							</form>
						</div>
						<div class="tab-pane fade" id="password">
							<form action="" method="post">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="password"><?php echo $newPassField; ?></label>
											<input type="text" class="form-control" name="password" required="" value="" />
											<span class="help-block"><?php echo $newPassFieldHelp; ?></span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="passwordr"><?php echo $repeatNewPassField; ?></label>
											<input type="text" class="form-control" name="passwordr" required="" value="" />
											<span class="help-block"><?php echo $repeatNewPassFieldHelp; ?></span>
										</div>
									</div>
								</div>
								<input type="hidden" name="passwordOld" value="<?php echo $row['password']; ?>" />
								<button type="input" name="submit" value="updatePassword" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
							</form>
						</div>
						<div class="tab-pane fade" id="type">
							<form action="" method="post">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="isActive"><?php echo $userAccStatusField; ?></label>
											<select class="form-control" name="isActive">
												<option value="1"><?php echo $activeText; ?></option>
												<option value="0" <?php echo $selActive; ?>><?php echo $inactiveText; ?></option>
											</select>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="accountType"><?php echo $accTypeTab; ?></label>
											<select class="form-control" name="accountType">
												<option value="0"><?php echo $userText; ?></option>
												<option value="1" <?php echo $isaAdmin; ?>><?php echo $administratorText; ?></option>
											</select>
										</div>
									</div>
								</div>
								<p class="text-muted"><?php echo $userAccQuip; ?></p>
								<button type="input" name="submit" value="updateStatus" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
							</form>
						</div>
						<div class="tab-pane fade" id="email">
							<form action="" method="post">
								<div class="form-group">
									<label for="emailText"><?php echo $emailUserField; ?></label>
									<textarea class="form-control" name="emailText" required="" rows="4"></textarea>
								</div>
								<input name="userEmail" type="hidden" value="<?php echo $row['userEmail']; ?>" />
								<button type="input" name="submit" value="sendEmail" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $sendEmailBtn; ?></button>
							</form>
						</div>
					</div>
				</div>
			</div>
			
			<hr class="mb-20" />
			
			<ul class="nav nav-tabs" role="tablist">
				<li class="active"><a href="#userConfessions" role="tab" data-toggle="tab"><i class="fa fa-comment"></i> <?php echo $usersConfTitle; ?></a></li>
				<li><a href="#userComments" role="tab" data-toggle="tab"><i class="fa fa-comments"></i> <?php echo $usersCommentsTitle; ?></a></li>
			</ul>
			
			<div class="tab-content">
				<div class="tab-pane fade in active" id="userConfessions">
					<?php if(mysqli_num_rows($results) < 1) { ?>
						<div class="alertMsg default no-margin">
							<i class="fa fa-warning"></i> <?php echo $noUserConfFound; ?>
						</div>
					<?php } else { ?>
						<table id="confData" class="display" cellspacing="0" width="100%">
							<thead>
								<tr>
									<th><?php echo $confessPlaceholder; ?></th>
									<th class="text-center"><?php echo $postedNameText; ?></th>
									<th class="text-center"><?php echo $datePostedText; ?></th>
									<th class="text-center"><?php echo $approvedText; ?></th>
									<th class="text-center"><?php echo $postersIpText; ?></th>
									<th><?php echo $actionsText; ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
									while ($a = mysqli_fetch_assoc($results)) {
										if ($a['firstName'] == '') { $firstName = $anonymousText; } else { $firstName = clean($a['firstName']); }
										if ($a['isActive'] == '1') {
											$active = '<span class="text-success">'.$yesBtn.'</span>';
											$action = '
														<form action="" method="post" class="tableForm">
															<input type="hidden" name="confessId" value="'.$a['confessId'].'" />
															<span data-toggle="tooltip" data-placement="left" title="'.$disableConfTooltip.'">
																<button type="input" name="submit" value="disable" class="label label-warning"><i class="fa fa-ban"></i></button>
															</span>
														</form>
													  ';
										} else {
											$active = '<strong class="text-danger">'.$noBtn.'</strong>';
											$action = '
														<form action="" method="post" class="tableForm">
															<input type="hidden" name="confessId" value="'.$a['confessId'].'" />
															<span data-toggle="tooltip" data-placement="left" title="'.$approveConfTooltip.'">
																<button type="input" name="submit" value="approve" class="label label-success"><i class="fa fa-check"></i></button>
															</span>
														</form>
													  ';
										}
								?>
										<tr>
											<td class="textContent">
												<span class="text-muted popover-icon" data-toggle="popover" data-placement="top" data-content="<?php echo htmlspecialchars($a['confessText']); ?>"><i class="fa fa-quote-left"></i></span>
												<a href="page.php?page=view&confession=<?php echo $a['confessId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewConfTooltip; ?>">
													<?php echo htmlspecialchars(ellipsis($a['confessText'], 50)); ?>
												</a>
											</td>
											<td class="text-center"><?php echo $firstName; ?></td>
											<td class="text-center"><?php echo $a['postDate']; ?></td>
											<td class="text-center"><?php echo $active; ?></td>
											<td class="text-center"><?php echo $a['postIp']; ?></td>
											<td>
												<?php echo $action; ?>
												<span data-toggle="tooltip" data-placement="left" title="<?php echo $modifyConfTooltip; ?>">
													<a href="page.php?page=viewConfession&confessId=<?php echo $a['confessId']; ?>" class="label label-primary"><i class="fa fa-edit"></i></a>
												</span>
												<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteConfTooltip; ?>">
													<a data-toggle="modal" href="#delete<?php echo $a['confessId']; ?>" class="label label-danger"><i class="fa fa-times"></i></a>
												</span>
											</td>
										</tr>
										
										<div class="modal fade" id="delete<?php echo $a['confessId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
											<div class="modal-dialog">
												<div class="modal-content">
													<form action="" method="post">
														<div class="modal-body">
															<p class="lead"><?php echo $deleteConfConfirmation; ?></p>
														</div>
														<div class="modal-footer">
															<input name="deleteId" type="hidden" value="<?php echo $a['confessId']; ?>" />
															<button type="input" name="submit" value="deleteConf" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $deleteConfBtn; ?></button>
															<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
														</div>
													</form>
												</div>
											</div>
										</div>
								<?php } ?>
							</tbody>
						</table>
					<?php } ?>
				</div>
				<div class="tab-pane fade" id="userComments">
					<?php if(mysqli_num_rows($result) < 1) { ?>
						<div class="alertMsg default no-margin">
							<i class="fa fa-warning"></i> <?php echo $noUserComFound; ?>
						</div>
					<?php } else { ?>
						<table id="commentData" class="display" cellspacing="0" width="100%">
							<thead>
								<tr>
									<th><?php echo $commentText; ?></th>
									<th class="text-center"><?php echo $postedByText; ?></th>
									<th class="text-center"><?php echo $datePostedText; ?></th>
									<th class="text-center"><?php echo $approvedText; ?></th>
									<th class="text-center"><?php echo $postersIpText; ?></th>
									<th><?php echo $actionsText; ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
									while ($b = mysqli_fetch_assoc($result)) {
										if ($b['fName'] == '') { $fName = ''.$anonymousText.''; } else { $fName = clean($b['fName']); }
										if ($b['isActive'] == '1') {
											$active = '<span class="text-success">'.$yesBtn.'</span>';
											$action = '
														<form action="" method="post" class="tableForm">
															<input type="hidden" name="commentId" value="'.$b['commentId'].'" />
															<span data-toggle="tooltip" data-placement="left" title="'.$disableCommentTooltip.'">
																<button type="input" name="submit" value="disableComment" class="label label-warning"><i class="fa fa-ban"></i></button>
															</span>
														</form>
													  ';
										} else {
											$active = '<strong class="text-danger">'.$noBtn.'</strong>';
											$action = '
														<form action="" method="post" class="tableForm">
															<input type="hidden" name="commentId" value="'.$b['commentId'].'" />
															<span data-toggle="tooltip" data-placement="left" title="'.$approveCommentTooltip.'">
																<button type="input" name="submit" value="approveComment" class="label label-success"><i class="fa fa-check"></i></button>
															</span>
														</form>
													  ';
										}
								?>
									<tr>
										<td class="textContent">
											<span class="text-muted popover-icon" data-toggle="popover" data-placement="top" data-content="<?php echo htmlspecialchars_decode($b['comments']); ?>"><i class="fa fa-quote-left"></i></span>
											<a href="page.php?page=view&confession=<?php echo $b['confessId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewConfTooltip; ?>">
												<?php echo ellipsis($b['comments'], 50); ?>
											</a>
										</td>
										<td class="text-center"><?php echo $fName; ?></td>
										<td class="text-center"><?php echo $b['commentDate']; ?></td>
										<td class="text-center"><?php echo $active; ?></td>
										<td class="text-center"><?php echo $b['commentIp']; ?></td>
										<td>
											<?php echo $action; ?>
											<span data-toggle="tooltip" data-placement="left" title="<?php echo $editCommentModal; ?>">
												<a data-toggle="modal" href="#editComment<?php echo $b['commentId']; ?>" class="label label-primary"><i class="fa fa-edit"></i></a>
											</span>
											<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteCommentText; ?>">
												<a data-toggle="modal" href="#deleteComment<?php echo $b['commentId']; ?>" class="label label-danger"><i class="fa fa-times"></i></a>
											</span>
										</td>
									</tr>
									
									<div class="modal fade" id="editComment<?php echo $b['commentId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
													<h4 class="modal-title"><?php echo $editCommentModal; ?></h4>
												</div>
												<form action="" method="post">
													<div class="modal-body">
														<div class="form-group">
															<label for="comments"><?php echo $commentsNavLink; ?></label>
															<textarea class="form-control" required="" name="comments" rows="4"><?php echo htmlspecialchars_decode($b['comments']); ?></textarea>
														</div>
													</div>
													<div class="modal-footer">
														<input name="commentId" type="hidden" value="<?php echo $b['commentId']; ?>" />
														<button type="input" name="submit" value="editComment" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
														<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
													</div>
												</form>
											</div>
										</div>
									</div>
									
									<div class="modal fade" id="deleteComment<?php echo $b['commentId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<form action="" method="post">
													<div class="modal-body">
														<p class="lead"><?php echo $deleteCommentConf; ?></p>
													</div>
													<div class="modal-footer">
														<input name="deleteId" type="hidden" value="<?php echo $b['commentId']; ?>" />
														<button type="input" name="submit" value="deleteComment" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $deleteCommentBtn; ?></button>
														<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
													</div>
												</form>
											</div>
										</div>
									</div>
								<?php } ?>
							</tbody>
						</table>
					<?php } ?>
				</div>
			</div>
		</div>
	</section>
<?php } ?>