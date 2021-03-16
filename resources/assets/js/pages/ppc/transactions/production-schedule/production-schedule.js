const { forEach } = require("lodash");

var selected_products = [];
var selected_material = {};
var saved_jo_details_arr = [];
var ref_id = [];
var materials_data = [];

initializePage();

$(function () {
    $('#btn_filter').on('click', function () {
        $('.srch-clear').val('');
        $('#modal_order_search').modal('show');
    });

    $("#frm_search").on('submit', function (e) {
        e.preventDefault();
        $('.loadingOverlay-modal').show();

        var search_param = objectifyForm($(this).serializeArray());

        OrdersDataTable($(this).attr('action'), search_param);
    });

    $('#btn_search_withdrawal').on('click', function() {
        $('#btn_save').show();
        $('#btn_cancel').show();
        MaterialsDataTable(getMaterialsURL,{ _token: token, rmw_no: $('#rmw_no').val() });
    });

    $('#rmw_no').on('keydown', function(e) {
        if (e.keyCode === 13) {
            $('#btn_save').show();
            $('#btn_cancel').show();
            MaterialsDataTable(getMaterialsURL,{ _token: token, rmw_no: $(this).val() });
        }
    });

    $('#tbl_materials tbody').on( 'click', '.btn_remove_material', function () {
        var materials_datatable = $('#tbl_materials').DataTable();
        materials_datatable.row( $(this).parents('tr') ).remove().draw();
    });

    $('#tbl_materials tbody').on( 'click', '.btn_open_modal', function () {
        var materials_datatable = $('#tbl_materials').DataTable();
        selected_material = materials_datatable.row( $(this).parents('tr') ).data();

        console.log(selected_material);

        $('#item_code').val(selected_material.item_code);
        $('#description').val(selected_material.description);
        $('#heat_no').val(selected_material.heat_no);
        $('#rmw_qty').val(selected_material.rmw_qty);

        MaterialsTableInItemModal([selected_material]);
        getProducts(getProductsURL, { _token: token });

        selected_material._token = token;

        getItemMaterials(selected_material);

        $('#modal_items').modal('show');
    });

    $('#tbl_products_item tbody').on('change', '.chk_product', function() {
        var products_datatable = $('#tbl_products_item').DataTable();
        var data = products_datatable.row( $(this).parents('tr') ).data();

        if ($(this).is(':checked')) {
            var material_used = selected_material.mat_description;

            selected_products.push({
                prod_sum_id: data.id,
                prod_code: data.prod_code,
                description: data.description,                
                back_order_qty: data.quantity,
                sc_no: data.sc_no,
                sched_qty: '',
                material_code: selected_material.mat_material_code,
                heat_no: selected_material.heat_no,
                rmw_qty: parseFloat(selected_material.rmw_qty),
                material_used: material_used,
                lot_no: '',
                blade_consumption: '',
                cut_weight: data.cut_weight,
                cut_length: data.cut_length,
                cut_width: data.cut_width,
                mat_length: selected_material.length,
                mat_weight: selected_material.weight,
                mat_width: selected_material.width,
                assign_qty: '',
                remaining_qty: 0,
                standard_material_used: data.standard_material_used,
                upd_inv_id: selected_material.upd_inv_id,
                inv_id: selected_material.inv_id,
                rmwd_id: selected_material.rmwd_id,
                size: selected_material.size,
                material_type: selected_material.material_type,
                qty_per_piece: 0,
                ship_date: '',
                count: ''
            });
        } else {
            selected_products = selected_products.filter(function( obj ) {
                return obj.prod_sum_id !== data.id;
            });
        }

        SelectedItemsDataTable(selected_products);
    });

    $('#tbl_bom tbody').on('change', '.sched_qty', function() {
        var count = $(this).attr('data-count');
        var material_type = $('#material_type_'+count).val();
        var material_length = ($('#mat_length_'+count).val() == '')? 0 : parseFloat($('#mat_length_'+count).val());
        var material_width = ($('#mat_width_'+count).val() == '')? 0 : parseFloat($('#mat_width_'+count).val());
        var cut_length = ($('#cut_length_'+count).val() == '')? 0 : parseFloat($('#cut_length_'+count).val());
        var cut_weight = ($('#cut_weight_'+count).val() == '')? 0 : parseFloat($('#cut_weight_'+count).val());
        var cut_width = ($('#cut_width_'+count).val() == '')? 0 : parseFloat($('#cut_width_'+count).val());
        var size = ($('#size_'+count).val() == '')? 0 : parseFloat($('#size_'+count).val());
        var sched_qty = ($(this).val() == '')? 0 : parseFloat($(this).val());
        var blade_consumption = ($('#blade_consumption_'+count).val() == '')? 0 : parseFloat($('#blade_consumption_'+count).val());
        var assign_qty = ($('#assign_qty_'+count).val() == '')? 0 : parseFloat($('#assign_qty_'+count).val());

        var computeObject = {
            count: count,
            material_type: material_type,
            material_length: material_length,
            material_width: material_width,
            cut_length: cut_length,
            cut_weight: cut_weight,
            cut_width: cut_width,
            size: size,
            sched_qty: sched_qty,
            blade_consumption: blade_consumption,
            assign_qty: assign_qty,
            state: 'ADD'
        };

        computeMaterial(computeObject);

        if ($('#assign_qty_'+count).val() !== '') {
            checkOverIssuance(computeObject);
        }
    });

    $('#tbl_bom tbody').on('change', '.blade_consumption', function() {
        var count = $(this).attr('data-count');
        var material_type = $('#material_type_'+count).val();
        var material_length = ($('#mat_length_'+count).val() == '')? 0 : parseFloat($('#mat_length_'+count).val());
        var material_width = ($('#mat_width_'+count).val() == '')? 0 : parseFloat($('#mat_width_'+count).val());
        var cut_length = ($('#cut_length_'+count).val() == '')? 0 : parseFloat($('#cut_length_'+count).val());
        var cut_weight = ($('#cut_weight_'+count).val() == '')? 0 : parseFloat($('#cut_weight_'+count).val());
        var cut_width = ($('#cut_width_'+count).val() == '')? 0 : parseFloat($('#cut_width_'+count).val());
        var size = ($('#size_'+count).val() == '')? 0 : parseFloat($('#size_'+count).val());
        var sched_qty = ($('#sched_qty_'+count).val() == '')? 0 : parseFloat($('#sched_qty_'+count).val());
        var blade_consumption = ($(this).val() == '')? 0 : parseFloat($(this).val());
        var assign_qty = ($('#assign_qty_'+count).val() == '')? 0 : parseFloat($('#assign_qty_'+count).val());

        var computeObject = {
            count: count,
            material_type: material_type,
            material_length: material_length,
            material_width: material_width,
            cut_length: cut_length,
            cut_weight: cut_weight,
            cut_width: cut_width,
            size: size,
            sched_qty: sched_qty,
            blade_consumption: blade_consumption,
            assign_qty: assign_qty,
            state: 'ADD'
        };

        computeMaterial(computeObject);

        if ($('#assign_qty_'+count).val() !== '') {
            checkOverIssuance(computeObject);
        }

    });

    $('#tbl_bom tbody').on('change', '.assign_qty', function() {
        var count = $(this).attr('data-count');
        var material_type = $('#material_type_'+count).val();
        var material_length = ($('#mat_length_'+count).val() == '')? 0 : parseFloat($('#mat_length_'+count).val());
        var material_width = ($('#mat_width_'+count).val() == '')? 0 : parseFloat($('#mat_width_'+count).val());
        var cut_length = ($('#cut_length_'+count).val() == '')? 0 : parseFloat($('#cut_length_'+count).val());
        var cut_weight = ($('#cut_weight_'+count).val() == '')? 0 : parseFloat($('#cut_weight_'+count).val());
        var cut_width = ($('#cut_width_'+count).val() == '')? 0 : parseFloat($('#cut_width_'+count).val());
        var size = ($('#size_'+count).val() == '')? 0 : parseFloat($('#size_'+count).val());
        var sched_qty = ($('#sched_qty_'+count).val() == '')? 0 : parseFloat($('#sched_qty_'+count).val());
        var blade_consumption = ($('#blade_consumption_'+count).val() == '')? 0 : parseFloat($('#blade_consumption_'+count).val());
        var assign_qty = ($(this).val() == '')? 0 : parseFloat($(this).val());

        var computeObject = {
            count: count,
            material_type: material_type,
            material_length: material_length,
            material_width: material_width,
            cut_length: cut_length,
            cut_weight: cut_weight,
            cut_width: cut_width,
            size: size,
            sched_qty: sched_qty,
            blade_consumption: blade_consumption,
            assign_qty: assign_qty,
            state: 'ADD'
        };
        checkOverIssuance(computeObject);
    });

    $('#btn_save_bom').on('click', function() {
        var error = 0;
        var over_assign_error = 0;
        var less_assign_error = 0;
        var over_issuance_error = 0;
        var total_withdrawal = ($('#total_withdrawal').val() == '' || isNaN($('#total_withdrawal').val()) || $('#total_withdrawal').val() == 'NaN')? 0 : parseFloat($('#total_withdrawal').val());
        var total_assign = ($('#total_assign').val() == '' || isNaN($('#total_assign').val()) || $('#total_assign').val() == 'NaN')? 0 : parseFloat($('#total_assign').val());

        $('.validate_bom').each(function() {
            if ($(this).val() == '') {
                error++;
            }
        });

        var assign_qty = 0;
        var rmw_issued_qty = parseFloat($('#rmw_qty').val());
        $('.assign_qty').each( function(i,x) {
            assign_qty += parseFloat($(x).val());
        });

        if (assign_qty > rmw_issued_qty) {
            over_assign_error++;
        }
        
        if (assign_qty < rmw_issued_qty) {
            less_assign_error++;
        }

        var count_over = 0;
        var row_over = '';
        $('.sched_qty_feedback').each( function(i,x) {
            if ($(x).html() !== '') {
                over_issuance_error++;
                var com = '';
                if (row_over !== '') {
                    com = ', ';
                }
                row_over += com + (count_over+1);
            }
            count_over++;
        });

        total_assign += assign_qty;

        if (total_assign < total_withdrawal) {
            $('#total_assign').val(total_assign);
        }

        if (error > 0) {
            msg('Please fill out all input field in the table.', 'warning');
        } else if (over_assign_error > 0) {
            msg('Assign qty is greater than Scheduled qty.', 'warning');
        } else if (less_assign_error > 0) {
            msg('Assign qty is less than Scheduled qty. Please assign all withdrawal Qty.', 'warning');
        } else if ($('#rmw_no').val() == "" && $('#rmw_no').val() == null) {
            msg('Please fill out Withdrawal Slip input field.', 'warning');
        } else if (over_issuance_error > 0) {
            swal({
                title: "Are you sure to save?",
                text: 'Some Scheduled Qty have Over Issuance in row: '+ row_over +'.',
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#f95454",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: true,
                closeOnCancel: false
            }, function(isConfirm){
                if (isConfirm) {
                    saveBOM();
                } else {
                    swal("Cancelled", "Saving Item datails is cancelled.");
                }
            });
        } else {
            saveBOM();
        }
    });

    $('#btn_save').on('click', function() {
        var materials_datatable = $('#tbl_materials').DataTable();
        materials = materials_datatable.rows().data();

        var total_withdrawal = 0;
        var total_assign = 0;

        $.each(materials, function(i,x) {
            total_withdrawal += parseFloat(x.rmw_qty);
            total_assign += parseFloat(x.temp_assign_qty);
        });

        // var total_withdrawal = ($('#total_withdrawal').val() == '' || isNaN($('#total_withdrawal').val()) || $('#total_withdrawal').val() == 'NaN')? 0 : parseFloat($('#total_withdrawal').val());
        // var total_assign = ($('#total_assign').val() == '' || isNaN($('#total_assign').val()) || $('#total_assign').val() == 'NaN')? 0 : parseFloat($('#total_assign').val());

        if (total_assign !== total_withdrawal) {
            msg('Assign all withdrawal quantity before saving.', 'failed');
        } else {
            $('.loadingOverlay').show();
            $.ajax({
                url: saveJODetailsURL,
                type: 'POST',
                dataType: 'JSON',
                data: {
                    _token: token,
                    ref_id: ref_id
                }
            }).done(function(data, textStatus, xhr) {
                $('#rmw_no').val('');
                OrdersDataTable(ordersURL,{ _token: token });
                MaterialsDataTable(getMaterialsURL,{ _token: token, rmw_no: $('#rmw_no').val() });
                TravelSheetDataTable(getTravelSheetURL, { _token: token });

                selected_products = [];
                selected_material = [];

                msg(data.msg,data.status);
            }).fail(function(xhr, textStatus, errorThrown) {
                ErrorMsg(xhr);
            }).always(function(xhr, textStatus) {
                $('.loadingOverlay').hide();
            });
        }
    });

    $('#tbl_travel_sheet tbody').on('click', ' .btn_show_jo',function() {
        var tbl_travel_sheet = $('#tbl_travel_sheet').DataTable();
        var data = tbl_travel_sheet.row( $(this).parents('tr') ).data();

        var jo_summary_id = data.jo_summary_id;
        var jo_no = data.jo_no;
        var status = $(this).attr('data-status');
        var sc_no = data.sc_no;

        $('#j_ship_date').val(data.ship_date);

        if (status == 3) {
            $('#btn_save_jo_item').prop('disabled', true);
            $('#j_ship_date').prop('readonly', true);
        } else {
            $('#btn_save_jo_item').prop('disabled', false);
            $('#j_ship_date').prop('readonly', false);
        }
        var param =  {
                        _token: token, 
                        jo_summary_id: jo_summary_id, 
                        sc_no: sc_no
                    };

        $('#j_jo_no').val(jo_no);
        JOdetailsDataTable(getJODetailsURL,param,status);
        $('#modal_jo_details').modal('show');
    });

    $('#tbl_travel_sheet tbody').on('click', ' .btn_cancel_jo',function() {
        var tbl_travel_sheet = $('#tbl_travel_sheet').DataTable();
        var data = tbl_travel_sheet.row( $(this).parents('tr') ).data();
        
        data._token = token;
        
        swal({
            title: "Are you sure to cancel?",
            text: "This will cancel the J.O. #: "+data.jo_no+".",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#f95454",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {
                CancelTravelSheet(data);
                //swal("Cancelled!", "J.O. # "+jo_no+" was successfully cancelled.");
            }
        });
    });

    $('#btn_cancel').on('click', function() {
        $('#rmw_no').val('');
        $('#btn_save').hide();
        $('#btn_cancel').hide();
        OrdersDataTable(ordersURL,{ _token: token });
        MaterialsDataTable(getMaterialsURL,{ _token: token, rmw_no: $('#rmw_no').val() });
        TravelSheetDataTable(getTravelSheetURL, { _token: token });
    });

    $('#btn_save_jo_item').on('click', function() {
        editJoDetailItem();
    });

    $('#btn_jo_filter').on('click', function() {
        $('.srch-clear').val('');
        $('#modal_jo_search').modal('show');
    });

    $("#frm_search_jo").on('submit', function (e) {
        e.preventDefault();
        $('.loadingOverlay-modal').show();

        var search_param = objectifyForm($(this).serializeArray());

        TravelSheetDataTable($(this).attr('action'), search_param);
    });
});

