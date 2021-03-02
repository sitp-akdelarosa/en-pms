var product_arr = [];
var vState = '';

$( function() {

    init();

    $(document).on('shown.bs.modal', function () {
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });

    $('#btn_clear').on('click', function() {
        clear();
        $('#btn_add').html('<i class="fa fa-plus"></i> Add');
    });

    $('#btn_first').on('click', function() {
        getWithdrawalTransaction('first', $('#trans_no').val());
    });

    $('#btn_prev').on('click', function() {
        getWithdrawalTransaction('prev', $('#trans_no').val());
    });

    $('#btn_next').on('click', function() {
        getWithdrawalTransaction('next', $('#trans_no').val());
    });

    $('#btn_last').on('click', function() {
        getWithdrawalTransaction('last', $('#trans_no').val());
    });

    $('#btn_search_item_code').on('click', function() {
        var trans_code = $('#item_code').val();
        if ($('#item_class').val() == "") {
            showErrors({ item_class: ["Please select an Item Class."] });
        } else {
            getInventory($('#item_class').val(), trans_code.trim(), 0, null);
        }
    });

    $('#btn_new').on('click', function() {
        product_arr = [];
        clear();
        $('#trans_no').val('');
        $('#id').val('');
        ProductDataTable(product_arr);
        viewState('ADD');
    });

    $('#btn_edit').on('click', function () {
        if ($('#trans_no').val() == '') {

        } else {
            viewState('EDIT');
        }
        
    });

    $('#btn_cancel').on('click', function () {
        clear();
        viewState('');
        getWithdrawalTransaction('', $('#trans_no').val());
    });

    $('#tbl_inventory_body').on('click', '.btn_pick_item',function() {
        $('#inv_id').val($(this).attr('data-id'));
        $('#item_class').val($(this).attr('data-item_class'));
        $('#jo_no').val($(this).attr('data-jo_no'));
        $('#item_code').val($(this).attr('data-item_code'));
        $('#product_line').val($(this).attr('data-product_line'));
        $('#item').val($(this).attr('data-item'));
        $('#alloy').val($(this).attr('data-alloy'));
        $('#size').val($(this).attr('data-size'));
        $('#schedule').val($(this).attr('data-schedule'));
        $('#qty_weight').val($(this).attr('data-qty_weight'));
        $('#inv_qty').val($(this).attr('data-current_stock'));
        $('#heat_no').val($(this).attr('data-heat_no'));
        $('#lot_no').val($(this).attr('data-lot_no'));

        $('#modal_inventory').modal('hide');
    });

    $('#btn_add').on('click', function() {
        var total_pcs = 0;

        if ($('#old_issued_qty').val() == '' || $('#old_issued_qty').val() == null) {
            total_pcs = parseInt($('#inv_qty').val());
        } else {
            total_pcs = parseInt($('#old_issued_qty').val()) + parseInt($('#inv_qty').val());
        }

        $same_code_total_qty = 0;
        
        // $.each(product_arr, function(i,x) {
        //     if (x.inv_id == $('#inv_id').val()) {
        //         $same_code_total_qty = (parseInt(x.issued_qty) + parseInt($('#issued_qty').val()));
        //     }
        // });

        if ($('#item_class').val() == '') {
            msg("Please select an Item Class.", "failed");
        } else if ($('#item_code').val() == '') {
            msg("Please select an Item Code.", "failed");
        } else if ($('#lot_no').val() == '' || $('#lot_no').val() == '') {
            msg("Please select an Item Code to populate other details.", "failed");
        } else if ($('#sc_no').val() == '') {
            msg("Please provide SC Number.", "failed");
        } else if (parseInt($('#issued_qty').val()) == 0 || $('#issued_qty').val() == '') {
            msg("Please provide Issuance Quantity.", "failed");
        } else if (parseInt($('#issued_qty').val()) > total_pcs) {
            msg("Please do not withdraw more than " + total_pcs + ".", "failed");
        } else if ($same_code_total_qty > parseInt($('#inv_qty').val())) {
            msg("You are withdrawing total of " + $same_code_total_qty + " for this inventory item.", "failed");
        } else {
            if (parseInt($('#item_count').val()) > 0) {
                var key = parseInt($('#item_count').val()) - 1;
                product_arr[key] = {
                    count: $('#item_count').val(),
                    item_id: $('#item_id').val(),
                    inv_id: $('#inv_id').val(),
                    item_class: $('#item_class').val(),
                    item_code: $('#item_code').val(),
                    inv_id: $('#inv_id').val(),
                    old_issued_qty: $('#old_issued_qty').val(),
                    jo_no: $('#jo_no').val(),
                    lot_no: $('#lot_no').val(),
                    heat_no: $('#heat_no').val(),
                    alloy: $('#alloy').val(),
                    item: $('#item').val(),
                    size: $('#size').val(),
                    schedule: $('#schedule').val(),
                    remarks: $('#remarks').val(),
                    sc_no: $('#sc_no').val(),
                    issued_qty: $('#issued_qty').val(),
                    create_user: $('#create_user').val(),
                    update_user: $('#update_user').val(),
                    created_at: $('#created_at').val(),
                    updated_at: $('#updated_at').val(),
                    deleted: 0
                }

                clear();
            } else {
                var count = product_arr.length + 1;

                product_arr.push({
                    count: count,
                    item_id: $('#item_id').val(),
                    inv_id: $('#inv_id').val(),
                    item_class: $('#item_class').val(),
                    item_code: $('#item_code').val(),
                    inv_id: $('#inv_id').val(),
                    old_issued_qty: $('#old_issued_qty').val(),
                    jo_no: $('#jo_no').val(),
                    lot_no: $('#lot_no').val(),
                    heat_no: $('#heat_no').val(),
                    alloy: $('#alloy').val(),
                    item: $('#item').val(),
                    size: $('#size').val(),
                    schedule: $('#schedule').val(),
                    remarks: $('#remarks').val(),
                    sc_no: $('#sc_no').val(),
                    issued_qty: $('#issued_qty').val(),
                    create_user: $('#create_user').val(),
                    update_user: $('#update_user').val(),
                    created_at: $('#created_at').val(),
                    updated_at: $('#updated_at').val(),
                    deleted: 0
                });

                clear();
            }
        }
        

        $('#btn_add').html('<i class="fa fa-plus"></i> Add');

        ProductDataTable(product_arr);
        
    });

    $('#frm_product').on('submit', function(e) {
        e.preventDefault();
        $('.loadingOverlay').show();

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            dataType: 'JSON',
            data: $(this).serialize(),
        }).done(function (data, textStatus, xhr) {
            clear();
            viewState('');
            msg(data.msg,data.status);
            plotValues(data.info, data.details);
        }).fail(function (xhr, textStatus, errorThrown) {
            ErrorMsg(xhr);
        }).always(function () {
            $('.loadingOverlay').hide();
        });
    });

    $('#tbl_product_body').on('click','.btn_withdrawal_detail', function() {
        $('.loadingOverlay').show();
        $('#item_count').val($(this).attr('data-count'));
        $('#item_id').val($(this).attr('data-item_id'));
        $('#item_class').val($(this).attr('data-item_class'));
        $('#item_code').val($(this).attr('data-item_code'));
        $('#inv_id').val($(this).attr('data-inv_id'));
        $('#old_issued_qty').val($(this).attr('data-old_issued_qty'));
        $('#jo_no').val($(this).attr('data-jo_no'));
        $('#lot_no').val($(this).attr('data-lot_no'));
        $('#heat_no').val($(this).attr('data-heat_no'));
        $('#alloy').val($(this).attr('data-alloy'));
        $('#item').val($(this).attr('data-item'));
        $('#size').val($(this).attr('data-size'));
        $('#schedule').val($(this).attr('data-schedule'));
        $('#remarks').val($(this).attr('data-remarks'));
        $('#sc_no').val($(this).attr('data-sc_no'));
        $('#issued_qty').val($(this).attr('data-issued_qty'));
        $('#create_user').val($(this).attr('data-create_user'));
        $('#update_user').val($(this).attr('data-update_user'));
        $('#created_at').val($(this).attr('data-created_at'));
        $('#updated_at').val($(this).attr('data-updated_at'));

        getInventory(
            $(this).attr('data-item_class'), 
            $(this).attr('data-item_code'), 
            $(this).attr('data-issued_qty'), 
            $(this).attr('data-inv_id'),
            'edit'
        );

        $('#btn_add').html('<i class="fa fa-edit"></i> Update');
    });

    $('#tbl_product_body').on('click', '.btn_withdrawal_detail_delete', function () {
        var key = parseInt($(this).attr('data-count')) - 1;
        // console.log(product_arr[key]);
        product_arr[key].deleted = 1;
        ProductDataTable(product_arr);
    });

    $('#btn_prepare_print').on('click', function() {
        $('#modal_withdrawal_slip').modal('show');
    })

    $('.btn_print').on('click', function () {
        var print_format = $(this).attr('data-print_format');

        var print_link = ProductWithdrawalSlipPrintURL +
            '?trans_id=' + $('#id').val() +
            '&&trans_no=' + $('#trans_no').val() +
            '&&date=' + $('#date').val() +
            '&&prepared_by=' + $('#prepared_by').val() +
            '&&issued_by=' + $('#issued_by').val() +
            '&&print_format=' + print_format +
            '&&plant=' + $('#plant').val() +
            '&&received_by=' + $('#received_by').val();

        if ($('#trans_no').val() == '' || $('#id').val() == '') {
            msg('Please Navigate to a Transaction Number first.', 'failed');
        } else if ($('#date').val() == '') {
            msg('Please input date of withdrawal.', 'failed');
        } else {
            window.open(print_link, '_tab');
        }
    })

    $('#btn_search_filter').on('click', function () {
        $('.srch-clear').val('');
        searchDataTable([]);
        $('#modal_product_search').modal('show');
    });

    $('#btn_search_excel').on('click', function () {

        window.location.href = excelSearchRawMaterialURL + "?srch_date_withdrawal_from=" + $('#srch_date_withdrawal_from').val() +
            "&srch_date_withdrawal_to=" + $('#srch_date_withdrawal_to').val() +
            "&srch_trans_no=" + $('#srch_trans_no').val() +
            "&srch_heat_no=" + $('#srch_heat_no').val() +
            "&srch_mat_code=" + $('#srch_mat_code').val() +
            "&srch_alloy=" + $('#srch_alloy').val() +
            "&srch_item=" + $('#srch_item').val() +
            "&srch_size=" + $('#srch_size').val() +
            "&srch_length=" + $('#srch_length').val() +
            "&srch_schedule=" + $('#srch_schedule').val();
    });

    $("#frm_search").on('submit', function (e) {
        e.preventDefault();
        $('.loadingOverlay-modal').show();

        $.ajax({
            url: $(this).attr('action'),
            type: 'GET',
            dataType: 'JSON',
            data: $(this).serialize(),
        }).done(function (data, textStatus, xhr) {
            searchDataTable(data);

        }).fail(function (xhr, textStatus, errorThrown) {
            ErrorMsg(xhr);
        }).always(function () {
            $('.loadingOverlay-modal').hide();
        });
    });

    $('#tbl_search').on('click', '.btn_pick_search_item', function() {
        getWithdrawalTransaction('', $(this).attr('data-trans_no'));
        $('#modal_product_search').modal('hide');
    });

    $('#btn_delete').on('click', function () {
        var id = $('#id').val();
        var status = $('#status').val();

        if ($('#trans_no').val() !== '') {
            if (status == 'CONFIRMED') {
                // for cancel function
                check_cancellation($('#trans_no').val(), function (exist) {
                    if (exist > 0) {
                        msg("This transaction is already used in Production Schedule.", "info");
                    } else {
                        DeleteAndCancel(id, status);
                    }
                });
            } else {
                // for delete function
                DeleteAndCancel(id, status);
            }
        } else {
            msg('Please add new withdrawal transaction.', 'warning');
        }
        
    });

    $('#btn_confirm').on('click', function() {
        var title = "Confirm Withdrawal";
        var text = "Are your sure to confirm this withdrawal?";
        var id = $('#id').val();
        var status = $('#status').val();
        var data_exist = 0;

        if ($('#trans_no').val() !== '') {
            if (status == 'CONFIRMED') {
                title = "Unconfirm Withdrawal";
                text = "Are your sure to unconfirm this withdrawal?";

                check_cancellation($('#trans_no').val(), function (exist) {
                    data_exist = exist;
                });
            }

            if (data_exist > 0) {
                msg('This transaction is already used in Production Schedule.', 'warning');
            } else {
                swal({
                    title: title,
                    text: text,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#f95454",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No",
                    closeOnConfirm: true,
                    closeOnCancel: false
                }, function (isConfirm) {
                    if (isConfirm) {
                        confirmWithdrawal(id, status);
                    } else {
                        swal.close();
                    }
                });
            }
        } else {
            msg('Please add new withdrawal transaction.', 'warning');
        }

        
    });



});

