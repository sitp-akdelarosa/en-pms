var process_select_arr = [];
var selected_process_arr = [];

$( function() {
	$('#set_list').slimscroll({ height: '100px',width: '100%' });
	get_set();
	processSelection();
	// get_dropdown_items_by_id(2,'#set');
	checkAllCheckboxesInTable('.check_all','.check_item');

	check_permission(code_permission);
	//selectedProcess('Default');

	$('#btn_add_process').on('click', function() {
		var chkArray = [];
		$('.check_item:checked').each(function() {
			chkArray.push($(this).val());
		});
		var ExistProcess = 1;
		if (chkArray.length > 0) {

			$.each(chkArray, function(i, x) {
				$.each(selected_process_arr, function(ii, xx) {
						if(xx.process == x ){ 
							ExistProcess = 0; 
						}
				});	
			});

			if(ExistProcess == 1){
			var cnt = selected_process_arr.length;
			$.each(chkArray, function(i, x) {
				cnt++;
				selected_process_arr.push({
					count: cnt,
					sequence: cnt,
					process: x
				});
			});
			selectedProcessTable(selected_process_arr);
		}else{
			msg("The Process already existing on the Process Table." , "failed");
		}
		} else {
			msg("Please select at least 1 item." , "failed");
		}

		$('.check_all').prop('checked',false);
	});

	$('#btn_save_process').on('click', function() {
		if ($('#selected_set').val() != '') {
			$('.loadingOverlay').show();

			$.ajax({
				url: saveProcessURL,
				type: 'POST',
				dataType: 'JSON',
				data: {
					_token: token,
					set_id: $('#selected_set').val(),
					sets: getSelectedText('selected_set'),
					processes: selected_process_arr
				},
			}).done(function(data, textStatus, xhr) {
				msg(data.msg,data.status);
				$('.check_item').prop('checked', false);
				$('.check_all').prop('checked', false);
			}).fail(function(xhr, textStatus, errorThrown) {
				msg(errorThrown,textStatus);
				$('.check_item').prop('checked', false);
				selected_process_arr = [];

				$('#tbl_selected_process_body').html('<tr>'+
	                        '<td colspan="3" class="text-center">No data available.</td>'+
	                    '</tr>');
			}).always(function() {
				$('.loadingOverlay').hide();
			});
		} else {
			msg("Please select at least 1 process and select your desired set.",'failed');
		}
	});

	$('#selected_set').on('change', function() {
		selectedProcess($(this).val());
	});

	$('#tbl_selected_process_body').on('click', '.delete', function() {
		var id = $(this).attr('data-count');
		id--;
		selected_process_arr.splice(id,1);
		var data = [];
		var cnt = 1;

		$.each(selected_process_arr, function(i, x) {
			data.push({
				count: cnt,
				sequence: cnt,
				process: x.process
			});
			cnt++;
		});

		selected_process_arr = [];
		selected_process_arr = data;

		selectedProcessTable(selected_process_arr);

		if ($('#tbl_selected_process_body > tr').length < 1) {
			$('#tbl_selected_process_body').html('<tr id="no_data">'+
                                            '<td colspan="4" class="text-center">No data available.</td>'+
                                        '</tr>');
		}
	});

	$('#frm_add_set').on('submit', function(e) {
		e.preventDefault();
		$('.loadingOverlay').show();

		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			dataType: 'JSON',
			data: $(this).serialize(),
		}).done(function(data, textStatus, xhr) {
			msg(data.msg,data.status);
			$('#set').val('');
			get_set();
		}).fail(function(xhr, textStatus, errorThrown) {
			var errors = xhr.responseJSON.errors;
			showErrors(errors);
		}).always(function() {
			$('.loadingOverlay').hide();
		});
	});

	$('#btn_delete_set').on('click', function() {
		delete_set();
	});
});


