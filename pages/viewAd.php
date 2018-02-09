<?php
	$adId = $_GET['adId'];
	$datePicker = 'true';
	$jsFile = 'viewAd';
	
	// Get the Ad Images Folder from the Site Settings
	$adsPath = $set['adsPath'];

	// Get the File Types allowed
	$fileExt = $set['adTypesAllowed'];
	$allowed = preg_replace('/,/', ', ', $fileExt);
	$ftypes = array($fileExt);
	$ftypes_data = explode( ',', $fileExt );
	
	// Update Ad
    if (isset($_POST['submit']) && $_POST['submit'] == 'saveAd') {
		// Validation
		if($_POST['adTitle'] == "") {
            $msgBox = alertBox($adTitleReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['adUrl'] == "") {
            $msgBox = alertBox($adURLReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$adTitle = $mysqli->real_escape_string($_POST['adTitle']);
			$adUrl = $mysqli->real_escape_string($_POST['adUrl']);
			$isActive = $mysqli->real_escape_string($_POST['isActive']);
			$adStartDate = $mysqli->real_escape_string($_POST['adStartDate']);
			$adEndDate = $mysqli->real_escape_string($_POST['adEndDate']);
			$adText = htmlentities($_POST['adText']);

			$stmt = $mysqli->prepare("UPDATE
										ads
									SET
										adTitle = ?,
										adText = ?,
										adUrl = ?,
										adStartDate = ?,
										adEndDate = ?,
										isActive = ?
									WHERE
										adId = ?"
			);
			$stmt->bind_param('sssssss',
								$adTitle,
								$adText,
								$adUrl,
								$adStartDate,
								$adEndDate,
								$isActive,
								$adId
			);
			$stmt->execute();
			$msgBox = alertBox($adUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
	}
	
	// Upload New Ad Image
	if (isset($_POST['submit']) && $_POST['submit'] == 'uploadImg') {
		$adImage = $mysqli->real_escape_string($_POST['adImage']);
		$adTitle = $mysqli->real_escape_string($_POST['adTitle']);

		if ($adImage != '') {
			$filePath = $adsPath.'/'.$adImage;
			if (file_exists($filePath)) {
				// Delete the File
				unlink($filePath);

				$ext = substr(strrchr(basename($_FILES['file']['name']), '.'), 1);			
				if (!in_array($ext, $ftypes_data)) {
					$msgBox = alertBox($invalidAdImageMsg, "<i class='fa fa-times-circle'></i>", "danger");
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
					
					$stmt = $mysqli->prepare("UPDATE
												ads
											SET
												adImage = ?
											WHERE
												adId = ?"
					);
					$stmt->bind_param('ss',
										$newAdfilename,
										$adId
					);
					if (move_uploaded_file($_FILES['file']['tmp_name'], $movePath)) {
						$stmt->execute();
						$msgBox = alertBox($newAdImageUploadedMsg, "<i class='fa fa-check-square'></i>", "success");
						$stmt->close();
					}
				}				
			} else {
				$msgBox = alertBox($newAdErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
			}
		}
    }
	
	$select = "SELECT
					adId,
					adType,
					adImage,
					adTitle,
					adText,
					adUrl,
					DATE_FORMAT(adStartDate,'%Y-%m-%d') AS adStartDate,
					DATE_FORMAT(adStartDate,'%b %d, %Y') AS startDate,
					DATE_FORMAT(adEndDate,'%Y-%m-%d') AS adEndDate,
					DATE_FORMAT(adEndDate,'%b %d, %Y') AS endDate,
					isActive,
					DATE_FORMAT(dateCreated,'%b %d, %Y at %h:%i %p') AS dateCreated
				FROM
					ads
				WHERE adId = ".$adId;
	$res = mysqli_query($mysqli, $select) or die('-1' . mysqli_error());
	$row = mysqli_fetch_assoc($res);
	
	if ($row['adType'] == '1') { $type = $adTypeField1; } else if ($row['adType'] == '2') { $type = $adTypeField2; } else { $type = $adTypeField3; }
	if ($row['isActive'] == '1') { $status = '<span class="text-success">'.$activeText.'</span>'; } else { $status = '<strong class="text-danger">'.$inactiveText.'</strong>'; }
	if ($row['adStartDate'] == '0000-00-00') { $starts = ''; } else { $starts = $row['adStartDate']; }
	if ($row['adEndDate'] == '0000-00-00') { $ends = ''; } else { $ends = $row['adEndDate']; }
	if ($row['isActive'] == '1') { $active = 'selected'; } else { $active = ''; }
	
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
			<h3 class="mb-20"><?php echo $viewAdPageHead; ?></h3>
			<?php if ($msgBox) { echo $msgBox; } ?>
			
			<ul class="nav nav-tabs" role="tablist">
				<li class="active"><a href="#details" role="tab" data-toggle="tab"><i class="fa fa-file-text-o"></i> <?php echo $adDetailsLink; ?></a></li>
				<?php if ($row['adImage'] != '') { ?>
					<li><a href="#image" role="tab" data-toggle="tab"><i class="fa fa-picture-o"></i> <?php echo $adImageLink; ?></a></li>
				<?php } ?>
				<li><a href="#updateAd" role="tab" data-toggle="tab"><i class="fa fa-edit"></i> <?php echo $updateAdLink; ?></a></li>
			</ul>
			
			<div class="tab-content">
				<div class="tab-pane fade in active" id="details">
					<?php
						if ($row['adImage'] != '') {
							//Get File Extension
							$ext = substr(strrchr($row['adImage'],'.'), 1);
							$imgExts = array('gif', 'GIF', 'jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'tiff', 'TIFF', 'tif', 'TIF', 'bmp', 'BMP');
							
							if (in_array($ext, $imgExts)) {
								echo '<p class="mb-20"><img alt="'.$adImageAlt.'" src="'.$adsPath.$row['adImage'].'" class="img-responsive" /></p>';
							}
						}
					?>
					<ul class="list-group mb-10">
						<li class="list-group-item"><?php echo $adTitleText; ?> <?php echo clean($row['adTitle']); ?></li>
						<li class="list-group-item">
							<?php echo $adURLText; ?> <a href="<?php echo clean($row['adUrl']); ?>" target="_blank"><?php echo clean($row['adUrl']); ?></a>
						</li>
					</ul>
					<div class="row">
						<div class="col-md-6">
							<ul class="list-group">
								<li class="list-group-item"><?php echo $adTypeText; ?> <?php echo $type; ?></li>
								<li class="list-group-item"><?php echo $adStartDateText; ?> <?php echo $row['startDate']; ?></li>
							</ul>
						</div>
						<div class="col-md-6">
							<ul class="list-group">
								<li class="list-group-item"><?php echo $adStatusText; ?> <?php echo $status; ?></li>
								<li class="list-group-item"><?php echo $adEndDateText; ?> <?php echo $row['endDate']; ?></li>
							</ul>
						</div>
					</div>
					<?php if ($row['adType'] == '1') { ?>
						<ul class="list-group mt-10">
							<li class="list-group-item"><?php echo nl2br(clean($row['adText'])); ?></li>
						</ul>
					<?php } ?>
				</div>
				<?php if ($row['adImage'] != '') { ?>
					<div class="tab-pane fade" id="image">
						<?php
							if ($row['adImage'] != '') {
								//Get File Extension
								$ext = substr(strrchr($row['adImage'],'.'), 1);
								$imgExts = array('gif', 'GIF', 'jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'tiff', 'TIFF', 'tif', 'TIF', 'bmp', 'BMP');
								
								if (in_array($ext, $imgExts)) {
									echo '<p class="mb-20"><img alt="'.$adImageAlt.'" src="'.$adsPath.$row['adImage'].'" class="img-responsive" /></p>';
								}
							}
						?>
						<p class="lead"><?php echo $adUploadImageQuip1; ?></p>
						<p><?php echo $adUploadImageQuip2; ?></p>
						<form action="" method="post" enctype="multipart/form-data">
							<div class="form-group">
								<label for="file"><?php echo $selectAdImageField; ?></label>
								<input type="file" id="file" name="file">
								<span class="help-block"><?php echo $selectAdImageFieldHelp; ?></span>
							</div>
							<input name="adImage" type="hidden" value="<?php echo $row['adImage']; ?>" />
							<input name="adTitle" type="hidden" value="<?php echo clean($row['adTitle']); ?>" />
							<button type="input" name="submit" value="uploadImg" class="btn btn-success btn-lg btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $uploadNewAdImageBtn; ?></button>
						</form>
					</div>
				<?php } ?>
				<div class="tab-pane fade" id="updateAd">
					<form action="" method="post">
						<p><?php echo $adDatesQuip; ?></p>
						
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="adTitle"><?php echo $adTitleField; ?></label>
									<input type="text" class="form-control" name="adTitle" required="" value="<?php echo clean($row['adTitle']); ?>" />
									<span class="help-block"><?php echo $adTitleFieldHelp; ?></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="adUrl"><?php echo $adURLField; ?></label>
									<input type="text" class="form-control" name="adUrl" required="" value="<?php echo clean($row['adUrl']); ?>" />
									<span class="help-block"><?php echo $adURLFieldHelp; ?></span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="isActive"><?php echo $adStatusField; ?></label>
									<select class="form-control" name="isActive">
										<option value="0"><?php echo $inactiveText; ?></option>
										<option value="1" <?php echo $active; ?>><?php echo $activeText; ?></option>
									</select>
									<span class="help-block"><?php echo $adStatusFieldHelp; ?></span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="adStartDate"><?php echo $startDateText; ?></label>
									<input type="text" class="form-control" name="adStartDate" id="adStartDate" value="<?php echo $starts; ?>" />
									<span class="help-block"><?php echo $adStartDateHelp; ?></span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="adEndDate"><?php echo $endDateText; ?></label>
									<input type="text" class="form-control" name="adEndDate" id="adEndDate" value="<?php echo $ends; ?>" />
									<span class="help-block"><?php echo $adEndDateHelp; ?></span>
								</div>
							</div>
						</div>
						<?php if ($row['adImage'] == '') { ?>
							<div class="form-group">
								<label for="adText"><?php echo $adTextField; ?></label>
								<textarea class="form-control" name="adText" rows="4"><?php echo clean($row['adText']); ?></textarea>
								<span class="help-block"><?php echo $adTextFieldHelp; ?></span>
							</div>
						<?php } else { ?>
							<input name="adText" type="hidden" />
						<?php } ?>
						<button type="input" name="submit" value="saveAd" class="btn btn-success btn-lg btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveUpdatesBtn; ?></button>
					</form>
				</div>
			</div>
		</div>
	</section>
<?php } ?>