var product_arr = [];

$( function() {

    init();

    $(document).on('shown.bs.modal', function () {
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });

    $('#btn_search_item_code').on('click', function() {
        if ($('#item_class').val() == "") {
            showErrors({ item_class: ["Please select an Item Class."] });
        } else {
            getInventory($('#item_class').val(), $('#item_code').val(), 0, null);
        }
    });

    $('#btn_new').on('click', function() {
        viewState('ADD');
    });

    $('#btn_edit').on('click', function () {
        viewState('EDIT');
    });

    $('#btn_cancel').on('click', function () {
        viewState('');
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
        $('#inv_qty').val($(this).attr('data-currrent_stock'));
        $('#heat_no').val($(this).attr('data-heat_no'));
        $('#lot_no').val($(this).attr('data-lot_no'));

        $('#modal_inventory').modal('hide');
    });

    $('#btn_add').on('click', function() {
        product_arr.push({
            id: $('#id').val(),
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
            qty_weight: $('#qty_weight').val(),
            inv_qty: $('#inv_qty').val(),
            issued_qty: $('#issued_qty').val(),
        });

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
            ProductDataTable([]);

        }).fail(function (xhr, textStatus, errorThrown) {
            var errors = xhr.responseJSON.errors;

            console.log(errors);
            showErrors(errors);
        }).always(function () {
            $('.loadingOverlay-modal').hide();
        });
    });
});

function init() {
    check_permission(code_permission, function (output) {
        if (output == 1) { }
    });

    viewState('');
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

            $('#add_new').hide();
            $('#edit').hide();
            $('#save').show();
            $('#delete').hide();
            $('#cancel').show();
            $('#print').hide();
            $('#search').hide();
            
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

            $('#add_new').hide();
            $('#edit').hide();
            $('#save').show();
            $('#delete').hide();
            $('#cancel').show();
            $('#print').hide();
            $('#search').hide();
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

            $('#add_new').show();
            $('#edit').show();
            $('#save').hide();
            $('#delete').hide();
            $('#cancel').hide();
            $('#print').show();
            $('#search').show();
            break;
    }
}

function ProductDataTable(arr) {
    $('.loadingOverlay').show();

    $('#tbl_product').dataTable().fnClearTable();
    $('#tbl_product').dataTable().fnDestroy();
    $('#tbl_product').dataTable({
        data: arr,
        order: [[13, 'asc']],
        scrollX: true,
        columns: [
            {
                data: function (x) {
                    return "<button class='btn btn-sm bg-blue btn_pick_item' type='button'" +
                        "data-id='" + $('#id').val() + "' " +
                        "data-item_id='" + $('#item_id').val() + "' " +
                        "data-item_class='" + $('#item_class').val() + "' " +
                        "data-item_code='" + $('#item_code').val() + "' " +
                        "data-inv_id='" + $('#inv_id').val() + "' " +
                        "data-old_issued_qty='" + $('#old_issued_qty').val() + "' " +
                        "data-jo_no='" + $('#jo_no').val() + "' " +
                        "data-lot_no='" + $('#lot_no').val() + "' " +
                        "data-heat_no='" + $('#heat_no').val() + "' " +
                        "data-alloy='" + $('#alloy').val() + "' " +
                        "data-item='" + $('#item').val() + "' " +
                        "data-size='" + $('#size').val() + "' " +
                        "data-schedule='" + $('#schedule').val() + "' " +
                        "data-remarks='" + $('#remarks').val() + "' " +
                        "data-sc_no='" + $('#sc_no').val() + "' " +
                        "data-qty_weight='" + $('#qty_weight').val() + "' " +
                        "data-inv_qty='" + $('#inv_qty').val() + "' " +
                        "data-issued_qty='" + $('#issued_qty').val() + "' " +
                        ">" +
                        "<i class='fa fa-edit'></i>" +
                        "</button>";
                }, orderable: false, searchable: false
            },
            { data: function(x) {
                return x.item_class + "<input type='hidden' name='detail_item_class[]' value='" + x.item_class + "'>"+
                        "<input type='hidden' name='detail_inv_id[]' value='" + x.inv_id + "'>" +
                        "<input type='hidden' name='detail_old_issued_qty[]' value='" + x.old_issued_qty + "'>" +
                        "<input type='hidden' name='detail_item_id[]' value='" + x.item_id + "'>";
            } },
            { data: function(x) {
                return x.jo_no + "<input type='hidden' name='detail_jo_no[]' value='" + x.jo_no + "'>";
            } },
            { data: function(x) {
                return x.item_code + "<input type='hidden' name='detail_item_code[]' value='" + x.item_code + "'>";
            } },
            { data: function(x) {
                return x.description + "<input type='hidden' name='detail_description[]' value='" + x.description + "'>";
            } },
            { data: function(x) {
                return x.lot_no + "<input type='hidden' name='detail_lot_no[]' value='" + x.lot_no + "'>";
            } },
            { data: function(x) {
                return x.heat_no + "<input type='hidden' name='detail_heat_no[]' value='" + x.heat_no + "'>";
            } },
            {
                data: function (x) {
                    return x.sc_no + "<input type='hidden' name='detail_sc_n[]' value='" + x.sc_no + "'>";
                }
            },
            { data: function(x) {
                return x.alloy + "<input type='hidden' name='detail_alloy[]' value='" + x.alloy + "'>";
            } },
            { data: function(x) {
                return x.item + "<input type='hidden' name='detail_item[]' value='" + x.item + "'>";
            } },
            { data: function(x) {
                return x.size + "<input type='hidden' name='detail_size[]' value='" + x.size + "'>";
            } },
            { data: function(x) {
                return x.schedule + "<input type='hidden' name='detail_schedule[]' value='" + x.schedule + "'>";
            } },

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
            $('.loadingOverlay').hide();
        }
    });
}

function getInventory(item_class,item_code, issued_qty,inv_id) {
    $('.loadingOverlay-modal').show();
    $.ajax({
        url: getInventoryURL,
        type: 'GET',
        dataType: 'JSON',
        data: {
            item_class: item_class,
            item_code: item_code,
            issued_qty: issued_qty,
            inv_id: inv_id
        },
    }).done(function (data, textStatus, xhr) {
        if (data.length > 0) {
            if (data.length > 1) {
                InventoryDataTable(data);
                $('#modal_inventory').modal('show');
            } else {
                var product = data[0];

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

                if (material.item_code == undefined) {
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

                    $('#inv_qty').val(product.current_stock);
                    $('#qty_weight').val(product.qty_weight);
                    $('#issued_qty').prop('readonly', false);

                    $('#inv_id').val(product.inv_id);
                }
            }
        } else {
            msg("No Items found for this Item Code.", 'warning');
        }

    }).fail(function (xhr, textStatus, errorThrown) {
        //msg(errorThrown, textStatus);
        var response = jQuery.parseJSON(xhr.responseText);
        ErrorMsg(response);
    }).always(function () {
        $('.loadingOverlay').hide();
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
                        "data-item='" + x.item + "' " +
                        "data-alloy='" + x.alloy + "' " +
                        "data-size='" + x.size + "' " +
                        "data-schedule='" + x.schedule + "' " +
                        "data-qty_weight='" + x.qty_weight + "' " +
                        "data-qty_pcs='" + x.qty_pcs + "' " +
                        "data-currrent_stock='" + x.currrent_stock + "' " +
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
            { data: 'product_line' },
            { data: 'lot_no' },
            { data: 'heat_no' },
            { data: 'qty_weight' },
            { data: 'qty_pcs' },
            { data: 'currrent_stock' },
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