function initializePage() {
    if (permission_access == '2' || permission_access == 2) {
        $('.permission').prop('readonly', true);
        $('.permission-button').prop('disabled', true);
    } else {
        $('.permission').prop('readonly', false);
        $('.permission-button').prop('disabled', false);
    }

    $('#btn_save').hide();
    $('#btn_cancel').hide();

    OrdersDataTable(ordersURL,{ _token: token });
    MaterialsDataTable(getMaterialsURL,{ _token: token, rmw_no: $('#rmw_no').val() });
    TravelSheetDataTable(getTravelSheetURL, { _token: token });
}

function computeMaterial(object) {
    $('#blade_consumption_'+object.count).show();
    $('#qty_per_piece_'+object.count).show();

    var blade_consumption_input = $('#blade_consumption_'+object.count);
    var qty_per_piece_input = $('#qty_per_piece_'+object.count);
    var cut_weight_input = $('#cut_weight_'+object.count);
    var qty_pcs = 0;

    if (object.state !== 'ADD') {
        blade_consumption_input = $('#j_blade_consumption_'+object.count);
        qty_per_piece_input = $('#j_qty_per_piece_'+object.count);
        cut_weight_input = $('#j_cut_weight_'+object.count);
    }

    switch (object.material_type) {
        case 'BAR':
            if (object.cut_weight !== 0 && blade_consumption_input.val() !== '') {
                qty_pcs = object.material_length / (object.cut_length + object.blade_consumption);
                qty_per_piece_input.val(toFixed(qty_pcs,2));

            } else if ((object.cut_weight == 0 && cut_weight_input.val() == '') && blade_consumption_input.val() !== '') {
                var length = (object.cut_weight / object.size / object.size / 6.2)*1000000;
                qty_pcs = object.material_length / (length + object.blade_consumption);
                qty_per_piece_input.val(toFixed(qty_pcs,2));
            }
            break;

        case 'PIPE':
            qty_pcs = object.material_length / (object.cut_length + object.blade_consumption)*2;
            qty_per_piece_input.val(toFixed(qty_pcs,2));
            break;

        case 'PIPE':
            // calculate product plate
            var prod_plate = (object.cut_length * object.cut_width);

            // calculate material plate
            var mat_plate = (object.material_length * object.material_width) + object.blade_consumption; // somehow addition 1.8 for product cut length

            // Calculate stocks
            qty_pcs = mat_plate / prod_plate;

            qty_per_piece_input.val(toFixed(qty_pcs,2));
            break;
        case 'FINISHED':
        case 'CRUDE':
            blade_consumption_input.hide().removeClass('validate_bom');
            qty_per_piece_input.hide().removeClass('validate_bom');
        default:
            
            break;
    }
}

