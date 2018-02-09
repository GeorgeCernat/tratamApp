$(document).ready(function() {
	$('#hideIt').hide();

	// Show the Password field as plain text
	$('#showIt').click(function(e) {
		e.preventDefault();
		$('#password1').prop('type','text');
		$('#password2').prop('type','text');
		$('#showIt').hide();
		$('#hideIt').show();
	});
	// Show the Password field as asterisks
	$('#hideIt').click(function(e) {
		e.preventDefault();
		$('#password1').prop('type','password');
		$('#password2').prop('type','password');
		$('#hideIt').hide();
		$('#showIt').show();
	});

	// Generate Random Password
	$('#generate').click(function (e) {
		e.preventDefault();

		// You can change the password length by changing the
		// integer to the length you want in generatePassword(8).
		var pwd = generatePassword(8);

		// Populates the fields with the new generated password
        $('#password1').val(pwd);
		$('#password2').val(pwd);
    });

	$('#userData').dataTable({
		"pageLength": 10,
		"pagingType": "simple_numbers",
		"order": [[3,'asc'], [4,'desc']]
	});
});