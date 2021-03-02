var process_select_arr = [];
var selected_process_arr = [];
var setArray = [];

$( function() {
	$('#sortable_process').sortable({
		multiDrag: true,
		selectedClass: 'selected',
		fallbackTolerance: 3, // So that we can select items on mobile
		animation: 150
	});

	init();

	$('#btn_add_process').on('click', function() {
		var setTable = $('#tbl_added_sets').DataTable();
		setArray = [];
		for (var x = 0; x < setTable.context[0].aoData.length; x++) {
			var cell = setTable.context[0].aoData[x].anCells;
			if (cell !== null && cell[0].firstChild.checked == true) {
				setArray.push(parseInt(cell[0].firstChild.value));
			}
		}

		var processTable = $('#tbl_select_process').DataTable();
		var chkArray = [];
		for (var x = 0; x < processTable.context[0].aoData.length; x++) {
			var cell = processTable.context[0].aoData[x].anCells;
			if (cell !== null && cell[0].firstChild.checked == true) {
				chkArray.push(cell[0].firstChild.value);
			}
		}

		// var chkArray = [];
		// $('.check_item:checked').each(function() {
		// 	chkArray.push($(this).val());
		// });

		var ExistProcess = 1;
		if (chkArray.length > 0) {

			// $.each(chkArray, function(i, x) {
			// 	$.each(selected_process_arr, function(ii, xx) {
			// 		if(xx.process == x ) { 
			// 			ExistProcess = 0; 
			// 		}
			// 	});	
			// });

			// if(ExistProcess == 1) {
				if ($('#state').val() !== 'edit') {
					$('#selected_set').val(setArray).trigger('change');
					if ($('#state').val() == 'add') {
						$('#selected_set').prop('readonly',true);
					}
				} else {

				}

				var cnt = selected_process_arr.length;
				$.each(chkArray, function(i, x) {
					cnt++;
					selected_process_arr.push({
						count: cnt,
						sequence: cnt,
						process: x,
						remarks: ''
					});
				});
				selectedProcessTable(selected_process_arr);
				$('#selected_set_feedback').html("Reminder: If one of the selected set has saved processes, the saved processes will be overight with selected processes below.");
			// } else {
			// 	msg("The Process already existing on the Process Table." , "failed");
			// }
		} else {
			msg("Please select at least 1 item." , "failed");
		}

		$('.check_all').prop('checked',false);
	});

	$('#btn_save_process').on('click', function() {
		var selectedSet = $('#selected_set').val();
		if (selectedSet.length > 0 && selected_process_arr.length > 0) {

			data = [];
			var cnt = 1;
			var remarks = $('input[name=remarks]');

			$.each($('.process_name'), function(i,x) {
				console.log($(remarks[i]).val());
				data.push({
					count: cnt,
					sequence: cnt,
					process: $(x).attr('data-process'),
					remarks: $(remarks[i]).val()
				});
				cnt++;
				console.log($(x).attr('data-process'));
			});

			selected_process_arr = data;

			console.log(selected_process_arr);

			$('.loadingOverlay').show();

			$.ajax({
				url: saveProcessURL,
				type: 'POST',
				dataType: 'JSON',
				data: {
					_token: token,
					set_id: selectedSet,
					//sets: getSelectedText('selected_set'),
					processes: selected_process_arr
				},
			}).done(function(data, textStatus, xhr) {
				msg(data.msg,data.status);
				// $('.check_item').prop('checked', false);
				// $('.check_all').prop('checked', false);

				$('#btn_cancel').click();

			}).fail(function(xhr, textStatus, errorThrown) {
				ErrorMsg(xhr);
				$('.check_item').prop('checked', false);
				selected_process_arr = [];

				$('#tbl_selected_process_body').html('<tr>'+
	                        '<td colspan="3" class="text-center">No data available.</td>'+
	                    '</tr>');
			}).always(function() {
				$('.loadingOverlay').hide();
			});
		} else {
			console.log($('#selected_set').val());
			msg("Please select at least 1 process and select your desired set.",'failed');
		}
	});


	$('#sortable_process').on('click', '.delete', function() {
		var id = $(this).attr('data-count');
		id--;
		selected_process_arr.splice(id,1);
		var data = [];
		var cnt = 1;

		$.each(selected_process_arr, function(i, x) {
			data.push({
				count: cnt,
				sequence: cnt,
				process: x.process,
				remarks: x.remarks
			});
			cnt++;
		});

		selected_process_arr = [];
		selected_process_arr = data;

		selectedProcessTable(selected_process_arr);

		console.log($('#sortable_process > .process_name').length);

		if ($('#sortable_process > .process_name').length < 1) {
			$('#sortable_process').html('<div class="list-group-item process_name" data-process="">No Process selected.</div>');
		}

		// if ($('#tbl_selected_process_body > tr').length < 1) {
		// 	$('#tbl_selected_process_body').html('<tr id="no_data">'+
        //                                     '<td colspan="4" class="text-center">No data available.</td>'+
        //                                 '</tr>');
		// }
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

			$('#btn_add_set').html('<i class="fa fa-plus"></i> Add Set');
			$('#btn_add_set').removeClass('bg-navy');
			$('#btn_add_set').addClass('bg-green');
			$('#cancel').hide();

			$('.btn_edit_set').prop('disabled', false);

			$('#set').val('');
			$('#set_id').val('');
			$('#product_line').select2().val(null).trigger('change.select2')
			get_set();
		}).fail(function(xhr, textStatus, errorThrown) {
			if (xhr.status == 500) {
				ErrorMsg(xhr);
			} else {
				var errors = xhr.responseJSON.errors;
				showErrors(errors);
			}
			
		}).always(function() {
			$('.loadingOverlay').hide();
		});
	});

	$('#btn_delete_set').on('click', function() {
		delete_set();
	});

	$('#tbl_added_sets').on('change', '.check_all_sets', function() {
		$('input:checkbox.check_set').not(this).prop('checked', this.checked);
		var table = $('#tbl_added_sets').DataTable();

		for (var x = 0; x < table.context[0].aoData.length; x++) {
			var aoData = table.context[0].aoData[x];
			var tr = aoData.nTr;
			if (aoData.anCells !== null && aoData.anCells[0].firstChild.checked == true) {
				console.log(aoData.anCells[0].firstChild.attributes);
				$(tr).addClass('selected');

				GUIState('add');
			} else {
				$(tr).removeClass('selected');
				GUIState('view');
			}
		}
	});

	$('#tbl_added_sets_body').on('change', '.check_set', function() {
		var tr = $(this).parent().parent()[0];
		if ($(this).is(':checked')) {
			$(tr).addClass('selected');
			$('#btn_delete_set').prop('disabled', false);

			GUIState('add');
		} else {
			$(tr).removeClass('selected');
			$('#btn_delete_set').prop('disabled', true);

			GUIState('view');
		}
	});

	$('#tbl_added_sets_body').on('click', '.btn_edit_set', function() {
		GUIState('edit');
		selectedProcess($(this).attr('data-id'));

		$('.btn_edit_set_name').prop('disabled',true);
		$('#selected_set').val([$(this).attr('data-id')]).trigger('change');
	});

	$('#btn_cancel_set').on('click', function() {
		$('#set_id').val('');
		$('#set').val('');
		$('#product_line').val(null).trigger('change.select2');

		$('.btn_edit_set').prop('disabled', false);

		$('#btn_add_set').html('<i class="fa fa-plus"></i> Add Set');
		$('#btn_add_set').removeClass('bg-navy');
		$('#btn_add_set').addClass('bg-green');
		$('#cancel').hide();
	});

	$('#tbl_added_sets_body').on('click', '.btn_edit_set_name', function () {
		$('#set_id').val($(this).attr('data-id'));
		$('#set').val($(this).attr('data-set'));
		getSelectedProductLine($(this).attr('data-id'));
		
		$('.btn_edit_set').prop('disabled',true);

		$('#btn_add_set').html('<i class="fa fa-check"></i> Update Set');
		$('#btn_add_set').removeClass('bg-green');
		$('#btn_add_set').addClass('bg-navy');
		$('#cancel').show();
	});

	$('#btn_cancel').on('click', function() {
		GUIState('view');
		$('.check_all_sets').prop('checked', false).trigger('change');
		$('.check_set').prop('checked', false).trigger('change');
		$('.check_all').prop('checked', false).trigger('change');
		$('.check_item').prop('checked', false).trigger('change');

		$('.btn_edit_set_name').prop('disabled', false);

		$('#btn_add_set').html('<i class="fa fa-plus"></i> Add Set');
		$('#btn_add_set').removeClass('bg-navy');
		$('#btn_add_set').addClass('bg-green');

		$('#selected_set').val([]).trigger('change');
		selected_process_arr = [];
		selectedProcessTable(selected_process_arr);
	});

});

