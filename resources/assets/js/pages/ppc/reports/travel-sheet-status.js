$( function() {
    init();
})

function init() {
    $('#status').select2({
        // allowClear: true,
        placeholder: 'Select Status'
    }).val(null);
        
    check_permission(code_permission, function(output) {
        if (output == 1) {}
    });
}