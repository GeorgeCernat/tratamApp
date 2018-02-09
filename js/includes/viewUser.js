$(document).ready(function() {
	$('#confData').dataTable({
		"pageLength": 10,
		"pagingType": "simple_numbers",
		"order": [[3,'asc'], [2,'desc']]
	});
	
	$('#commentData').dataTable({
		"pageLength": 10,
		"pagingType": "simple_numbers",
		"order": [[3,'asc']]
	});
});