function checkOverIssuance(object) {
    var sched_qty_input = $('#sched_qty_'+object.count);
    var qty_per_piece_input = $('#qty_per_piece_'+object.count);
    var over = 0;

    if (object.state !== 'ADD') {
        sched_qty_input = $('#j_sched_qty_'+object.count);
    }

    var qty_per_piece_whole = (qty_per_piece_input.val() == '')? 0 :parseInt(qty_per_piece_input.val());    
    var sched_qty = (sched_qty_input.val() == '')? 0 :parseFloat(sched_qty_input.val());

    if  (object.material_type == 'FINISHED' || object.material_type == 'CRUDE') {
        var remaining_qty = 0;
        remaining_qty = sched_qty - object.assign_qty;

        if (remaining_qty < 0) {
            sched_qty_input.addClass('is-invalid');
            sched_qty_input.next().addClass('invalid-feedback').html("Over Issuance.");
        } else {
            sched_qty_input.removeClass('is-invalid');
            sched_qty_input.next().removeClass('invalid-feedback').html('');
        }
        
    } else {
        over = sched_qty - (qty_per_piece_whole * object.assign_qty);
        if (over > 0) {
            sched_qty_input.addClass('is-invalid');
            sched_qty_input.next().addClass('invalid-feedback').html("Over Issuance.");
        } else {
            sched_qty_input.removeClass('is-invalid');
            sched_qty_input.next().removeClass('invalid-feedback').html('');
        }

        // remaining qty
        var remaining_qty = ((object.material_length * object.assign_qty) - (sched_qty * (object.cut_length + object.blade_consumption))) / object.material_length;
    }

    console.log(remaining_qty);

    $('#remaining_qty_'+object.count).val(toFixed(remaining_qty,4));
}

function OrdersDataTable(ajax_url, object_data) {
    $('#tbl_orders').dataTable().fnClearTable();
    $('#tbl_orders').dataTable().fnDestroy();
    $('#tbl_orders').dataTable({
        ajax: {
            url: ajax_url,
            data: object_data
        },
        processing: true,
        deferRender: true,
        order: [[1,'asc']],
        language: {
            aria: {
                sortAscending: ": activate to sort column ascending",
                sortDescending: ": activate to sort column descending"
            },
            emptyTable: "No data available in table",
            info: "Showing _START_ to _END_ of _TOTAL_ records",
            infoEmpty: "No records found",
            infoFiltered: "(filtered1 from _MAX_ total records)",
            lengthMenu: "Show _MENU_",
            search: "Search:",
            zeroRecords: "No matching records found",
            paginate: {
                "previous":"Prev",
                "next": "Next",
                "last": "Last",
                "first": "First"
            }
        },
        columns: [
            { data: 'sc_no', name: 'ps.sc_no', width: '8.5%' },
            { data: 'prod_code', name: 'ps.prod_code', width: '12.5%' },
            { data: 'description', name: 'ps.description', width: '16.5%' },
            { data: 'quantity', name: 'ps.quantity', width: '12.5%' },
            { data: 'sched_qty', name: 'ps.sched_qty', width: '12.5%' },
            { data: 'po', name: 'ps.po', width: '12.5%' },
            { data: 'status', name: 'ps.status', width: '12.5%' },
            { data: 'date_upload', name: 'ps.date_upload', width: '12.5%' }
        ],
        initComplete: function() {
            $('.loadingOverlay').hide();
            $('.loadingOverlay-modal').hide();
        }
    });
}

function MaterialsDataTable(ajax_url, object_data) {
    $('.loadingOverlay').show();
    var total_withdrawal = 0;

    var materials_datatable = $('#tbl_materials').DataTable();

    materials_datatable.clear();
    materials_datatable.destroy();
    $('#tbl_materials').DataTable({
        ajax: {
            url: ajax_url,
            data: object_data,
            dataSrc: function(returnedData, textStatus, xhr) {
                if (returnedData.data['status']) {}
                return returnedData.data;
            },
            error: function(xhr, textStatus, errorThrown) {
                ErrorMsg(xhr);
            }
        },
        lengthChange: false,
        searching: false,
        processing: true,
        deferRender: true,
        pageLength: 50,
        paging: false,
        order: [[1,'asc']],
        language: {
            aria: {
                sortAscending: ": activate to sort column ascending",
                sortDescending: ": activate to sort column descending"
            },
            emptyTable: "No data available in table",
            info: "Showing _START_ to _END_ of _TOTAL_ records",
            infoEmpty: "No records found",
            infoFiltered: "(filtered1 from _MAX_ total records)",
            lengthMenu: "Show _MENU_",
            search: "Search:",
            zeroRecords: "No matching records found",
            paginate: {
                "previous":"Prev",
                "next": "Next",
                "last": "Last",
                "first": "First"
            }
        },
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false, width: '5%' },
            { data: 'item_code', name: 'prod_code', orderable: false, searchable: false, width: '10%' },
            { data: 'description', name: 'description', orderable: false, searchable: false, width: '15%' },
            { data: 'heat_no', name: 'heat_no', orderable: false, searchable: false, width: '10%' },
            { data: 'rmw_qty', name: 'rmw_qty', orderable: false, searchable: false, width: '10%' },
            { data: 'temp_assign_qty', name: 'temp_assign_qty', orderable: false, searchable: false, width: '10%' },

            { data: 'size', name: 'size', orderable: false, searchable: false, width: '5%' },
            { data: 'length', name: 'length', orderable: false, searchable: false, width: '10%' },
            { data: 'weight', name: 'weight', orderable: false, searchable: false, width: '10%' },
            { data: 'width', name: 'width', orderable: false, searchable: false, width: '5%' },
            { data: 'material_type', name: 'material_type', orderable: false, searchable: false, width: '10%' },
        ],
        createdRow: function(row, data, dataIndex) {
            //total_withdrawal += parseFloat(data.rmw_qty);
        },
        initComplete: function() {
            
            $('.loadingOverlay').hide();
            $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
        },
        fnDrawCallback: function() {
            var api = this.api();
            // Output the data for the visible rows to the browser's console
            var data = api.rows( {page:'current'} ).data();

            total_withdrawal = 0;
            $.each(data, function(i,x) {
                total_withdrawal += parseFloat(x.rmw_qty);
            });

            $('#total_withdrawal').val(total_withdrawal);

            $("#tbl_materials").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
    });
}