function init() {
	GUIState('view');

	if (permission_access == '2' || permission_access == 2) {
        $('.permission').prop('readonly', true);
        $('.permission-button').prop('disabled', true);
    } else {
        $('.permission').prop('readonly', false);
        $('.permission-button').prop('disabled', false);
		$('#btn_delete_set').prop('disabled', true);
    }

	get_dropdown_productline();
	get_set();
	processSelection();
	checkAllCheckboxesInTable('#tbl_added_sets', '.check_all_sets', '.check_set', '#btn_delete_set');
	checkAllCheckboxesInTable('#tbl_select_process', '.check_all', '.check_item');
}

function GUIState(state) {
	switch(state) {
		case 'view':
			$('.check_all_sets').prop('disabled', false);
			$('#tbl_added_sets').DataTable().$('.check_set').prop('disabled', false);

			$('.page-link').prop('disabled', true);

			$('.check_all').prop('disabled', true);
			$('#tbl_select_process').DataTable().$('.check_item').prop('disabled', true);
			//$('#tbl_select_process .check_item').prop('disabled', true);
			

			$('#cancel').hide();
			$('#btn_cancel').hide();
			$('#state').val(state);

			$("#selected_set").select2({disabled:'readonly'});
		break;

		case 'add':
			$('.check_all').prop('disabled', false);
			$('#tbl_select_process').DataTable().$('.check_item').prop('disabled', false);
			//$('#tbl_select_process .check_item').prop('disabled', false);
			$('#btn_cancel').show();
			$('#state').val(state);
		break;

		case 'edit':
			$('.check_all_sets').prop('disabled', true);
			$('#tbl_added_sets').DataTable().$('.check_set').prop('disabled', true);
			//$('.check_set').prop('disabled', true);

			$('.check_all').prop('disabled', false);
			$('#tbl_select_process').DataTable().$('.check_item').prop('disabled', false);
			//$('#tbl_select_process .check_item').prop('disabled', true);
			$('#btn_cancel').show();
			$('#state').val(state);
		break;
	}
}

