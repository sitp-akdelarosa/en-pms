
$( function() {
	$('#div_cancel').hide();
	get_dropdown_material_type_assembly()
    checkAllCheckboxesInTable('#tbl_matcode_assembly','.check_all','.check_item');
	assemblyDataTable();


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

    $('#frm_mat_assembly').on('submit', function(e) {
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
				assemblyDataTable();
				new_assembly();
				showDropdowns($('#mat_type').val())
				$('#material_type').val($('#mat_type').val());
			}
		}).fail(function(xhr, textStatus, errorThrown) {
			if (xhr.status == 500) {
				ErrorMsg(xhr);
			} else {
				var errors = xhr.responseJSON.errors;
				showErrors(errors);
			}
		}).always( function() {
			$('.loadingOverlay').hide();
		});
	});

    $('#tbl_matcode_assembly_body').on('click', '.btn_edit_assembly', function(e) {
		e.preventDefault();
		update_assembly();
		$('#assembly_id').val($(this).attr('data-id'));
		$('#mat_type').val($(this).attr('data-mat_type'));
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
	$(checkboxClass+":checked").each(function() {
		chkArray.push($(this).val());
	});

	if (chkArray.length > 0) {
		confirm_delete(chkArray,token,deleteURL,true,'tbl_matcode_assembly',assemblyListURL,dataColumn);
	} else {
		msg("Please select at least 1 item." , "failed");
	}

	$('.check_all').prop('checked',false);
}

function get_dropdown_material_type_assembly() {
    var opt = "<option value=''></option>";
    $('#mat_type').html(opt);
    $.ajax({
        url: getMaterialTypeURL,
        type: 'GET',
        dataType: 'JSON',
        data: {_token: token},
    }).done(function(data, textStatus, xhr) {
        $.each(data, function(i, x) {
			opt = "<option value='" + x.material_type + "'>" + x.material_type+"</option>";
            $('#mat_type').append(opt);
        });
    }).fail(function(xhr, textStatus, errorThrown) {
        msg(errorThrown,textStatus);
    });
}

function assemblyDataTable() {
	$('#tbl_matcode_assembly').dataTable().fnClearTable();
	$('#tbl_matcode_assembly').dataTable().fnDestroy();
	$('#tbl_matcode_assembly').dataTable({
		ajax: {
			url: assemblyListURL,
			error: function (xhr, textStatus, errorThrown) {
				ErrorMsg(xhr);
			}
		},
		stateSave: true,
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
				}, name: 'id', 'orderable': false, 'searchable': false
			},
			{ data: function(data) {
				return '<button class="btn btn-sm bg-blue btn_edit_assembly" data-id="'+data.id+'" data-mat_type="'+data.mat_type+'" ' +
							'data-character_num="'+data.character_num+'" data-character_code="'+data.character_code+'" ' +
							'data-description="'+data.description+'">'+
								'<i class="fa fa-edit"></i>' +
						'</button>';
			}, name: 'action', 'orderable': false, 'searchable': false },
			{ data: 'mat_type', name: 'mat_type' },
			{ data: 'character_num', name: 'character_num' },
			{ data: 'character_code', name: 'character_code' },
			{ data: 'description', name: 'description' },
			{ data: 'updated_at', name: 'updated_at' },
		]
	});
}