function MaterialsTableInItemModal(arr) {
    var tbl_mateials_item = $('#tbl_mateials_item').DataTable();

    tbl_mateials_item.clear();
    tbl_mateials_item.destroy();
    $('#tbl_mateials_item').DataTable({
        data: arr,
        lengthChange: false,
        searching: false,
        deferRender: true,
        pageLength: 50,
        paging: false,
        order: [[0,'asc']],
        language: {
            aria: {
                sortAscending: ": activate to sort column ascending",
                sortDescending: ": activate to sort column descending"
            },
            emptyTable: "No data available in table",
            info: "Showing _START_ to _END_ of _TOTAL_ records",
            infoEmpty: "No records found",
            infoFiltered: "(filtered1 from _MAX_ total records)",
            lengthMenu: "Show _MENU_",
            search: "Search:",
            zeroRecords: "No matching records found",
            paginate: {
                "previous":"Prev",
                "next": "Next",
                "last": "Last",
                "first": "First"
            }
        },
        columns: [
            { data: 'item_code', orderable: false, searchable: false, width: '25%' },
            { data: 'description', orderable: false, searchable: false, width: '25%' },
            { data: 'heat_no', orderable: false, searchable: false, width: '25%' },
            { data: 'rmw_qty', orderable: false, searchable: false, width: '25%' },
        ],
        initComplete: function() {
            $('.loadingOverlay-modal').hide();
            $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
        }
    });
}

function getProducts(ajax_url, object_data) {
    $('#tbl_products_item').dataTable().fnClearTable();
    $('#tbl_products_item').dataTable().fnDestroy();
    $('#tbl_products_item').dataTable({
        ajax: {
            url: ajax_url,
            data: object_data
        },
        processing: true,
        deferRender: true,
        order: [[1,'asc']],
        pageLength: 5,
        scrollX: true,
        lengthMenu: [
			[5, 10, 15, 20, -1],
			[5, 10, 15, 20, "All"]
		],
        language: {
            aria: {
                sortAscending: ": activate to sort column ascending",
                sortDescending: ": activate to sort column descending"
            },
            emptyTable: "No data available in table",
            info: "Showing _START_ to _END_ of _TOTAL_ records",
            infoEmpty: "No records found",
            infoFiltered: "(filtered1 from _MAX_ total records)",
            lengthMenu: "Show _MENU_",
            search: "Search:",
            zeroRecords: "No matching records found",
            paginate: {
                "previous":"Prev",
                "next": "Next",
                "last": "Last",
                "first": "First"
            }
        },
        columns: [
            { data: 'action', name: 'action', width: '3.11%' },
            { data: 'sc_no', name: 'sc_no', width: '11.11%' },
            { data: 'prod_code', name: 'prod_code', width: '15.11%' },
            { data: 'description', name: 'description', width: '15.11%' },
            { data: 'quantity', name: 'quantity', width: '11.11%' },
            { data: 'sched_qty', name: 'sched_qty', width: '11.11%' },
            { data: 'po', name: 'po', width: '11.11%' },
            { data: 'status', name: 'status', width: '11.11%' },
            { data: 'date_upload', name: 'date_upload', width: '11.11%' }
        ],
        initComplete: function() {
            $('.loadingOverlay').hide();
            $('.loadingOverlay-modal').hide();
            $($.fn.DataTable.tables(true)).DataTable().columns.adjust();
        }
    });
}

