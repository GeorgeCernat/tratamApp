$(document).ready(function() {
    $('#adStartDate').datetimepicker({
		format: 'yyyy-mm-dd',
		todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		minView: 2,
		forceParse: 0
	});
	
	$('#adEndDate').datetimepicker({
		format: 'yyyy-mm-dd',
		todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		minView: 2,
		forceParse: 0
	});
	
	$('#adData').dataTable({
		"pageLength": 10,
		"pagingType": "simple_numbers",
		"order": [[3,'desc'], [2,'desc']]
	});
});