$( function() {
	init();

	$(document).on('keydown', function (e) {
		if ($('#product_code_assembly_tab').hasClass('active')) {
			switch (e.keyCode) {
				//F1: Block F1
				case 112:
					e.preventDefault();
					window.onhelp = function () {
						return false;
					}
					if (!$('#btn_add_assembly').is(':disabled') && !$('#btn_add_assembly').is(':hidden')) {
						$('#btn_add_assembly').click();
					}
					break;
				//F2: SAVE
				case 113:
					e.preventDefault();
					if (!$('#btn_save_assembly').is(':disabled') && !$('#btn_save_assembly').is(':hidden')) {
						$('#btn_save_assembly').click();
					}
					break;
				//F3: UPDATE
				case 114:
					e.preventDefault();
					if (!$('#btn_update_assembly').is(':disabled') && !$('#btn_update_assembly').is(':hidden')) {
						$('#btn_update_assembly').click();
					}
					break;
				//F4: CLEAR
				case 115:
					e.preventDefault();
					if (!$('#btn_clear_assembly').is(':disabled') && !$('#btn_clear_assembly').is(':hidden')) {
						$('#btn_clear_assembly').click();
					}
					break;
				//F6: Block F6
				case 117:
					e.preventDefault();
					break;
				//F8: DELETE
				case 119:
					e.preventDefault();
					if (!$('#btn_delete_assembly').is(':disabled') && !$('#btn_delete_assembly').is(':hidden')) {
						$('#btn_delete_assembly').click();
					}
					break;
				//F10: 
				case 121:
					e.preventDefault();
					
					break;
				//F12: CLOSE
				case 123:
					e.preventDefault();
					if (!$('#btn_cancel_assembly').is(':disabled') && !$('#btn_cancel_assembly').is(':hidden')) {
						$('#btn_cancel_assembly').click();
					}
					break;
				default:

			}
		}
    });

	$('body').on('keydown', '.switch', function(e) {
		
		var self = $(this)
			, form = self.parents('form:eq(0)')
			, focusable
			, next
			;
		if (e.keyCode == 40) {
			focusable = form.find('.switch').filter(':visible');
			next = focusable.eq(focusable.index(this)+1);

			if (next.is(":disabled")) {
				next = focusable.eq(focusable.index(this) + 2);
			}

			if (next.length) {
				next.focus();
			}
			return false;
		}

		if (e.keyCode == 38) {
			focusable = form.find('.switch').filter(':visible');
			next = focusable.eq(focusable.index(this)-1);

			if (next.is(":disabled")) {
				next = focusable.eq(focusable.index(this) - 2);
			}

			if (next.length) {
				next.focus();
			}
			return false;
		}

		if (e.keyCode === 13) {
			focusable = form.find('.switch').filter(':visible');
			next = focusable.eq(focusable.index(this));

			if (next.length) {
				switch (e.target.type) {
					case "submit":
						next.form.submit();
						break;
					default:
						next.click();
				}
				next.focus();
			}
			return false;
		}
		
	});

	$('#character_num').on('change', function() {
		hideErrors('character_num');
	});

	$('.validate').on('keyup', function(e) {
		var no_error = $(this).attr('id');
		hideErrors(no_error)
	});

	$('#frm_code_assembly').on('submit', function(e) {
		e.preventDefault();
		$('.loadingOverlay').show();
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			dataType: 'JSON',
			data: $(this).serialize(),
		}).done(function(data, textStatus, xhr) {
			if (textStatus == 'success') {
				msg("Data was successfully saved.","success");
				getAssemblies();
				view_assembly();
			}
		}).fail(function(xhr, textStatus, errorThrown) {
			if (xhr.status == 422) {
				var errors = xhr.responseJSON.errors;
				showErrors(errors);
			} else {
				ErrorMsg(xhr);
			}
		}).always( function() {
			$('.loadingOverlay').hide();
		});
	});

	$('#tbl_prodcode_assembly_body').on('click', '.btn_edit_assembly', function(e) {
		e.preventDefault();
		update_assembly();
		$('#assembly_id').val($(this).attr('data-id'));
		$('#prod_type').val($(this).attr('data-prod_type'));
		$('#character_num').val($(this).attr('data-character_num'));
		$('#character_code').val($(this).attr('data-character_code'));
		$('#description').val($(this).attr('data-description'));
	});

	$('#btn_clear_assembly').on('click', function(e) {
		clear();
	});

	$('#btn_cancel_assembly').on('click', function(e) {
		cancel_assembly();
	});

	$('#btn_delete_assembly').on('click', function(e) {
		delete_assembly('.check_item',assemblyDeleteURL);
	});

	$('#btn_add_assembly').on('click', function() {
		$('#prod_type').focus();
		if ($('#assembly_id').val() !== '') {
			$('#div_add').hide();
			$('#div_save').show();
			$('#div_clear').hide();
			$('#div_cancel').show();
			$('#div_delete').hide();

			$('.readonly_assembly').prop('disabled', false);
			$('#tbl_prodcode_assembly .dt-checkboxes').prop('disabled', true);
			$('#tbl_prodcode_assembly .dt-checkboxes-select-all').prop('disabled', true);
			$('#tbl_prodcode_assembly .dt-checkboxes-select-all input[type=checkbox]').prop('disabled', true);
			$('.btn_edit_assembly').prop('disabled', true);
		} else {
			new_assembly();
		}
	});

	$('#tbl_prodcode_assembly .dt-checkboxes-select-all').on('click', function() {
		if ($('#tbl_prodcode_assembly .dt-checkboxes-select-all input[type=checkbox]').is(':checked')) {
			$('.btn_edit_assembly').prop('disabled', true);
		} else {
			$('.btn_edit_assembly').prop('disabled', false);
		}
	});

	$('#tbl_prodcode_assembly_body').on('click', 'td:first-child',function() {
		if ($('#tbl_prodcode_assembly_body .dt-checkboxes').is(':checked')) {
			$('.btn_edit_assembly').prop('disabled', false);
		} else {
			$('.btn_edit_assembly').prop('disabled', true);
		}
	});
	

	$('#tbl_prodcode_assembly_body').on('change', '.dt-checkboxes',function() {
		if ($(this).is(':checked')) {
			$('.btn_edit_assembly').prop('disabled', true);
		} else {
			$('.btn_edit_assembly').prop('disabled', false);
		}
	});
});