function processSelection() {
	$('.loadingOverlay').show();
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
		ErrorMsg(xhr);
	});
}

function makeProcessesTable(arr) {
	$('#tbl_select_process').dataTable().fnClearTable();
    $('#tbl_select_process').dataTable().fnDestroy();
    $('#tbl_select_process').dataTable({
        data: arr,
        bLengthChange : false,
        //scrollY: "300px",
        //searching: false,
        pageLength: 10,
	    //paging: false,
	    order: [[1,'asc']],
        columns: [
            { data: function(x) {
                return '<input type="checkbox" class="table-checkbox check_item" value="'+x.process+'">';
            }, searchable: false, orderable: false },

            { data: function(x) {
                return x.process+"<input type='hidden' name='process[]' value='"+x.process+"'>";
            }}
        ],
        fnInitComplete: function() {
        	GUIState('view');
			$('.dataTables_scrollBody').slimscroll();
			$('.loadingOverlay').hide();
		}
    });
}

function selectedProcess(set_id) {
	$('.loadingOverlay').show();
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
				process: x.process,
				remarks: x.remarks
			});
			cnt++;
		});
		$('#selected_set').val(set_id);
		selectedProcessTable(selected_process_arr);
	}).fail(function(xhr, textStatus, errorThrown) {
		ErrorMsg(xhr);
	}).always( function() {
		$('.loadingOverlay').hide();
	});
}

