<?php
	// Output headers so that the file is downloaded rather than displayed
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=mailinglist.csv');

	// Create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');

	// Output the column headings
	fputcsv($output, array(
		$idText,
		$emailAddressField,
		$signUpDateText,
		$activeText,
		$ipAddyText
	));

	// Get Data
	$sql = "SELECT
				listId,
				emailAddress,
				DATE_FORMAT(signupDate,'%M %d, %Y') AS signupDate,
				isActive,
				signupIp
			FROM
				mailinglist";
    $res = mysqli_query($mysqli, $sql) or die('-1' . mysqli_error());

	// Loop through the rows
	while ($row = mysqli_fetch_assoc($res)) {
		$items_array = array();
		
		if ($row['isActive'] == '0') { $isActive = $noBtn; } else { $isActive = $yesBtn; }
		
		$items_array[] = $row['listId'];
		$items_array[] = clean($row['emailAddress']);
		$items_array[] = $row['signupDate'];
		$items_array[] = $isActive;
		$items_array[] = $row['signupIp'];

		// Output the Data to the CSV
		fputcsv($output, $items_array);
	}
	exit;
?>