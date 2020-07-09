$( function() {
    check_permission(code_permission);
    makeOperatorTable();

	$("#frm_operator").on('submit',function(e){
        e.preventDefault();
        $.ajax({
            dataType: 'json',
            type:'POST',
            url:  $(this).attr("action"),
            data:  $(this).serialize(),
        }).done(function(data, textStatus, xhr){
            if (data.status == 'failed') {
                msg(data.msg,data.status);
            } else {
            	makeOperatorTable(data.ppo)
            }
        }).fail( function(xhr, textStatus, errorThrown) {
            var errors = xhr.responseJSON.errors;
            showErrors(errors);
        });
    });
	
});

function makeOperatorTable(arr) {
    $('#tbl_operator').dataTable().fnClearTable();
    $('#tbl_operator').dataTable().fnDestroy();
    $('#tbl_operator').dataTable({
        data: arr,
        bLengthChange : false,
        searching: false,
        paging: false,
        columns: [ 
            {data: 'jo_no', name: 'jo_no'},
		    {data: 'prod_code', name: 'prod_code'},
		    {data: 'total_issued_qty', name: 'total_issued_qty'},
		    {data: 'previous_process', name: 'previous_process'},
		    {data: 'current_process', name: 'current_process'},
		    {data: 'unprocessed', name: 'unprocessed'},
		    {data: 'good', name: 'good'},
		    {data: 'rework', name: 'rework'},
		    {data: 'scrap', name: 'scrap'},
            {data: 'created_at', name: 'created_at'},
        ],
    });
}