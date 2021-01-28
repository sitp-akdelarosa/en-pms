var FG_arr = [];
$( function() {
    init();

    $('#tbl_fg_summary').on('click', '.btn_edit_fg', function() {
		$('#id').val($(this).attr('data-id'));
        $('#current_sc_no').val($(this).attr('data-sc_no'));
		$('#prod_code').val($(this).attr('data-prod_code'));
		$('#description').val($(this).attr('data-description'));
		$('#order_qty').val($(this).attr('data-order_qty'));
		$('#qty').val($(this).attr('data-qty'));
		get_sc_no($(this).attr('data-sc_no'),$(this).attr('data-prod_code'));
		$('#modal_fg_summary').modal('show');
	});

	$("#frm_fg_summary").on('submit',function(e){ e.preventDefault();
        if(parseInt($('#qty').val()) > parseInt($('#order_qty').val())){
            msg('Qty need to be less than order qty' , 'warning');
        }else if(parseInt($('#qty').val()) > parseInt($('#total_order_qty').val())){
            msg('Qty need to be less than order qty of SC # selected' , 'warning');
        }else if(parseInt($('#qty').val()) < 0 || parseInt($('#qty').val()) == 0 ){
            msg('Please input valid number' , 'warning');
        }else{
            var form_action = $(this).attr("action");
            $.ajax({
                dataType: 'json',
                type:'POST',
                url: form_action,
                data:  $(this).serialize(),
            }).done(function(data, textStatus, xhr){
                msg(data.msg,data.status);
                getFG($('#status').val());
                if(data.status == 'success'){
                    $('#modal_fg_summary').modal('hide');
                }
            }).fail( function(xhr, textStatus, errorThrown) {
                var errors = xhr.responseJSON.errors;
                showErrors(errors);
            });
        }       
    });


    $('#qty').on('change', function(e) { e.preventDefault();
        if(parseInt($(this).val()) > parseInt($('#order_qty').val())){
            msg('Qty need to be less than order qty' , 'warning');
        }else if(parseInt($(this).val()) > parseInt($('#total_order_qty').val())){
            msg('Qty need to be less than order qty of SC # selected' , 'warning');
        }       
    });

    $('#sc_no').on('change', function(e) { e.preventDefault();
        $('#total_order_qty').val($(this).find("option:selected").attr('data-order_qty'));
    });

    $('#status').on('change', function(e) {
        getFG($(this).val());
    });

});

function init() {
    check_permission(code_permission, function(output) {
        if (output == 1) {}

        getFG(0);
    });
}

function getFG(status) {
    transfer_item_arr = [];
    $.ajax({
        url: getFGURL,
        type: 'GET',
        dataType: 'JSON',
        data: {
            _token: token,status:status
        },
    }).done(function(data, textStatus, xhr) {
        FG_arr = data;
        makeFGTable(FG_arr);
    }).fail(function(xhr, textStatus, errorThrown) {
        msg(errorThrown,textStatus);
    });
}

function makeFGTable(arr) {
    $('#tbl_fg_summary').dataTable().fnClearTable();
    $('#tbl_fg_summary').dataTable().fnDestroy();
    $('#tbl_fg_summary').dataTable({
        data: arr,
        bLengthChange : false,
        searching: true,
        paging: true,
        columns: [ 
		    { data: function(x) {
                return '<button class="btn btn-sm bg-blue btn_edit_fg" '+
                            'data-id="'+x.id+'" '+
                            'data-sc_no="'+x.sc_no+'" '+
                            'data-prod_code="'+x.prod_code+'" '+
                            'data-description="'+x.description+'" '+
                            'data-order_qty="'+x.order_qty+'" '+
                            'data-qty="'+x.qty+'">'+
                            '<i class="fa fa-edit"></i>'+
                        '</button>';
            }, searchable: false, orderable: false },
            {data: 'sc_no', name: 'sc_no'},
		    {data: 'prod_code', name: 'prod_code'},
		    {data: 'description', name: 'description'},
		    {data: 'order_qty', name: 'order_qty'},
		    {data: 'qty', name: 'qty'},
        ],
    });
}

function get_sc_no(sc_nos,prod_code) {
	var sc_no = '<option></option>';
	$('#sc_no').html(sc_no);
	$.ajax({
		url: getSc_noURL,
		type: 'GET',
		dataType: 'JSON',
		data: {_token: token,prod_code:prod_code},
	}).done(function(data, textStatus, xhr) {
		$.each(data, function(i, x) {
            if(x.sc_no != sc_nos){
                sc_no = '<option value="'+x.sc_no+'" data-order_qty="'+x.order_qty+'">'+x.sc_no+'</option>';
                $('#sc_no').append(sc_no);
            }
		});
	}).fail(function(xhr, textStatus, errorThrown) {
		msg(errorThrown,textStatus);
	});
}

function clear() {
    $('.clear').val('');
}