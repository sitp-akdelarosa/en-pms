$( function() {
    init();

	$("#frm_summary").on('submit',function(e){
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
                makeSummaryTable(data.ppo);
            }
        }).fail( function(xhr, textStatus, errorThrown) {
            ErrorMsg(xhr);
        }).always( function() {
            $('.loadingOverlay').hide();
        });
    });

    $('#btnDownload').on('click', function () {
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

    makeSummaryTable();
}

function makeSummaryTable(arr) {
    $('#tbl_summary').dataTable().fnClearTable();
    $('#tbl_summary').dataTable().fnDestroy();
    $('#tbl_summary').dataTable({
        data: arr,
        lengthMenu: [
			[5, 10, 15, 20, -1],
			[5, 10, 15, 20, "All"]
		],
        pageLength: 10,
        order: [[9,'desc']],
        columns: [ 
            { data: 'date_upload', name: 'date_upload'},
            { data: 'sc_no', name: 'sc_no'},
            { data: 'prod_code', name: 'prod_code'},
            { data: 'description', name: 'description'},
            { data: 'alloy', name: 'alloy'},
            { data: 'size', name: 'size'},
            { data: 'class', name: 'class'},
            { data: 'heatno', name: 'heatno'},
            { data: 'quantity', name: 'quantity'},
            { data: 'good', name: 'good'},
            { data: 'rework', name: 'rework'},
            { data: 'scrap', name: 'scrap'},
            { data: 'finish_weight', name: 'finish_weight'},
            { data: 'wgood', name: 'wgood'},
            { data: 'wrework', name: 'wrework'},
            { data: 'wscrap', name: 'wscrap'},
            { data: 'rrework', name: 'rrework'},
            { data: 'rscrap', name: 'rscrap'},
            { data: 'jono', name: 'jono'},
        ],
        fnDrawCallback: function() {
            $("#tbl_summary").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        initComplete: function() {
            $('.loadingOverlay').hide();
        }
    });
}