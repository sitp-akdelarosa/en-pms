$(function () {
    $('.loadingOverlay').show();

    get_select_warehouse();
    get_users();
    get_assigned_warehouse([0]);

    init();

    $('.select-validate').on('change', function (e) {
        var no_error = $(this).attr('id');
        hideErrors(no_error)
    });

    $('#btn_save').on('click', function () {
        var user_id = [];
        var warehouse = [];

        var utable = $('#tbl_users').DataTable();
        for (var x = 0; x < utable.context[0].aoData.length; x++) {
            var cells = utable.context[0].aoData[x].anCells;
            if (cells !== null && cells[0].firstChild.checked == true) {
                user_id.push(cells[0].firstChild.value);
            }
        }

        var ptable = $('#tbl_warehouse').DataTable();
        for (var x = 0; x < ptable.context[0].aoData.length; x++) {
            var cells = ptable.context[0].aoData[x].anCells;
            if (cells !== null && cells[0].firstChild.checked == true) {
                warehouse.push(cells[0].firstChild.value);
            }
        }

        if (user_id.length == 0 && warehouse.length == 0) {
            msg('Please select User and Warehouse to assign.', 'warning');
        }

        if (user_id.length > 0 && warehouse.length == 0) {
            msg('Please select Warehouse to assign to user.', 'warning');
        }

        if (user_id.length == 0 && warehouse.length > 0) {
            msg('Please select User to whom to assign the Warehouse.', 'warning');
        }

        if (user_id.length > 0 && warehouse.length > 0) {
            $('.loadingOverlay').show();

            $.ajax({
                url: SaveURL,
                type: 'POST',
                dataType: 'JSON',
                data: {
                    _token: token,
                    user_id: user_id,
                    warehouse: warehouse
                },
            }).done(function (data, textStatus, xhr) {
                msg(data.msg, data.status);
                get_assigned_warehouse(data.user_id);
                clear();
                $('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
            }).fail(function (xhr, textStatus, errorThrown) {
                var errors = xhr.responseJSON.errors;
                showErrors(errors);
            });
        }

    });

    $('#tbl_assign_warehouse_body').on('click', '.btn_edit_warehouse', function (e) {
        e.preventDefault();
        $('#id').val($(this).attr('data-id'));
        $('#user_id').val($(this).attr('data-user_id'));
        $('#warehouse').val($(this).attr('data-warehouse'));
        $('#btn_save').html('<i class="fa fa-check"></i> Update');
    });

    $('#btn_clear').on('click', function (e) {
        clear();
        get_assigned_warehouse([0]);
        $('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
    });

    $('#btn_delete').on('click', function (e) {
        delete_items('.check_item', WarehouseDeleteURL);
    });

    $('#tbl_users').on('click', '.btn_view_mat', function () {
        get_assigned_warehouse([$(this).attr('data-user_id')]);
    });

    $('#tbl_users').on('change', '.check_all_users', function () {
        $('input:checkbox.check_user').not(this).prop('checked', this.checked);
        var table = $('#tbl_users').DataTable();

        for (var x = 0; x < table.context[0].aoData.length; x++) {
            var aoData = table.context[0].aoData[x];
            var tr = aoData.nTr;
            if (aoData.anCells !== null && aoData.anCells[0].firstChild.checked == true) {
                console.log(tr);
                $(tr).addClass('selected');
            } else {
                $(tr).removeClass('selected');
            }
        }
    });

    $('#tbl_users_body').on('change', '.check_user', function () {
        var tr = $(this).parent().parent()[0];
        if ($(this).is(':checked')) {
            $(tr).addClass('selected');
        } else {
            $(tr).removeClass('selected');
        }
    });

    $('#warehouse').on('change', '.check_all_whs', function () {
        $('input:checkbox.check_whs').not(this).prop('checked', this.checked);
        var table = $('#tbl_warehouse').DataTable();

        for (var x = 0; x < table.context[0].aoData.length; x++) {
            var aoData = table.context[0].aoData[x];
            var tr = aoData.nTr;
            if (aoData.anCells !== null && aoData.anCells[0].firstChild.checked == true) {
                console.log(tr);
                $(tr).addClass('selected');
            } else {
                $(tr).removeClass('selected');
            }
        }
    });

    $('#tbl_warehouse_body').on('change', '.check_whs', function () {
        var tr = $(this).parent().parent()[0];

        if ($(this).is(':checked')) {
            $(tr).addClass('selected');
        } else {
            $(tr).removeClass('selected');
        }
    });

    $('#tbl_assign_warehouse').on('change', '.check_all', function () {
        $('input:checkbox.check_item').not(this).prop('checked', this.checked);
        var table = $('#tbl_assign_warehouse').DataTable();

        for (var x = 0; x < table.context[0].aoData.length; x++) {
            var aoData = table.context[0].aoData[x];
            var tr = aoData.nTr;
            if (aoData.anCells !== null && aoData.anCells[0].firstChild.checked == true) {
                console.log(tr);
                $(tr).addClass('selected');
            } else {
                $(tr).removeClass('selected');
            }
        }
    });

    $('#tbl_assign_warehouse_body').on('change', '.check_item', function () {
        var tr = $(this).parent().parent()[0];
        if ($(this).is(':checked')) {
            $(tr).addClass('selected');
            $('#btn_delete').prop('disabled', false);
        } else {
            $(tr).removeClass('selected');
            $('#btn_delete').prop('disabled', true);
        }
    });
});

function init() {
    if (permission_access == '2' || permission_access == 2) {
        $('.permission').prop('readonly', true);
        $('.permission-button').prop('disabled', true);
    } else {
        $('.permission').prop('readonly', false);
        $('.permission-button').prop('disabled', false);
        $("#btn_delete").prop('disabled', true);
    }
}

function get_assigned_warehouse(user_id) {
    $('.loadingOverlay').show();
    $.ajax({
        url: WarehouseListURL,
        type: 'GET',
        dataType: 'JSON',
        data: {
            user_id: user_id
        },
    }).done(function (data, textStatus, xhr) {
        assignedWarehouseTable(data);
    }).fail(function (xhr, textStatus, errorThrown) {
        console.log("error");
    });

}

function assignedWarehouseTable(arr) {
    $('#tbl_assign_warehouse').dataTable().fnClearTable();
    $('#tbl_assign_warehouse').dataTable().fnDestroy();
    $('#tbl_assign_warehouse').dataTable({
        data: arr,
        processing: true,
        deferRender: true,
        bLengthChange: false,
        paging: true,
        pageLength: 10,
        // searching: false,
        order: [[2, 'desc']],
        columns: [
            {
                data: function (data) {
                    return '<input type="checkbox" class="table-checkbox check_item" value="' + data.id + '" data-id="' + data.id + '">';
                }, orderable: false, searchable: false
            },
            { data: 'warehouse' },
            { data: 'fullname' },
            { data: 'updated_at' },

        ],
        initComplete: function () {
            $('.loadingOverlay').hide();
            $('.check_all_items').prop('checked', false);
        },
        fnDrawCallback: function () {
            checkAllCheckboxesInTable("#tbl_assign_warehouse", ".check_all", ".check_item", "#btn_delete");
        },
    });
}

function get_select_warehouse() {
    var opt = "<option value=''></option>";
    $("#warehouse").html(opt);
    $.ajax({
        url: dropdownWarehouse,
        type: 'GET',
        dataType: 'JSON',
        data: { _token: token },
    }).done(function (data, textStatus, xhr) {
        selectWarehouseTable(data);
    }).fail(function (xhr, textStatus, errorThrown) {
        msg(errorThrown, textStatus);
    });
}

function selectWarehouseTable(arr) {
    $('#tbl_warehouse').dataTable().fnClearTable();
    $('#tbl_warehouse').dataTable().fnDestroy();
    $('#tbl_warehouse').dataTable({
        data: arr,
        processing: true,
        deferRender: true,
        bLengthChange: false,
        pageLength: 10,
        // searching: false,
        order: [[1, 'asc']],
        columns: [
            {
                data: function (data) {
                    return '<input type="checkbox" class="table-checkbox check_whs" value="' + data.dropdown_item + '">';
                }, orderable: false, searchable: false
            },
            { data: 'dropdown_item' },
            { data: 'dropdown_name' },

        ],
        initComplete: function () {
            $('.loadingOverlay').hide();
            $('.check_all_whs').prop('checked', false);
        },
        fnDrawCallback: function () {
            checkAllCheckboxesInTable('#tbl_warehouse', '.check_all_whs', '.check_whs');
        },
    });
}

function get_users() {
    var opt = '<option value=""></option>';
    $('#user_id').html(opt);
    $.ajax({
        url: getUserURL,
        type: 'GET',
        dataType: 'JSON',
        data: { _token: token },
    }).done(function (data, textStatus, xhr) {
        usersTable(data);
    }).fail(function (xhr, textStatus, errorThrown) {
        msg(errorThrown, textStatus);
    });
}

function usersTable(arr) {
    $('#tbl_users').dataTable().fnClearTable();
    $('#tbl_users').dataTable().fnDestroy();
    $('#tbl_users').dataTable({
        data: arr,
        processing: true,
        deferRender: true,
        bLengthChange: false,
        pageLength: 10,
        // searching: false,
        order: [[1, 'asc']],
        columns: [
            {
                data: function (data) {
                    return '<input type="checkbox" class="table-checkbox check_user" value="' + data.id + '" data-id="' + data.id + '">';
                }, orderable: false, searchable: false
            },
            { data: 'user_id' },
            { data: 'fullname' },
            {
                data: function (data) {
                    return '<button class="btn btn-blue btn-flat btn-sm btn_view_mat" data-user_id="' + data.id + '">' +
                        '<i class="fa fa-laptop"></i>' +
                        '</button>';
                }, orderable: false, searchable: false
            }

        ],
        initComplete: function () {
            $('.loadingOverlay').hide();
            $('.check_all_users').prop('checked', false);
        },
        fnDrawCallback: function () {
            checkAllCheckboxesInTable('#tbl_users', '.check_all_users', '.check_user');
        }
    });
}

function delete_items(checkboxClass, deleteURL) {
    var chkArray = [];

    var table = $('#tbl_assign_warehouse').DataTable();
    for (var x = 0; x < table.context[0].aoData.length; x++) {
        var aoData = table.context[0].aoData[x];
        if (aoData.anCells !== null && aoData.anCells[0].firstChild.checked == true) {
            chkArray.push(table.context[0].aoData[x].anCells[0].firstChild.attributes['data-id'].value)
        }
    }
    if (chkArray.length > 0) {
        swal({
            title: "Are you sure to delete this data?",
            text: "You will not be able to recover your data!",
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
                    url: deleteURL,
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        _token: token,
                        id: chkArray
                    },
                }).done(function (data, textStatus, xhr) {
                    get_assigned_warehouse([0]);

                    if (data.status == 'success') {
                        msg(data.msg, data.status)
                        is_confirmed_deleted = true;
                    } else {
                        msg(data.msg, data.status)
                    }

                    return data.status;
                }).fail(function (xhr, textStatus, errorThrown) {
                    msg(errorThrown, textStatus);
                });
            } else {
                swal("Cancelled", "Your data is safe and not deleted.");
            }
        });
    } else {
        msg("Please select at least 1 item.", "warning");
    }

    $('.check_all').prop('checked', false);
    clear();
    $('#btn_save').html('<i class="fa fa-plus"></i> Add');
}

function clear() {
    $('.clear').val('');
    get_select_warehouse()
    get_users()
}