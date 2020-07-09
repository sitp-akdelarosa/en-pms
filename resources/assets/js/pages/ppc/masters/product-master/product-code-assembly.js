$( function() {
	$('#div_cancel').hide();
    checkAllCheckboxesInTable('.check_all','.check_item');
	getAssemblies()
	get_dropdown_product_assembly();

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
				getAssemblies()
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
		confirm_delete(chkArray,token,deleteURL,true,'tbl_prodcode_assembly',assemblyListURL,dataColumn);
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
	$.ajax({
		url: assemblyListURL,
		type: 'GET',
		dataType: 'JSON',
	}).done(function(data, textStatus, xhr) {
		AssemblyTable(data);
	}).fail(function(xhr, textStatus, errorThrown) {
		msg(errorThrown,textStatus);
	});
}

function AssemblyTable(arr) {
	$('#tbl_prodcode_assembly').dataTable().fnClearTable();
    $('#tbl_prodcode_assembly').dataTable().fnDestroy();
    $('#tbl_prodcode_assembly').dataTable({
        data: arr,
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
                "previous":"Prev",
                "next": "Next",
                "last": "Last",
                "first": "First"
            }
        },
        columns: [
		    {data: function(data) {
		    	return '<input type="checkbox" class="table-checkbox check_item" value="'+data.id+'">';
		    }, orderable: false, searchable: false},
		    {data: function(data) {
		    	return "<button class='btn btn-sm bg-blue btn_edit_assembly' data-id='"+data.id+"' "
		    				"data-prod_type='"+data.prod_type+"' "
		    				"data-character_num='"+data.character_num+"' "
		    				"data-character_code='"+data.character_code+"' "
		    				"data-description='"+data.description+"'>"
                            "<i class='fa fa-edit'></i>"
                        "</button>";
		    }, orderable: false, searchable: false},
		    {data: 'prod_type'},
		    {data: 'character_num'},
		    {data: 'character_code'},
		    {data: 'description'},
		]
    });
}