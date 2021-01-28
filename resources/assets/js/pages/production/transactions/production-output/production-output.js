var prod_output_arr = [];
var searched_jo_arr = [];

$( function() {

    makeSearchTable(searched_jo_arr);
    checkAllCheckboxesInTable('.check_all','.check_item');
	init();

	$('#tbl_searched_jo_body').on('click', '.btn_edit_travel_sheet',function() {
        $('#travel_sheet_process_id').val($(this).attr('data-id'));
        $('#travel_sheet_id').val($(this).attr('data-travel_sheet_id'));
        $('#jo_sequence').val($(this).attr('data-jo_sequence'));
        $('#jo_no').val($(this).attr('data-jo_no'));
        $('#prod_order').val($(this).attr('data-prod_order_no'));
        $('#prod_code').val($(this).attr('data-prod_code'));
        $('#description').val($(this).attr('data-description'));
        $('#mat_used').val($(this).attr('data-material_used'));
        $('#material_heat_no').val($(this).attr('data-material_heat_no'));
        $('#lot_no').val($(this).attr('data-lot_no'));
        $('#type').val($(this).attr('data-type'));
        $('#order_qty').val($(this).attr('data-order_qty'));
        $('#issued_qty').val($(this).attr('data-issued_qty'));
        $('#prev_process').val($(this).attr('data-previous_process'));
        $('#current_process').val($(this).attr('data-process'));
        $('#sequence').val($(this).attr('data-sequence'));
        $('#unprocessed').val($(this).attr('data-unprocessed'));
        getTransferQty($(this).attr('data-id'));
        getOutputs($(this).attr('data-id'));
		$('#modal_production_output').modal('show');
	});

    $('#btn_delete_set').on('click', function() {
        var id;
        $(".check_item:checked").each(function() {
            id = ($(this).attr('data-travel_sheet_process_id'));
        });
        if ($('.check_item:checked').length != 0){
            $.ajax({
                url: checkSequence,
                type: 'POST',
                dataType: 'JSON',
                data: {_token: token,id:id},
            }).done(function(data, textStatus, xhr) {
                if(data.status == 'success'){
                    delete_set();
                }else{
                    msg('The quantity already done or ongoing to other processes','failed')
                }
            }).fail(function(xhr, textStatus, errorThrown) {
                ErrorMsg(xhr);
            });
        }else{
            msg('Please select at least 1 item' , 'warning');
        }
    });

    $('#frm_search_jo').on('submit', function(e) {
        e.preventDefault();
        $('.loadingOverlay').show();
        searched_jo_arr = [];
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            dataType: 'JSON',
            data: $(this).serialize(),
        }).done(function(data, textStatus, xhr) {
            if (data.status == 'failed') {
                makeSearchTable([]);
                msg(data.msg,data.status);
            } else {
                searched_jo_arr = data.jo;
                makeSearchTable(searched_jo_arr);
            }
        }).fail(function(xhr, textStatus, errorThrown) {
            makeSearchTable([]);
            ErrorMsg(xhr);
        }).always( function(xhr,textStatus) {
            $('.loadingOverlay').hide();
        });      
    });

    $('#search_jo').on('keyup',function() {
        if ($(this).val().trim() == "") {
            makeSearchTable([]);
        } 
    })

    $("#frm_prod_output").on('submit',function(e){ 
        e.preventDefault();
        var unprocessed = parseInt($('#unprocessed').val());
        var qtyTransfer = parseInt($('#total_qty_transfer').val());
        var qty = (qtyTransfer == 0)? unprocessed:( unprocessed - qtyTransfer);
        unprocessed = unprocessed - (parseInt($('#rework').val()) + parseInt($('#scrap').val()) + parseInt($('#good').val()));
        qty = qty - (parseInt($('#rework').val()) + parseInt($('#scrap').val()) + parseInt($('#good').val()));
        //alert(qty);
        if(parseInt($('#unprocessed').val()) == unprocessed){
            msg('Please put some value on good , rework or scrap' , 'warning');
        }else if(unprocessed < 0 && $('#current_process').val() !== 'CUTTING'){
            msg("Please Input less than unprocessed","warning");
        }else if(qtyTransfer > 0){
            msg("The process has a pending Transfer Item of "+qtyTransfer,"warning");
        }else if($('#good').val() < 0 || $('#scrap').val() < 0 || $('#rework').val() < 0 || $('#nc').val() < 0 || $('#alloy_mix').val() < 0 || $('#convert').val() < 0){
            msg("Please Input valit number","warning");
        }else{
            $('.loadingOverlay').show();
            $.ajax({
                dataType: 'json',
                type:'POST',
                url: $(this).attr("action"),
                data:  $(this).serialize()
            }).done(function(data, textStatus, xhr){
                prod_output_arr = [];
                prod_output_arr = data.outputs;
                makeProdOutputTable(prod_output_arr);
                searched_jo_arr = [];
                searched_jo_arr = data.travel_sheet;
                makeSearchTable(searched_jo_arr);
                msg(data.msg, data.status);
                zero_it();
                clear();
                if (data.unprocessed !== undefined) {
                    $('#unprocessed').val(data.unprocessed);
                }     
            }).fail( function(xhr, textStatus, errorThrown) {
                ErrorMsg(xhr);
            }).always( function(xhr,textStatus) {
                $('.loadingOverlay').hide();
            });
        }

    });

    $('#good').on('change', function() {
        if ($(this).val() == '') {
            $(this).val(0);
        } else {
            deductUnprocessed('good',$(this).val());
        }
    });

    $('#scrap').on('change', function() {
        if ($(this).val() == '') {
            $(this).val(0);
        } else {
            deductUnprocessed('scrap',$(this).val());
        }
    });

    $('#rework').on('change', function() {
        if ($(this).val() == '') {
            $(this).val(0);
        } else {
            deductUnprocessed('rework',$(this).val());
        }
    });

    $('#operator').on('change', function() {
        getOperator();
    });

});

