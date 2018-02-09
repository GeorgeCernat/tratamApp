<?php
	$datePicker = 'true';
	$jsFile = 'advertising';
	
	// Get the Ad Images Folder from the Site Settings
	$adsPath = $set['adsPath'];

	// Get the File Types allowed
	$fileExt = $set['adTypesAllowed'];
	$allowed = preg_replace('/,/', ', ', $fileExt);
	$ftypes = array($fileExt);
	$ftypes_data = explode( ',', $fileExt );

	if (isset($_POST['submit']) && $_POST['submit'] == 'saveAd') {
		// Validation
		if($_POST['adTitle'] == "") {
            $msgBox = alertBox($adTitleReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['adUrl'] == "") {
            $msgBox = alertBox($adUrlReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['adType'] == "...") {
            $msgBox = alertBox($adTypeReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$adTitle = $mysqli->real_escape_string($_POST['adTitle']);
			$adUrl = $mysqli->real_escape_string($_POST['adUrl']);
			$adType = $mysqli->real_escape_string($_POST['adType']);
			$isActive = $mysqli->real_escape_string($_POST['isActive']);
			$adStartDate = $mysqli->real_escape_string($_POST['adStartDate']);
			$adEndDate = $mysqli->real_escape_string($_POST['adEndDate']);
			$adText = htmlentities($_POST['adText']);
			$dateCreated = date("Y-m-d H:i:s");
			
			if ($adType != '1') {
				// Image Advertisement
				$ext = substr(strrchr(basename($_FILES['file']['name']), '.'), 1);
				if (!in_array($ext, $ftypes_data)) {
					$msgBox = alertBox($invalidAdImgMsg, "<i class='fa fa-times-circle'></i>", "danger");
				} else {
					// Generate Random Hash
					$randomHash = md5(uniqid(rand()));
					$imageHash = substr($randomHash, 0, 8);
					
					// Replace any spaces with an underscore
					// And set to all lower-case
					$newAdName = str_replace(' ', '_', $adTitle);
					$fileAdName = strtolower($newAdName);
					
					// Set the upload path
					$uploadAdTo = $adsPath;
					$adImageUrl = basename($_FILES['file']['name']);
					
					// Get the files original Ext
					$adExtension = end(explode(".", $adImageUrl));
					
					// Set the files name to the name set in the form
					// And add the original Ext
					$newAdfilename = $fileAdName.'-'.$imageHash.'.'.$adExtension;
					$movePath = $uploadAdTo.'/'.$newAdfilename;
					
					// Save the Ad
					$stmt = $mysqli->prepare("
										INSERT INTO
											ads(
												adType,
												adImage,
												adTitle,
												adText,
												adUrl,
												adStartDate,
												adEndDate,
												isActive,
												dateCreated
											) VALUES (
												?,
												?,
												?,
												?,
												?,
												?,
												?,
												?,
												?
											)
					");
					$stmt->bind_param('sssssssss',
						$adType,
						$newAdfilename,
						$adTitle,
						$adText,
						$adUrl,
						$adStartDate,
						$adEndDate,
						$isActive,
						$dateCreated
					);
					if (move_uploaded_file($_FILES['file']['tmp_name'], $movePath)) {
						$stmt->execute();
						$msgBox = alertBox($newAdSavedMsg1, "<i class='fa fa-check-square'></i>", "success");
						// Clear the Form of values
						$_POST['adTitle'] = $_POST['adUrl'] = $_POST['adStartDate'] = $_POST['adEndDate'] = $_POST['adText'] = '';
						$stmt->close();
					}
				}				
			} else {
				// Text Only Advertisement
				$stmt = $mysqli->prepare("
									INSERT INTO
										ads(
											adType,
											adTitle,
											adText,
											adUrl,
											adStartDate,
											adEndDate,
											isActive,
											dateCreated
										) VALUES (
											?,
											?,
											?,
											?,
											?,
											?,
											?,
											?
										)
				");
				$stmt->bind_param('ssssssss',
					$adType,
					$adTitle,
					$adText,
					$adUrl,
					$adStartDate,
					$adEndDate,
					$isActive,
					$dateCreated
				);
				$stmt->execute();
				$msgBox = alertBox($newAdSavedMsg2, "<i class='fa fa-check-square'></i>", "success");
				// Clear the Form of values
				$_POST['adTitle'] = $_POST['adUrl'] = $_POST['adStartDate'] = $_POST['adEndDate'] = $_POST['adText'] = '';
				$stmt->close();
			}
		}
	}
	
	// Delete Ad
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteAd') {
		$deleteId = $mysqli->real_escape_string($_POST['deleteId']);
		$adImage = $mysqli->real_escape_string($_POST['adImage']);

		if ($adImage != '') {
			$filePath = $adsPath.'/'.$adImage;
			if (file_exists($filePath)) {
				// Delete the File
				unlink($filePath);

				// Delete the Record
				$stmt = $mysqli->prepare("DELETE FROM ads WHERE adId = ?");
				$stmt->bind_param('s', $deleteId);
				$stmt->execute();
				$msgBox = alertBox($deleteAdMsg1, "<i class='fa fa-check-square'></i>", "success");
				$stmt->close();
			} else {
				$msgBox = alertBox($deleteAdMsg2, "<i class='fa fa-times-circle'></i>", "danger");
			}
		} else {
			// Delete the Record
			$stmt = $mysqli->prepare("DELETE FROM ads WHERE adId = ?");
			$stmt->bind_param('s', $deleteId);
			$stmt->execute();
			$msgBox = alertBox($deleteAdMsg3, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
    }
	
	$select = "SELECT
					adId,
					adType,
					adImage,
					adTitle,
					adText,
					DATE_FORMAT(adStartDate,'%b %d, %Y') AS adStartDate,
					DATE_FORMAT(adEndDate,'%b %d, %Y') AS adEndDate,
					isActive,
					DATE_FORMAT(dateCreated,'%b %d, %Y at %h:%i %p') AS dateCreated
				FROM
					ads";
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
			<h3 class="mb-20"><?php echo $advertisingPageHead; ?></h3>
			<?php if ($msgBox) { echo $msgBox; } ?>
			
			
			<ul class="nav nav-tabs" role="tablist">
				<li class="active"><a href="#ads" role="tab" data-toggle="tab"><i class="fa fa-money"></i> <?php echo $advertisementsLink; ?></a></li>
				<li><a href="#newAd" role="tab" data-toggle="tab"><i class="fa fa-plus"></i> <?php echo $newAdLink; ?></a></li>
			</ul>
			
			<div class="tab-content">
				<div class="tab-pane fade in active" id="ads">
					<?php if(mysqli_num_rows($res) < 1) { ?>
						<div class="alertMsg default no-margin">
							<i class="fa fa-warning"></i> <?php echo $noAdsFoundMsg; ?>
						</div>
					<?php } else { ?>
						<table id="adData" class="display" cellspacing="0" width="100%">
							<thead>
								<tr>
									<th><?php echo $adTitleText; ?></th>
									<th class="text-center"><?php echo $adTypeText; ?></th>
									<th class="text-center"><?php echo $adStatusText; ?></th>
									<th class="text-center"><?php echo $startDateText; ?></th>
									<th class="text-center"><?php echo $endDateText; ?></th>
									<th class="text-center"><?php echo $dateCreatedText; ?></th>
									<th><?php echo $actionsText; ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
									while ($row = mysqli_fetch_assoc($res)) {
										if ($row['adType'] == '1') { $type = $adTypeSelect1; } else if ($row['adType'] == '2') { $type = $adTypeSelect2; } else { $type = $adTypeSelect3; }
										if ($row['isActive'] == '1') { $active = '<span class="text-success">'.$activeText.'</span>'; } else { $active = '<strong class="text-danger">'.$inactiveText.'</strong>'; }
										if ($row['adStartDate'] == '0000-00-00') { $starts = ''; } else { $starts = $row['adStartDate']; }
										if ($row['adEndDate'] == '0000-00-00') { $ends = ''; } else { $ends = $row['adEndDate']; }
								?>
										<tr>
											<td>
												<a href="page.php?page=viewAd&adId=<?php echo $row['adId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewAdTooltip; ?>">
													<?php echo clean($row['adTitle']); ?>
												</a>
											</td>
											<td class="text-center"><?php echo $type; ?></td>
											<td class="text-center"><?php echo $active; ?></td>
											<td class="text-center"><?php echo $starts; ?></td>
											<td class="text-center"><?php echo $ends; ?></td>
											<td class="text-center"><?php echo $row['dateCreated']; ?></td>
											<td>
												<span data-toggle="tooltip" data-placement="left" title="<?php echo $viewAdTooltip; ?>">
													<a href="page.php?page=viewAd&adId=<?php echo $row['adId']; ?>" class="label label-primary"><i class="fa fa-edit"></i></a>
												</span>
												<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteAdTooltip; ?>">
													<a data-toggle="modal" href="#deleteAd<?php echo $row['adId']; ?>" class="label label-danger"><i class="fa fa-times"></i></a>
												</span>
											</td>
										</tr>
										
										<div class="modal fade" id="deleteAd<?php echo $row['adId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
											<div class="modal-dialog">
												<div class="modal-content">
													<form action="" method="post">
														<div class="modal-body">
															<p class="lead"><?php echo $deleteAdConf; ?></p>
														</div>
														<div class="modal-footer">
															<input name="deleteId" type="hidden" value="<?php echo $row['adId']; ?>" />
															<input name="adImage" type="hidden" value="<?php echo $row['adImage']; ?>" />
															<button type="input" name="submit" value="deleteAd" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $deleteAdBtn; ?></button>
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
				<div class="tab-pane fade" id="newAd">
					<p><?php echo $newAdQuip; ?></p>

					<form action="" method="post" enctype="multipart/form-data">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="adTitle"><?php echo $adTitleText; ?></label>
									<input type="text" class="form-control" name="adTitle" required="" value="" />
									<span class="help-block"><?php echo $adTitleFieldHelp; ?></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="adUrl"><?php echo $adUrlField; ?></label>
									<input type="text" class="form-control" name="adUrl" required="" value="" />
									<span class="help-block"><?php echo $adUrlFieldHelp; ?></span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="adType"><?php echo $adTypeField; ?></label>
									<select class="form-control" name="adType">
										<option value="..."><?php echo $selectOption; ?></option>
										<option value="1"><?php echo $adTypeField1; ?></option>
										<option value="2"><?php echo $adTypeField2; ?></option>
										<option value="3"><?php echo $adTypeField3; ?></option>
									</select>
									<span class="help-block"><?php echo $adTypeFieldHelp; ?></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="isActive"><?php echo $adStatusField; ?></label>
									<select class="form-control" name="isActive">
										<option value="0"><?php echo $inactiveText; ?></option>
										<option value="1"><?php echo $activeText; ?></option>
									</select>
									<span class="help-block"><?php echo $adStatusFieldHelp; ?></span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="adStartDate"><?php echo $startDateText; ?></label>
									<input type="text" class="form-control" name="adStartDate" id="adStartDate" value="" />
									<span class="help-block"><?php echo $startDateTextHelp; ?></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="adEndDate"><?php echo $endDateText; ?></label>
									<input type="text" class="form-control" name="adEndDate" id="adEndDate" value="" />
									<span class="help-block"><?php echo $endDateTextHelp; ?></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="file"><?php echo $uploadAdImgField; ?></label>
							<input type="file" id="file" name="file">
							<span class="help-block"><?php echo $uploadAdImgFieldHelp; ?></span>
						</div>
						<div class="form-group">
							<label for="adText"><?php echo $adTextField; ?></label>
							<textarea class="form-control" name="adText" rows="4"></textarea>
							<span class="help-block"><?php echo $adTextFieldHelp; ?></span>
						</div>
						<button type="input" name="submit" value="saveAd" class="btn btn-success btn-lg btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveNewAdBtn; ?></button>
					</form>
				</div>
			</div>
		</div>
	</section>
<?php } ?>