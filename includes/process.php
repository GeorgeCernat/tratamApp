<?php
	// Access DB Info
	include('../config.php');

	// Set some variables to empty
	$likeType = $confId = $voteIp = $theIp = $hasVoted1 = $hasVoted2 = $hasVoted3 = $hasVoted4 = '';

	// Get Today's Date
	$voteDate = date("Y-m-d H:i:s");

	// Get the Confession Id
	$confId = $mysqli->real_escape_string($_POST['confId']);

	// Get the voters IP Address
	$theIp = $_SERVER['REMOTE_ADDR'];

	// Get which vote was clicked
	if (isset($_POST['isLiked']) && $_POST['isLiked'] == '1') {
		$likeType = 'likeIt';
	} else if ($_POST["isDisliked"] == '2') {
		$likeType = 'dislikeIt';
	} else {

	}

	switch ($likeType) {
		case 'likeIt':
			// Check for votes by IP and ConfessionId
			$chkLikes = mysqli_query($mysqli,"SELECT 'X' FROM likes WHERE confessId = ".$confId." AND likeIp = '".$theIp."' LIMIT 1");
			$hasLike = mysqli_num_rows($chkLikes);
			if ($hasLike == 0) { $hasVoted1 = '0'; }

			$chkDislikes = mysqli_query($mysqli,"SELECT 'X' FROM dislikes WHERE confessId = ".$confId." AND dislikeIp = '".$theIp."' LIMIT 1");
			$hasDislike = mysqli_num_rows($chkDislikes);
			if ($hasDislike == 0) { $hasVoted2 = '0'; }

			// If the have not all ready voted, allow the vote
			if (($hasVoted1 == '0') && ($hasVoted2 == '0')) {
				$stmt = $mysqli->prepare("
									INSERT INTO
										likes(
											confessId,
											likeDate,
											likeIp
										) VALUES (
											?,
											?,
											?
										)
				");
				$stmt->bind_param('sss',
					$confId,
					$voteDate,
					$theIp
				);
				$stmt->execute();
				$stmt->close();

				// Get the new vote count
				$totLikes = mysqli_query($mysqli,"SELECT 'X' FROM likes WHERE confessId = ".$confId);
				$likesTotal = mysqli_num_rows($totLikes);

				if ($likesTotal > 0) {
					echo ($likesTotal);
				} else {
					echo 0;
				}
            } else {
				// else do nothing
				exit();
			}
		break;
		case 'dislikeIt':
			// Check for votes by IP and ConfessionId
			$chkLikes = mysqli_query($mysqli,"SELECT 'X' FROM likes WHERE confessId = ".$confId." AND likeIp = '".$theIp."' LIMIT 1");
			$hasLike = mysqli_num_rows($chkLikes);
			if ($hasLike == 0) { $hasVoted3 = '0'; }

			$chkDislikes = mysqli_query($mysqli,"SELECT 'X' FROM dislikes WHERE confessId = ".$confId." AND dislikeIp = '".$theIp."' LIMIT 1");
			$hasDislike = mysqli_num_rows($chkDislikes);
			if ($hasDislike == 0) { $hasVoted4 = '0'; }

			// If the have not all ready voted, allow the vote
			if (($hasVoted3 == '0') && ($hasVoted4 == '0')) {
                $stmt = $mysqli->prepare("
									INSERT INTO
										dislikes(
											confessId,
											dislikeDate,
											dislikeIp
										) VALUES (
											?,
											?,
											?
										)
				");
				$stmt->bind_param('sss',
					$confId,
					$voteDate,
					$theIp
				);
				$stmt->execute();
				$stmt->close();

				// Get the new vote count
				$totDislikes = mysqli_query($mysqli,"SELECT 'X' FROM dislikes WHERE confessId = ".$confId);
				$dislikesTotal = mysqli_num_rows($totDislikes);

				if ($dislikesTotal > 0) {
					echo ($dislikesTotal);
				} else {
					echo 0;
				}
            } else {
				// else do nothing
				exit();
			}
		break;
	}
?>