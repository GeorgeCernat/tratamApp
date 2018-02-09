<?php
	$msgBox = '';

	// Update Global Site Settings
    if (isset($_POST['submit']) && $_POST['submit'] == 'updateSettings') {
        // Validation
		if($_POST['installUrl'] == "") {
            $msgBox = alertBox($installUrlMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['siteName'] == "") {
            $msgBox = alertBox($siteNameMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['siteEmail'] == "") {
            $msgBox = alertBox($siteEmalMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if(($_POST['allowUploads'] == "1") && ($_POST['uploadPath'] == "")) {
			$msgBox = alertBox($uploadsPathReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if(($_POST['allowUploads'] == "1") && ($_POST['fileTypesAllowed'] == "")) {
			$msgBox = alertBox($uploadFileTypesReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if(($_POST['enableAds'] == "1") && ($_POST['adsPath'] == "")) {
			$msgBox = alertBox($adPathReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if(($_POST['enableAds'] == "1") && ($_POST['adTypesAllowed'] == "")) {
			$msgBox = alertBox($adsFileTypesReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			// Add the trailing slash if there is not one
			$installUrl = $mysqli->real_escape_string($_POST['installUrl']);
			$uploadPath = $mysqli->real_escape_string($_POST['uploadPath']);
			$adsPath = $mysqli->real_escape_string($_POST['adsPath']);
			if(substr($installUrl, -1) != '/') { $install = $installUrl.'/'; } else { $install = $installUrl; }
			if(substr($uploadPath, -1) != '/') { $uploadsDir = $uploadPath.'/'; } else { $uploadsDir = $uploadPath; }
			if(substr($adsPath, -1) != '/') { $adsDir = $adsPath.'/'; } else { $adsDir = $adsPath; }

			$localization = $mysqli->real_escape_string($_POST['localization']);
			$siteName = $mysqli->real_escape_string($_POST['siteName']);
			$siteEmail = $mysqli->real_escape_string($_POST['siteEmail']);
			$analyticsCode = htmlspecialchars($_POST['analyticsCode']);
			$fileTypesAllowed = $mysqli->real_escape_string($_POST['fileTypesAllowed']);
			$adTypesAllowed = $mysqli->real_escape_string($_POST['adTypesAllowed']);
			$moderation = $mysqli->real_escape_string($_POST['moderation']);
			$useFilter = $mysqli->real_escape_string($_POST['useFilter']);
			$allowRegistrations = $mysqli->real_escape_string($_POST['allowRegistrations']);
			$allowUploads = $mysqli->real_escape_string($_POST['allowUploads']);
			$enableAds = $mysqli->real_escape_string($_POST['enableAds']);
			$aboutUs = htmlspecialchars($_POST['aboutUs']);
			$siteRules = htmlspecialchars($_POST['siteRules']);

            $stmt = $mysqli->prepare("
                                UPDATE
                                    sitesettings
                                SET
									installUrl = ?,
									localization = ?,
									siteName = ?,
									analyticsCode = ?,
									siteEmail = ?,
									uploadPath = ?,
									fileTypesAllowed = ?,
									adsPath = ?,
									adTypesAllowed = ?,
									moderation = ?,
									useFilter = ?,
									allowRegistrations = ?,
									allowUploads = ?,
									enableAds = ?,
									aboutUs = ?,
									siteRules = ?
			");
            $stmt->bind_param('ssssssssssssssss',
								   $install,
								   $localization,
								   $siteName,
								   $analyticsCode,
								   $siteEmail,
								   $uploadsDir,
								   $fileTypesAllowed,
								   $adsDir,
								   $adTypesAllowed,
								   $moderation,
								   $useFilter,
								   $allowRegistrations,
								   $allowUploads,
								   $enableAds,
								   $aboutUs,
								   $siteRules
			);
            $stmt->execute();
			$msgBox = alertBox($siteSettingsSavedMsg, "<i class='fa fa-check-square'></i>", "success");
            $stmt->close();
		}
	}

	// Get Data
	$sqlStmt = "SELECT * FROM sitesettings";
	$res = mysqli_query($mysqli, $sqlStmt) or die('-1' . mysqli_error());
	$row = mysqli_fetch_assoc($res);

	if ($row['localization'] == 'en') { $en = 'selected'; } else { $en = ''; }

	if ($row['moderation'] == '1') { $moderation = 'selected'; } else { $moderation = ''; }
	if ($row['useFilter'] == '1') { $useFilter = 'selected'; } else { $useFilter = ''; }
	if ($row['allowRegistrations'] == '1') { $allowReg = 'selected'; } else { $allowReg = ''; }
	if ($row['allowUploads'] == '1') { $allowUploads = 'selected'; } else { $allowUploads = ''; }
	if ($row['enableAds'] == '1') { $enableAds = 'selected'; } else { $enableAds = ''; }

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
			<h3 class="mb-20"><?php echo $siteSettingsPageTitle; ?></h3>
			<?php if ($msgBox) { echo $msgBox; } ?>
			
			<form action="" method="post">
				<h5><?php echo $appSettingsTitle; ?></h5>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="installUrl"><?php echo $installUrlField; ?></label>
							<input type="text" class="form-control" required="" name="installUrl" value="<?php echo $row['installUrl']; ?>" />
							<span class="help-block"><?php echo $installUrlHelper; ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="localization"><?php echo $localizationField; ?></label>
							<select class="form-control" name="localization">
								<option value="en" <?php echo $en; ?>><?php echo $optionEnglish; ?> &mdash; en.php</option>
							</select>
							<span class="help-block"><?php echo $localizationHelper; ?></span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="siteName"><?php echo $siteNameField; ?></label>
							<input type="text" class="form-control" required="" name="siteName" value="<?php echo clean($row['siteName']); ?>" />
							<span class="help-block"><?php echo $siteNameHelper; ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="siteEmail"><?php echo $siteEmailField; ?></label>
							<input type="text" class="form-control" required="" name="siteEmail" value="<?php echo clean($row['siteEmail']); ?>" />
							<span class="help-block"><?php echo $siteEmailHelper; ?></span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="analyticsCode"><?php echo $googleCodeField; ?></label>
					<textarea class="form-control" name="analyticsCode" rows="4"><?php echo htmlspecialchars_decode($row['analyticsCode']); ?></textarea>
				</div>
				
				<h5><?php echo $advertisingTitle; ?></h5>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="enableAds"><?php echo $adField; ?></label>
							<select class="form-control" name="enableAds">
								<option value="0"><?php echo $noBtn; ?></option>
								<option value="1" <?php echo $enableAds; ?>><?php echo $yesBtn; ?></option>
							</select>
							<span class="help-block"><?php echo $adFieldHelp; ?></span>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="adsPath"><?php echo $adsPathField; ?></label>
							<input type="text" class="form-control" required="" name="adsPath" value="<?php echo clean($row['adsPath']); ?>" />
							<span class="help-block"><?php echo $adsPathFieldHelp; ?></span>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="adTypesAllowed"><?php echo $adFileTypesField; ?></label>
							<input type="text" class="form-control" required="" name="adTypesAllowed" value="<?php echo clean($row['adTypesAllowed']); ?>" />
							<span class="help-block"><?php echo $adFileTypesFieldHelp; ?></span>
						</div>
					</div>
				</div>
				<h5><?php echo $aboutUsTitle; ?></h5>
				<div class="form-group">
					<label for="aboutUs"><?php echo $aboutUsField; ?></label>
					<textarea class="form-control" name="aboutUs" rows="14"><?php echo htmlspecialchars_decode($row['aboutUs']); ?></textarea>
					<span class="help-block"><?php echo $aboutUsFieldHelp; ?></span>
				</div>
				<button type="input" name="submit" value="updateSettings" class="btn btn-success btn-lg btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveSettingsBtn; ?></button>
			</form>
		</div>
	</div>
<?php } ?>