function init() {
    viewState('');
    
    if (permission_access == '2' || permission_access == 2) {
        $('.permission').prop('readonly', true);
        $('.permission-button').prop('disabled', true);
    } else {
        $('.permission').prop('readonly', false);
        $('.permission-button').prop('disabled', false);
    }

    getWithdrawalTransaction('', ''); 
}

function clear() {
    $('.clear').val('');
}

function viewState(state) {
    switch (state) {
        case 'ADD':
            $('.btn_navigation').prop('disabled', true);

            $('#trans_no').prop('readonly', true);
            $('#item_class').prop('disabled', false);
            $('#item_code').prop('readonly', false);
            $('#schedule').prop('readonly', true);
            $('#remarks').prop('disabled', false);
            $('#sc_no').prop('readonly', false);
            $('#issued_qty').prop('readonly',false);

            $('#btn_search_item_code').prop('disabled',false);

            $('#controls').show();
            // $('#btn_add').hide();
            // $('#btn_clear').hide();
            $('.btn_withdrawal_detail').prop('disabled', true);
            $('.btn_withdrawal_detail_delete').prop('disabled', false);

            $('#add_new').hide();
            $('#confirm').hide();
            $('#edit').hide();
            $('#save').show();
            $('#delete').hide();
            $('#cancel').show();
            $('#print').hide();
            $('#search').hide();

            vState = 'ADD';
            
            break;

        case 'EDIT':
            $('.btn_navigation').prop('disabled', true);

            $('#trans_no').prop('readonly', true);
            $('#item_class').prop('disabled', false);
            $('#item_code').prop('readonly', false);
            $('#schedule').prop('readonly', true);
            $('#remarks').prop('disabled', false);
            $('#sc_no').prop('readonly', false);
            $('#issued_qty').prop('readonly',false);

            $('#btn_search_item_code').prop('disabled', false);

            $('#controls').show();
            // $('#btn_add').hide();
            // $('#btn_clear').hide();
            $('.btn_withdrawal_detail').prop('disabled', false);
            $('.btn_withdrawal_detail_delete').prop('disabled', false);

            $('#add_new').hide();
            $('#confirm').hide();
            $('#edit').hide();
            $('#save').show();
            $('#delete').hide();
            $('#cancel').show();
            $('#print').hide();
            $('#search').hide();

            vState = 'EDIT';
            break;
    
        default:
            $('.btn_navigation').prop('disabled', false);

            $('#trans_no').prop('readonly',false);
            $('#item_class').prop('disabled',true);
            $('#item_code').prop('readonly',true);
            $('#schedule').prop('readonly',true);
            $('#remarks').prop('disabled',true);
            $('#sc_no').prop('readonly',true);
            $('#issued_qty').prop('readonly',true);

            $('#btn_search_item_code').prop('disabled', true);

            $('#controls').hide();
            // $('#btn_add').hide();
            // $('#btn_clear').hide();
            $('.btn_withdrawal_detail').prop('disabled', true);
            $('.btn_withdrawal_detail_delete').prop('disabled', true);

            $('#add_new').show();
            $('#confirm').show();
            $('#edit').show();
            $('#save').hide();
            $('#delete').show();
            $('#cancel').hide();
            $('#print').show();
            $('#search').show();

            buttonState($('#status').val());

            vState = '';
            break;
    }
}