function selectedProcessTable(arr) {

	if (arr.length > 0) {
		var list = '';
		$('#sortable_process').html(list);

		$.each(arr, function (i, x) {
			var remarks = (x.remarks == null) ? '' : x.remarks;

			list += "<div class='form-group row process_name' data-process='" + x.process + "' data-remarks='" + remarks + "'>" +
						"<div class='col-sm-5'>" + x.process + "</div>"+
						"<div class='col-sm-6'>"+
							"<input type='text' class='form-control form-control-sm' name='remarks' placeholder='Remarks..' value='" + remarks + "'>" +
						"</div>"+
						"<button type='button' class='btn btn-sm bg-red delete col-sm-1 btn-block' data-count='" + x.count + "'>" +
							"<i class='fa fa-times'></i> " +
						"</button>" +
					"</div>";
		});
		$('#sortable_process').html(list);
		$('.loadingOverlay').hide();
	} else {
		$('#sortable_process').html('<div class="list-group-item process_name" data-process="">No Process selected.</div>');
		$('.loadingOverlay').hide();
	}


	
	

		
	
	// $('#tbl_selected_process_body').html('');
	// $('#tbl_selected_process').dataTable().fnClearTable();
    // $('#tbl_selected_process').dataTable().fnDestroy();
    // $('#tbl_selected_process').dataTable({
    //     data: arr,
    //     bLengthChange : false,
    //     searching: false,
	//     paging: false,
	//     order: [[1,'asc']],
    //     columns: [
    //     	{ data: function() {
    //     		return '<i class="text-blue fa fa-arrows"></i>';
    //     	}, searchable: false, orderable: false },
    //         { data: 'sequence' },
    //         { data: 'process' },
    //         { data: function(x) {
    //     		return '<i class="text-red fa fa-times delete" data-count="'+x.count+'"></i>';
    //     	}, searchable: false, orderable: false },
	// 	],
    //     rowReorder: {
    //         dataSrc: 'process'
    //     }
    // });
}

function get_set() {
	$('.loadingOverlay').show();

	$.ajax({
		url: getSetURL,
		type: 'GET',
		dataType: 'JSON',
		data: {_token: token},
	}).done(function(data, textStatus, xhr) {
		$('#selected_set').select2({
			data: data
		});
		setTable(data);
	}).fail(function(xhr, textStatus, errorThrown) {
		ErrorMsg(xhr);
	});
}

function setTable(dataArr) {
	var table = $('#tbl_added_sets');

	table.dataTable().fnClearTable();
	table.dataTable().fnDestroy();
	table.dataTable({
		data: dataArr,
		processing: true,
		deferRender: true,
		// searching: false,
		pageLength: 10,
		bLengthChange : false,
		order: [[1,'desc']],
		columns: [
			{
				data: function(x) {
					return "<input type='checkbox' class='table-checkbox check_set' value='"+x.id+"'>";
			}, name: 'id', orderable: false, searchable: false},
			{ data: 'text', name: 'text' },
			{data: function(x) {
				return "<button type='button' class='btn btn-sm bg-purple btn_edit_set_name' " +
							"data-id='" + x.id + "' data-set='" + x.text + "' title='Edit Set Name'>" +
							"<i class='fa fa-arrow-up'></i>" +
						"</button>"+
						"<button type='button' class='btn btn-sm bg-blue btn_edit_set' data-id='" + x.id +"' title='Edit Process Set'>"+
							"<i class='fa fa-edit'></i>"+
						"</button>";
			}, orderable: false, searchable: false},
		],
		initComplete: function () {
			$('.loadingOverlay').hide();
		},
		fnDrawCallback: function () {
		},
	});
}

function delete_set() {
	var chkArray = [];

	var table = $('#tbl_added_sets').DataTable();
	for (var x = 0; x < table.context[0].aoData.length; x++) {
		var aoData = table.context[0].aoData[x];
		if (aoData.anCells !== null && aoData.anCells[0].firstChild.checked == true) {
			chkArray.push(table.context[0].aoData[x].anCells[0].firstChild.value)
		}
	}

	// $(".set_item:checked").each(function() {
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
					ErrorMsg(xhr);
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

function get_dropdown_productline() {
	var opt = "<option value=''></option>";
	$('#product-type').html(opt);
	$.ajax({
		url: productLineURL,
		type: 'GET',
		dataType: 'JSON',
		data: { _token: token },
	}).done(function (data, textStatus, xhr) {
		$('#product_line').select2({
			allowClear: true,
			placeholder: 'Select a Product Line',
			data: data
		}).val(null);
	}).fail(function (xhr, textStatus, errorThrown) {
		ErrorMsg(xhr);
	});
}

function getSelectedProductLine(set_id) {
	$('.loadingOverlay').show();
	$.ajax({
		url: selectedProductLineURL,
		type: 'GET',
		dataType: 'JSON',
		data: { _token: token, set_id: set_id },
	}).done(function (data, textStatus, xhr) {
		console.log(data);
		$('#product_line').select2({
			allowClear: true,
			placeholder: 'Select a Product Line',
		}).val(data).trigger('change.select2');
	}).fail(function (xhr, textStatus, errorThrown) {
		ErrorMsg(xhr);
	}).always( function() {
		$('.loadingOverlay').hide();
	});
}