function init() {
	view_assembly();

	if (permission_access == '2' || permission_access == 2) {
        $('.permission').prop('readonly', true);
        $('.permission-button').prop('disabled', true);
    } else {
        $('.permission').prop('readonly', false);
        $('.permission-button').prop('disabled', false);
    }

	$('#div_cancel').hide();
	checkAllCheckboxesInTable('#tbl_prodcode_assembly', '.check_all', '.check_item', '#btn_delete_assembly');
	getAssemblies();
	get_dropdown_product_assembly();

	
}

function view_assembly() {
	$('#btn_add_assembly').html("<i class='fa fa-plus'></i> Add");
	$('#btn_add_assembly').removeClass('bg-blue');
	$('#btn_add_assembly').addClass('bg-green');

	$('#div_add').show();
	$('#div_save').hide();
	$('#div_cancel').hide();

	$('#div_clear').hide();
	$('#div_delete').show();

	$('.readonly_assembly').prop('disabled', true);
	$('#tbl_prodcode_assembly .dt-checkboxes').prop('disabled', false);
	$('#tbl_prodcode_assembly .dt-checkboxes-select-all').prop('disabled', false);
	$('#tbl_prodcode_assembly .dt-checkboxes-select-all input[type=checkbox]').prop('disabled', false);
	$('.btn_edit_assembly').prop('disabled', false);
}

function new_assembly() {
	$('#assembly_id').val('');
	$('#div_add').hide();
	$('#div_save').show();
	$('#div_clear').show();
	$('#div_cancel').show();
	$('#div_delete').hide();

	$('.readonly_assembly').prop('disabled', false);
	$('#tbl_prodcode_assembly .dt-checkboxes').prop('disabled', true);
	$('#tbl_prodcode_assembly .dt-checkboxes-select-all').prop('disabled', true);
	$('#tbl_prodcode_assembly .dt-checkboxes-select-all input[type=checkbox]').prop('disabled', true);
	$('.btn_edit_assembly').prop('disabled', true);
}