function buttonState(status) {
    $('#edit').show();
    $('#confirm').show();
    $('#delete').show();
    $('#print').show();

    switch (status) {
        case 'CANCELLED':
            $('#edit').hide();
            $('#confirm').hide();
            $('#delete').hide();
            $('#print').hide();
            break;

        case 'CONFIRMED':
            $('#btn_edit').prop('disabled', true);
            $('#btn_confirm').html('<i class="fa fa-circle"></i> Unconfirm Withdrawal');
            $('#btn_confirm').removeClass('btn-success');
            $('#btn_confirm').addClass('btn-secondary');
            $('#btn_delete').html('<i class="fa fa-times"></i> Cancel Transaction');
            break;

        default:
            $('#btn_edit').prop('disabled', false);
            $('#btn_confirm').html('<i class="fa fa-check"></i> Confirm Withdrawal');
            $('#btn_confirm').removeClass('btn-secondary');
            $('#btn_confirm').addClass('btn-success');
            $('#btn_delete').html('<i class="fa fa-trash"></i> Delete');
            break;
    }
}

function getWithdrawalTransaction(to,trans_no) {
    $('.loadingOverlay').show();
    $.ajax({
        url: getWithdrawalTransactionURL,
        type: 'GET',
        dataType: 'JSON',
        data: {
            to: to,
            trans_no: trans_no
        },
    }).done(function (data, textStatus, xhr) {
        plotValues(data.info, data.details);
    }).fail(function (xhr, textStatus, errorThrown) {
        ErrorMsg(xhr);
    }).always(function () {
        $('.loadingOverlay').hide();
    });
}

