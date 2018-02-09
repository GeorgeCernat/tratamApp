<?php
	$jsFile = 'subscriptions';
	$msgBox = '';
	
	// Activate
	if (isset($_POST['submit']) && $_POST['submit'] == 'activateSub') {
		$listId = $mysqli->real_escape_string($_POST['listId']);
		$isActive = '1';
		$stmt = $mysqli->prepare("UPDATE mailinglist SET isActive = ? WHERE listId = ?");
		$stmt->bind_param('ss', $isActive, $listId);
		$stmt->execute();
		$msgBox = alertBox($subActiveMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
	}
	
	// Deactivate
	if (isset($_POST['submit']) && $_POST['submit'] == 'deactivateSub') {
		$listId = $mysqli->real_escape_string($_POST['listId']);
		$isActive = '0';
		$stmt = $mysqli->prepare("UPDATE mailinglist SET isActive = ? WHERE listId = ?");
		$stmt->bind_param('ss', $isActive, $listId);
		$stmt->execute();
		$msgBox = alertBox($subInactiveMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
	}
	
	// Delete
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteSub') {
		$deleteId = $mysqli->real_escape_string($_POST['deleteId']);
		$stmt = $mysqli->prepare("DELETE FROM mailinglist WHERE listId = ?");
		$stmt->bind_param('s', $deleteId);
		$stmt->execute();
		$msgBox = alertBox($subDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
	}
	
	$select = "SELECT
				listId,
				emailAddress,
				DATE_FORMAT(signupDate,'%b %d, %Y at %h:%i %p') AS signupDate,
				isActive,
				signupIp
			FROM
				mailinglist";
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
					<h3><?php echo $subscriptionsPageHead; ?></h3>
				</div>
				<div class="col-md-6">
					<a href="page.php?page=export" class="btn btn-primary btn-icon pull-right mt-20"><i class="fa fa-file-excel-o"></i> <?php echo $subExportBtn; ?></a>
				</div>
			</div>
			<?php if ($msgBox) { echo $msgBox; } ?>
			
			<table id="subscriptionData" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>ID</th>
						<th class="text-center"><?php echo $emailAddressField; ?></th>
						<th class="text-center"><?php echo $signUpDateText; ?></th>
						<th class="text-center"><?php echo $activeText; ?></th>
						<th class="text-center"><?php echo $signUpIpText; ?></th>
						<th><?php echo $actionsText; ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
						while ($row = mysqli_fetch_assoc($res)) {
							if ($row['isActive'] == '1') {
								$activeSub = '<span class="text-success">'.$yesBtn.'</span>';
								$action = '
											<form action="" method="post" class="tableForm">
												<input type="hidden" name="listId" value="'.$row['listId'].'" />
												<span data-toggle="tooltip" data-placement="left" title="'.$deactivateSubText.'">
													<button type="input" name="submit" value="deactivateSub" class="label label-warning"><i class="fa fa-ban"></i></button>
												</span>
											</form>
										  ';
							} else {
								$activeSub = '<strong class="text-danger">'.$noBtn.'</span>';
								$action = '
											<form action="" method="post" class="tableForm">
												<input type="hidden" name="listId" value="'.$row['listId'].'" />
												<span data-toggle="tooltip" data-placement="left" title="'.$activateSubText.'">
													<button type="input" name="submit" value="activateSub" class="label label-success"><i class="fa fa-check"></i></button>
												</span>
											</form>
										  ';
							}
					?>
							<tr>
								<td><?php echo $row['listId']; ?></td>
								<td class="text-center"><?php echo clean($row['emailAddress']); ?></td>
								<td class="text-center"><?php echo $row['signupDate']; ?></td>
								<td class="text-center"><?php echo $activeSub; ?></td>
								<td class="text-center"><?php echo $row['signupIp']; ?></td>
								<td>
									<?php echo $action; ?>
									<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteSubTooltip; ?>">
										<a data-toggle="modal" href="#delete<?php echo $row['listId']; ?>" class="label label-danger"><i class="fa fa-times"></i></a>
									</span>
								</td>
							</tr>
							
							<div class="modal fade" id="delete<?php echo $row['listId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<form action="" method="post">
											<div class="modal-body">
												<p class="lead"><?php echo $deleteSubConf; ?></p>
											</div>
											<div class="modal-footer">
												<input name="deleteId" type="hidden" value="<?php echo $row['listId']; ?>" />
												<button type="input" name="submit" value="deleteSub" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $deleteSubBtn; ?></button>
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