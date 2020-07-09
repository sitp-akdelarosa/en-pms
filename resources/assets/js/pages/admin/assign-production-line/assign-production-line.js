$( function() {
	$('.loadingOverlay').show();

	get_dropdown_productline();
	get_users();
	getProdLines([0]);

	check_permission(code_permission);

	$('.select-validate').on('change', function(e) {
		var no_error = $(this).attr('id');
		hideErrors(no_error)
	});

	$('#frm_assign_productline').on('submit', function(e) {
		e.preventDefault();

		var user_id = $('#user_id').val();
		var product_line = $('#product_line').val();

		if (user_id.length > 0 && product_line.length > 0) {
			$('.loadingOverlay').show();
		
	   		$.ajax({
				url: $(this).attr('action'),
				type: 'POST',
				dataType: 'JSON',
				data: $(this).serialize(),
			}).done(function(data, textStatus, xhr) {
				msg(data.msg,data.status);
				getProdLines([$('#user_id').val()]);
				clear();
				$('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
			}).fail(function(xhr, textStatus, errorThrown) {
				var errors = xhr.responseJSON.errors;
				showErrors(errors);
			});
			// .always(function() {
			// 	$('.loadingOverlay').hide();
			// });
		} else {
			msg("Please select your desired values.", "warning");
		}
		
	});

	$('#tbl_assign_productline_body').on('click', '.btn_edit_prodline', function(e) {
		e.preventDefault();
		$('#id').val($(this).attr('data-id'));
		$('#user_id').val($(this).attr('data-user_id'));
		$('#product_line').val($(this).attr('data-product_line'));
		$('#btn_save').html('<i class="fa fa-check"></i> Update');
	});

	$('#btn_clear').on('click', function(e) {
		clear();
		$('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
	});

	$('#btn_delete').on('click', function(e) {
		delete_items('.check_item',prodLineDeleteURL);
	});

	$('#user_id').on('change', function() {
		getProdLines([$(this).val()]);
	});

});

function getProdLines(user_id) {
	
	$.ajax({
		url: prodLineListURL,
		type: 'GET',
		dataType: 'JSON',
		data: {
			user_id: user_id
		},
	}).done(function(data, textStatus, xhr) {
		ProdLineTable(data);
	}).fail(function(xhr, textStatus, errorThrown) {
		console.log("error");
	});
	
}

function ProdLineTable(arr) {
	$('#tbl_assign_productline').dataTable().fnClearTable();
    $('#tbl_assign_productline').dataTable().fnDestroy();
    $('#tbl_assign_productline').dataTable({
        data: arr,
        processing: true,
        deferRender: true,
        bLengthChange: false,
        paging: true,
        pageLength: 5,
        columns: [
		    {data: function(data) {
		    	return '<input type="checkbox" class="table-checkbox check_item" value="'+data.id+'" data-id="'+data.id+'">';
		    }, orderable: false, searchable: false},
		    {data: 'product_line'},
		    {data: 'updated_at'},
		    
		],
		initComplete: function () {
			$('.loadingOverlay').hide();
        },
        fnDrawCallback: function () {
            checkAllCheckboxesInTable("#tbl_assign_productline", ".check_all", ".check_item", "#btn_delete");
        },
    });
}

function get_dropdown_productline() {
    var opt = "<option value=''></option>";
    $("#product_line").html(opt);
    $.ajax({
        url: dropdownProduct,
        type: 'GET',
        dataType: 'JSON',
        data: {_token: token},
    }).done(function(data, textStatus, xhr) {
        $.each(data, function(i, x) {
            opt = "<option value='"+x.dropdown_item+"'>"+x.dropdown_item+"</option>";
            $("#product_line").append(opt);
        });
    }).fail(function(xhr, textStatus, errorThrown) {
        msg(errorThrown,textStatus);
    });
}

function get_users() {
	var opt = '<option value=""></option>';
	$('#user_id').html(opt);
	$.ajax({
		url: getUserURL,
		type: 'GET',
		dataType: 'JSON',
		data: {_token: token},
	}).done(function(data, textStatus, xhr) {
		$.each(data, function(i, x) {
			opt = '<option value="'+x.id+'">'+x.user_id+'</option>';
			$('#user_id').append(opt);
		});
	}).fail(function(xhr, textStatus, errorThrown) {
		msg(errorThrown,textStatus);
	});
}

function delete_items(checkboxClass,deleteURL) {
	var chkArray = [];

	var table = $('#tbl_assign_productline').DataTable();
    for (var x = 0; x < table.context[0].aoData.length; x++) {
    	var aoData = table.context[0].aoData[x];
        if (aoData.anCells !== null && aoData.anCells[0].firstChild.checked == true) {
            chkArray.push(table.context[0].aoData[x].anCells[0].firstChild.attributes['data-id'].value)
        }
    }
	if (chkArray.length > 0) {
		swal({
	        title: "Are you sure to delete this data?",
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
	                getProdLines([0]);

	                if (data.status == 'success') {
	        			msg(data.msg,data.status)
	                    is_confirmed_deleted = true;
	        		} else {
	                    msg(data.msg,data.status)
	                }

	                return data.status;
	        	}).fail(function(xhr, textStatus, errorThrown) {
	        		msg(errorThrown,textStatus);
	        	});
	        } else {
	            swal("Cancelled", "Your data is safe and not deleted.");
	        }
	    });
	} else {
		msg("Please select at least 1 item.", "warning");
	}

	$('.check_all').prop('checked',false);
	clear();
	$('#btn_save').html('<i class="fa fa-plus"></i> Add');
}

function clear() {
	$('.clear').val('');
	clearSelected('user_id');
	clearSelected('product_line');
}

function clearSelected(id){
    var elements = document.getElementById(id).options;

    for(var i = 0; i < elements.length; i++){
    	elements[i].selected = false;
    }
}