function plotValues(info,details) {
    $('#id').val(info.id);
    $('#trans_no').val(info.trans_no);
    $('#status').val(info.status);

    buttonState(info.status);

    product_arr = [];

    var count = product_arr.length;

    console.log(details);

    $.each(details, function(i,x) {
        count++;
        product_arr.push({
            count: count,
            item_id: x.id,
            inv_id: x.inv_id,
            item_class: x.item_class,
            item_code: x.item_code,
            inv_id: x.inv_id,
            old_issued_qty: x.issued_qty,
            jo_no: x.jo_no,
            lot_no: x.lot_no,
            heat_no: x.heat_no,
            alloy: x.alloy,
            item: x.item,
            size: x.size,
            schedule: x.schedule,
            remarks: x.remarks,
            sc_no: x.sc_no,
            issued_qty: x.issued_qty,
            create_user: x.create_user,
            update_user: x.update_user,
            created_at: x.created_at,
            updated_at: x.updated_at,
            deleted: 0
        });
    });

    ProductDataTable(product_arr);
}

function ProductDataTable(arr) {
    $('.loadingOverlay').show();
    var table = $('#tbl_product');

    table.dataTable().fnClearTable();
    table.dataTable().fnDestroy();
    table.dataTable({
        data: arr,
        order: [[1, 'asc']],
        // scrollX: true,
        columns: [
            {
                data: function (x) {
                    return "<button class='btn btn-sm bg-blue btn_withdrawal_detail' type='button'" +
                                "data-item_id='" + x.item_id+ "' " +
                                "data-count='" + x.count + "' " +
                                "data-item_class='" + x.item_class+ "' " +
                                "data-item_code='" + x.item_code+ "' " +
                                "data-inv_id='" + x.inv_id+ "' " +
                                "data-old_issued_qty='" + x.issued_qty+ "' " +
                                "data-jo_no='" + x.jo_no+ "' " +
                                "data-lot_no='" + x.lot_no+ "' " +
                                "data-heat_no='" + x.heat_no+ "' " +
                                "data-alloy='" + x.alloy+ "' " +
                                "data-item='" + x.item+ "' " +
                                "data-size='" + x.size+ "' " +
                                "data-schedule='" + x.schedule+ "' " +
                                "data-remarks='" + x.remarks+ "' " +
                                "data-sc_no='" + x.sc_no+ "' " +
                                "data-issued_qty='" + x.issued_qty+ "' " +
                                "data-create_user='" + x.create_user + "' " +
                                "data-update_user='" + x.update_user + "' " +
                                "data-created_at='" + x.created_at + "' " +
                                "data-updated_at='" + x.updated_at + "' " +
                            ">" +
                                "<i class='fa fa-edit'></i>" +
                            "</button>" + 

                            "<button class='btn btn-sm bg-red btn_withdrawal_detail_delete' type='button'" +
                                "data-item_id='" + x.item_id + "' " +
                                "data-count='" + x.count + "' " +
                            ">" +
                                "<i class='fa fa-times'></i>" +
                            "</button>";
                }, orderable: false, searchable: false
            },
            {
                data: function (x) {
                    return x.count + "<input type='hidden' name='detail_count[]' value='" + x.count + "'>";
                }
            },
            { 
                data: function(x) {
                return x.item_class + "<input type='hidden' name='detail_item_class[]' value='" + x.item_class + "'>"+
                        "<input type='hidden' name='detail_inv_id[]' value='" + x.inv_id + "'>" +
                        "<input type='hidden' name='detail_old_issued_qty[]' value='" + x.old_issued_qty + "'>" +
                        "<input type='hidden' name='detail_item_id[]' value='" + x.item_id + "'>"+
                        "<input type='hidden' name='detail_deleted[]' value='" + x.deleted + "'>";
                } 
            },
            { 
                data: function(x) {
                    return x.jo_no + "<input type='hidden' name='detail_jo_no[]' value='" + x.jo_no + "'>";
                } 
            },
            { 
                data: function(x) {
                    return x.item_code + "<input type='hidden' name='detail_item_code[]' value='" + x.item_code + "'>";
                } 
            },
            { 
                data: function(x) {
                    return x.lot_no + "<input type='hidden' name='detail_lot_no[]' value='" + x.lot_no + "'>";
                } 
            },
            { 
                data: function(x) {
                    return x.heat_no + "<input type='hidden' name='detail_heat_no[]' value='" + x.heat_no + "'>";
                } 
            },
            {
                data: function (x) {
                    return x.sc_no + "<input type='hidden' name='detail_sc_no[]' value='" + x.sc_no + "'>";
                }
            },
            { 
                data: function(x) {
                    return x.alloy + "<input type='hidden' name='detail_alloy[]' value='" + x.alloy + "'>";
                } 
            },
            { data: function(x) {
                return x.item + "<input type='hidden' name='detail_item[]' value='" + x.item + "'>";
            } },
            { 
                data: function(x) {
                    return x.size + "<input type='hidden' name='detail_size[]' value='" + x.size + "'>";
                } 
            },
            { 
                data: function(x) {
                    return x.schedule + "<input type='hidden' name='detail_schedule[]' value='" + x.schedule + "'>";
                } 
            },

            {
                data: function (x) {
                    return x.issued_qty + "<input type='hidden' name='detail_issued_qty[]' value='" + x.issued_qty + "'>";
                }
            },

            {
                data: function (x) {
                    return x.remarks + "<input type='hidden' name='detail_remarks[]' value='" + x.remarks + "'>";
                }
            }
        ],
        initComplete: function () {
            if (vState == '') {
                $('.btn_withdrawal_detail').prop('disabled', true);
                $('.btn_withdrawal_detail_delete').prop('disabled', true);
            } else {
                $('.btn_withdrawal_detail').prop('disabled', false);
                $('.btn_withdrawal_detail_delete').prop('disabled', false);
            }
            $('.loadingOverlay').hide();
        },
        createdRow: function (row, data, dataIndex) {
            if (data.deleted === 1) {
                $(row).css('background-color', '#ff6266');
                $(row).css('color', '#fff');
            }
        },
    });
}

