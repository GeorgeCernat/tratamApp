<?php
	$msgBox = '';
	include('includes/header.php');
?>
	<section id="main-container">
		<div class="container">
			<?php if ($msgBox) { echo $msgBox; } ?>
			
			<div class="row">
				<div class="col-md-9">
					<?php echo htmlspecialchars_decode($set['siteRules']); ?>
				</div>
				<div class="col-md-3">
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