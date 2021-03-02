var dataColumn = [
	{
		data: function (data) {
			return '<input type="checkbox" class="table-checkbox check_item" value="' + data.id + '">';
		}, name: 'id', orderable: false, searchable: false, width: '5.5%'
	},
	{ data: 'action', name: 'action', orderable: false, searchable: false, width: '5.5%' },
	{ data: 'div_code', name: 'div_code', width: '12.5%' },
	{ data: 'div_name', name: 'div_name', width: '21.5%' },
	{ data: 'plant', name: 'plant', width: '12.5%' },
	{ data: 'leader', name: 'leader', width: '12.5%' },
	{ data: 'updated_at', name: 'updated_at', width: '17.5%' },
	{
		data: function (data) {
			var enable_disable;
			var bg_color = "";
			if (data.is_disable == 0) {
				enable_disable = "Set to Disable";
				bg_color = "btn-danger";
			} else {
				enable_disable = "Set to Enable";
				bg_color = "btn-primary";
			}
			return '<button type="button" class="btn ' + bg_color + ' btn_enable_disable" data-id="' + data.id + '">' + enable_disable + '</button>';
		}, name: '', orderable: false, searchable: false, width: '12.5%'
	}
];

var process_arr = [];
var view_process_arr = [];
var productline_arr = [];

