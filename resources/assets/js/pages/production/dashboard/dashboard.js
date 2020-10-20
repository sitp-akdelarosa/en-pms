var dashboard_arr = [];

$( function() {
    getDashboard();
});

function getDashboard(){
     $.ajax({
        url: getDashBoardURL,
        type: 'GET',
        dataType: 'JSON',
    }).done(function(data, textStatus, xhr) {
        dashboard_arr = data;
        makeDashTable(dashboard_arr)
    }).fail(function() {
        console.log("error");
    });
}

function makeDashTable(arr) {
    $('#tbl_prod_dashboard').dataTable().fnClearTable();
    $('#tbl_prod_dashboard').dataTable().fnDestroy();
    $('#tbl_prod_dashboard').dataTable({
        data: arr,
        bLengthChange : false,
        paging: true,
        searching: true,
        columns: [
            {data: 'jo_sequence', name: 'ts.jo_sequence'},
            {data: 'prod_code', name: 'ts.prod_code'},
            {data: 'description', name: 'ts.description'},
            { data: 'lot_no', name: 'ts.lot_no' },
            { data: 'issued_qty', name: 'ts.issued_qty' },
            {data: 'process', name: 'p.process'},
            { data: 'unprocessed', name: 'p.unprocessed' },
            { data: 'good', name: 'p.good' },
            { data: 'rework', name: 'p.rework' },
            { data: 'scrap', name: 'p.scrap'},
            // {data: 'div_code', name: 'p.div_code'},
            // {data: 'plant', name: 'd.plant'},
            // {data: 'material_used', name: 'ts.material_used'},
            // {data: 'material_heat_no', name: 'ts.material_heat_no'},
            {data: function(x) {
                var status = 'ON PROCESS';
                if (x.status == 1) {
                    status = 'READY FOR FG';
                }else if(x.status == 2){
                    status = 'FINISHED';
                }else if(x.status == 3){
                    status = 'CANCELLED';
                }else if(x.status == 4){
                    status = 'TRANSFER ITEM';
                }
                return status;
            }},
        ]
    });
}