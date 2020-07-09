var dataColumn = [
    {data: function(data) {
    	return "<input type='checkbox' class='table-checkbox check_item' value='"+data.id+"'>";
    }, name: 'id', orderable: false, searchable: false},
    {data: 'action', name: 'action', orderable: false, searchable: false},
    {data: 'description', name: 'description'},
    {data: 'category', name: 'category'},
];

$( function() {
	checkAllCheckboxesInTable('.check_all','.check_item');
	getDatatable('tbl_type',typeListURL,dataColumn,[],0);

	check_permission(code_permission);

	$('#frm_user_type').on('submit', function(e) {
		e.preventDefault();
   		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			dataType: 'JSON',
			data: $(this).serialize(),
		}).done(function(data, textStatus, xhr) {
			if (textStatus) {
				msg("User Type was successfully added.",textStatus);
				getDatatable('tbl_type',typeListURL,dataColumn,[],0);
			}
			clear();
			$('#btn_save').removeClass('bg-green');
			$('#btn_save').addClass('bg-blue');
			$('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
		}).fail(function(xhr, textStatus, errorThrown) {
			var errors = xhr.responseJSON.errors;
			showErrors(errors);
		});
	});

	$('#tbl_type_body').on('click', '.btn_edit', function(e) {
		e.preventDefault();
		$('#id').val($(this).attr('data-id'));
		$('#description').val($(this).attr('data-description'));
		$('#category').val($(this).attr('data-category'));

		$('#btn_save').removeClass('bg-blue');
		$('#btn_save').addClass('bg-green');
		$('#btn_save').html('<i class="fa fa-check"></i> Update');
	});

	$('#btn_delete').on('click', function(e) {
		delete_items('.check_item',typeDeleteURL);
	});

});

function clear() {
	$('.clear').val('');
}

function delete_items(checkboxClass,deleteURL) {
	var chkArray = [];
	$(checkboxClass+":checked").each(function() {
		chkArray.push($(this).val());
	});

	if (chkArray.length > 0) {
		confirm_delete(chkArray,token,deleteURL,true,'tbl_type',typeListURL,dataColumn);
	} else {
		msg("Please select at least 1 item." , "warning");
	}

	$('.check_all').prop('checked',false);
	clear();
	$('#btn_save').removeClass('bg-green');
	$('#btn_save').addClass('bg-blue');
	$('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
}