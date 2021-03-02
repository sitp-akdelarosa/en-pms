$( function() {
    init();

	$("#frm_operator").on('submit',function(e){
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
                makeOperatorTable(data.ppo);
            }
        }).fail( function(xhr, textStatus, errorThrown) {
            ErrorMsg(xhr);
        }).always( function() {
            $('.loadingOverlay').hide();
        });
    });

    $('#btnDownload').on('click', function () {
        alert("");
		window.location.href = downloadExcel+"?date_from="+$('#date_from').val() +"&date_to="+$('#date_to').val() +"&search_operator="+$('#search_operator').val();
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

    makeOperatorTable();
}

function makeOperatorTable(arr) {
    $('#tbl_operator').dataTable().fnClearTable();
    $('#tbl_operator').dataTable().fnDestroy();
    $('#tbl_operator').dataTable({
        data: arr,
        lengthMenu: [
			[5, 10, 15, 20, -1],
			[5, 10, 15, 20, "All"]
		],
        pageLength: 10,
        order: [[9,'desc']],
        columns: [ 
            { data: 'jo_no', name: 'jo_no'},
		    { data: 'prod_code', name: 'prod_code'},
		    { data: 'total_issued_qty', name: 'total_issued_qty'},
		    { data: 'previous_process', name: 'previous_process'},
		    { data: 'current_process', name: 'current_process'},
		    { data: 'unprocessed', name: 'unprocessed'},
		    { data: 'good', name: 'good'},
		    { data: 'rework', name: 'rework'},
		    { data: 'scrap', name: 'scrap'},
            { data: 'updated_at', name: 'updated_at'},
        ],
        fnDrawCallback: function() {
            $("#tbl_operator").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        initComplete: function() {
            $('.loadingOverlay').hide();
        }
    });
}