function SelectedItemsDataTable(arr) {
    var count = 0;
    var tbl_bom = $('#tbl_bom').DataTable();

    tbl_bom.clear();
    tbl_bom.destroy();
    $('#tbl_bom').DataTable({
        data: arr,
        lengthChange: false,
        searching: false,
        deferRender: true,
        pageLength: 50,
        paging: false,
        scrollX: true,
        order: [[0,'asc']],
        language: {
            aria: {
                sortAscending: ": activate to sort column ascending",
                sortDescending: ": activate to sort column descending"
            },
            emptyTable: "No data available in table",
            info: "Showing _START_ to _END_ of _TOTAL_ records",
            infoEmpty: "No records found",
            infoFiltered: "(filtered1 from _MAX_ total records)",
            lengthMenu: "Show _MENU_",
            search: "Search:",
            zeroRecords: "No matching records found",
            paginate: {
                "previous":"Prev",
                "next": "Next",
                "last": "Last",
                "first": "First"
            }
        },
        columns: [
            { data: function(x) {
                return x.prod_code + "<input type='hidden' id='prod_code_"+count+"' class='prod_code' data-count='"+count+"' name='prod_code[]' value='"+x.prod_code+"' />"+
                "<input type='hidden' data-count='"+count+"' id='prod_sum_id_"+count+"' class='prod_sum_id' name='prod_sum_id[]' value='"+x.prod_sum_id+"' />"+
                "<input type='hidden' data-count='"+count+"' id='upd_inv_id_"+count+"' class='upd_inv_id' name='upd_inv_id[]' value='"+x.upd_inv_id+"' />"+
                "<input type='hidden' data-count='"+count+"' id='inv_id_"+count+"' class='inv_id' name='inv_id[]' value='"+x.inv_id+"' />"+
                "<input type='hidden' data-count='"+count+"' id='rmwd_id_"+count+"' class='rmwd_id' name='rmwd_id[]' value='"+x.rmwd_id+"' />"+
                "<input type='hidden' data-count='"+count+"' id='size_"+count+"' class='size' name='size[]' value='"+x.size+"' />"+
                "<input type='hidden' data-count='"+count+"' id='material_type_"+count+"' class='material_type' name='material_type[]' value='"+x.material_type+"' />"+
                "<input type='hidden' data-count='"+count+"' id='heat_no_"+count+"' class='heat_no' name='heat_no[]' value='"+x.heat_no+"' />"+
                "<input type='hidden' data-count='"+count+"' id='rmw_qty_"+count+"' class='rmw_qty' name='rmw_qty[]' value='"+x.rmw_qty+"' />"+
                "<input type='hidden' data-count='"+count+"' id='material_code_"+count+"' class='material_code' name='material_code[]' value='"+x.material_code+"' />"+
                "<input type='hidden' data-count='"+count+"' id='standard_material_used_"+count+"' class='standard_material_used' name='standard_material_used[]' value='"+x.standard_material_used+"' />"+
                "<input type='hidden' data-count='"+count+"' id='count_"+count+"' class='count' name='count[]' value='"+count+"' />";
            }, orderable: false, searchable: false, width: '9.67%' },
            { data: function(x) {
                return x.description + "<input type='hidden' class='description' data-count='"+count+"' id='description_"+count+"' name='description[]' value='"+x.description+"' />";
            }, orderable: false, searchable: false, width: '12.67%' },
            { data: function(x) {
                return x.sc_no + "<input type='hidden' class='sc_no' data-count='"+count+"' id='sc_no_"+count+"' name='sc_no[]' value='"+x.sc_no+"' />";
            }, orderable: false, searchable: false, width: '3.67%' },
            { data: function(x) {
                return x.back_order_qty + "<input type='hidden' class='back_order_qty' data-count='"+count+"' id='back_order_qty_"+count+"' name='back_order_qty[]' value='"+x.back_order_qty+"' />";
            }, orderable: false, searchable: false, width: '6.67%' },

            { data: function(x) {
                return "<input type='text' style='width:60px' class='cut_weight validate_bom' data-count='"+count+"' id='cut_weight_"+count+"' name='cut_weight[]' value='"+x.cut_weight+"' onkeypress='return isNumberKey(event)' />";
            }, orderable: false, searchable: false, width: '3.67%' },
            { data: function(x) {
                return "<input type='text' style='width:60px' class='cut_length validate_bom' data-count='"+count+"' id='cut_length_"+count+"' name='cut_length[]' value='"+x.cut_length+"' onkeypress='return isNumberKey(event)' />";
            }, orderable: false, searchable: false, width: '3.67%' },
            { data: function(x) {
                return "<input type='text' style='width:60px' class='cut_width validate_bom' data-count='"+count+"' id='cut_width_"+count+"' name='cut_width[]' value='"+x.cut_width+"' onkeypress='return isNumberKey(event)' />";
            }, orderable: false, searchable: false, width: '3.67%' },

            { data: function(x) {
                return "<input type='text' style='width:60px' class='sched_qty validate_bom' data-count='"+count+"' id='sched_qty_"+count+"' name='sched_qty[]' value='"+x.sched_qty+"' onkeypress='return isNumberKey(event)' />"+
                "<div id='sched_qty_" + count +"_feedback' class='sched_qty_feedback'></div>";;
            }, orderable: false, searchable: false, width: '6.67%' },
            
            { data: function(x) {
                return "<input type='text' style='width:100px' class='blade_consumption validate_bom' data-count='"+count+"' id='blade_consumption_"+count+"' name='blade_consumption[]' value='"+x.blade_consumption+"' onkeypress='return isNumberKey(event)' />";
            }, orderable: false, searchable: false, width: '6.67%' },
            { data: function(x) {
                return "<input type='text' style='width:80px' class='qty_per_piece' data-count='"+count+"' id='qty_per_piece_"+count+"' name='qty_per_piece[]' value='"+x.qty_per_piece+"' readonly/>";
            }, orderable: false, searchable: false, width: '4.67%' },

            { data: function(x) {
                return "<input type='text' style='width:80px' class='assign_qty validate_bom' data-count='"+count+"' id='assign_qty_"+count+"' name='assign_qty[]' value='"+x.assign_qty+"' onkeypress='return isNumberKey(event)' />";
            }, orderable: false, searchable: false, width: '3.67%' },
            { data: function(x) {
                return "<input type='text' style='width:80px' class='remaining_qty' data-count='"+count+"' id='remaining_qty_"+count+"' name='remaining_qty[]' value='"+x.remaining_qty+"' readonly />";
            }, orderable: false, searchable: false, width: '4.67%' },

            { data: function(x) {
                return "<input type='text' style='width:60px' class='lot_no validate_bom' data-count='"+count+"' id='lot_no_"+count+"' name='lot_no[]' value='"+x.lot_no+"' />";
            }, orderable: false, searchable: false, width: '6.67%' },

            { data: function(x) {
                return "<input type='date' style='width:100px' class='ship_date validate_bom' data-count='"+count+"' id='ship_date_"+count+"' name='ship_date[]' value='"+x.ship_date+"' />";
            }, orderable: false, searchable: false, width: '6.67%' },

            { data: function(x) {
                return x.material_used+"<input type='hidden' style='width:200px' class='material_used validate_bom' data-count='"+count+"' id='material_used_"+count+"' name='material_used[]' value='"+x.material_used+"' />";
            }, orderable: false, searchable: false, width: '12.67%' },
            
            { data: function(x) {
                return x.mat_length + "<input type='hidden' class='mat_length' data-count='"+count+"' id='mat_length_"+count+"' name='mat_length[]' value='"+x.mat_length+"' />";
            }, orderable: false, searchable: false, width: '3.67%' },
            { data: function(x) {
                return x.mat_weight + "<input type='hidden' class='mat_weight' data-count='"+count+"' id='mat_weight_"+count+"' name='mat_weight[]' value='"+x.mat_weight+"' />";
            }, orderable: false, searchable: false, width: '4.67%' },
            { data: function(x) {
                return x.mat_width + "<input type='hidden' class='mat_width' data-count='"+count+"' id='mat_width_"+count+"' name='mat_width[]' value='"+x.mat_width+"' />";
            }, orderable: false, searchable: false, width: '3.67%' },
            { data: function(x) {
                return x.size + "<input type='hidden' class='size' data-count='"+count+"' id='size_"+count+"' name='size[]' value='"+x.size+"' />";
            }, orderable: false, searchable: false, width: '3.67%' }
            
            
        ],
        createdRow: function(row, data, dataIndex) {
            var dataRow = $(row);
            var mat_used_td = $(dataRow[0].cells[14]);
            var cut_weight_input = $(dataRow[0].cells[4].firstChild);
            var cut_length_input = $(dataRow[0].cells[5].firstChild);
            var cut_width_input = $(dataRow[0].cells[6].firstChild);

            if (data.material_code == data.standard_material_used) {
                mat_used_td.removeClass('bg-red');
                cut_weight_input.prop('readonly', true).removeClass('mat_validate');
                cut_length_input.prop('readonly', true).removeClass('mat_validate');
                cut_width_input.prop('readonly', true).removeClass('mat_validate');

                $('.sched_qty:first').focus();
            } else {
                var error = "This is not the Product's Standard Material";

                mat_used_td.addClass('bg-red');
                cut_weight_input.prop('readonly', false).addClass('mat_validate');
                cut_length_input.prop('readonly', false).addClass('mat_validate');
                cut_width_input.prop('readonly', false).addClass('mat_validate');
            }
            $(row).attr('id', 'tr_'+count);

            count++;
        },
        initComplete: function() {
            $('.loadingOverlay-modal').hide();
            $($.fn.DataTable.tables(true)).DataTable().columns.adjust();

            $('.sched_qty:first').focus();
        }
    });
}

function saveBOM() {
    $('.loadingOverlay-modal').show();

    var param = {
        prod_sum_id: $('input[name="prod_sum_id[]"]').map(function(){return $(this).val();}).get(),
        prod_code: $('input[name="prod_code[]"]').map(function(){return $(this).val();}).get(),
        description: $('input[name="description[]"]').map(function(){return $(this).val();}).get(),
        back_order_qty: $('input[name="back_order_qty[]"]').map(function(){return $(this).val();}).get(),
        sc_no: $('input[name="sc_no[]"]').map(function(){return $(this).val();}).get(),
        sched_qty: $('input[name="sched_qty[]"]').map(function(){return $(this).val();}).get(),
        material_code: $('input[name="material_code[]"]').map(function(){return $(this).val();}).get(),
        heat_no: $('input[name="heat_no[]"]').map(function(){return $(this).val();}).get(),
        rmw_qty: $('input[name="rmw_qty[]"]').map(function(){return $(this).val();}).get(),
        material_used: $('input[name="material_used[]"]').map(function(){return $(this).val();}).get(),
        lot_no: $('input[name="lot_no[]"]').map(function(){return $(this).val();}).get(),
        blade_consumption: $('input[name="blade_consumption[]"]').map(function(){return $(this).val();}).get(),
        cut_weight: $('input[name="cut_weight[]"]').map(function(){return $(this).val();}).get(),
        cut_length: $('input[name="cut_length[]"]').map(function(){return $(this).val();}).get(),
        cut_width: $('input[name="cut_width[]"]').map(function(){return $(this).val();}).get(),
        mat_length: $('input[name="mat_length[]"]').map(function(){return $(this).val();}).get(),
        mat_weight: $('input[name="mat_weight[]"]').map(function(){return $(this).val();}).get(),
        mat_width: $('input[name="mat_width[]"]').map(function(){return $(this).val();}).get(),
        assign_qty: $('input[name="assign_qty[]"]').map(function(){return $(this).val();}).get(),
        remaining_qty: $('input[name="remaining_qty[]"]').map(function(){return $(this).val();}).get(),
        standard_material_used: $('input[name="standard_material_used[]"]').map(function(){return $(this).val();}).get(),
        upd_inv_id: $('input[name="upd_inv_id[]"]').map(function(){return $(this).val();}).get(),
        inv_id: $('input[name="inv_id[]"]').map(function(){return $(this).val();}).get(),
        rmwd_id: $('input[name="rmwd_id[]"]').map(function(){return $(this).val();}).get(),
        size: $('input[name="size[]"]').map(function(){return $(this).val();}).get(),
        material_type: $('input[name="material_type[]"]').map(function(){return $(this).val();}).get(),
        qty_per_piece: $('input[name="qty_per_piece[]"]').map(function(){return $(this).val();}).get(),
        ship_date: $('input[name="ship_date[]"]').map(function(){return $(this).val();}).get(),
        count: $('input[name="count[]"]').map(function () { return $(this).val(); }).get(),
        rmw_no: $('#rmw_no').val(),
        _token: token
    };

    $.ajax({
        url: saveItemMaterialsURL,
        type: 'POST',
        dataType: 'JSON',
        data: param
    }).done(function(data, textStatus, xhr) {
        ref_id.push(data.ref_id);
        MaterialsDataTable(getMaterialsURL,{ _token: token, rmw_no: $('#rmw_no').val() });
        // var input = $('.sched_qty');
        // var sched_qty = 0;
        // var total = 0;

        // for(var i = 0; i < input.length; i++){
        //     sched_qty += (isNaN(parseFloat($(input[i]).val())))? 0 :  parseFloat($(input[i]).val());
        // }
        // $('#td_item_' + $('#item_count').val()).html(sched_qty + "<input type='hidden' "+
        //             "id='sched_qty_item_"+$('#item_count').val()+"' "+
        //             "class='form-control form-control-sm sched_qty_item' name='sched_qty_item[]' "+
        //             "value='"+sched_qty+"'>");

        // var sched_item_input = $('.sched_qty_item');
        // for(var i = 0; i < sched_item_input.length; i++){
        //     var sched_item = (isNaN(parseFloat($(sched_item_input[i]).val())))? 0 :  parseFloat($(sched_item_input[i]).val());
        //     total += sched_item;
        // }

        // $('#total_sched_qty').val(total)

        // if (sched_qty > 0) {
        //     $('#btn_save_div').show();
        //     $('#btn_edit_div').hide();
        //     $('#btn_cancel_div').show();
        // } else {
        //     $('#btn_save_div').hide();
        //     $('#btn_edit_div').show();
        //     $('#btn_cancel_div').hide();
        // }

        msg(data.msg,data.status);
    }).fail(function(xhr, textStatus, errorThrown) {
        ErrorMsg(xhr);
    }).always(function(xhr, textStatus) {
        $('.loadingOverlay-modal').hide();
    });
}

