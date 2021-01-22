$(function () {
	dashboardDataTable([]);
	get_chart();
	get_jono('');

	$('#jo_no').on('change', function (e) {
		e.preventDefault();
		get_chart($(this).val());
	});

	$("#tbl_dashboard").on( 'column-sizing.dt', function ( e, settings ) {
		$(".dataTables_scrollHeadInner").css( "width", "100%" );
	});

	$('#btn_search_filter').on('click', function() {
		$('#modal_ppc_dashboard').modal('show');
	});

	$('#srch_date_from').on('change', function () {
		var selected_date = new Date($(this).val()).toISOString().split('T')[0];
		document.getElementsByName("srch_date_to")[0].setAttribute('min', selected_date);
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

			dashboardDataTable(data);

		}).fail(function (xhr, textStatus, errorThrown) {
			var errors = xhr.responseJSON.errors;

			console.log(errors);
			showErrors(errors);
		}).always(function () {
			$('.loadingOverlay-modal').hide();
		});
	});

	$('#btn_search_excel').on('click', function () {
		var srch_date_from = $('#srch_date_from').val();
		var srch_date_to = $('#srch_date_to').val();
		var srch_jo_sequence = $('#srch_jo_sequence').val();
		var srch_prod_code = $('#srch_prod_code').val();
		var srch_description = $('#srch_description').val();
		var srch_div_code = $('#srch_div_code').val();
		var srch_plant = $('#srch_plant').val();
		var srch_process = $('#srch_process').val();
		var srch_material_used = $('#srch_material_used').val();
		var srch_material_heat_no = $('#srch_material_heat_no').val();
		var srch_lot_no = $('#srch_lot_no').val();
		var srch_status = $('#srch_status').val();

		var url = downloadSearchExcelURL + "?srch_date_from=" + srch_date_from +
			"&srch_date_to=" + srch_date_to +
			"&srch_jo_sequence=" + srch_jo_sequence +
			"&srch_prod_code=" + srch_prod_code +
			"&srch_description=" + srch_description +
			"&srch_div_code=" + srch_div_code +
			"&srch_plant=" + srch_plant +
			"&srch_process=" + srch_process +
			"&srch_material_used=" + srch_material_used +
			"&srch_material_heat_no=" + srch_material_heat_no +
			"&srch_lot_no=" + srch_lot_no +
			"&srch_status=" + srch_status;

		window.location.href = url;
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

function dashboardDataTable(arr) {
	var table = $('#tbl_dashboard');

	table.dataTable().fnClearTable();
	table.dataTable().fnDestroy();
	table.dataTable({
		processing: true,
		data: arr,
		deferRender: true,
		scrollX: true,
		order: [[18, 'desc']],
		columns: [
			{ data: 'jo_sequence', name: 'jo_sequence' },
			{ data: 'prod_code', name: 'prod_code' },
			{ data: 'description', name: 'description' },
			{ data: 'div_code', name: 'div_code' },
			{ data: 'plant', name: 'plant' },
			{ data: 'process', name: 'process' },
			{ data: 'material_used', name: 'material_used' },
			{ data: 'material_heat_no', name: 'material_heat_no' },
			{ data: 'lot_no', name: 'lot_no' },
			{ data: 'sched_qty', name: 'sched_qty' },
			{ data: 'unprocessed', name: 'unprocessed' },
			{ data: 'good', name: 'good' },
			{ data: 'rework', name: 'rework' },
			{ data: 'scrap', name: 'scrap' },

			{ data: 'total_output', name: 'total_output' },

			{ data: 'order_qty', name: 'order_qty' },
			{ data: 'total_issued_qty', name: 'total_issued_qty' },
			{ data: 'issued_qty', name: 'issued_qty' },
			{ data: 'update_date', name: 'update_date' },
			{ data: 'end_date', name: 'end_date' },
			{ data: function(x) {
				switch (x.status) {
                        case 1:
                        case '1':
                            return 'DONE PROCESS';
                            break;
                        case 2:
                        case '2':
                            return 'ON-GOING';
                            break;
                        case 3:
                        case '31':
                            return 'CANCELLED';
                            break;
                        case 4:
                        case '4':
                            return 'TRANSFER ITEM';
                            break;
                        case 5:
                        case '5':
                            return 'ALL PROCESS DONE';
                            break;
                    
                        default:
                            return 'PENDING';
                            break;
                    }
			}, name: 'status'}
			// { data: 'status', name: 'p.status'}
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
		// bStateSave: true,
		lengthMenu: [
			[5, 10, 15, 20, -1],
			[5, 10, 15, 20, "All"]
		],
		pageLength: 10,
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

            if (data.status == 5  || data.status == '1') {
                $(row).css('background-color', 'rgb(139 241 191)'); // GREEN
				$(row).css('color', '#000000');
            }

            if (data.status == 6  || data.status == '5') {
                $(row).css('background-color', 'rgb(121 204 241)'); // BLUE
				$(row).css('color', '#000000');
            }
        },
	});
}



