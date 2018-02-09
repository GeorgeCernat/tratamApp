<?php
	$jsFile = 'comments';
	$msgBox = '';
	
	// Approve Comment
	if (isset($_POST['submit']) && $_POST['submit'] == 'approve') {
		$commentId = $mysqli->real_escape_string($_POST['commentId']);
		$isActive = '1';
		$stmt = $mysqli->prepare("UPDATE comments SET isActive = ? WHERE commentId = ?");
		$stmt->bind_param('ss', $isActive, $commentId);
		$stmt->execute();
		$msgBox = alertBox($commentApprovedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
	}
	
	// Disable Comment
	if (isset($_POST['submit']) && $_POST['submit'] == 'disable') {
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
	
	$select = "SELECT
				commentId,
				confessId,
				(IFNULL(firstName, '')) AS firstName,
				comments,
				DATE_FORMAT(commentDate,'%b %d %Y') AS commentDate,
				isActive,
				commentIp
			FROM
				comments";
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
			<h3><?php echo $commentsPageHead; ?></h3>
			<?php if ($msgBox) { echo $msgBox; } ?>
			
			<?php if(mysqli_num_rows($res) < 1) { ?>
				<div class="alertMsg default no-margin">
					<i class="fa fa-warning"></i> <?php echo $noCommentsFoundMsg; ?>
				</div>
			<?php } else { ?>
				<table id="comData" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><?php echo $commentText; ?></th>
							<th class="text-center"><?php echo $postedByText; ?></th>
							<th class="text-center"><?php echo $datePostedText; ?></th>
							<th class="text-center"><?php echo $postersIpText; ?></th>
							<th><?php echo $actionsText; ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
							while ($row = mysqli_fetch_assoc($res)) {
								if ($row['firstName'] == '') { $firstName = $anonymousText; } else { $firstName = clean($row['firstName']); }
								if ($row['isActive'] == '1') {
									$action = '
												<form action="" method="post" class="tableForm">
													<input type="hidden" name="commentId" value="'.$row['commentId'].'" />
													<span data-toggle="tooltip" data-placement="left" title="'.$disableCommentTooltip.'">
														<button type="input" name="submit" value="disable" class="label label-warning"><i class="fa fa-ban"></i></button>
													</span>
												</form>
											  ';
								} else {
									$action = '
												<form action="" method="post" class="tableForm">
													<input type="hidden" name="commentId" value="'.$row['commentId'].'" />
													<span data-toggle="tooltip" data-placement="left" title="'.$approveCommentTooltip.'">
														<button type="input" name="submit" value="approve" class="label label-success"><i class="fa fa-check"></i></button>
													</span>
												</form>
											  ';
								}
						?>
								<tr>
									<td>
										<span class="text-muted popover-icon" data-toggle="popover" data-placement="top" data-content="<?php echo clean($row['comments']); ?>"><i class="fa fa-quote-left"></i></span>
										<a href="page.php?page=view&confession=<?php echo $row['confessId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewConfTooltip; ?>">
											<?php echo ellipsis($row['comments'], 50); ?>
										</a>
									</td>
									<td class="text-center"><?php echo $firstName; ?></td>
									<td class="text-center"><?php echo $row['commentDate']; ?></td>
									<td class="text-center"><?php echo $row['commentIp']; ?></td>
									<td>
										<?php echo $action; ?>
										<span data-toggle="tooltip" data-placement="left" title="<?php echo $viewConfTooltip; ?>">
											<a href="page.php?page=viewConfession&confessId=<?php echo $row['confessId']; ?>" class="label label-primary"><i class="fa fa-comment"></i></a>
										</span>
										<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteCommentTooltip; ?>">
											<a data-toggle="modal" href="#delete<?php echo $row['commentId']; ?>" class="label label-danger"><i class="fa fa-times"></i></a>
										</span>
									</td>
								</tr>
								
								<div class="modal fade" id="delete<?php echo $row['commentId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<form action="" method="post">
												<div class="modal-body">
													<p class="lead"><?php echo $deleteCommentConf; ?></p>
												</div>
												<div class="modal-footer">
													<input name="deleteId" type="hidden" value="<?php echo $row['commentId']; ?>" />
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
	</section>
<?php } ?>