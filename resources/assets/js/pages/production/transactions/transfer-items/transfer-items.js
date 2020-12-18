var transfer_item_arr = [];
var received_items_arr = [];

$( function() {
    getTransferEntry();
    getReceiveItems();
    checkAllCheckboxesInTable('.check_all_transfer_item','.check_item');
    init();

    $('#jo_no').on('change', function() {
        getJOdetails($(this).val());
    });

    $('#curr_process').on('change', function() {
        checkIfSameDivCode();
        if($(this).val() != ''){
            getDivCodeProcess($('#jo_no').val(),$(this).find("option:selected").text());
            getUnprocessed($(this).find("option:selected").text(),$('#jo_no').val(),$(this).val());
        }else{
            $('#unprocessed').val(0);
        }
    });

    $('#process').on('change', function() {
        DivisionCode($(this).val(),'');
        checkIfSameDivCode();
    });

    $('#div_code').on('change', function() {
        checkIfSameDivCode();
    });

    $('#qty').on('change', function() {
        var qty = parseInt($(this).val());
        var unprocessed = parseInt($('#unprocessed').val());
        if(qty > unprocessed){
            var curr_process = $("#curr_process").find("option:selected").text();
            if (curr_process !== 'CUTTING') {
                msg('Qty must be less than in # of item to transfer','warning');
            }
        }
    });

    $('#btn_add').on('click', function() {
        $('#process').html("<option value=''></option>");
        clear();
        $('#hide_create').show();
        $('#modal_transfer_entry').modal('show');
    });

    $("#frm_transfer_items").on('submit',function(e){
        e.preventDefault();
        transfer_item_arr = [];
        var totalqty = parseInt($('#UnprocessTransfer').val()) + parseInt($('#qty').val());

        if ($('#process').val() == $("#curr_process").find("option:selected").text() && $('#userDivCode').val() == $("#div_code").find("option:selected").text()) {
            msg("You cannot transfer to your division with the same process","warning");
        } else if(parseInt($('#unprocessed').val()) < parseInt($('#UnprocessTransfer').val())){
            totalqty -= parseInt($('#unprocessed').val());
            msg('The total of pending qty is greather than '+totalqty+' to # of item to transfer','warning')
        } else if($('#qty').val() < 0){
            msg("Please Input valit number","warning");
        } else {
            var curr_process = $("#curr_process").find("option:selected").text();

            if(curr_process !== 'CUTTING' && (parseInt($('#qty').val()) > parseInt($('#unprocessed').val()))) {
                msg('Qty must be less than in # of item to transfer','warning');
            } else {
                $('.loadingOverlay-modal').show();
                $.ajax({
                    dataType: 'json',
                    type:'POST',
                    url: $(this).attr("action"), 
                    data:  $(this).serialize()
                }).done(function(data, textStatus, xhr){
                    msg(data.msg, data.status);
                    transfer_item_arr = data.transfer_item;
                    makeTransferItemTable(transfer_item_arr);
                    getReceiveItems();
                    $('#modal_transfer_entry').modal('hide');
                }).fail( function(xhr, textStatus, errorThrown) {
                    ErrorMsg(xhr);
                }).always( function() {
                    $('.loadingOverlay-modal').hide();
                });
            }
        }
    });

    $('#tbl_transfer_entry_body').on('click', '.btn_edit', function(e) {
        var data = [];
        data['id'] = $(this).attr('data-id');
        data['jo_no'] = $(this).attr('data-jo_no');
        data['prod_order_no'] = $(this).attr('data-prod_order_no');
        data['prod_code'] = $(this).attr('data-prod_code');
        data['description'] = $(this).attr('data-description');
        data['current_process'] = $(this).attr('data-current_process');
        data['div_code'] = $(this).attr('data-div_code');
        data['process'] = $(this).attr('data-process');
        data['qty'] = $(this).attr('data-qty');
        data['status'] = $(this).attr('data-status');
        data['remarks'] = $(this).attr('data-remarks');
        data['create_user'] = $(this).attr('data-create_user');
        data['created_at'] = $(this).attr('data-created_at');
        data['update_user'] = $(this).attr('data-update_user');
        data['updated_at'] = $(this).attr('data-updated_at');
        data['div_code_code'] = $(this).attr('data-div_code_code');
        data.length = 1;

        getJOdetails($(this).attr('data-jo_no'),data);

        $('#modal_transfer_entry').modal('show');
    });

    $('#btn_delete_set').on('click', function() {
        delete_set('.check_item',deleteTransferItem);
    });

    $('#tbl_received_items_body').on('click', '.btn_receive', function(event) { 
        event.preventDefault();
        $('#id_r').val($(this).attr('data-id'));
        $('#jo_no_r').val($(this).attr('data-jo_no'));
        $('#prod_code_r').val($(this).attr('data-prod_code'));
        $('#process_r').val($(this).attr('data-process'));
        $('#qty_r').val($(this).attr('data-remaining_qty'));
        $('#transferred_qty_r').val($(this).attr('data-qty'));
        $('#qty').val($(this).attr('data-qty'));
        $('#receive_qty').val($(this).attr('data-receive_qty'));
        $('#remaining_qty').val($(this).attr('data-remaining_qty'));
        $('#current_process_r').val($(this).attr('data-current_process'));
        $('#div_code_code_r').val($(this).attr('data-div_code_code'));
        $('#current_div_code_r').val($(this).attr('data-current_div_code'));
        $('#current_process_name_r').val($(this).attr('data-current_process_name'));
        $('#current_process_name_r').val($(this).attr('data-current_process_name'));
        $('#status_r').val($(this).attr('data-status'));
        $('#note').val($(this).attr('data-remarks'));
        $('#modal_receive_item').modal('show');
    });

    $("#frm_receive_item").on('submit',function(e){
        e.preventDefault();
        if (parseInt($('#transferred_qty_r').val()) < parseInt($('#qty_r').val())){
            msg('Input qty is greather than transfer qty','warning')
        } else if ($('#qty_r').val() < 0){
            msg("Please Input valit number","warning");
        } else {
            $.ajax({
                dataType: 'json',
                type:'POST',
                url: $(this).attr("action"), 
                data: $(this).serialize()
            }).done(function(data, textStatus, xhr){
                msg('Process Received', 'success');
                getReceiveItems();
                getTransferEntry();
                $('#modal_receive_item').modal('hide');
                console.log(data);
            }).fail( function(xhr, textStatus, errorThrown) {
                msg(xhr,textStatus);
            });
        }
    });
});

