$( function() {
    init();

	$("#frm_travel_sheet_status").on('submit',function(e){
        e.preventDefault();
        $('.loadingOverlay').show();
        $.ajax({
            dataType: 'json',
            type:'POST',
            url:  $(this).attr("action"),
            data:  $(this).serialize(),
        }).done(function(data, textStatus, xhr){
            if (data.status == 'failed') {
                msg(data.msg,data.status);
                $("#btnDownload").attr("disabled", true);
            } else {
                $("#btnDownload").attr("disabled", false);
                maketblTravelSheetStatusTable(data.ppo);
            }
        }).fail( function(xhr, textStatus, errorThrown) {
            ErrorMsg(xhr);
        }).always( function() {
            $('.loadingOverlay').hide();
        });
    });

    $('#btnDownload').on('click', function () {
        alert("");
		window.location.href = downloadExcel+"?date_from="+$('#date_from').val() +"&date_to="+$('#date_to').val();
	});
	
});

function init() {
    if (permission_access == '2' || permission_access == 2) {
        $('.permission').prop('readonly', true);
        $('.permission-button').prop('disabled', true);
    } else {
        $('.permission').prop('readonly', false);
        $('.permission-button').prop('disabled', false);
    }

    maketblTravelSheetStatusTable();
}

function maketblTravelSheetStatusTable(arr) {
    $('#tbl_travel_sheet_status').dataTable().fnClearTable();
    $('#tbl_travel_sheet_status').dataTable().fnDestroy();
    $('#tbl_travel_sheet_status').dataTable({
        data: arr,
        lengthMenu: [
			[5, 10, 15, 20, -1],
			[5, 10, 15, 20, "All"]
		],
        pageLength: 10,
        order: [[0,'desc']],
        columns: [ 
            { data: 'SC', name: 'SC'},
            { data: 'JO', name: 'JO'},
            { data: 'ProductCode', name: 'ProductCode'},
            { data: 'Description', name: 'Description'},
            { data: 'BasedQty', name: 'BasedQty'},
            { data: 'ProdOutputQty', name: 'ProdOutputQty'},
            { data: 'Remaining', name: 'Remaining'},
            { data: 'CurrentProcess', name: 'CurrentProcess'},
            { data: 'Status', name: 'Status'},
            { data: 'FGStocks', name: 'FGStocks'},
            { data: 'CRUDEStocks', name: 'CRUDEStocks'},
        ],
        fnDrawCallback: function() {
            $("#tbl_travel_sheet_status").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        initComplete: function() {
            $('.loadingOverlay').hide();
        }
    });
}