function init() {
    check_permission(code_permission, function(output) {
        if (output == 1) {}
    });
}

function deductUnprocessed(el_name,value) {
    if (isNaN(value)) {
        $('#'+el_name).addClass('is-invalid');
        $('#'+el_name+'_feedback').addClass('invalid-feedback');
        $('#'+el_name+'_feedback').html("Please input numbers only.");
    } else {
        var unprocessed = parseInt($('#unprocessed').val());
        unprocessed = unprocessed - (parseInt($('#rework').val()) + parseInt($('#scrap').val()) + parseInt($('#good').val()));
        if(unprocessed < 0 && $('#current_process').val() !== 'CUTTING'){
            msg("Please Input less than unprocessed","warning");
        }
    }
}

function zero_it() {
    $('.zero').val(0);
}

function clear() {
	$('.clear').val('');
}

function getOutputs(id) {
    prod_output_arr = [];
    $.ajax({
        url: getOutputsURL,
        type: 'GET',
        dataType: 'JSON',
        data: {
            _token: token,
            id: id
        },
    }).done(function(data, textStatus, xhr){
        prod_output_arr = data;
        makeProdOutputTable(prod_output_arr);
    }).fail( function(xhr, textStatus, errorThrown) {
        ErrorMsg(xhr);
    });
}

function makeProdOutputTable(arr) {
	$('#tbl_production_ouput').dataTable().fnClearTable();
    $('#tbl_production_ouput').dataTable().fnDestroy();
    $('#tbl_production_ouput').dataTable({
        data: arr,
        bLengthChange : false,
        searching: false,
	    paging: false,
        order: [[9,'asc']],
        columns: [ 
        	{ data: function(x) {
                return "<input type='checkbox' class='table-checkbox check_item' data-travel_sheet_id='"+x.travel_sheet_id+"' data-travel_sheet_process_id='"+x.travel_sheet_process_id+"' value='"+x.id+"'>";
            }, searchable: false, orderable: false },
            {data:'unprocessed'},
            {data:'good'},
            {data:'rework'},
            {data:'scrap'},
            {data:'convert'},
            {data:'alloy_mix'},
            {data:'nc'},
            {data: 'total'},
            {data:'process_date'},
        ],
        fnInitComplete: function() {
            $('.dataTables_scrollBody').slimscroll();
        },
    });
}