function init() {
    check_permission(code_permission, function(output) {
        if (output == 1) {}
    });
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
        ErrorMsg(xhr);
    });
}

function makeTransferItemTable(arr) {
    $('#tbl_transfer_entry').dataTable().fnClearTable();
    $('#tbl_transfer_entry').dataTable().fnDestroy();
    $('#tbl_transfer_entry').dataTable({
        data: arr,
        bLengthChange : false,
        searching: true,
        paging: false,
        order: [[2,'asc']],
        columns: [ 
            { data: function(x) {
                return "<input type='checkbox' class='table-checkbox check_item' value='"+x.id+"'>";
            }, searchable: false, orderable: false },
            { data: function(x) {
                var disabled ='';
                if(x.item_status != 0){
                    disabled = 'disabled';
                }
                return "<button class='btn btn-sm btn-primary btn_edit'"+
                            "data-id='"+x.id+"'"+
                            "data-jo_no='"+x.jo_no+"'"+
                            "data-prod_order_no='"+x.prod_order_no+"'"+
                            "data-prod_code='"+x.prod_code+"'"+
                            "data-description='"+x.description+"'"+
                            "data-current_process='"+x.current_process+"'"+
                            "data-div_code='"+x.div_code+"'"+
                            "data-div_code_code='"+x.div_code_code+"'"+
                            "data-process='"+x.process+"'"+
                            "data-qty='"+x.qty+"'"+
                            "data-status='"+x.status+"'"+
                            "data-remarks='"+x.remarks+"'"+
                            "data-create_user='"+x.create_user+"'"+
                            "data-created_at='"+x.created_at+"'"+
                            "data-update_user='"+x.update_user+"'"+
                            "data-updated_at='"+x.updated_at+"'"+disabled+">"+
                            '<i class="fa fa-edit"></i>'+
                        '</button>';
            }, searchable: false, orderable: false },
            { data: 'jo_no' }, 
            { data: 'prod_code' },
            { data: 'current_process_name' },
            { data: 'div_code_code' },
            { data: 'process' },
            { data: 'qty' },
            { data: 'status' },
            { data: 'remarks' },
            { data: 'created_at' },
            { data: function(x) {
                if(x.item_status == 1){
                    return "RECEIVED";
                }else{
                    return "READY FOR RECEIVE";
                }
            }}
        ],
        fnInitComplete: function() {
            $('.dataTables_scrollBody').slimscroll();
        },
    });
}