function update_assembly() {
	$('#btn_save_assembly').html("<i class='fa fa-check'></i> Update");
	$('#btn_save_assembly').removeClass('bg-blue');
	$('#btn_save_assembly').addClass('bg-navy');

	$('#btn_add_assembly').html("<i class='fa fa-edit'></i> Edit");
	$('#btn_add_assembly').removeClass('bg-green');
	$('#btn_add_assembly').addClass('bg-blue');

	$('#div_save').hide();
	$('#div_add').show();
	$('#div_clear').hide();
	$('#div_delete').hide();
	$('#div_cancel').show();

	$('.readonly_assembly').prop('disabled', true);
	$('#tbl_prodcode_assembly .dt-checkboxes').prop('disabled', true);
	$('#tbl_prodcode_assembly .dt-checkboxes-select-all').prop('disabled', true);

	$('#tbl_prodcode_assembly .dt-checkboxes-select-all input[type=checkbox]').prop('disabled', true);
}

function cancel_assembly() {
	clear();
	view_assembly();
	$('#div_delete').show();
}

function clear() {
	$('.clear').val('');
}

function delete_assembly(checkboxClass,deleteURL) {
	var chkArray = [];
	var table = $('#tbl_prodcode_assembly').DataTable();

	for (var x = 0; x < table.context[0].aoData.length; x++) {
		var DataRow = table.context[0].aoData[x];
		if (DataRow.anCells !== null && DataRow.anCells[0].firstChild.checked == true) {
			var checkbox = table.context[0].aoData[x].anCells[0].firstChild;
			chkArray.push($(checkbox).attr('data-id'))
		}
	}

	console.log(chkArray);

	if (chkArray.length > 0) {
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
				$('.loadingOverlay').show();
				$.ajax({
					url: deleteURL,
					type: 'POST',
					dataType: 'JSON',
					data: {
						_token:token,
						id: chkArray
					},
				}).done(function(data, textStatus, xhr) {
					msg(data.msg,data.status);
					getAssemblies();
				}).fail(function(xhr, textStatus, errorThrown) {
					$('.loadingOverlay').hide();
					msg(errorThrown,'error');
				});
			} else {
				$('#tbl_prodcode_assembly .dt-checkboxes-select-all').click();
				swal("Cancelled", "Your data is safe and not deleted.");
			}
		});
	} else {
		msg("Please select at least 1 item." , "failed");
	}
}

function get_dropdown_product_assembly() {
	var opt = "<option value=''></option>";
	$('#prod_type').html(opt);
	$.ajax({
		url: getdropdownproduct,
		type: 'GET',
		dataType: 'JSON',
		data: {_token: token},
	}).done(function(data, textStatus, xhr) {
		$.each(data, function(i, x) {
			opt = "<option value='"+x.product_line+"'>"+x.product_line+"</option>";
			$('#prod_type').append(opt);
		});
	}).fail(function(xhr, textStatus, errorThrown) {
		msg(errorThrown,textStatus);
	});
}

function getAssemblies() {
	$('.loadingOverlay').show();
	$('#tbl_prodcode_assembly').dataTable().fnClearTable();
	$('#tbl_prodcode_assembly').dataTable().fnDestroy();
	$('#tbl_prodcode_assembly').dataTable({
		ajax: assemblyListURL,
		serverSide: true,
		processing: true,
		deferRender: true,
		stateSave: true,
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
			selector: 'td:not(:nth-child(2)):not(:nth-child(3)):not(:nth-child(4)):not(:nth-child(5)):not(:nth-child(6)):not(:nth-child(7))',
			style: 'multi'
		},
		order: [[6, 'desc']],
		columns: [
			{
				data: function (data) {
					return data.id;//'<input type="checkbox" class="table-checkbox check_item" value="' + data.id + '">';
				}, name: 'pca.id', orderable: false, searchable: false, width: '3.28%'
			},
			{
				data: 'action', name:' action', orderable: false, searchable: false, width: '5.28%'
			},
			{ data: 'prod_type', name: 'pca.prod_type', width: '22.28%' },
			{ data: 'character_num', name: 'pca.character_num', width: '15.28%' },
			{ data: 'character_code', name: 'pca.character_code', width: '15.28%' },
			{ data: 'description', name: 'pca.description', width: '22.28%' },
			{ data: 'updated_at', name: 'pca.updated_at', width: '14.28%' }
		],
		initComplete: function () {
			$('.loadingOverlay').hide();

			$('#tbl_prodcode_assembly .dt-checkboxes-select-all input[type=checkbox]').addClass('table-checkbox');
		},
		fnDrawCallback: function () {
		},
		createdRow: function (row, data, dataIndex) {
			var dataRow = $(row);
			var checkbox = $(dataRow[0].cells[0].firstChild);

			checkbox.attr('data-id', data.id);
			checkbox.addClass('table-checkbox check_item');
		},
	});
}