function delete_set() {
	var chkArray = [];
	$(".check_item:checked").each(function() {
        chkArray.push({
            travel_sheet_process_id: $(this).attr('data-travel_sheet_process_id'),
            travel_sheet_id: $(this).attr('data-travel_sheet_id'),
            id: $(this).val()

        });
	});

	if (chkArray.length > 0) {
		$.ajax({
			url: deleteProductonOutput,
			type: 'POST',
			dataType: 'JSON',
			data: {_token: token,chkArray:chkArray},
		}).done(function(data, textStatus, xhr) {
			msg("Production Output was successfully deleted.", 'success');
            $('#unprocessed').val(data.unprocessed);
			getOutputs( $('#travel_sheet_process_id').val());
            searched_jo_arr = [];
            searched_jo_arr = data.travel_sheet;
            makeSearchTable(searched_jo_arr);
            clear();
		}).fail(function(xhr, textStatus, errorThrown) {
			ErrorMsg(xhr);
		});
		
	}
    $('.check_all').prop('checked',false);
}

function makeSearchTable(arr) {
    console.log(arr);
    $('#tbl_searched_jo').dataTable().fnClearTable();
    $('#tbl_searched_jo').dataTable().fnDestroy();
    $('#tbl_searched_jo').dataTable({
        data: arr,
        bLengthChange : false,
        searching: false,
        paging: false,
        order: [[1,'asc']],
        columns: [ 
            { data: function(x) {
                var disabled = 'disabled';

                if (x.unprocessed > 0) {
                    disabled = '';
                }
                return "<button class='btn btn-sm bg-blue btn_edit_travel_sheet' "+
                            "data-travel_sheet_id='"+x.travel_sheet_id+"' "+
                            "data-id='"+x.id+"' "+
                            "data-jo_no='"+x.jo_no+"' "+
                            "data-jo_sequence='"+x.jo_sequence+"' "+
                            "data-prod_order_no='"+x.prod_order_no+"' "+
                            "data-material_used='"+x.material_used+"' "+
                            "data-material_heat_no='"+x.material_heat_no+"' "+
                            "data-lot_no='"+x.lot_no+"' "+
                            "data-type='"+x.type+"' "+
                            "data-order_qty='"+x.order_qty+"' "+
                            "data-previous_process='"+x.previous_process+"' "+
                            "data-process='"+x.process+"' "+
                            "data-sequence='"+x.sequence+"' "+
                            "data-unprocessed='"+x.unprocessed+"' "+ 
                            "data-prod_code='"+x.prod_code+"' "+
                            "data-description='"+x.description+"' "+
                            "data-total_issued_qty='"+x.total_issued_qty+"' "+
                            "data-issued_qty='"+x.issued_qty+"' "+
                            "data-sc_no='"+x.sc_no+"' "+
                            ""+disabled+">"+
                            "<i class='fa fa-edit'></i>"+
                        "</button>";
            }, searchable: false, orderable: false, width: '3.33%' },
            { data:'jo_no', width: '8.33%'},
            { data:'jo_sequence', width: '10.33%'},
            { data:'prod_code', width: '11.33%'},
            { data:'div_code', width: '8.33%'},
            { data:'issued_qty', width: '8.33%'},
            { data:'process', width: '8.33%'},
            { data:'unprocessed', width: '8.33%'},
            { data:'good', width: '8.33%'},
            { data:'rework', width: '8.33%'},
            { data:'scrap', width: '8.33%'},
            { data: function(x) {
                var status = 'ON PROCESS';
                if (x.status == 1) {
                    status = 'DONE'; //READY FOR FG
                }else if(x.status == 5){
                    status = 'FINISHED';
                }else if(x.status == 3){
                    status = 'TRANSFER ITEM';
                }
                return status;
            }, width: '8.33%'},
        ],
        createdRow: function(row, data, dataIndex) {
            if (data.status == 1 || data.status == '1') {
                $(row).css('background-color', 'rgb(139 241 191)'); // GREEN
				$(row).css('color', '#000000');
            }
        },
    });
}

function getOperator() {
    $.ajax({
        url: getOperatorURl,
        type: 'POST',
        dataType: 'JSON',
        data: {_token: token,operator:$('#operator').val()},
    }).done(function(data, textStatus, xhr) {
        console.log('success');
    }).fail( function(xhr, textStatus, errorThrown) {
        ErrorMsg(xhr);
    });
}

function getTransferQty(id) {
    $.ajax({
        url: getTransferQtyURL,
        type: 'POST',
        dataType: 'JSON',
        data: {_token: token,id:id},
    }).done(function(data, textStatus, xhr) {
        $('#total_qty_transfer').val(data);
    }).fail( function(xhr, textStatus, errorThrown) {
        ErrorMsg(xhr);
    });
}