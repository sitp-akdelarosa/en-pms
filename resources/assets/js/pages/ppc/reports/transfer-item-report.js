var transfer_item_arr = [];

$( function() {
    
    init();

});

function init() {
    if (permission_access == '2' || permission_access == 2) {
        $('.permission').prop('readonly', true);
        $('.permission-button').prop('disabled', true);
    } else {
        $('.permission').prop('readonly', false);
        $('.permission-button').prop('disabled', false);
    }

    getTransferEntry();
}

function getTransferEntry() {
    transfer_item_arr = [];
    $.ajax({
        url: getTransferEntryURL,
        type: 'GET',
        dataType: 'JSON',
        data: {
            _token: token
        },
    }).done(function(data, textStatus, xhr) {
        transfer_item_arr = data;
        makeTransferItemTable(transfer_item_arr);
    }).fail(function(xhr, textStatus, errorThrown) {
        msg(errorThrown,textStatus);
    });
}

function makeTransferItemTable(arr) {
    $('#tbl_transfer_item').dataTable().fnClearTable();
    $('#tbl_transfer_item').dataTable().fnDestroy();
    $('#tbl_transfer_item').dataTable({
        data: arr,
        bLengthChange : false,
        searching: true,
        paging: true,
        columns: [ 
            { data: 'jo_no' },
            { data: 'prod_order_no' }, 
            { data: 'prod_code' },
            { data: 'current_div_code' },
            { data: 'current_process_name' },
            { data: 'div_code_code' },
            { data: 'process' },
            { data: 'qty' },
            { data: 'status' },
            { data: 'remarks' },
            { data: function(x) {
                if(x.item_status == 1){
                    return "READY FOR RECEIVE";
                }else if(x.item_status == 2){
                    return "RECEIVED";
                }else if (x.item_status == 3){
                    return "DISAPPROVED";
                }else{
                    return "NOT YET APPROVED";
                }
            }},
            { data: function(x) {
                if(x.item_status == 2){
                    return x.updated_at;
                }else{
                    return "";
                }

            }},
            { data: function(x) {
                if(x.item_status == 2){
                    return x.receive_qty;
                }else{
                    return "";
                }

            }},
            { data: function(x) {
                if(x.item_status == 2){
                    return x.receive_remarks;
                }else{
                    return "";
                }

            }},

        ],
        fnInitComplete: function() {
            $('.dataTables_scrollBody').slimscroll();
        },
    });
}