$(function () {
	init();

	$('.validate').on('keyup', function (e) {
		var no_error = $(this).attr('id');
		hideErrors(no_error)
	});

	$('.select-validate').on('change', function (e) {
		var no_error = $(this).attr('id');
		hideErrors(no_error)
	});

	$('#frm_division').on('submit', function (e) {
		$('.loadingOverlay').show();
		e.preventDefault();
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			dataType: 'JSON',
			data: {
				id: $('#id').val(),
				div_code: $('#div_code').val(),
				div_name: $('#div_name').val(),
				plant: $('#plant').val(),
				leader: $('#leader').val(),
				user_id: $('#user_id').val(),
				processes: $('input[name="processes[]"]').map(function () { return $(this).val(); }).get(),
				productlines: $('input[name="productline[]"]').map(function () { return $(this).val(); }).get()
			},
		}).done(function (data, textStatus, xhr) {
			view_process_arr = [];
			process_arr = [];
			addProcess();
			if (textStatus) {
				getProcess(data.id);
				if (data.status == "failed") {
					msg(data.msg, data.status);
				} else {
					msg("Division data was successfully saved.", textStatus);
				}
				// getDatatable('tbl_division', divListURL, dataColumn, [], 0);
				divisionTable();
				view_div();
				clear();
			}
		}).fail(function (xhr, textStatus, errorThrown) {
			var errors = xhr.responseJSON.errors;
			showErrors(errors);
		}).always(function (xhr, textStatus) {
			$('.loadingOverlay').hide();
		});
	});

	$('#tbl_division_body').on('click', '.btn_edit_div', function (e) {
		e.preventDefault();
		show();
		$('#id').val($(this).attr('data-id'));
		$('#div_code').val($(this).attr('data-div_code'));
		$('#div_name').val($(this).attr('data-div_name'));
		$('#plant').val($(this).attr('data-plant'));
		$('#process').val($(this).attr('data-process'));
		$('#leader').val($(this).attr('data-leader')).trigger('change');
		$('#user_id').val($(this).attr('data-user_id'));
		process_arr = [];
		productline_arr = [];
		view_process_arr = [];
		$('#tbl_process_body').html('<tr><td colspan="3">No data displayed.</td></tr>');
		$('#tbl_prodlines_body').html('<tr><td colspan="3">No data displayed.</td></tr>');


		getProcess($(this).attr('data-id'));
		getProductline($(this).attr('data-id'));
	});

	$('#tbl_division_body').on('click', '.btn_enable_disable', function (e) {
		var data_id = $(this).attr('data-id');
		$.ajax({
			url: disableEnableDivisionURL,
			type: 'POST',
			dataType: 'JSON',
			data: { _token: token, id: data_id }
		}).done(function (data, txtStatus, xhr) {
			divisionTable();
		}).fail(function (xhr, txtStatus, errorThrown) {
			console.log(errorThrown);
		});
	});

	$('#btn_delete').on('click', function (e) {
		delete_items('.check_item', divDeleteURL);
	});

	$('#btn_clear').on('click', function (e) {
		clear();
		process_arr = [];
		productline_arr = [];
		$('#tbl_process_body').html('<tr><td colspan="3">No data displayed.</td></tr>');
		$('#tbl_prodlines_body').html('<tr><td colspan="3">No data displayed.</td></tr>');
	});

	$('#btn_cancel').on('click', function (e) {
		cancel();
		process_arr = [];
		productline_arr = [];
		viewProcess(process_arr);
		$('#tbl_process_body').html('<tr><td colspan="3">No data displayed.</td></tr>');
		$('#tbl_prodlines_body').html('<tr><td colspan="3">No data displayed.</td></tr>');

	});

	$('#leader').on('change', function () {
		$('#user_id').val($(this).val());
		// $.ajax({
		// 	url: getuserIDURL,
		// 	type: 'GET',
		// 	dataType: 'JSON',
		// 	data: {
		// 		_token: token,
		// 		leader_name: $(this).val()
		// 	},
		// }).done(function (data, textStatus, xhr) {
		// 	$('#user_id').val(data.id);
		// }).fail(function (xhr, textStatus, errorThrown) {
		// 	msg(errorThrown, textStatus);
		// });
	});

	$('#btn_process').on('click', function () {
		$('#modal_process').modal('show');
	});

	$('#btn_prodline').on('click', function () {
		$('#modal_prodline').modal('show');
	});

	$('#btn_add_process').on('click', function () {
		if ($('#process').val() == "") {
			msg("The Process field is required.", "failed");
		} else {
			if (process_arr.indexOf($('#process').val()) != -1) {
				msg("The Process already existing.", "failed");
			} else {
				process_arr.push($('#process').val());
				addProcess(process_arr);
			}
		}
	});

	$('#tbl_process_body').on('click', '.btn_remove_process', function () {
		var count = $(this).attr('data-count');
		$('#' + count).remove();
		count--;
		process_arr.splice(count, 1);
		addProcess(process_arr);

		if ($('#tbl_process_body > tr').length < 1) {
			$('#tbl_process_body').html('<tr>' +
				'<td colspan="3" class="text-center">No data displayed.</td>' +
				'</tr>');
		}
	});

	//division master productline 
	$('#btn_add_prod_lines').on('click', function () {
		if ($('#prod_lines').val() != "") {
			if (!productline_arr.includes($('#prod_lines').val())) {
				productline_arr.push($('#prod_lines').val());
				addProductline(productline_arr);
			} else {
				msg("The Productline already existing.", "failed");
			}
		} else {
			msg("The Productline field is required.", "failed");
		}
	});

	$('#tbl_prodlines_body').on('click', '.btn_remove_pline', function () {
		var count = $(this).attr('pline-data-count');
		$('#' + count).remove();
		count--;
		productline_arr.splice(count, 1);
		addProductline(productline_arr);

		if ($('#tbl_prodlines_body > tr').length < 1) {
			$('#tbl_prodlines_body').html('<tr>' +
				'<td colspan="3" class="text-center">No data displayed.</td>' +
				'</tr>');
		}
	});

	$('#btn_add').on('click', function() {
		if ($('#id').val() == '') {
			new_div();
		} else {
			update();
		}
	});

	$('#tbl_division').on('click', 'th:first-child',function() {
		if ($('.check_all').is(':checked')) {
			$('.btn_edit_div').prop('disabled', true);
			$('#tbl_division_body .btn_enable_disable').prop('disabled', true);
		} else {
			$('.btn_edit_div').prop('disabled', false);
			$('#tbl_division_body .btn_enable_disable').prop('disabled', false);
		}
	});

	$('#tbl_division_body').on('click', 'td:first-child',function() {
		if ($('#tbl_division_body .dt-checkboxes').is(':checked')) {
			$('.btn_edit_div').prop('disabled', false);
			$('#tbl_division_body .btn_enable_disable').prop('disabled', false);
		} else {
			$('.btn_edit_div').prop('disabled', true);
			$('#tbl_division_body .btn_enable_disable').prop('disabled', true);
		}

	});

	$('#tbl_division_body').on('change', '.dt-checkboxes',function() {
		if ($(this).is(':checked')) {
			$('.btn_edit_div').prop('disabled', true);
			$('#tbl_division_body .btn_enable_disable').prop('disabled', true);
		} else {
			$('.btn_edit_div').prop('disabled', false);
			$('#tbl_division_body .btn_enable_disable').prop('disabled', false);
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
    }

	view_div();
	get_dropdown_productline();

	getLeaders();

	viewProcess(view_process_arr);

	// getDatatable('tbl_division', divListURL, dataColumn, [], 0);
	divisionTable();
	get_dropdown_items_by_id(1, '#process');

	checkAllCheckboxesInTable('#tbl_division','.check_all', '.check_item');
}

function divisionTable() {
	$('#tbl_division').dataTable().fnClearTable();
	$('#tbl_division').dataTable().fnDestroy();
	$('#tbl_division').dataTable({
		ajax: {
			url: divListURL,
			error: function (xhr, textStatus, errorThrown) {
				ErrorMsg(xhr);
			}
		},
		stateSave: true,
		serverSide: true,
		processing: true,
		deferRender: true,
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
				"previous": "Prev",
				"next": "Next",
				"last": "Last",
				"first": "First"
			}
		},
		columnDefs: [
			{
				targets: 0,
				checkboxes: {
					selectRow: true
				}
			}
		],
		select: {
			selector: 'td:not(:nth-child(2)):not(:nth-child(3)):not(:nth-child(4)):not(:nth-child(5)):not(:nth-child(6)):not(:nth-child(7)):not(:nth-child(8))',
			style: 'multi'
		},
		columns: [
			{
				data: function (data) {
					return '<input type="checkbox" class="table-checkbox check_item" value="' + data.id + '">';
				}, name: 'id', orderable: false, searchable: false, width: '5.5%'
			},
			{ data: 'action', name: 'action', orderable: false, searchable: false, width: '5.5%' },
			{ data: 'div_code', name: 'div_code', width: '12.5%' },
			{ data: 'div_name', name: 'div_name', width: '21.5%' },
			{ data: 'plant', name: 'plant', width: '12.5%' },
			{ data: 'leader', name: 'leader', width: '19.5%' },
			{ data: 'updated_at', name: 'updated_at', width: '17.5%' },
			{
				data: function (data) {
					var enable_disable;
					var bg_color = "";
					if (data.is_disable == 0) {
						enable_disable = "<i class='fa fa-ban'></i>";
						bg_color = "btn-danger";
					} else {
						enable_disable = "<i class='fa fa-toggle-on'></i>";
						bg_color = "btn-primary";
					}
					return '<button type="button" class="btn ' + bg_color + ' btn_enable_disable" data-id="' + data.id + '" '+
								'data-disabled="' + data.is_disable+'" '+
								'data-toggle="popover" '+
								'data-content="This Button is to Disable / Enable '+data.div_name+'" '+
								'data-placement="right" '+
							'>' + enable_disable + '</button>';
				}, name: '', orderable: false, searchable: false, width: '5.5%'
			}
		],
		order: [[6,'desc']],
		initComplete: function() {
			$('.btn_edit_div').popover({
				trigger: 'hover focus'
			});

			$('.btn_enable_disable').popover({
				trigger: 'hover focus'
			});
			
			$('#tbl_division .dt-checkboxes-select-all input[type=checkbox]').addClass('table-checkbox check_all');
		},
		fnDrawCallback: function() {
		},
		createdRow: function (row, data, dataIndex) {
			if (data.is_disable == 1) {
				$(row).css('background-color', '#ff6266');
				$(row).css('color', '#fff');
			}
			var dataRow = $(row);
			var checkbox = $(dataRow[0].cells[0].firstChild);

			checkbox.attr('data-id', data.id);
			checkbox.addClass('table-checkbox check_item');
		}
	});
}