function getInventory(item_class,item_code, issued_qty,inv_id,state) {
    $('.loadingOverlay-modal').show();
    $.ajax({
        url: getInventoryURL,
        type: 'GET',
        dataType: 'JSON',
        data: {
            item_class: item_class,
            item_code: item_code,
            issued_qty: issued_qty,
            inv_id: inv_id,
            state: state
        },
    }).done(function (data, textStatus, xhr) {
        console.log(data);
        if (data.length > 0) {
            if (data.length > 1) {
                InventoryDataTable(data);
                $('#modal_inventory').modal('show');
            } else {
                var product = data[0];

                $('#inv_id').val("");

                $('#jo_no').val("");
                $('#lot_no').val("");
                $('#heat_no').val("");

                $('#alloy').val("");
                $('#item').val("");
                $('#size').val("");
                $('#schedule').val("");
                
                $('#inv_qty').val("");
                $('#qty_weight').val("");
                $('#inv_id').val("");

                if (product.item_code == undefined) {
                    $('#issued_qty').prop('readonly', true);
                    msg("Item Code is not in the list.", "failed");
                } else if (product.current_stock == 0 && $('#item_id').val() == '') {
                    $('#issued_qty').prop('readonly', true);
                    msg("Item Code have no qty inventory.", "failed");
                } else {
                    $('#jo_no').val(product.jo_no);
                    $('#lot_no').val(product.lot_no);
                    $('#heat_no').val(product.heat_no);

                    $('#alloy').val(product.alloy);
                    $('#item').val(product.item);
                    $('#size').val(product.size);
                    $('#schedule').val(product.schedule);

                    var iss_qty = 0;
                    if ($('#issued_qty').val() !== "" && $('#issued_qty').val() !== undefined) {
                        iss_qty = parseFloat($('#issued_qty').val());
                    }

                    var curr_stock = parseFloat(product.current_stock) - iss_qty;

                    $('#inv_qty').val(curr_stock);
                    $('#qty_weight').val(product.qty_weight);
                    $('#issued_qty').prop('readonly', false);

                    $('#inv_id').val(product.id);
                }
            }
        } else {
            msg("No Items found for this Item Code or maybe the Stock Quantities are all 0.", 'warning');
        }

    }).fail(function (xhr, textStatus, errorThrown) {
        //msg(errorThrown, textStatus);
        var response = jQuery.parseJSON(xhr.responseText);
        ErrorMsg(response);
    }).always(function () {
        $('.loadingOverlay').hide();
        $('.loadingOverlay-modal').hide();
    });
}