function getItemMaterials(param) {
    $('.loadingOverlay-modal').hide();
    $.ajax({
        url: getItemMaterialsURL,
        type: 'GET',
        dataType: 'JSON',
        data: param
    }).done(function(data, textStatus, xhr) {
        selected_products = data;
        SelectedItemsDataTable(selected_products);
    }).fail(function(xhr, textStatus, errorThrown) {
        ErrorMsg(xhr);
    }).always(function(xhr, textStatus) {
        $('.loadingOverlay-modal').hide();
    });
}

function TravelSheetDataTable(ajax_url, object_data) {
    var tbl_travel_sheet = $('#tbl_travel_sheet').DataTable();

    tbl_travel_sheet.clear();
    tbl_travel_sheet.destroy();
    tbl_travel_sheet = $('#tbl_travel_sheet').DataTable({
        ajax: {
            url: ajax_url,
            data: object_data,
            error: function(xhr,textStatus, errorThrown) {
                ErrorMsg(xhr);
            }
        },
        processing: true,
        order: [[13,'desc']],
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false, width: '3.14%' },
            { data: 'jo_no', name: 'jo_no', width: '7.14%' },
            { data: 'sc_no', name: 'sc_no', width: '7.14%' },
            { data: 'product_code', name: 'product_code', width: '7.14%' },
            { data: 'description', name: 'description', width: '10.14%' },
            { data: 'back_order_qty', name: 'back_order_qty', width: '7.14%' },
            { data: 'sched_qty', name: 'sched_qty', width: '7.14%' },
            { data: 'issued_qty', name: 'issued_qty', width: '7.14%' },
            { data: 'rmw_no', name: 'rmw_no', width: '7.14%' },
            { data: 'material_used', name: 'material_used', width: '7.14%' },
            { data: 'material_heat_no', name: 'material_heat_no', width: '5.14%' },
            { data: 'lot_no', name: 'lot_no', width: '5.14%' },
            { data: 'ship_date', name: 'ship_date', width: '5.14%' },
            { data: function(data) {
				switch (data.status) {
					case 0:
						return 'No quantity issued';
						break;
                    case '1':
						return 'Ready to Issue';
						break;
					case '2':
						return 'On-going Process';
						break;
					case '3':
						return 'Cancelled';
						break;
					case '4':
						return 'Proceeded to Production';
						break;
					case '5':
						return 'Closed';
						break;
					case '6':
						return 'In Production';
						break;
				}
			}, name: 'status', width: '7.14%' },
            { data: 'updated_at', name: 'updated_at', width: '7.14%' },
        ],
        createdRow: function(row, data, dataIndex) {            
            if (data.status == 2 || data.status == '2') {
                $(row).css('background-color', '#001F3F'); // NAVY
				$(row).css('color', '#fff');
            }

            if (data.status == 3  || data.status == '3') {
                $(row).css('background-color', '#ff6266'); // RED
                $(row).css('color', '#fff');
            }

            if (data.status == 4  || data.status == '4') {
                $(row).css('background-color', '#7460ee'); // PURPLE
				$(row).css('color', '#fff');
            }

            if (data.status == 5  || data.status == '5') {
                $(row).css('background-color', 'rgb(139 241 191)'); // GREEN
				$(row).css('color', '#000000');
            }

            if (data.status == 6  || data.status == '6') {
                $(row).css('background-color', 'rgb(121 204 241)'); // BLUE
				$(row).css('color', '#000000');
            }
        },
        fnDrawCallback: function() {
            $("#tbl_travel_sheet").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        initComplete: function() {
            $('.loadingOverlay-modal').hide();
        }
    });
}

