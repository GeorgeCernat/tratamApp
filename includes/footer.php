	<section id="bottom">
		<div class="container">
			<div class="row">
				<div class="col-md-2 text-center">
					<?php
						$ad1 = "SELECT
									adImage, adTitle, adUrl,
									adStartDate, adEndDate, isActive
								FROM
									ads
								WHERE
									(isActive = 1 OR
									adStartDate <= DATE_SUB(CURDATE(),INTERVAL 0 DAY) AND
									adEndDate >= DATE_SUB(CURDATE(),INTERVAL 0 DAY)) AND
									adType = 3
								ORDER BY RAND()
								LIMIT 1";
						$adres1 = mysqli_query($mysqli, $ad1) or die('-89' . mysqli_error());

						if(mysqli_num_rows($adres1) > 0) {
							while ($ad1 = mysqli_fetch_assoc($adres1)) {
								echo '
										<a href="'.clean($ad1['adUrl']).'" data-toggle="tooltip" data-placement="top" title="'.$advertisementText.'">
											<img alt="'.clean($ad1['adTitle']).'" src="'.$adsPath.clean($ad1['adImage']).'" class="img-responsive" />
										</a>
									';
							}
						}	
					?>
				</div>
				<div class="col-md-2 text-center">
					<?php
						$ad2 = "SELECT
									adImage, adTitle, adUrl,
									adStartDate, adEndDate, isActive
								FROM
									ads
								WHERE
									(isActive = 1 OR
									adStartDate <= DATE_SUB(CURDATE(),INTERVAL 0 DAY) AND
									adEndDate >= DATE_SUB(CURDATE(),INTERVAL 0 DAY)) AND
									adType = 3
								ORDER BY RAND()
								LIMIT 1";
						$adres2 = mysqli_query($mysqli, $ad2) or die('-88' . mysqli_error());

						if(mysqli_num_rows($adres2) > 0) {
							while ($ad2 = mysqli_fetch_assoc($adres2)) {
								echo '
										<a href="'.clean($ad2['adUrl']).'" data-toggle="tooltip" data-placement="top" title="'.$advertisementText.'">
											<img alt="'.clean($ad2['adTitle']).'" src="'.$adsPath.clean($ad2['adImage']).'" class="img-responsive" />
										</a>
									';
							}
						}	
					?>
				</div>
			</div>
		</div>
	</section>

	<section id="footer">
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center">
					<p><?php echo $footerLine1.' '.date('Y'); ?> <?php echo $set['siteName']; ?>. <?php echo $footerLine2; ?></p>
				</div>
			</div>
		</div>
	</section>


	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/datatables.js"></script>
	<?php if (isset($datePicker)) { echo '<script type="text/javascript" src="js/datetimepicker.js"></script>'; } ?>
	<script type="text/javascript" src="js/custom.js"></script>
	<?php if (isset($jsFile)) { echo '<script type="text/javascript" src="js/includes/'.$jsFile.'.js"></script>'; } ?>
	<?php if (isset($homePage)) { echo '<script type="text/javascript" src="js/includes/home.js"></script>'; } ?>
	
	<?php echo htmlspecialchars_decode($set['analyticsCode']); ?>
	
</body>
</html>