function getReceiveItems() {
    received_items_arr = [];
    $.ajax({
        url: getReceiveItemsURL,
        type: 'GET',
        dataType: 'JSON',
        data: {
            _token: token
        },
    }).done(function(data, textStatus, xhr) {
        received_items_arr = data;
        console.log(received_items_arr);
        makeReceiveItemsTable(received_items_arr);
    }).fail(function(xhr, textStatus, errorThrown) {
        ErrorMsg(xhr);
    });
}

function makeReceiveItemsTable(arr) {
    $('#tbl_received_items').dataTable().fnClearTable();
    $('#tbl_received_items').dataTable().fnDestroy();
    $('#tbl_received_items').dataTable({
        data: arr,
        bLengthChange : false,
        searching: true,
        paging: false,
        order: [[11,'asc']],
        columns: [ 
            { data: function(x) {
                return "<input type='checkbox' class='table-checkbox check_receive_item' value='"+x.id+"'>";
            }, searchable: false, orderable: false },
            { data: function(x) {
                return "<button class='btn btn-sm btn-primary btn_receive'"+
                            "data-id='"+x.id+"'"+
                            "data-jo_no='"+x.jo_no+"'"+
                            "data-current_process_name='"+x.current_process_name+"'"+
                            "data-div_code_code='"+x.div_code_code+"'"+
                            "data-current_process='"+x.current_process+"'"+
                            "data-qty='"+x.qty+"'"+
                            "data-receive_qty='"+x.receive_qty+"'"+
                            "data-remaining_qty='"+x.remaining_qty+"'"+
                            "data-process='"+x.process+"'"+
                            "data-current_div_code='"+x.current_div_code+"'"+
                            "data-prod_order_no='"+x.prod_order_no+"'"+
                            "data-prod_code='"+x.prod_code+"'"+
                            "data-description='"+x.description+"'"+
                            "data-div_code='"+x.div_code+"'"+
                            "data-status='"+x.status+"'"+
                            "data-remarks='"+x.remarks+"'"+
                            "data-create_user='"+x.create_user+"'"+
                            "data-created_at='"+x.created_at+"'"+
                            "data-update_user='"+x.update_user+"'"+
                            "data-updated_at='"+x.updated_at+"'>"+
                            '<i class="fa fa-edit"></i> Receive'+
                        '</button>';
            }, searchable: false, orderable: false },
            { data: 'jo_no' },
            { data: 'prod_code' },
            { data: 'current_div_code' },
            { data: 'current_process_name' },
            { data: 'div_code_code' },
            { data: 'process' },
            { data: 'qty' },
            { data: 'status' },
            { data: 'remarks' },
            { data: 'created_at' }
        ],
        fnInitComplete: function() {
            $('.dataTables_scrollBody').slimscroll();
        },
    });
}

function delete_set(checkboxClass,deleteTransferItem) {
    var chkArray = [];
    $(checkboxClass+":checked").each(function() {
        chkArray.push($(this).val());
    });

    if (chkArray.length > 0) {
        swal({
        title: "Are you sure?",
        text: "You will not be able to recover your data!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#f95454",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: true,
        closeOnCancel: false
        }, function(isConfirm){
            if (isConfirm) {
                $.ajax({
                    url: deleteTransferItem,
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        _token:token,
                        id: chkArray
                    },
                }).done(function(data, textStatus, xhr) {
                    $('.check_all_transfer_item').prop('checked',false);
                    msg(data.msg,data.status)
                    getTransferEntry();
                    getReceiveItems();
                    clear();
                }).fail(function(xhr, textStatus, errorThrown) {
                    msg(errorThrown,'error');
                });
            } else {
                swal("Cancelled", "Your data is safe and not deleted.");
            }
        });
    }else {
        msg("Please select at least 1 item." ,"failed");
    }
}

