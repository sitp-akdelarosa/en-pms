$( function() {
    getAuditTrailData();
    init();
});

function init() {
	check_permission(code_permission, function(output) {
		if (output == 1) {}
	});
}