function InventoryDataTable(arr) {
    $('.loadingOverlay-modal').show();

    $('#tbl_inventory').dataTable().fnClearTable();
    $('#tbl_inventory').dataTable().fnDestroy();
    $('#tbl_inventory').dataTable({
        data: arr,
        order: [[13, 'asc']],
        scrollX: true,
        columns: [
            {
                data: function (x) {
                    return "<button class='btn btn-sm bg-blue btn_pick_item' type='button'" +
                        "data-id='" + x.id + "' " +
                        "data-item_class='" + x.item_class + "' " +
                        "data-jo_no='" + x.jo_no + "' " +
                        "data-item_code='" + x.item_code + "' " +
                        "data-product_line='" + x.product_line + "' " +
                        "data-description='" + x.description + "' " +
                        "data-item='" + x.item + "' " +
                        "data-alloy='" + x.alloy + "' " +
                        "data-size='" + x.size + "' " +
                        "data-schedule='" + x.schedule + "' " +
                        "data-qty_weight='" + x.qty_weight + "' " +
                        "data-qty_pcs='" + x.qty_pcs + "' " +
                        "data-current_stock='" + x.current_stock + "' " +
                        "data-heat_no='" + x.heat_no + "' " +
                        "data-lot_no='" + x.lot_no + "' " +
                        "data-received_id='" + x.received_id + "' " +
                        ">" +
                        "<i class='fa fa-edit'></i>" +
                        "</button>";
                }, orderable: false, searchable: false
            },
            { data: 'item_class' },
            { data: 'jo_no' },
            { data: 'item_code' },
            { data: 'description' },
            { data: 'product_line' },
            { data: 'lot_no' },
            { data: 'heat_no' },
            { data: 'qty_weight' },
            { data: 'qty_pcs' },
            { data: 'current_stock' },
            { data: 'alloy' },
            { data: 'item' },
            { data: 'size' },
            { data: 'schedule' }
        ],
        initComplete: function () {
            $('.loadingOverlay-modal').hide();
        }
    });
}