function getJOdetails(jo_no,edit) {
    var curr_process = '<option value=""></option>';
    $('#curr_process').html(curr_process);
    $.ajax({
        url: getJOdetailsURL,
        type: 'GET',
        dataType: 'JSON',
        data: {
            _token: token,
            jo_no: jo_no
        },
    }).done(function(data, textStatus, xhr) {

        console.log(data);
        $('#unprocessed').val(0);
        var jo = data.jo;
        if( jo.status == 3){
            msg("J.O. Number is already cancelled","failed");
        }else if(jo != ''){
            $('#prod_order_no').val(jo.prod_order_no);
            $('#prod_code').val(jo.prod_code);
            $('#description').val(jo.description);
        }else{
            $('#prod_order_no').val('');
            $('#prod_code').val('');
            $('#description').val('');
            msg("J.O. Number is not in the list.","failed");
        }
        $.each(data.current_processes, function(i, x) {
            curr_process = '<option value="'+x.id+'">'+x.process+'</option>';
            $('#curr_process').append(curr_process);
        });

        if (edit !== undefined) {
            if (edit.length > 0) {
                $('#id').val(edit['id']);
                $('#jo_no').val(edit['jo_no']);
                $('#curr_process').val(edit['current_process']);
                getUnprocessed(edit['process'],edit['jo_no'],edit['current_process']);
                getDivCodeProcess(edit['jo_no'],edit['process']);
                DivisionCode(edit['process'],edit['div_code_code']);
                $('#qty').val(edit['qty']);
                $('#status').val(edit['status']);
                $('#remarks').val(edit['remarks']);
                $('#create_user').val(edit['create_user']);
                $('#created_date').val(edit['created_at']);
                $('#update_user').val(edit['update_user']);
                $('#updated_date').val(edit['updated_at']);
            }
        }
            
    }).fail(function(xhr, textStatus, errorThrown) {
        ErrorMsg(xhr);
    });
}

function getDivCodeProcess(jo_no,process) {
    var options = '<option value=""></option>';
    $('#process').html(options);
    $.ajax({
        url: getDivCodeProcessURL,
        type: 'GET',
        dataType: 'JSON',
        data: { jo_no: jo_no },
    }).done(function(data, textStatus, xhr) {
        $.each(data, function(i, x) {
            if (x.process == process) {
                options = "<option value='"+x.process+"' selected>"+x.process+"</option>";
            } else {
                options = "<option value='"+x.process+"'>"+x.process+"</option>";
            }
            $('#process').append(options);
        });
        $('#process').trigger('change');
    }).fail(function(xhr, textStatus, errorThrown) {
        ErrorMsg(xhr);
    });
}

function DivisionCode(process,div_code) {
    var opt = "<option value=''></option>";
    $('#div_code').html(opt);
    $.ajax({
        url: getDivisionCode,
        type: 'GET',
        dataType: 'JSON',
        data: {_token: token,process:process},
    }).done(function(data, textStatus, xhr) {
        var select = '';
        $.each(data, function(i, x) {
            if (x.div_code == div_code) {
                select = 'selected';
            }else if($('#userDivCode').val() == x.div_code){
                 select = 'selected';
            }
            $('#div_code').append("<option value='"+x.id+"'"+select+">"+x.div_code+"</option>");
            select = '';
        });
    }).fail(function(xhr, textStatus, errorThrown) {
        console.log(errorThrown);
    });
}

function getUnprocessed(process,jo_no,current_process) {
    $.ajax({
        url: unprocessedItem,
        type: 'POSt',
        dataType: 'JSON',
        data: {_token: token,
            process:process,
            jo_no:jo_no,
            current_process:current_process
        },
    }).done(function(data, textStatus, xhr) {
        //$.each(data.UnprocessTravel, function(i, x) {
            $('#unprocessed').val(data.UnprocessTravel['total_qty']);
            $('#userDivCode').val(data.UnprocessTravel['div_code']);
        //});
        $('#UnprocessTransfer').val(data.UnprocessTransfer);
    }).fail(function(xhr, textStatus, errorThrown) {
        console.log(errorThrown);
    });
}

function checkIfSameDivCode() {
    if ($('#process').val() == $("#curr_process").find("option:selected").text() && $('#userDivCode').val() == $("#div_code").find("option:selected").text()) {
        msg("You cannot transfer to your division with the same process","warning");
    }
}

function clear() {
    $('.clear').val('');
}