function processSelection() {
	process_select_arr = [];
	$.ajax({
		url: processListURL,
		type: 'GET',
		dataType: 'JSON',
		data: {_token: token},
	}).done(function(data, textStatus, xhr) {
		process_select_arr = data;
		makeProcessesTable(process_select_arr);
	}).fail(function(xhr, textStatus, errorThrown) {
		msg(errorThrown,textStatus);
	});
}

function selectedProcess(set_id) {
	selected_process_arr = [];
	$.ajax({
		url: selectedProcessListURL,
		type: 'GET',
		dataType: 'JSON',
		data: {_token: token,set_id:set_id},
	}).done(function(data, textStatus, xhr) {
		var cnt = 1;
		$.each(data, function(i, x) {
			selected_process_arr.push({
				count: cnt,
				sequence: x.sequence,
				process: x.process
			});
			cnt++;
		});
		$('#selected_set').val(set_id);
		selectedProcessTable(selected_process_arr);
	}).fail(function(xhr, textStatus, errorThrown) {
		msg(errorThrown,textStatus);
	});
}

function makeProcessesTable(arr) {
	$('#tbl_select_process').dataTable().fnClearTable();
    $('#tbl_select_process').dataTable().fnDestroy();
    $('#tbl_select_process').dataTable({
        data: arr,
        bLengthChange : false,
        scrollY: "300px",
        searching: false,
	    paging: false,
        columns: [
            { data: function(x) {
                return '<input type="checkbox" class="table-checkbox check_item" value="'+x.process+'">';
            }, searchable: false, orderable: false },

            { data: function(x) {
                return x.process+"<input type='hidden' name='process[]' value='"+x.process+"'>";
            }}
        ],
        fnInitComplete: function() {
            $('.dataTables_scrollBody').slimscroll();
        },
    });
}

function selectedProcessTable(arr) {
	$('#tbl_selected_process_body').html('');
	$('#tbl_selected_process').dataTable().fnClearTable();
    $('#tbl_selected_process').dataTable().fnDestroy();
    $('#tbl_selected_process').dataTable({
        data: arr,
        bLengthChange : false,
        searching: false,
	    paging: false,
        columns: [
        	{ data: function() {
        		return '<i class="text-blue fa fa-arrows"></i>';
        	} },
            { data: 'sequence' },
            { data: 'process' },
            { data: function(x) {
        		return '<i class="text-red fa fa-times delete" data-count="'+x.count+'"></i>';
        	} },
        ],
        rowReorder: {
            dataSrc: 'process'
        }
    });
}

function get_set() {
	var set = '<option value=""></option>';
	var set_list = '';
	$('#selected_set').html(set);
	$('#set_list').html(set_list);
	$.ajax({
		url: getSetURL,
		type: 'GET',
		dataType: 'JSON',
		data: {_token: token},
	}).done(function(data, textStatus, xhr) {
		$.each(data, function(i, x) {
			set = '<option value="'+x.id+'">'+x.set+'</option>';
			$('#selected_set').append(set);

			set_list = '<h6>'+
							'<input type="checkbox" class="set_item" value="'+x.id+'">'+x.set+
						'</h6>';
			$('#set_list').append(set_list);
		});
	}).fail(function(xhr, textStatus, errorThrown) {
		msg(errorThrown,textStatus);
	});
}

function delete_set() {
	var chkArray = [];
	$(".set_item:checked").each(function() {
		chkArray.push($(this).val());
	});
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
				$.ajax({
					url: deleteSetURL,
					type: 'POST',
					dataType: 'JSON',
					data: {_token: token,id:chkArray},
				}).done(function(data, textStatus, xhr) {
					msg(data.msg,data.status);
					selected_process_arr = [];
					selectedProcessTable(selected_process_arr);
					get_set();
				}).fail(function(xhr, textStatus, errorThrown) {
					msg(errorThrown,textStatus);
				});
				$('.check_all_product').prop('checked',false);
	        } else {
	            swal("Cancelled", "Your data is safe and not deleted.");
	        }
	    });
	} else {
		msg("Please select at least 1 Set." , "failed");
	}	
}