function searchDataTable(arr) {
    $('.loadingOverlay-modal').show();

    $('#tbl_search').dataTable().fnClearTable();
    $('#tbl_search').dataTable().fnDestroy();
    $('#tbl_search').dataTable({
        data: arr,
        order: [[14, 'asc']],
        scrollX: true,
        columns: [
            {
                data: function (x) {
                    return "<button class='btn btn-sm bg-blue btn_pick_search_item' type='button'" +
                            "data-trans_no='" + x.trans_no + "'" +
                            "data-item_class='" + x.item_class + "'" +
                            "data-item_code='" + x.item_code + "'" +
                            "data-jo_no='" + x.jo_no + "'" +
                            "data-lot_no='" + x.lot_no + "'" +
                            "data-heat_no='" + x.heat_no + "'" +
                            "data-sc_no='" + x.sc_no + "'" +
                            "data-alloy='" + x.alloy + "'" +
                            "data-item='" + x.item + "'" +
                            "data-size='" + x.size + "'" +
                            "data-schedule='" + x.schedule + "'" +
                            "data-issued_qty='" + x.issued_qty + "'" +
                            "data-remarks='" + x.remarks + "'" +
                            "data-created_at='" + x.created_at + "'" +
                        ">" +
                        "<i class='fa fa-edit'></i>" +
                        "</button>";
                }, orderable: false, searchable: false
            },
            { data: 'trans_no' },
            { data: 'item_class' },
            { data: 'item_code' },
            { data: 'jo_no' },
            { data: 'lot_no' },
            { data: 'heat_no' },
            { data: 'sc_no' },
            { data: 'alloy' },
            { data: 'item' },
            { data: 'size' },
            { data: 'schedule' },
            { data: 'issued_qty' },
            { data: 'remarks' },
            { data: 'created_at' },
        ],
        initComplete: function () {
            $('.loadingOverlay-modal').hide();
        }
    });
}

