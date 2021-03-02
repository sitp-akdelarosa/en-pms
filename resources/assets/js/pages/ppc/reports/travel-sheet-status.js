$( function() {
    init();
})

function init() {
    $('#status').select2({
        // allowClear: true,
        placeholder: 'Select Status'
    }).val(null);

    if (permission_access == '2' || permission_access == 2) {
        $('.permission').prop('readonly', true);
        $('.permission-button').prop('disabled', true);
    } else {
        $('.permission').prop('readonly', false);
        $('.permission-button').prop('disabled', false);
    }
}