function JOdetailsDataTable(ajax_url,ajax_data,status) {
    var count = 0;
    $('#tbl_jo_item_details').dataTable().fnClearTable();
    $('#tbl_jo_item_details').dataTable().fnDestroy();
    $('#tbl_jo_item_details').dataTable({
        ajax: {
            url: ajax_url,
            data: ajax_data,
            error: function(xhr,textStatus, errorThrown) {
                ErrorMsg(xhr);
            }
        },
        processing: true,
        searching: false,
        lengthChange: false,
        paging: false,
        columns: [
            { 
                data: function(x) {
                    var indx = count;
                    if (x.count !== undefined) {
                        indx = x.count;
                    }
                    return "<button type='button' class='btn btn-sm bg-red btn_remove_detail' "+
                                "data-jo_summary_id='"+x.jo_summary_id+"' data-id='"+x.id+"' data-count='"+indx+"'>"+
                                "<i class='fa fa-times'></i>" +
                            "</button>"+
                            "<input type='hidden' data-count='"+indx+"' name='j_jd_id[]' id='j_jd_id_"+indx+"' class='j_jd_id' value='"+x.id+"'/>" +
                            "<input type='hidden' data-count='"+indx+"' name='j_jo_summary_id[]' id='j_jo_summary_id_"+indx+"' class='j_jo_summary_id' value='"+x.jo_summary_id+"'/>" +
                            "<input type='hidden' data-count='"+indx+"' name='j_prod_sched_id[]' id='j_prod_sched_id_"+indx+"' class='j_prod_sched_id' value='"+x.prod_sched_id+"'/>" +
                            "<input type='hidden' data-count='"+indx+"' name='j_heat_no_id[]' id='j_heat_no_id_"+indx+"' class='j_heat_no_id' value='"+x.heat_no_id+"'/>" +
                            "<input type='hidden' data-count='"+indx+"' name='j_upd_inv_id[]' id='j_upd_inv_id_"+indx+"' class='j_upd_inv_id' value='"+x.upd_inv_id+"'/>" +
                            "<input type='hidden' data-count='"+indx+"' name='j_inv_id[]' id='j_inv_id_"+indx+"' class='j_inv_id' value='"+x.inv_id+"'/>" +
                            "<input type='hidden' data-count='"+indx+"' name='j_rmwd_id[]' id='j_rmwd_id_"+indx+"' class='j_rmwd_id' value='"+x.rmw_id+"'/>" +
                            "<input type='hidden' data-count='"+indx+"' name='j_size[]' id='j_size_"+indx+"' class='j_size' value='"+x.size+"'/>" +
                            "<input type='hidden' data-count='"+indx+"' name='j_computed_per_piece[]' id='j_computed_per_piece_"+indx+"' class='j_computed_per_piece' value='"+x.computed_per_piece+"'/>" +
                            "<input type='hidden' data-count='"+indx+"' name='j_material_type[]' id='j_material_type_"+indx+"' class='j_material_type' value='"+x.material_type+"'/>"+
                            "<input type='hidden' data-count='"+indx+"' name='j_count[]' id='j_count_"+indx+"' class='j_count' value='"+indx+"'/>";
                }, searchable: false, sortable: false, width: '3.55%' 
            },
            { 
                data: function(x) {
                    var indx = count;
                    if (x.count !== undefined) {
                        indx = x.count;
                    }
                    return x.sc_no+"<input type='hidden' data-count='"+indx+"' name='j_sc_no[]' id='j_sc_no_"+indx+"' class='j_sc_no' value='"+x.sc_no+"'/>";
                }, searchable: false, sortable: false, width: '5.55%' 
            },
            { 
                data: function(x) {
                    var indx = count;
                    if (x.count !== undefined) {
                        indx = x.count;
                    }
                    return x.product_code+"<input type='hidden' data-count='"+indx+"' name='j_prod_code[]' id='j_prod_code_"+indx+"' class='j_prod_code' value='"+x.product_code+"'/>";
                }, searchable: false, sortable: false, width: '5.55%' 
            },
            { 
                data: function(x) {
                    var indx = count;
                    if (x.count !== undefined) {
                        indx = x.count;
                    }
                    return x.description+"<input type='hidden' data-count='"+indx+"' name='j_description[]' id='j_description_"+indx+"' class='j_description' value='"+x.description+"'/>";
                }, searchable: false, sortable: false, width: '5.55%' 
            },
            { 
                data: function(x) {
                    var indx = count;
                    if (x.count !== undefined) {
                        indx = x.count;
                    }
                    return x.back_order_qty+"<input type='hidden' data-count='"+indx+"' name='j_order_qty[]' id='j_order_qty_"+indx+"' class='j_order_qty' value='"+x.back_order_qty+"'/>";
                }, searchable: false, sortable: false, width: '5.55%' 
            },
            { 
                data: function(x) {
                    var indx = count;
                    if (x.count !== undefined) {
                        indx = x.count;
                    }
                    return "<input type='number' step='0.01' data-count='"+indx+"' name='j_sched_qty[]' id='j_sched_qty_"+indx+"' class='form-control form-control-sm jo_validate j_sched_qty' value='"+x.sched_qty+"'/>"+
                    "<div id='j_sched_qty_" + indx +"_feedback' class='j_sched_qty__feedback'></div>";
                }, searchable: false, sortable: false, width: '5.55%' 
            },
            { 
                data: function(x) {
                    var indx = count;
                    if (x.count !== undefined) {
                        indx = x.count;
                    }
                    return "<input type='text' data-count='"+indx+"' name='j_material_heat_no[]' id='j_material_heat_no_"+indx+"' class='form-control form-control-sm j_material_heat_no' value='"+x.material_heat_no+"' readonly />";
                }, searchable: false, sortable: false, width: '5.55%' 
            },

            { 
                data: function(x) {
                    var indx = count;
                    if (x.count !== undefined) {
                        indx = x.count;
                    }
                    return "<div class='input-group input-group-sm'>" +
                            "<input type='number' data-count='"+indx+"' step='0.01' id='j_rmw_issued_qty_"+indx+"' name='j_rmw_issued_qty[]' class='form-control form-control-sm j_rmw_issued_qty' value='"+x.rmw_issued_qty+"' readonly>"+
                            "<div class='input-group-append'>" +
                                "<span class='input-group-text'>PCS</span>" +
                            "</div>";
                }, searchable: false, sortable: false, width: '5.55%' 
            },
            { 
                data: function(x) {
                    var indx = count;
                    if (x.count !== undefined) {
                        indx = x.count;
                    }
                    return "<input type='text' data-count='"+indx+"' name='j_material_used[]' id='j_material_used_"+indx+"' class='form-control form-control-sm j_material_used' value='"+x.material_used+"' readonly/>" +
                    "<div id='j_material_used_" + indx +"_feedback' class='j_material_used__feedback'></div>";
                }, searchable: false, sortable: false, width: '8.55%' 
            },
            { 
                data: function(x) {
                    var indx = count;
                    if (x.count !== undefined) {
                        indx = x.count;
                    }
                    return "<input type='text' data-count='"+indx+"' name='j_lot_no[]' class='form-control form-control-sm jo_validate j_lot_no' value='"+x.lot_no+"'/>";
                }, searchable: false, sortable: false, width: '5.55%' 
            },
            { 
                data: function(x) {
                    var indx = count;
                    if (x.count !== undefined) {
                        indx = x.count;
                    }
                    return "<input type='number' data-count='"+indx+"' step='0.01' id='j_blade_consumption_"+indx+"' name='j_blade_consumption[]' class='form-control form-control-sm jo_validate j_blade_consumption' value='"+x.blade_consumption+"'/>";
                }, searchable: false, sortable: false, width: '5.55%' 
            },
            { 
                data: function(x) {
                    var indx = count;
                    if (x.count !== undefined) {
                        indx = x.count;
                    }
                    return "<input type='number' data-count='"+indx+"' step='0.01' id='j_cut_weight_"+indx+"' name='j_cut_weight[]' class='form-control form-control-sm jo_validate j_cut_weight' value='"+x.cut_weight+"'/>";
                }, searchable: false, sortable: false, width: '5.55%' 
            },
            { 
                data: function(x) {
                    var indx = count;
                    if (x.count !== undefined) {
                        indx = x.count;
                    }
                    return "<input type='number' data-count='"+indx+"' step='0.01' id='j_cut_length_"+indx+"' name='j_cut_length[]' class='form-control form-control-sm j_cut_length'value='"+x.cut_length+"' />";
                }, searchable: false, sortable: false, width: '5.55%' 
            },
            { 
                data: function(x) {
                    var indx = count;
                    if (x.count !== undefined) {
                        indx = x.count;
                    }
                    return "<input type='number' data-count='"+indx+"' step='0.01' id='j_cut_width_"+indx+"' name='j_cut_width[]' class='form-control form-control-sm j_cut_width' value='"+x.cut_width+"'/>";
                }, searchable: false, sortable: false, width: '5.55%' 
            },
            { 
                data: function(x) {
                    var indx = count;
                    if (x.count !== undefined) {
                        indx = x.count;
                    }
                    return "<input type='number' data-count='"+indx+"' step='0.01' id='j_mat_length_"+indx+"' name='j_mat_length[]' class='form-control form-control-sm j_mat_length' value='"+x.mat_length+"' readonly/>";
                }, searchable: false, sortable: false, width: '5.55%' 
            },
            { 
                data: function(x) {
                    var indx = count;
                    if (x.count !== undefined) {
                        indx = x.count;
                    }
                    return "<input type='number' data-count='"+indx+"' step='0.01' id='j_mat_weight_"+indx+"' name='j_mat_weight[]' class='form-control form-control-sm jo_validate j_mat_weight' value='"+x.mat_weight+"' readonly/>";
                }, searchable: false, sortable: false, width: '5.55%' 
            },
            { 
                data: function(x) {
                    var indx = count;
                    if (x.count !== undefined) {
                        indx = x.count;
                    }
                    return "<input type='number' data-count='"+indx+"' step='0.01' id='j_assign_qty_"+indx+"' name='j_assign_qty[]' class='form-control form-control-sm jo_validate j_assign_qty' value='"+x.assign_qty+"'/>";
                }, searchable: false, sortable: false, width: '5.55%' 
            },
            { 
                data: function(x) {
                    var indx = count;
                    if (x.count !== undefined) {
                        indx = x.count;
                    }
                    return "<input type='number' data-count='"+indx+"' step='0.01' id='j_remaining_qty_"+indx+"' name='j_remaining_qty[]' class='form-control form-control-sm j_remaining_qty' value='"+x.remaining_qty+"' readonly/>";
                }, searchable: false, sortable: false, width: '5.55%' 
            },
            
        ],
        createdRow: function(row, data, dataIndex) {
            var dataRow = $(row);
            var sched_qty = $(dataRow[0].cells[5].firstChild);
            var mat_used_input = $(dataRow[0].cells[8].firstChild);
            var lot_no = $(dataRow[0].cells[9].firstChild);
            var blade_consp = $(dataRow[0].cells[10].firstChild);
            var cut_weight_input = $(dataRow[0].cells[11].firstChild);
            var cut_length_input = $(dataRow[0].cells[12].firstChild);
            var cut_width_input = $(dataRow[0].cells[13].firstChild);
            var assign_qty = $(dataRow[0].cells[16].firstChild);

            if (data.material_code == data.standard_material_used) {
                mat_used_input.removeClass('is-invalid');
                mat_used_input.next().removeClass('invalid-feedback').html('');

                cut_weight_input.prop('readonly', true).removeClass('jo_validate');
                cut_length_input.prop('readonly', true).removeClass('jo_validate');
                cut_width_input.prop('readonly', true).removeClass('jo_validate');
            } else {
                var error = "This is not the Product's Standard Material";

                mat_used_input.addClass('is-invalid');
                mat_used_input.next().addClass('invalid-feedback').html(error);

                cut_weight_input.prop('readonly', false).addClass('jo_validate');
                cut_length_input.prop('readonly', false).addClass('jo_validate');
                cut_width_input.prop('readonly', false).addClass('jo_validate');
            }

            if (data.cancelled == 1) {
                sched_qty.prop('readonly', true).removeClass('jo_validate');
                lot_no.prop('readonly', true).removeClass('jo_validate');
                blade_consp.prop('readonly', true).removeClass('jo_validate');
                cut_weight_input.prop('readonly', true).removeClass('jo_validate');
                cut_length_input.prop('readonly', true).removeClass('jo_validate');
                cut_width_input.prop('readonly', true).removeClass('jo_validate');
                assign_qty.prop('readonly', true).removeClass('jo_validate');
            }

            saved_jo_details_arr.push(data);

            $(row).attr('id', 'j_tr_'+count);

            count++;
        },
        fnDrawCallback: function (oSettings) {
            // $('.dataTables_scrollBody').slimScroll({
            //     height: '300px'
            // });
            $("#tbl_jo_item_details").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        initComplete: function() {
            $("#tbl_jo_item_details").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");

            // if (saved_jo_details_arr.length > 0) {
            //     $('#j_ship_date').val(saved_jo_details_arr[0].ship_date);
            // }

            if (status == 3) {
                $('.btn_remove_detail').prop('disabled', true);
            } else {
                $('.btn_remove_detail').prop('disabled', false);
            }
        }
    });

    $('#tbl_jo_item_details tbody').on('click', '.btn_remove_detail', function() {
        var  id = $(this).attr('data-id');
        var jo_summary_id = $(this).attr('data-jo_summary_id');
        swal({
            title: "Are you sure to delete this Item?",
            text: "This data will be deleted from this J.O. Number.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#f95454",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: true,
            closeOnCancel: false
        }, function(isConfirm){
            if (isConfirm) {
               deleteJoDetailItem(id, jo_summary_id);
            } else {
                swal("Cancelled", "The data is safe and not deleted.");
            }
        });
    });
}

function CancelTravelSheet(param) {
    $('.loadingOverlay').hide();
    $.ajax({
        url: cancelTravelSheetURL,
        type: 'POST',
        dataType: 'JSON',
        data: param
    }).done(function(data, textStatus, xhr) {
        OrdersDataTable(ordersURL,{ _token: token });
        TravelSheetDataTable(getTravelSheetURL, { _token: token });
        msg(data.msg,data.status);
    }).fail(function(xhr, textStatus, errorThrown) {
        ErrorMsg(xhr);
    }).always(function(xhr, textStatus) {
        $('.loadingOverlay').hide();
    });
}

function deleteJoDetailItem(id, jo_summary_id) {
    $.ajax({
        url: deleteJoDetailItemURL,
        type: 'POST',
        dataType: 'JSON',
        data: {
            _token: token,
            id: id
        }
    }).done(function(data, textStatus, xhr) {
        JOdetailsDataTable(getJODetailsURL, {_toke: token, jo_summary_id: jo_summary_id});
        TravelSheetDataTable(getTravelSheetURL, { _token: token });
        msg(data.msg,data.status);
    }).fail(function(xhr, textStatus, errorThrown) {
        ErrorMsg(xhr);
    }).always(function(xhr, textStatus) {
        $('.loadingOverlay-modal').hide();
    });
}

function editJoDetailItem() {
    $('.loadingOverlay-modal').show();
    var param = {
        _token: token,
        j_jd_id: $('input[name="j_jd_id[]"]').map(function () { return $(this).val(); }).get(),
        j_upd_inv_id: $('input[name="j_upd_inv_id[]"]').map(function () { return $(this).val(); }).get(),
        j_inv_id: $('input[name="j_inv_id[]"]').map(function () { return $(this).val(); }).get(),
        j_rmwd_id: $('input[name="j_rmwd_id[]"]').map(function () { return $(this).val(); }).get(),
        j_size: $('input[name="j_size[]"]').map(function () { return $(this).val(); }).get(),
        j_computed_per_piece: $('input[name="j_computed_per_piece[]"]').map(function () { return $(this).val(); }).get(),
        j_material_type: $('input[name="j_material_type[]"]').map(function () { return $(this).val(); }).get(),
        j_sc_no: $('input[name="j_sc_no[]"]').map(function () { return $(this).val(); }).get(),
        j_prod_code: $('input[name="j_prod_code[]"]').map(function () { return $(this).val(); }).get(),
        j_description: $('input[name="j_description[]"]').map(function () { return $(this).val(); }).get(),
        j_order_qty: $('input[name="j_order_qty[]"]').map(function () { return $(this).val(); }).get(),
        j_sched_qty: $('input[name="j_sched_qty[]"]').map(function () { return $(this).val(); }).get(),
        j_material_heat_no: $('input[name="j_material_heat_no[]"]').map(function () { return $(this).val(); }).get(),
        j_rmw_issued_qty: $('input[name="j_rmw_issued_qty[]"]').map(function () { return $(this).val(); }).get(),
        j_material_used: $('input[name="j_material_used[]"]').map(function () { return $(this).val(); }).get(),
        j_lot_no: $('input[name="j_lot_no[]"]').map(function () { return $(this).val(); }).get(),
        j_blade_consumption: $('input[name="j_blade_consumption[]"]').map(function () { return $(this).val(); }).get(),
        j_cut_weight: $('input[name="j_cut_weight[]"]').map(function () { return $(this).val(); }).get(),
        j_cut_length: $('input[name="j_cut_length[]"]').map(function () { return $(this).val(); }).get(),
        j_cut_width: $('input[name="j_cut_width[]"]').map(function () { return $(this).val(); }).get(),
        j_mat_length: $('input[name="j_mat_length[]"]').map(function () { return $(this).val(); }).get(),
        j_mat_weight: $('input[name="j_mat_weight[]"]').map(function () { return $(this).val(); }).get(),
        j_assign_qty: $('input[name="j_assign_qty[]"]').map(function () { return $(this).val(); }).get(),
        j_remaining_qty: $('input[name="j_remaining_qty[]"]').map(function () { return $(this).val(); }).get(),
        j_heat_no_id: $('input[name="j_heat_no_id[]"]').map(function () { return $(this).val(); }).get(),
        j_prod_sched_id: $('input[name="j_prod_sched_id[]"]').map(function () { return $(this).val(); }).get(),
        j_jo_summary_id: $('input[name="j_jo_summary_id[]"]').map(function () { return $(this).val(); }).get(),
        j_ship_date: $('#j_ship_date').val()
    }

    $.ajax({
        url: editJoDetailItemURL,
        type: 'POST',
        dataType: 'JSON',
        data: param
    }).done(function(data, textStatus, xhr) {
        JOdetailsDataTable(getJODetailsURL, {_toke: token, jo_summary_id: data.jo_summary_id});
        TravelSheetDataTable(getTravelSheetURL, { _token: token });
        msg(data.msg,data.status);
    }).fail(function(xhr, textStatus, errorThrown) {
        ErrorMsg(xhr);
    }).always(function(xhr, textStatus) {
        $('.loadingOverlay-modal').hide();
    });
    
}