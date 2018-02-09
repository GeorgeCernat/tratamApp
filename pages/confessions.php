<?php
	$jsFile = 'confessions';
	$msgBox = '';
	
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

	$select = "SELECT
				confessId,
				(IFNULL(firstName, '')) AS firstName,
				confessText,
				DATE_FORMAT(postDate,'%b %d %Y') AS postDate,
				isActive,
				postIp,
				(SELECT COUNT(*) FROM views WHERE views.confessId = confessions.confessId ) as totalViews,
				(SELECT COUNT(*) FROM likes WHERE likes.confessId = confessions.confessId ) as totalLikes,
				(SELECT COUNT(*) FROM dislikes WHERE dislikes.confessId = confessions.confessId ) as totalDislikes
			FROM
				confessions";
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
			<h3><?php echo $confPageHead; ?></h3>
			<?php if ($msgBox) { echo $msgBox; } ?>
			
			<?php if(mysqli_num_rows($res) < 1) { ?>
				<div class="alertMsg default no-margin">
					<i class="fa fa-warning"></i> <?php echo $noConfFoundMsg; ?>
				</div>
			<?php } else { ?>
				<table id="confData" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><?php echo $confessPlaceholder; ?></th>
							<th class="text-center"><?php echo $postedByText; ?></th>
							<th class="text-center"><?php echo $datePostedText; ?></th>
							<th class="text-center"><?php echo $approvedText; ?></th>
							<th class="text-center"><?php echo $likesDislikesText; ?></th>
							<th class="text-center"><?php echo $viewsText; ?></th>
							<th class="text-center"><?php echo $postersIpText; ?></th>
							<th><?php echo $actionsText; ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
							while ($row = mysqli_fetch_assoc($res)) {
								if ($row['firstName'] == '') { $firstName = 'Anonymous'; } else { $firstName = clean($row['firstName']); }
								if ($row['isActive'] == '1') {
									$active = '<span class="text-success">'.$yesBtn.'</span>';
									$action = '
												<form action="" method="post" class="tableForm">
													<input type="hidden" name="confessId" value="'.$row['confessId'].'" />
													<span data-toggle="tooltip" data-placement="left" title="'.$disableConfTooltip.'">
														<button type="input" name="submit" value="disable" class="label label-warning"><i class="fa fa-ban"></i></button>
													</span>
												</form>
											  ';
								} else {
									$active = '<strong class="text-danger">'.$noBtn.'</strong>';
									$action = '
												<form action="" method="post" class="tableForm">
													<input type="hidden" name="confessId" value="'.$row['confessId'].'" />
													<span data-toggle="tooltip" data-placement="left" title="'.$approveConfTooltip.'">
														<button type="input" name="submit" value="approve" class="label label-success"><i class="fa fa-check"></i></button>
													</span>
												</form>
											  ';
								}
						?>
								<tr>
									<td>
										<span class="text-muted popover-icon" data-toggle="popover" data-placement="top" data-content="<?php echo clean($row['confessText']); ?>"><i class="fa fa-quote-left"></i></span>
										<a href="page.php?page=view&confession=<?php echo $row['confessId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewConfTooltip; ?>">
											<?php echo htmlspecialchars(ellipsis($row['confessText'], 50)); ?>
										</a>
									</td>
									<td class="text-center"><?php echo $firstName; ?></td>
									<td class="text-center"><?php echo $row['postDate']; ?></td>
									<td class="text-center"><?php echo $active; ?></td>
									<td class="text-center"><span class="text-success"><?php echo $row['totalLikes'].'</span> / <span class="text-danger">'.$row['totalDislikes']; ?></span></td>
									<td class="text-center"><?php echo $row['totalViews']; ?></td>
									<td class="text-center"><?php echo $row['postIp']; ?></td>
									<td>
										<?php echo $action; ?>
										<span data-toggle="tooltip" data-placement="left" title="<?php echo $modifyConfTooltip; ?>">
											<a href="page.php?page=viewConfession&confessId=<?php echo $row['confessId']; ?>" class="label label-primary"><i class="fa fa-edit"></i></a>
										</span>
										<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteConfTooltip; ?>">
											<a data-toggle="modal" href="#delete<?php echo $row['confessId']; ?>" class="label label-danger"><i class="fa fa-times"></i></a>
										</span>
									</td>
								</tr>
								
								<div class="modal fade" id="delete<?php echo $row['confessId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<form action="" method="post">
												<div class="modal-body">
													<p class="lead"><?php echo $deleteConfConfirmation; ?></p>
												</div>
												<div class="modal-footer">
													<input name="deleteId" type="hidden" value="<?php echo $row['confessId']; ?>" />
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
	</section>
<?php } ?>