$( function() {
	$('#div_cancel').hide();
	checkAllCheckboxesInTable('#tbl_prodcode_assembly','.check_all','.check_item','#btn_delete_assembly');
	getAssemblies();
	get_dropdown_product_assembly();

	init();

	$('body').on('keydown', '.switch', function(e) {
		var self = $(this)
			, form = self.parents('form:eq(0)')
			, focusable
			, next
			;
		if (e.keyCode == 40) {
			focusable = form.find('.switch').filter(':visible');
			next = focusable.eq(focusable.index(this)+1);

			if (next.length) {
				next.focus();
			}
			return false;
		}

		if (e.keyCode == 38) {
			focusable = form.find('.switch').filter(':visible');
			next = focusable.eq(focusable.index(this)-1);

			if (next.length) {
				next.focus();
			}
			return false;
		}
	});

	$('.validate').on('keyup', function(e) {
		var no_error = $(this).attr('id');
		hideErrors(no_error)
	});

	$('#frm_code_assembly').on('submit', function(e) {
		e.preventDefault();
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			dataType: 'JSON',
			data: $(this).serialize(),
		}).done(function(data, textStatus, xhr) {
			if (textStatus == 'success') {
				msg("Data was successfully saved.","success");
				getAssemblies();
				new_assembly();
			}
		}).fail(function(xhr, textStatus, errorThrown) {
			var errors = xhr.responseJSON.errors;
			showErrors(errors);
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
});

function init() {
	check_permission(code_permission, function(output) {
		if (output == 1) {}
	});
}

function new_assembly() {
	$('#assembly_id').val('');
	$('#btn_save_assembly').html("<i class='fa fa-floppy-o'></i> Save");
	$('#btn_save_assembly').removeClass('bg-green');
	$('#btn_save_assembly').addClass('bg-blue');

	$('#div_cancel').hide();

	$('#div_clear').show();
	$('#div_delete').show();
}

function update_assembly() {
	$('#btn_save_assembly').html("<i class='fa fa-check'></i> Update");
	$('#btn_save_assembly').removeClass('bg-blue');
	$('#btn_save_assembly').addClass('bg-green');
	$('#div_clear').hide();
	$('#div_delete').hide();

	$('#div_cancel').show();
}

function cancel_assembly() {
	clear();
	$('#btn_save_assembly').html("<i class='fa fa-floppy-o'></i> Save");
	$('#btn_save_assembly').removeClass('bg-green');
	$('#btn_save_assembly').addClass('bg-blue');
	$('#div_cancel').hide();

	$('#div_clear').show();
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
			chkArray.push(table.context[0].aoData[x].anCells[0].firstChild.value)
		}
	}

	// $(checkboxClass+":checked").each(function() {
	// 	chkArray.push($(this).val());
	// });

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
				swal("Cancelled", "Your data is safe and not deleted.");
			}
		});
	} else {
		msg("Please select at least 1 item." , "failed");
	}

	$('.check_all').prop('checked',false);
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
		order: [[6, 'desc']],
		columns: [
			{
				data: function (data) {
					return '<input type="checkbox" class="table-checkbox check_item" value="' + data.id + '">';
				}, orderable: false, searchable: false, width: '5.28%'
			},
			{
				data: function (data) {
					return "<button class='btn btn-sm bg-blue btn_edit_assembly' data-id='" + data.id + "' " +
						"data-prod_type='" + data.prod_type + "' " +
						"data-character_num='" + data.character_num + "' " +
						"data-character_code='" + data.character_code + "' " +
						"data-description='" + data.description + "'>" +
						"<i class='fa fa-edit'></i>" +
						"</button>";
				}, orderable: false, searchable: false, width: '5.28%'
			},
			{ data: 'prod_type', width: '22.28%' },
			{ data: 'character_num', width: '14.28%' },
			{ data: 'character_code', width: '14.28%' },
			{ data: 'description', width: '22.28%' },
			{ data: 'updated_at', width: '14.28%' }
		],
		"initComplete": function () {
			$('.loadingOverlay').hide();
		},
		"fnDrawCallback": function () {
		},
	});
}