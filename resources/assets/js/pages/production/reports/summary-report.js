$(function() {
    init();

    $("#frm_search").on('submit', function(e) {
        e.preventDefault();
        $('.loadingOverlay-modal').show();

        $.ajax({
            url: $(this).attr('action'),
            type: 'GET',
            dataType: 'JSON',
            data: $(this).serialize(),
        }).done(function(data, textStatus, xhr) {
            $('#dl_date_from').val($('#date_from').val());
            $('#dl_date_to').val($('#date_to').val());
            $('#dl_jo_no').val($('#jo_no').val());
            $('#dl_prod_code').val($('#prod_code').val());
            $('#dl_code_description').val($('#code_description').val());
            $('#dl_div_code').val($('#div_code').val());
            $('#dl_process_name').val($('#process_name').val());

            productionSummaryDataTable(data);

        }).fail(function(xhr, textStatus, errorThrown) {
            ErrorMsg(xhr);
        }).always(function() {
            $('.loadingOverlay-modal').hide();
        });
    });

    $('#btn_filter').on('click', function() {
        $('#modal_search').modal('show');
    });

    $('#btn_download').on('click', function() {
        window.location.href = downloadExcel + '?date_from=' + $('#dl_date_from').val() +
            '&date_to=' + $('#dl_date_to').val() +
            '&jo_no=' + $('#dl_jo_no').val() +
            '&prod_code=' + $('#dl_prod_code').val() +
            '&code_description=' + $('#dl_code_description').val() +
            '&div_code=' + $('#dl_div_code').val() +
            '&process_name=' + $('#dl_process_name').val();
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

    productionSummaryDataTable([]);

    $('#date_from').on('change', function() {
        setMinxDate('date_to', $(this).val());
    });

    $('#date_to').on('change', function() {
        setMaxDate('date_from', $(this).val());
    });
}

function productionSummaryDataTable(arr) {
    $('#tbl_summary').dataTable().fnClearTable();
    $('#tbl_summary').dataTable().fnDestroy();
    $('#tbl_summary').dataTable({
        data: arr,
        lengthMenu: [
            [5, 10, 15, 20, -1],
            [5, 10, 15, 20, "All"]
        ],
        pageLength: 10,
        order: [
            [0, 'desc']
        ],
        columns: [
            { data: 'created_at', name: 'created_at' },
            { data: 'machine_no', name: 'machine_no' },
            { data: 'prod_code', name: 'prod_code' },
            { data: 'code_description', name: 'code_description' },
            { data: 'item', name: 'item' },
            { data: 'alloy', name: 'alloy' },
            { data: 'size', name: 'size' },
            { data: 'class', name: 'class' },
            { data: 'heat_no', name: 'heat_no' },
            { data: 'lot_no', name: 'lot_no' },
            { data: 'div_code', name: 'div_code' },
            { data: 'process_name', name: 'process_name' },
            { data: 'total', name: 'total' },
            { data: 'good', name: 'good' },
            { data: 'rework', name: 'rework' },
            { data: 'scrap', name: 'scrap' },
            { data: 'alloy_mix', name: 'alloy_mix' },
            { data: 'convert', name: 'convert' },
            { data: 'finish_weight', name: 'finish_weight' },
            { data: 'wgood', name: 'wgood' },
            { data: 'wrework', name: 'wrework' },
            { data: 'wscrap', name: 'wscrap' },
            { data: 'walloy_mix', name: 'walloy_mix' },
            { data: 'wconvert', name: 'wconvert' },
            { data: 'rrework', name: 'rrework' },
            { data: 'rscrap', name: 'rscrap' },
            { data: 'jo_no', name: 'jo_no' }
        ],
        fnDrawCallback: function() {
            $("#tbl_summary").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        initComplete: function() {
            $('.loadingOverlay').hide();
        }
    });
}