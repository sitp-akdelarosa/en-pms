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
    check_permission(code_permission, function(output) {
        if (output == 1) {}

        makeOperatorTable();
    });
}

function makeOperatorTable(arr) {
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
            { data: 'date', name: 'date'},
            { data: 'mc', name: 'mc'},
            { data: 'jo_no', name: 'jo_no'},
            { data: 'item', name: 'item'},
            { data: 'alloy', name: 'alloy'},
            { data: 'size', name: 'size'},
            { data: 'class', name: 'class'},
            { data: 'heatno', name: 'heatno'},
            { data: 'total', name: 'total'},
            { data: 'oGood', name: 'oGood'},
            { data: 'oRework', name: 'oRework'},
            { data: 'oScrap', name: 'oScrap'},
            { data: 'weight', name: 'weight'},
            { data: 'wGood', name: 'wGood'},
            { data: 'wRework', name: 'wRework'},
            { data: 'wScrap', name: 'wScrap'},
            { data: 'rRework', name: 'rRework'},
            { data: 'rScrap', name: 'rScrap'},
            { data: 'jo_no', name: 'jo_no'},
        ],
        fnDrawCallback: function() {
            $("#tbl_summary").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        initComplete: function() {
            $('.loadingOverlay').hide();
        }
    });
}