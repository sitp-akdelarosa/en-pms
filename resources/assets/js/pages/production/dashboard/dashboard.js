var dashboard_arr = [];

$( function() {
    getDashboard();
});

function getDashboard() {
    var tbl_prod_dashboard = $('#tbl_prod_dashboard').DataTable();

    tbl_prod_dashboard.clear();
    tbl_prod_dashboard.destroy();
    tbl_prod_dashboard = $('#tbl_prod_dashboard').DataTable({
        ajax: {
            url: getDashBoardURL,
            data: { _token: token },
            error: function(xhr,textStatus, errorThrown) {
                ErrorMsg(xhr);
            }
        },
        processing: true,
        order: [[0,'desc']],
        columns: [
            { data: 'jo_sequence', name: 'ts.jo_sequence' },
            { data: 'prod_code', name: 'ts.prod_code' },
            { data: 'description', name: 'ts.description' },
            { data: 'lot_no', name: 'ts.lot_no' },
            { data: 'issued_qty', name: 'ts.issued_qty' },
            { data: 'process', name: 'p.process' },
            { data: 'unprocessed', name: 'p.unprocessed' },
            { data: 'good', name: 'p.good' },
            { data: 'rework', name: 'p.rework' },
            { data: 'scrap', name: 'p.scrap' },
            { data: function(x) {
                switch (x.status) {
                    case 1:
                    case '1':
                        return 'DONE PROCESS'
                        break;
                    case 2:
                    case '2':
                        return 'ON-GOING'
                        break;
                    case 3:
                    case '31':
                        return 'CANCELLED'
                        break;
                    case 4:
                    case '4':
                        return 'TRANSFER ITEM'
                        break;
                    case 5:
                    case '5':
                        return 'ALL PROCESS DONE'
                        break;
                
                    default:
                        return 'PENDING';
                        break;
                }
                // var status = '';
                // if (x.status == 1) {
                //     status = 'DONE'; //READY FOR FG
                // } else if (x.status == 2){
                //     status = 'FINISHED';
                // } else if (x.status == 3){
                //     status = 'CANCELLED';
                // } else if (x.status == 4){
                //     status = 'TRANSFER ITEM';
                // }
                // return status;
            } },
            { data: 'end_date', name: 'p.end_date' },
        ],
        fnDrawCallback: function() {
            $("#tbl_prod_dashboard").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        initComplete: function() {
            $('.loadingOverlay').hide();
        }
    });
}