function confirmWithdrawal(id,status) {
    $('.loadingOverlay').show();
    $.ajax({
        url: confirmWithdrawalURL,
        type: 'POST',
        datatype: "json",
        loadonce: true,
        data: {
            _token: token,
            id: id,
            status: status
        },
    }).done(function (data, textStatus, xhr) {
        msg(data.msg, data.status);
        getWithdrawalTransaction('',$('#trans_no').val());
    }).fail(function (xhr, textStatus, errorThrown) {
        ErrorMsg(xhr);
    }).always(function () {
        $('.loadingOverlay').hide();
    });
}

function check_cancellation(pw_no, handleData) {
    $('.loadingOverlay').show();
    $.ajax({
        url: checkWithdrawalCancellationURL,
        type: 'GET',
        dataType: 'JSON',
        data: { pw_no: pw_no }
    }).done(function (data, textStatus, xhr) {
        handleData(data.exist);
    }).fail(function (xhr, textStatus, errorThrown) {
        ErrorMsg(xhr);
    }).always(function () {
        $('.loadingOverlay').hide();
    });

}

function DeleteAndCancel(id, status) {
    var title = "Delete Withdrawal Transaction";
    var text = "Are your sure to delete this withdrawal transaction?";
    var CancelTitle = "Not Deleted!";
    var CancelMsg = "Your data is safe and not deleted.";

    if (status) {
        title = "Cancel Withdrawal Transaction";
        text = "Are your sure to cancel this withdrawal transaction?";
        CancelTitle = "Not Cancelled!";
        CancelMsg = "Widthrawal transaction cancellation was revoke.";
    }

    swal({
        title: title,
        text: text,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#f95454",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: true,
        closeOnCancel: false
    }, function (isConfirm) {
        if (isConfirm) {
            $('.loadingOverlay').show();
            $.ajax({
                url: deleteWithdrawalURL,
                type: 'POST',
                dataType: 'JSON',
                data: {
                    _token: token,
                    id: id,
                    status: status
                },
            }).done(function (data, textStatus, xhr) {
                if (data.status == 'success') {
                    msg(data.msg, data.status)
                } else {
                    msg(data.msg, data.status)
                }

                getWithdrawalTransaction('', '');
            }).fail(function (xhr, textStatus, errorThrown) {
                ErrorMsg(xhr);
            }).always(function () {
                $('.loadingOverlay').hide();
            });
        } else {
            swal(CancelTitle, CancelMsg);
        }
    });
}