async function get_dropdown_productline() {
	var items = [];
	var opt = "<option value=''></option>";
	$("#prod_lines").html(opt);
	var promise = $.ajax({
		url: dropdownProduct,
		type: 'GET',
		dataType: 'JSON',
		data: { _token: token },
	}).done(function (data, textStatus, xhr) {
		items = data;
	}).fail(function (xhr, textStatus, errorThrown) {
		msg(errorThrown, textStatus);
		items = [];
	});

	var result = await promise;

	$.each(result, function (i, x) {
		opt = "<option value='" + x.dropdown_item + "'>" + x.dropdown_item + "</option>";
		$("#prod_lines").append(opt);
	});
}

function clear() {
	$('.clear').val('');
	$('#leader').val(null).trigger('change');
}

function view_div() {
	$('#id').val('');
	$('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
	$('#btn_save').removeClass('bg-green');
	$('#btn_save').addClass('bg-blue');

	$('#btn_add').html('<i class="fa fa-plus"></i> Add New');

	$('#btn_add_div').show();
	$('#btn_save_div').hide();
	$('#btn_cancel_div').hide();
	$('#btn_clear_div').hide();
	$('#btn_delete_div').show();

	$('.readonly').prop('disabled', true);
	$('.dt-checkboxes-select-all input[type=checkbox]').prop('disabled', false);
	$('.dt-checkboxes').prop('disabled', false);
	$('.btn_edit_div').prop('disabled', false);
	$('.btn_enable_disable').prop('disabled', false);

	$('#btn_process').prop('disabled', true);
	$('#btn_prodline').prop('disabled', true);
}

function new_div() {
	$('#id').val('');
	$('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
	$('#btn_save').removeClass('bg-purple');
	$('#btn_save').addClass('bg-blue');

	$('#btn_add_div').hide();
	$('#btn_save_div').show();
	$('#btn_cancel_div').show();
	$('#btn_clear_div').show();
	$('#btn_delete_div').hide();

	$('.readonly').prop('disabled', false);
	$('.dt-checkboxes-select-all input[type=checkbox]').prop('disabled', true);
	$('.dt-checkboxes').prop('disabled', true);
	$('.btn_edit_div').prop('disabled', true);
	$('.btn_enable_disable').prop('disabled', true);

	$('#btn_process').prop('disabled', false);
	$('#btn_prodline').prop('disabled', false);
}

function cancel() {
	clear();
	view_div();
	// $('#btn_save').html("<i class='fa fa-floppy-o'></i> Save");
	// $('#btn_save').removeClass('bg-green');
	// $('#btn_save').addClass('bg-blue');
	// $('#btn_cancel').hide();
	// $('#btn_cancel_div').hide();

	// $('#btn_clear').show();
	// $('#btn_delete').show();
	// $('#btn_clear_div').show();
	// $('#btn_delete_div').show();
}

function show() {
	$('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
	$('#btn_save').removeClass('bg-green');
	$('#btn_save').addClass('bg-blue');

	$('#btn_add').html('<i class="fa fa-edit"></i> Edit');

	$('#btn_add_div').show();
	$('#btn_save_div').hide();
	$('#btn_cancel_div').show();
	$('#btn_clear_div').hide();
	$('#btn_delete_div').hide();

	$('.readonly').prop('disabled', true);
	$('.dt-checkboxes-select-all input[type=checkbox]').prop('disabled', true);
	$('.dt-checkboxes').prop('disabled', true);
	$('.btn_edit_div').prop('disabled', false);
	$('.btn_enable_disable').prop('disabled', false);

	$('#btn_process').prop('disabled', true);
	$('#btn_prodline').prop('disabled', true);
}

function update() {
	$('#btn_save').html("<i class='fa fa-check'></i> Update");
	$('#btn_save').removeClass('bg-blue');
	$('#btn_save').addClass('bg-purple');

	$('#btn_add_div').hide();
	$('#btn_clear_div').hide();
	$('#btn_delete_div').hide();
	$('#btn_cancel_div').show();
	$('#btn_save_div').show();

	$('.readonly').prop('disabled', false);
	$('.dt-checkboxes-select-all input[type=checkbox]').prop('disabled', true);
	$('.dt-checkboxes').prop('disabled', true);
	$('.btn_edit_div').prop('disabled', true);
	$('.btn_enable_disable').prop('disabled', true);

	$('#btn_process').prop('disabled', false);
	$('#btn_prodline').prop('disabled', false);
}

function delete_items(checkboxClass, deleteURL) {
	var chkArray = [];
	$(checkboxClass + ":checked").each(function () {
		chkArray.push($(this).attr('data-id'));
	});

	if (chkArray.length > 0) {
		// confirm_delete(chkArray, token, deleteURL, true, 'tbl_division', divListURL, dataColumn);

		$('.loadingOverlay').show();
		swal({
	        title: "Are you sure?",
	        text: "You will not be able to recover your data!",
	        type: "warning",
	        showCancelButton: true,
	        confirmButtonColor: "#f95454",
	        confirmButtonText: "Yes",
	        cancelButtonText: "No",
	        closeOnConfirm: true,
	        closeOnCancel: false
	    }, function(isConfirm){
	        if (isConfirm) {
	        	$.ajax({
	        		url: deleteURL,
	        		type: 'POST',
	        		dataType: 'JSON',
	        		data: {
	        			_token:token,
	        			id: chkArray
	        		},
	        	}).done(function(data, textStatus, xhr) {
	        		msg(data.msg,data.status)
	                divisionTable();
	        	}).fail(function(xhr, textStatus, errorThrown) {
	        		ErrorMsg(xhr);
	        	}).always(function() {
	        		$('.loadingOverlay').hide();
	        	});
	        } else {
				$('.loadingOverlay').hide();
				$('.check_all').click();
	            swal("Cancelled", "Your data is safe and not deleted.");
	        }
	    });
	} else {
		msg("Please select at least 1 item.", "warning");
	}

	$('.check_all').prop('checked', false);
}

function addProcess(arr) {
	var tbl = '';
	$('#tbl_process_body').html(tbl);

	var cnt = 1;
	$.each(arr, function (i, x) {
		tbl = '<tr id="' + cnt + '">' +
			'<td>' + cnt + '</td>' +
			'<td>' + x +
			'<input type="hidden" name="processes[]" value="' + x + '">' +
			'</td>' +
			'<td>' +
			'<span class="btn_remove_process" data-count="' + cnt + '">' +
			'<i class="text-red fa fa-times"></i>' +
			'</span>' +
			'</td>' +
			'</tr>';
		$('#tbl_process_body').append(tbl);
		cnt++;
	});
}

function addProductline(arr) {
	var tbl = '';
	$('#tbl_prodlines_body').html(tbl);

	var cnt = 1;
	$.each(arr, function (i, x) {
		tbl = '<tr id="' + cnt + '">' +
			'<td>' + cnt + '</td>' +
			'<td>' + x +
			'<input type="hidden" name="productline[]" value="' + x + '">' +
			'</td>' +
			'<td>' +
			'<span class="btn_remove_pline" pline-data-count="' + cnt + '">' +
			'<i class="text-red fa fa-times"></i>' +
			'</span>' +
			'</td>' +
			'</tr>';
		$('#tbl_prodlines_body').append(tbl);
		cnt++;
	});
}

function viewProcess(arr) {
	$('#tbl_view_process').dataTable().fnClearTable();
	$('#tbl_view_process').dataTable().fnDestroy();
	$('#tbl_view_process').dataTable({
		data: arr,
		bLengthChange: false,
		searching: false,
		paging: false,
		columns: [
			{ data: 'process' },
		]
	});
}

function getProcess(id) {
	$.ajax({
		url: getProcessURL,
		type: 'GET',
		dataType: 'JSON',
		data: { _token: token, division_id: id },
	}).done(function (data, textStatus, xhr) {
		$.each(data, function (i, x) {
			process_arr.push(x.process);
			view_process_arr.push({
				process: x.process
			});
		});
		addProcess(process_arr);
		viewProcess(view_process_arr);
	}).fail(function (xhr, textStatus, errorThrown) {
		msg(errorThrown, textStatus);
	});
}

function getProductline(id) {
	$.ajax({
		url: getProductlineURL,
		type: 'GET',
		dataType: 'JSON',
		data: { _token: token, division_id: id },
	}).done(function (data, textStatus, xhr) {
		$.each(data, function (i, x) {
			productline_arr.push(x.productline);
		});
		addProductline(productline_arr);
	}).fail(function (xhr, textStatus, errorThrown) {
		msg(errorThrown, textStatus);
	});
}


function getLeaders() {
	// var opt = "<option value=''></option>";
	// $(el).html(opt);
	$.ajax({
		url: getLeaderURL,
		type: 'GET',
		dataType: 'JSON',
		data: {_token: token},
	}).done(function(data, textStatus, xhr) {
		$('#leader').select2({
			allowClear: true,
			placeholder: 'Select a Leader',
			data: data
		}).val(null).trigger('change');

		// $('#leader').val(null).trigger('change');
		// $.each(data, function(i, x) {
		// 	opt = "<option value='"+x.name+"'>"+x.name+"</option>";
		// 	$(el).append(opt);
		// });
	}).fail(function(xhr, textStatus, errorThrown) {
		console.log("error");
	});
}
