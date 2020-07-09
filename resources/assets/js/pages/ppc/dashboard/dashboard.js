var dashboard_arr = [];
var dataColumn = [
    { data: 'jo_sequence', name: 'ts.jo_sequence' },
    { data: 'prod_code', name: 'ts.prod_code' },
    { data: 'description', name: 'ts.description' },
    { data: 'div_code', name: 'p.div_code' },
    { data: 'plant', name: 'd.plant' },
    { data: 'process', name: 'p.process' },
    { data: 'material_used', name: 'ts.material_used' },
    { data: 'material_heat_no', name: 'ts.material_heat_no' },
    { data: 'lot_no', name: 'ts.lot_no' },
    { data: 'order_qty', name: 'ts.order_qty' },
    { data: 'issued_qty', name: 'ts.issued_qty' },
    { 
        data: function (e) {
            return e.status;
    }, name: 'p.status'}
    // { data: 'status', name: 'p.status'}
];

$(function () {
    getDatatable('tbl_dashboard', get_dashboard, dataColumn, [], 0);
    get_chart();
    get_jono('');

    $('#jo_no').on('change', function (e) {
        e.preventDefault();
        get_chart($(this).val());
    });

    $("#search").on('click', function (e) {
        if ($('#date_from').val() != '' || $('#date_from').val() != '') {
            getDatatable('tbl_dashboard', get_dashboard + '?date_from=' + $('#date_from').val() + '&date_to=' + $('#date_to').val(), dataColumn, [], 0);
        } else {
            msg('Please Input date', 'warning');
        }

    });
});

function get_chart(jo_no) {
    $('#chart').html('');
    var count = 0;
    $.ajax({
        url: get_chartURl,
        type: 'GET',
        dataType: 'JSON',
        data: { _token: token, jo_no: jo_no },
    }).done(function (data, textStatus, xhr) {
        $.each(data, function (i, x) {
            count++;
            chart = '<div class="col-md-6">' +
                        '<div class="box box-solid">' +
                            '<div class="box-body text-center">' +

                                '<div class="row">'+
                                    '<div class="col-md-12">'+
                                        '<div id="' + count + '" style="height: 370px; max-width: 920px; margin: 0px auto;"></div>' +
                                    '</div>'+
                                '</div>'+
                                
                            '</div>' +
                        '</div>' +
                    '</div>';
            $('#chart').append(chart);

            var options = {
                    title: { text: x.process, fontSize: 20 },
                    theme: "light2",
                    exportEnabled: true,
                    animationEnabled: true,
                    legend:{
                        cursor: "pointer",
                        itemclick: explodePie
                    },
                    data: [{
                            type: "pie",
                            toolTipContent: "{label}: <strong>{y}%</strong>",
                            showInLegend: "true",
                            legendText: "{label}",
                            yValueFormatString: "##0.00\"%\"",
                            indexLabel: "{label} {y}",
                            dataPoints: x.records
                    }]
                };
            $('#' + count).CanvasJSChart(options);

            function explodePie (e) {
                if(typeof (e.dataSeries.dataPoints[e.dataPointIndex].exploded) === "undefined" || !e.dataSeries.dataPoints[e.dataPointIndex].exploded) {
                    e.dataSeries.dataPoints[e.dataPointIndex].exploded = true;
                } else {
                    e.dataSeries.dataPoints[e.dataPointIndex].exploded = false;
                }
                e.chart.render();

            }
        });
    }).fail(function (xhr, textStatus, errorThrown) {
        msg(errorThrown, textStatus);
    });
}

function get_jono() {
    $('#jo_no').html("<option value=''></option>");
    $.ajax({
        url: get_jonoURL,
        type: 'GET',
        dataType: 'JSON',
        data: { _token: token },
    }).done(function (data, textStatus, xhr) {
        $.each(data, function (i, x) {
            $('#jo_no').append("<option value='" + x.jo_sequence + "'>" + x.jo_sequence + "</option>");
        });
    }).fail(function (xhr, textStatus, errorThrown) {
        msg(errorThrown, textStatus);
    });
}



