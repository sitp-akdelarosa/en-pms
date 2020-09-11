$(function () {
	dashboardDataTable(get_dashboard);
	get_chart();
	get_jono('');

	$('#jo_no').on('change', function (e) {
		e.preventDefault();
		get_chart($(this).val());
	});

	$("#search").on('click', function (e) {
		if ($('#date_from').val() != '' || $('#date_from').val() != '') {
			dashboardDataTable(get_dashboard + '?date_from=' + $('#date_from').val() + '&date_to=' + $('#date_to').val());
		} else {
			msg('Please Input date', 'warning');
		}

	});

	$("#tbl_dashboard").on( 'column-sizing.dt', function ( e, settings ) {
		$(".dataTables_scrollHeadInner").css( "width", "100%" );
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

function dashboardDataTable(data_url) {
	var table = $('#tbl_dashboard');

	table.dataTable().fnClearTable();
	table.dataTable().fnDestroy();
	table.dataTable({
		processing: true,
		serverSide: true,
		ajax: {
			url: data_url,
			error: function(jqXHR, ajaxOptions, thrownError) {
				var response = jqXHR.responseJSON;
				ErrorMsg(response);
			}
		},
		deferRender: true,
		scrollX: true,
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
			{ data: 'scrap', name: 'scrap' },

			{ data: 'total_output', name: 'total_output' },

			{ data: 'order_qty', name: 'order_qty' },
			{ data: 'total_issued_qty', name: 'total_issued_qty' },
			{ data: 'issued_qty', name: 'issued_qty' },
			// { data: 'status', name: 'status' },
			// { data: 'travel_sheet_status', name: 'travel_sheet_status' },
			{ data: 'end_date', name: 'end_date' },
			//{ data: 'updated_at', name: 'updated_at' },

			// { data: 'jo_sequence', name: 'jo_sequence' },
			// { data: 'prod_code', name: 'prod_code' },
			// { data: 'description', name: 'description' },
			// { data: 'div_code', name: 'div_code' },
			// { data: 'plant', name: 'plant' },
			// { data: 'process', name: 'process' },
			// { data: 'material_used', name: 'material_used' },
			// { data: 'material_heat_no', name: 'material_heat_no' },
			// { data: 'lot_no', name: 'lot_no' },
			// { data: 'order_qty', name: 'order_qty' },
			// { data: 'issued_qty', name: 'issued_qty' },
			{ 
				data: function (e) {
					return e.status;
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
		order: [
			[0, "desc"]
		]
	});
}



