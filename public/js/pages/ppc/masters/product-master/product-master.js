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
			{ data: 'created_at', width: '14.28%' }
		],
		"initComplete": function () {
			$('.loadingOverlay').hide();
		},
		"fnDrawCallback": function () {
		},
	});
}
var process_array = [];
var product_type = [];
var code_arr = [];

$( function() {
	get_dropdown_product();
	$('#product_code').prop('readonly', true);
	$('#code_description').prop('readonly', true);
	$('#product_code').mask('AAAAA-AAA-AAAAAA', {
		'translation': {
			A: { pattern: /[A-Za-z0-9.]/ },
			S: { pattern: /[A-Za-z.]/ },
			Y: { pattern: /[0-9.]/ }
		},
		'placeholder': '_____-___-______'
	});
	$("#product-type").on('keyup', showProductType);
	checkAllCheckboxesInTable('.check_all_process','.check_process_item');
	checkAllCheckboxesInTable('#tbl_product_code','.check_all_product','.check_product_item');
	$('#btn_process').prop('disabled', true);
	getProductCodes();
	defaultSizes();
	get_dropdown_items_by_id(1,'#process');
	autoComplete("#standard_material_used", getStandardMaterialURL, "code_description");

	//$($.fn.dataTable.tables(true)).DataTable().columns.adjust();

	get_set();

	//check_permission(code_permission);

	$('#product-type').on('change', function(e) {
		e.preventDefault();
		showDropdowns($(this).val())
		$('#product_type').val($(this).val());

		if ($(this).val() != '') {
			$('#product_code').prop('readonly', false);
			$('#code_description').prop('readonly', false);
		}else{
			$('#product_code').prop('readonly', true);
			$('#code_description').prop('readonly', true);
		}	
		$('#product_code').val('');
		$('#code_description').val('');
		$('#cut_weight').val(0);
		$('#cut_length').val(0);
		$('#cut_length_uom').val('N/A');
		$('#cut_weight_uom').val('N/A');

	});

	$('.select-code').on('change', function(e) {
		e.preventDefault();id = $(this).attr('id');
		if ($(this).val() == '') {
			$('#'+$(this).attr('id')+'_val').val('');
			$('#'+$(this).attr('id').replace('_val','')).val('');
		} else {
			showCode($(this).attr('id'),$('#product-type').val(),$(this).val());
		}
	});

	// $('#process').on('change', function(e) {
	// 	showProcess($(this).val());
	// });

	$('#set').on('change', function(e) {
		selectedProcess($(this).val(),$('#prod_id').val());
		$('#process').val('');
		$('#div_code').val('');
		$('#btn_save_process').html('<i class="fa fa-floppy-o"></i> Save');
		$('#process_id').val('');
	});

	$('#btn_cancel_process').on('click', function() {
		$('#process').val('');
		$('#process_id').val('');
		$('#btn_add_process').html('<i class="fa fa-plus"></i> Add Process');
		$('#btn_add_process').removeClass('bg-navy');
		$('#btn_add_process').addClass('bg-green');

		$('#sequence').prop('readonly',false);

		$(this).hide();

		showProcessList($('#prod_id').val(),$('#set').val());
	});

	$('#tbl_selected_set_process').on('click', '.btn_edit_process', function() {
		$('#process_id').val($(this).attr('data-id'));
		$('#prod_code').val($(this).attr('data-prod_code'));
		$('#process').val($(this).attr('data-process'));
		showProcess($(this).attr('data-process'));
		$('#div_code').val($(this).attr('data-div_code'));
		$('#btn_save_process').html('<i class="fa fa-check"></i> Update');
	});

	$('#tbl_product_code_body').on('click', '.btn_edit_product', function() {
		$('#product_id').val($(this).attr('data-id'));
		$('#product_type').val($(this).attr('data-product_type'));
		$('#product_code').val($(this).attr('data-product_code'));
		$('#code_description').val($(this).attr('data-code_description'));

		$('#cut_weight').val($(this).attr('data-cut_weight'));
		defaultSizes('#cut_weight',$('#cut_weight').val());
		$('#cut_weight_uom').val($(this).attr('data-cut_weight_uom'));

		$('#cut_length').val($(this).attr('data-cut_length'));
		defaultSizes('#cut_length',$('#cut_length').val())
		$('#cut_length_uom').val($(this).attr('data-cut_length_uom'));

		$('#cut_width').val($(this).attr('data-cut_width'));
		defaultSizes('#cut_width', $('#cut_width').val())
		$('#cut_width_uom').val($(this).attr('data-cut_width_uom'));
		
		$('#product-type').val($(this).attr('data-product_type'));
		showDropdowns($(this).attr('data-product_type'));
		$('#product_code').prop('readonly', false);
		$('#code_description').prop('readonly', false);
		$('#item').val($(this).attr('data-item'));
		$('#class').val($(this).attr('data-class'));
		$('#standard_material_used').val($(this).attr('data-standard_material_used'));
		$('#finish_weight').val($(this).attr('data-finish_weight'));
		$('#alloy').val($(this).attr('data-alloy'));
		$('#size').val($(this).attr('data-size'));
		$('#btn_save').html('<i class="fa fa-check"></i> Update');
	});

	$('#tbl_product_code_body').on('click', '.btn_assign_process', function() {
		$('#prod_id').val($(this).attr('data-id'));
		$('#prod_code').val($(this).attr('data-product_code'));

		$('#set').val('Default');
		$('#process').val('');
		$('#div_code').val('');
		$('#btn_save_process').html('<i class="fa fa-floppy-o"></i> Save');
		$('#process_id').val('');

		showProcessList($('#prod_id').val(),$('#set').val());

		$('#btn_cancel_process').hide();

		$('#modal_process').modal('show');
	});

	$('#frm_product_processes').on('submit', function(e) {
		var data = {
			_token: token,
			prod_id: $('#prod_id').val(),
			prod_code: $('#prod_code').val(),
			sequence: $('input[name="sequence[]"]').map(function(){return $(this).val();}).get(),
			process: $('input[name="process[]"]').map(function(){return $(this).val();}).get(),
			remarks: $('input[name="remarks[]"]').map(function () { return $(this).val(); }).get(),
			sets: $('input[name="sets[]"]').map(function(){return $(this).val();}).get(),
		};

		e.preventDefault();
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			dataType: 'JSON',
			data: data,
		}).done(function(data, textStatus, xhr) {
			if (textStatus == 'success') {
				process_array = [];
				$.each(data, function(i, x) {
					process_array.push({
						id: x.id,
						// prod_code: x.prod_code,
						process: x.process,
						sets: x.set,
					});
				});

				msg('Processes were successfully saved.',textStatus);
			}
			$('#btn_save_process').html('<i class="fa fa-floppy-o"></i> Save');
			$('#process_id').val('');
			$('#process').val('');

			var sequence = process_array.length + 1;
			$('#sequence').val(sequence);
		}).fail(function(xhr, textStatus, errorThrown) {
			msg("There was an error while processesing.",textStatus);
		});
	});

	$('#frm_prod_code').on('submit', function(e) {
		e.preventDefault();
		if($('#cut_weight').val() < 0 || $('#cut_length').val() < 0){
			msg("Please Input valid Number.","warning");
		}else{
			$('.loadingOverlay').show();
			$.ajax({
				url: $(this).attr('action'),
				type: 'POST',
				dataType: 'JSON',
				data: $(this).serialize(),
			}).done(function(data, textStatus, xhr) {
				if(data.status !== undefined) {
		            msg(data.msg,data.status);
		        }

				if (textStatus == 'success') {
					msg("Data was successfully saved.",textStatus);
					getProductCodes();
				}
				$('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
				$('#product_id').val('');
				clearCode();
			}).fail(function(xhr, textStatus, errorThrown) {
				var errors = xhr.responseJSON.errors;
				showErrors(errors);
			}).always(function() {
				$('.loadingOverlay').hide();
			});
		}		
	});

	$('#btn_delete_process').on('click', function(e) {
		delete_process('.check_process_item',processDeleteURL);
	});

	$('#btn_delete_product').on('click', function(e) {
		delete_product('.check_product_item',productDeleteURL);
	});

	$('#btn_cancel').on('click', function() {
		clearCode();
		showDropdowns();
		$('#product_code').prop('readonly', true);
		$('#code_description').prop('readonly', true);
		$('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
	});

	$('#btn_add_process').on('click', function() {
		var sameProcess = 0;
		$.each(process_array, function(i, x) {
            if(x.process == $('#process').val()){
            	sameProcess = 1}
        });
		 if(sameProcess == 0){
			if ($('#process').val() == '') {
				msg("Please Select a Process.",'warning');
			} else {
				if ($('#process_id').val() !== '') {
					var id = $('#process_id').val();
					id--;
					process_array[id] = {
						id: $('#process_id').val(),
						sequence: $('#sequence').val(),
						remarks: '',
						process: $('#process').val(),
						sets: $('#set').val(),
					};
					$('#process_id').val('');
					$('#btn_add_process').html('<i class="fa fa-plus"></i> Add Process');
					$('#btn_add_process').removeClass('bg-navy');
					$('#btn_add_process').addClass('bg-green');
				} else {
					$('#no_data').remove();
					// process_array.push({
					// 	id: '',
					// 	sequence: $('#sequence').val(),
					// 	prod_code: $('#prod_code').val(),
					// 	process: $('#process').val(),
					// 	sets: $('#set').val(),
					// });
					
					var seq = parseInt($('#sequence').val());

					seq--;

					process_array.splice(seq,0,{
						id: '',
						sequence: $('#sequence').val(),
						prod_code: $('#prod_code').val(),
						remarks: '',
						process: $('#process').val(),
						sets: $('#set').val(),
					});

					var sequence = process_array.length + 1;
					$('#sequence').val(sequence);
				}
				makeProcessList(process_array);
			}
		}else{
			msg("The Process already existing.","failed");
		}
		$('#btn_cancel_process').hide();
	});

	$('#tbl_prod_process_body').on('click', '.btn_proc_delete', function() {
		var id = $(this).attr('data-id');
		$('#'+id).remove();
		id--;
		process_array.splice(id,1);

		makeProcessList(process_array);

		// if ($('#tbl_prod_process_body > tr').length < 1) {
		// 	$('#tbl_prod_process_body').html('<tr id="no_data">'+
        //                                     '<td colspan="4" class="text-center">No data available.</td>'+
        //                                 '</tr>');
		// }
	});

	$('#tbl_prod_process_body').on('click', '.btn_proc_edit', function() {
		var id = $(this).attr('data-id');
		$('#process_id').val(id);
		$('#sequence').val(id);
		$('#set').val($(this).attr('data-sets'));
		$('#sequence').prop('readonly',true);

		id--;
		var proc = process_array[id].process;
		$('#process').val(proc);


		$('#btn_add_process').html('<i class="fa fa-check"></i> Update Process');
		$('#btn_add_process').removeClass('bg-green');
		$('#btn_add_process').addClass('bg-navy');
		$('#btn_cancel_process').show();
	});

	$('.size').on('change', function() {
		defaultSizes($(this).val());
	});

	$('#product_code').on('keyup', function() {
		var code = $(this).val();
		autoAssignSelectBox(code);
	});

	$('#product_code').on('focusout', function() {
		var code = $(this).val();
		$(this).val(code.toUpperCase());

		$('#code_description').val('');
		showDescription();
	});

	$('#btn_excel_product').on('click', function() {
		var that = this;
		var page_url = downloadExcelFileURL;

		var percentage = 10;

		$('#progress').show();
		$('.progress-bar').css('width', '10%');
		$('.progress-bar').attr('aria-valuenow', percentage);

		var req = new XMLHttpRequest();

		req.open("GET", page_url, true);
		
		setTimer(percentage);

		req.addEventListener("progress", function (evt) {
			if (evt.lengthComputable) {
				var percentComplete = evt.loaded / evt.total;
				console.log(percentComplete);
			}
		}, false);

		req.responseType = "blob";
		req.onreadystatechange = function () {
			if (req.readyState == 2 && req.status == 200) {
				stopTimer();
				$('.progress-msg').html("Download is being started");
			}
			else if (req.readyState == 3) {
				$('.progress-msg').html("Download is under progress");
				$('.progress-bar').css('width', '80%');
				$('.progress-bar').attr('aria-valuenow', 80);
			}
			else if (req.readyState === 4 && req.status === 200) {

				$('.progress-bar').css('width', '100%');
				$('.progress-bar').attr('aria-valuenow', 100);

				$('.progress-msg').html("Downloaing has finished");

				percentage = 100;

				var disposition = req.getResponseHeader('content-disposition');
				var matches = /"([^"]*)"/.exec(disposition);
				var filename = (matches != null && matches[1] ? matches[1] : 'Product_Master.xlsx');

				// var filename = $(that).data('filename');
				if (typeof window.chrome !== 'undefined') {
					// Chrome version
					var link = document.createElement('a');
					link.href = window.URL.createObjectURL(req.response);
					link.download = filename;
					link.click();
					if (percentage == 100) {
						$('#progress').hide();
					}
				} else if (typeof window.navigator.msSaveBlob !== 'undefined') {
					// IE version
					var blob = new Blob([req.response], { type: 'application/force-download' });
					window.navigator.msSaveBlob(blob, filename);
					if (percentage == 100) {
						$('#progress').hide();
					}
				} else {
					// Firefox version
					var file = new File([req.response], filename, { type: 'application/force-download' });
					window.open(URL.createObjectURL(file));
					if (percentage == 100) {
						$('#progress').hide();
					}
				}
			}
		};
		var param = '_token='+token;
		req.send(param);
	});
});

var timer;

function setTimer(percentage) {
	percentage = 20;
	timer = setInterval(function () {
		console.log(percentage);
		$('.progress-bar').css('width', percentage.toString() + '%');
		$('.progress-bar').attr('aria-valuenow', percentage);
		$('.progress-msg').html("Please wait.. Retrieving data.");
		percentage = percentage + 5;
	}, 100000);
}

function stopTimer() {
	clearInterval(timer);
}

function clearCode() {
	$('.select-code').val('');
	$('.clear').val('');
	$('#cut_weight').val(0);
	$('#cut_length').val(0);
	$('#cut_width').val(0);
	$('#cut_length_uom').val('N/A');
	$('#cut_weight_uom').val('N/A');
	$('#cut_width_uom').val('N/A');
	$('#hide_3rd').show();
	$('#hide_9th').show();
	$('#hide_14th').show();
}

function showDropdowns(prod_type) {
	$.ajax({
		url: showDropdownURL,
		type: 'GET',
		dataType: 'JSON',
		data: {_token: token, prod_type: prod_type}
	}).done(function(data, textStatus, xhr) {
		$.each(data, function(i,x) {
			switch(i) {
				case 'first':
					var first = "<option></option>";
					$('#first').html(first);
					$.each(x, function(ii, xx) {
						first = '<option value="'+xx.character_code+'">'+xx.description+'</option>';
						$('#first').append(first);
					});

					var first_val = "<option></option>";
					$('#first_val').html(first_val);
					$.each(x, function(ii, xx) {
						first_val = '<option value="'+xx.character_code+'">'+xx.character_code+'</option>';
						$('#first_val').append(first_val);
					});
				break;

				case 'second':
					var second = "<option></option>";
					$('#second').html(second);
					$.each(x, function(ii, xx) {
						second = '<option value="'+xx.character_code+'">'+xx.description+'</option>';
						$('#second').append(second);
					});

					var second_val = "<option></option>";
					$('#second_val').html(second_val);
					$.each(x, function(ii, xx) {
						second_val = '<option value="'+xx.character_code+'">'+xx.character_code+'</option>';
						$('#second_val').append(second_val);
					});
				break;

				case 'third':
					var third = "<option></option>";
					$('#third').html(third);
					$.each(x, function(ii, xx) {
						third = '<option value="'+xx.character_code+'">'+xx.description+'</option>';
						$('#third').append(third);
					});

					var third_val = "<option></option>";
					$('#third_val').html(third_val);
					$.each(x, function(ii, xx) {
						third_val = '<option value="'+xx.character_code+'">'+xx.character_code+'</option>';
						$('#third_val').append(third_val);
					});
				break;

				case 'forth':
					var forth = "<option></option>";
					$('#forth').html(forth);
					$.each(x, function(ii, xx) {
						forth = '<option value="'+xx.character_code+'">'+xx.description+'</option>';
						$('#forth').append(forth);
					});

					var forth_val = "<option></option>";
					$('#forth_val').html(forth_val);
					$.each(x, function(ii, xx) {
						forth_val = '<option value="'+xx.character_code+'">'+xx.character_code+'</option>';
						$('#forth_val').append(forth_val);
					});
				break;

				case 'fifth':
					var fifth = "<option></option>";
					$('#fifth').html(fifth);
					$.each(x, function(ii, xx) {
						fifth = '<option value="'+xx.character_code+'">'+xx.description+'</option>';
						$('#fifth').append(fifth);
					});

					var fifth_val = "<option></option>";
					$('#fifth_val').html(fifth_val);
					$.each(x, function(ii, xx) {
						fifth_val = '<option value="'+xx.character_code+'">'+xx.character_code+'</option>';
						$('#fifth_val').append(fifth_val);
					});
				break;

				case 'seventh':
					var seventh = "<option></option>";
					$('#seventh').html(seventh);
					$.each(x, function(ii, xx) {
						seventh = '<option value="'+xx.character_code+'">'+xx.description+'</option>';
						$('#seventh').append(seventh);
					});

					var seventh_val = "<option></option>";
					$('#seventh_val').html(seventh_val);
					$.each(x, function(ii, xx) {
						seventh_val = '<option value="'+xx.character_code+'">'+xx.character_code+'</option>';
						$('#seventh_val').append(seventh_val);
					});
				break;

				case 'eighth':
					var eighth = "<option></option>";
					$('#eighth').html(eighth);
					$.each(x, function(ii, xx) {
						eighth = '<option value="'+xx.character_code+'">'+xx.description+'</option>';
						$('#eighth').append(eighth);
					});

					var eighth_val = "<option></option>";
					$('#eighth_val').html(eighth_val);
					$.each(x, function(ii, xx) {
						eighth_val = '<option value="'+xx.character_code+'">'+xx.character_code+'</option>';
						$('#eighth_val').append(eighth_val);
					});
				break;

				case 'ninth':
					var ninth = "<option></option>";
					$('#ninth').html(ninth);
					$.each(x, function(ii, xx) {
						ninth = '<option value="'+xx.character_code+'">'+xx.description+'</option>';
						$('#ninth').append(ninth);
					});

					var ninth_val = "<option></option>";
					$('#ninth_val').html(ninth_val);
					$.each(x, function(ii, xx) {
						ninth_val = '<option value="'+xx.character_code+'">'+xx.character_code+'</option>';
						$('#ninth_val').append(ninth_val);
					});
				break;

				case 'eleventh':
					var eleventh = "<option></option>";
					$('#eleventh').html(eleventh);
					$.each(x, function(ii, xx) {
						eleventh = "<option value='"+xx.character_code+"'>"+xx.description+"</option>";
						$('#eleventh').append(eleventh);
					});

					var eleventh_val = "<option></option>";
					$('#eleventh_val').html(eleventh_val);
					$.each(x, function(ii, xx) {
						eleventh_val = '<option value="'+xx.character_code+'">'+xx.character_code+'</option>';
						$('#eleventh_val').append(eleventh_val);
					});
				break;

				case 'forteenth':
					var forteenth = "<option></option>";
					$('#forteenth').html(forteenth);
					$.each(x, function(ii, xx) {
						forteenth = '<option value="'+xx.character_code+'">'+xx.description+'</option>';
						$('#forteenth').append(forteenth);
					});

					var forteenth_val = "<option></option>";
					$('#forteenth_val').html(forteenth_val);
					$.each(x, function(ii, xx) {
						forteenth_val = '<option value="'+xx.character_code+'">'+xx.character_code+'</option>';
						$('#forteenth_val').append(forteenth_val);
					});
				break;

			}
		});
		autoAssignSelectBox($('#product_code').val());
	}).fail(function(xhr, textStatus, errorThrown) {
		msg(errorThrown,textStatus);
	});
}

function showCode(el,prod_type,code) {
	$('#'+el+'_val').val(code);
	$('#'+el.replace('_val','')).val(code);	

	if (el.replace('_val','') == 'second') {
		if (code.length == 2) {
			$('#hide_3rd').hide();
		} else {
			$('#hide_3rd').show();
		}
	}

	if (el.replace('_val','') == 'eighth') {
		if (code.length == 2) {
			$('#hide_9th').hide();
		} else {
			$('#hide_9th').show();
		}
	}

	if (el.replace('_val','') == 'eleventh') {
		if (code.length == 6) {
			$('#hide_14th').hide();
		} else {
			$('#hide_14th').show();
		}
	}

	showProductCode();
	showDescription();
}

function showProductCode() {
	var first_val = ($('#first_val').val() == null)? '':$('#first_val').val();
	var second_val = ($('#second_val').val() == null)? '':$('#second_val').val();
	var third_val = ($('#third_val').val() == null)? '':$('#third_val').val();
	var forth_val = ($('#forth_val').val() == null)? '':$('#forth_val').val();
	var fifth_val = ($('#fifth_val').val() == null)? '':$('#fifth_val').val();
	var seventh_val = ($('#seventh_val').val() == null)? '':$('#seventh_val').val();
	var eighth_val = ($('#eighth_val').val() == null)? '':$('#eighth_val').val();
	var ninth_val = ($('#ninth_val').val() == null)? '':$('#ninth_val').val();
	var eleventh_val = ($('#eleventh_val').val() == null)? '':$('#eleventh_val').val();
	var forteenth_val = ($('#forteenth_val').val() == null)? '':$('#forteenth_val').val();
	$('#product_code').val(first_val+second_val+third_val+forth_val+fifth_val+'-'+seventh_val+eighth_val+ninth_val+'-'+eleventh_val+forteenth_val);
}

function showDescription() {
	var eleventhcode = $('#eleventh_val').val();
	var fifth = (getSelectedText('fifth') == null)? '': getSelectedText('fifth');
	var eleventh = (getSelectedText('eleventh') == null)? '': getSelectedText('eleventh');
	var forteenth = (getSelectedText('forteenth') == null)? '': getSelectedText('forteenth');
	var eighth = (getSelectedText('eighth') == null)? '': getSelectedText('eighth');
	var forth = (getSelectedText('forth') == null)? '': getSelectedText('forth');
	var seventh = (getSelectedText('seventh') == null)? '': getSelectedText('seventh');

	if (eleventhcode.length == 3) {
		$('#code_description').val(
			fifth+' '+
			eleventh+' x '+
			forteenth+' '+
			seventh+' '+
			eighth+' '+
			forth
		);
	} else {
		$('#code_description').val(
			fifth+' '+
			eleventh+' '+
			seventh+' '+
			eighth+' '+
			forth
		);
	}
	$('#class').val(forth);
	$('#alloy').val(fifth);
	$('#item').val(seventh+' '+eighth);
	$('#size').val(eleventh);
}

function showProcess(prodprocess) {
	var opt = '<option value=""></option>';
	$('#div_code').html(opt);
	$.ajax({
		url: divProcessURL,
		type: 'GET',
		dataType: 'JSON',
		data: {_token: token, process: prodprocess},
	}).done(function(data, textStatus, xhr) {
		$.each(data, function(i, x) {
			opt = '<option value="'+x.div_code+'">'+x.div_name+'</option>';
			$('#div_code').append(opt);
		});
	}).fail(function(xhr, textStatus, errorThrown) {
		msg(errorThrown,textStatus);
	});
}

function delete_product(checkboxClass,deleteURL) {
	$('.loading').show();
	var chkArray = [];
	$(checkboxClass+":checked").each(function() {
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
	        		url: deleteURL,
	        		type: 'POST',
	        		dataType: 'JSON',
	        		data: {
	        			_token:token,
	        			id: chkArray
	        		},
	        	}).done(function(data, textStatus, xhr) {
	        		msg(data.msg,data.status)
	                getProductCodes();
	        	}).fail(function(xhr, textStatus, errorThrown) {
	        		msg(errorThrown,'error');
	        	}).always(function() {
	        		$('.loading').hide();
	        	});
	        } else {
	        	$('.loading').hide();
	            swal("Cancelled", "Your data is safe and not deleted.");
	        }
	    });


		clearCode();
		$('#product_code').prop('readonly', true);
		$('#code_description').prop('readonly', true);
		$('#product_id').val('');
		$('#product_code').val('');
		$('#code_description').val('');
		$('#product-type').val('');
		$('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');

	} else {
		msg("Please select at least 1 item.", "failed");
	}

	$('.check_all_product').prop('checked',false);
}

function delete_process(checkboxClass,deleteURL) {
	var chkArray = [];
	$(checkboxClass+":checked").each(function() {
		chkArray.push($(this).val());
	});

	if (chkArray.length > 0) {
		// confirm_delete(chkArray,token,deleteURL,true,'tbl_prod_process',prodProcessListURL+'?set='+
		// 			$('#set').val()+'&prod_code='+$('#prod_code').val(),prodProcess_dataColumn);
	} else {
		msg("Please select at least 1 item." , "failed");
	}

	$('.check_all_process').prop('checked',false);
}

function makeProcessList(arr) {
	var row = 1;

	$('#tbl_prod_process').dataTable().fnClearTable();
	$('#tbl_prod_process').dataTable().fnDestroy();
	$('#tbl_prod_process').dataTable({
		data: arr,
		order: [[0, 'asc']],
		bLengthChange: false,
		scrollY: "300px",
		paging: false,
		columns: [
			{
				data: function (x) {
					return '<input type="hidden" name="proc_id[]" value="' + x.id + '">' +
						'<input type="hidden" name="sequence[]" value="' + row + '">' + row;
				}, orderable: false, searchable: false
			},
			{
				data: function (x) {
					return '<input type="hidden" name="process[]" value="' + x.process + '">' + x.process;
				}, orderable: false, searchable: false
			},
			{
				data: function (x) {
					return '<input type="text" class="form-control form-control-sm" name="remarks[]" autocomplete="off" value="' + x.remarks + '">';
				}, orderable: false, searchable: false
			},
			{
				data: function (x) {
					return '<button type="button" class="btn btn-sm bg-blue btn_proc_edit" data-id="' + row + '" data-sets="' + x.sets + '">'+
								'<i class="fa fa-edit"></i>' +
							'</button>'+
						'<input type="hidden" name="sets[]" value="' + x.sets + '">';
				}, orderable: false, searchable: false
			},
			{
				data: function (x) {
					return '<button type="button" class="btn bg-red btn-sm btn_proc_delete" data-id="' + row + '">' +
								'<i class="fa fa-times"></i>' +
							'</button>';
				}, orderable: false, searchable: false
			}
		],
		createdRow: function (nRow, aData, iDataIndex) {
			$(nRow).attr('id', aData[0]);
			row++;
		}
	});
}

function showProcessList(prod_code,set) {
	$.ajax({
		url: prodProcessListURL,
		type: 'GET',
		dataType: 'JSON',
		data: {
			_token:token,
			prod_id:prod_code,
			sets:set
		}
	}).done(function(data, textStatus, xhr) {
		if (textStatus == 'success') {
			process_array = [];
			$.each(data, function(i, x) {
				process_array.push({
					id: x.id,
					sequence: x.sequence,
					remarks: (x.remarks == null)? '': x.remarks,
					process: x.process,
					sets: x.set,
				});
			});

			var seq = process_array.length + 1;
			$('#sequence').val(seq);
		}
		makeProcessList(process_array);
	}).fail(function(xhr, textStatus, errorThrown) {
		msg(errorThrown,textStatus);
	});
}

function showProductType() {
    var options = '';
    var datas = $("#product-type").val();
    $.ajax({
        url: productTypeURL,
        type: 'GET',
        datatype: "json",
        loadonce: true,
        data: {_token: token, data:datas},
        rowNum: 1000,
        success: function (returnData) {
        	if (returnData.length > 0) {
        		if(returnData.length > 1)
	        	{
		            options = "";
		            if (returnData.length > 20) {
		                l = 10;
		            }
		            else {
		                l = returnData.length;
		            }
		            for (var i = 0; i < l; i++) {
		                options += '<option value="' + returnData[i].prod_type + '" />';
		            }
		            $("#producttype").empty().append(options);
		            document.getElementById('producttype').innerHTML = options;
	        	}
	        	else if(returnData[0].prod_type == datas){
	        		options = "";
	        		$("#producttype").empty().append(options);
		            document.getElementById('producttype').innerHTML = options;
	        	}
	        	else{
	        		options += '<option value="' + returnData[0].prod_type + '" />';
	        		$("#producttype").empty().append(options);
	        		document.getElementById('producttype').innerHTML = options;
	        	}
        	}

        },
        error: function(xhr, textStatus, errorThrown) {
           msg(errorThrown,textStatus);
        }
    });
}

function defaultSizes(id = '',val = '') {
	if (id == '') {
		if (val == '') {
			$('.size').val(0.00);
		} else {
			$('.size').val(val);
		}
	} else {
		if (val == '') {
			$(id).val(0.00);
		} else {
			$(id).val(val);
		}
	}
}

function autoAssignSelectBox(code) {
	if (code != '') {
		var first = jsUcfirst(code.charAt(0));
		var second1 = jsUcfirst(code.charAt(1));
		var second2 = jsUcfirst(code.charAt(1))+jsUcfirst(code.charAt(2));
		var third = jsUcfirst(code.charAt(2));
		var forth = jsUcfirst(code.charAt(3));
		var fifth = jsUcfirst(code.charAt(4));

		var seventh = jsUcfirst(code.charAt(6));
		var eighth1 = jsUcfirst(code.charAt(7));
		var eighth2 = jsUcfirst(code.charAt(7))+jsUcfirst(code.charAt(8));
		var ninth = jsUcfirst(code.charAt(8));

		var eleventh1 = jsUcfirst(code.charAt(10))+jsUcfirst(code.charAt(11))+jsUcfirst(code.charAt(12));
		var eleventh2 = jsUcfirst(code.charAt(10))+jsUcfirst(code.charAt(11))+jsUcfirst(code.charAt(12))+jsUcfirst(code.charAt(13))+jsUcfirst(code.charAt(14))+jsUcfirst(code.charAt(15));
		var forteenth = jsUcfirst(code.charAt(13))+jsUcfirst(code.charAt(14))+jsUcfirst(code.charAt(15));

		$('#first_val').val(first);
		$('#first').val(first);

		$('#second_val').val(second1);
		$('#second').val(second1);

		if ($('#second_val').val() != null) {
			$('#third_val').val(third);
			$('#third').val(third);
			$('#hide_3rd').show();
		}else{
			$('#second_val').val(second2);
			$('#second').val(second2);
			$('#hide_3rd').hide();
		}

		$('#forth_val').val(forth);
		$('#forth').val(forth);

		$('#fifth_val').val(fifth);
		$('#fifth').val(fifth);

		$('#seventh_val').val(seventh);
		$('#seventh').val(seventh);

		$('#eighth_val').val(eighth1);
		$('#eighth').val(eighth1);

		if ($('#eighth_val').val() != null) {
			$('#ninth_val').val(ninth);
			$('#ninth').val(ninth);
			$('#hide_9th').show();
		}else{
			$('#eighth_val').val(eighth2);
			$('#eighth').val(eighth2);
			$('#hide_9th').hide();
		}

		$('#eleventh_val').val(eleventh1);
		$('#eleventh').val(eleventh1);

		if ($('#eleventh_val').val() != null) {
			$('#forteenth_val').val(forteenth);
			$('#forteenth').val(forteenth);
			$('#hide_14th').show();
		}else{
			$('#eleventh_val').val(eleventh2);
			$('#eleventh').val(eleventh2);
			$('#forteenth_val').val('');
			$('#forteenth').val('');
			$('#hide_14th').hide();
		}

	}
}

function get_set() {
	var set = '<option value=""></option>';
	$('#set').html(set);
	$.ajax({
		url: getSetURL,
		type: 'GET',
		dataType: 'JSON',
		data: {_token: token},
	}).done(function(data, textStatus, xhr) {
		console.log(data);
		$.each(data, function(i, x) {
			set = '<option value="'+x.id+'">'+x.text+'</option>';
			$('#set').append(set);
		});
	}).fail(function(xhr, textStatus, errorThrown) {
		msg(errorThrown,textStatus);
	});
}

function selectedProcess(set_id, prod_id ) {
	$.ajax({
		url: getProcessURL,
		type: 'POST',
		dataType: 'JSON',
		data: {_token: token, set_id:set_id, prod_id: prod_id},
	}).done(function(data, textStatus, xhr) {
		if (textStatus == 'success') {
			process_array = [];
			$.each(data, function(i, x) {
				process_array.push({
					process: x.process,
					remarks: (x.remarks==null)?'':x.remarks,
					sets: x.set_id,
				});
			});

			var seq = process_array.length + 1;
			$('#sequence').val(seq);
		}
		makeProcessList(process_array);
	}).fail(function(xhr, textStatus, errorThrown) {
		msg(errorThrown,textStatus);
	});
}

function get_dropdown_product() {
    var opt = "<option value=''></option>";
    $('#product-type').html(opt);
    $.ajax({
        url: getdropdownproduct,
        type: 'GET',
        dataType: 'JSON',
        data: {_token: token},
    }).done(function(data, textStatus, xhr) {
        $.each(data, function(i, x) {
            opt = "<option value='"+x.product_line+"'>"+x.product_line+"</option>";
            $('#product-type').append(opt);
        });
    }).fail(function(xhr, textStatus, errorThrown) {
        msg(errorThrown,textStatus);
    });
}


function getProductCodes() {
	$('#tbl_product_code').dataTable().fnClearTable();
	$('#tbl_product_code').dataTable().fnDestroy();
	$('#tbl_product_code').dataTable({
		ajax: {
			url: prodCodeListURL,
			error: function(xhr,textStatus,errorThrown) {
				ErrorMsg(xhr);
			}
		},
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
		order: [[5, 'desc']],
		columns: [
			{
				data: function (data) {
					return '<input type="checkbox" class="table-checkbox check_product_item" value="' + data.id + '">';
				}, orderable: false, searchable: false, width: '3.66%'
			},
			{
				data: function (data) {
					return "<button class='btn btn-sm bg-blue btn_edit_product' " +
						"data-id='" + data.id + "' " +
						"data-product_type='" + data.product_type + "'" +
						"data-product_code='" + data.product_code + "' " +
						"data-code_description='" + data.code_description + "'" +
						"data-cut_weight='" + data.cut_weight + "'" +
						"data-cut_weight_uom='" + data.cut_weight_uom + "'" +
						"data-cut_length='" + data.cut_length + "'" +
						"data-cut_length_uom='" + data.cut_length_uom + "'" +
						"data-cut_width='" + data.cut_width + "'" +
						"data-cut_width_uom='" + data.cut_width_uom + "'" +
						"data-item='" + data.item + "'" +
						"data-alloy='" + data.alloy + "'" +
						"data-class='" + data.class + "'" +
						"data-size='" + data.size + "'" +
						"data-standard_material_used='" + data.standard_material_used + "'" +
						"data-finish_weight='" + data.finish_weight + "'" +
						"data-create_user='" + data.create_user + "'" +
						"data-created_at='" + data.created_at + "'>" +
						"<i class='fa fa-edit'></i>" +
						"</button><button class='btn btn-sm bg-purple btn_assign_process' " +
						"data-id='" + data.id + "' " +
						"data-product_code='" + data.product_code + "' title='Assign Process'>" +
						"<i class='fa fa-refresh'></i>" +
						"</button>";
				}, orderable: false, searchable: false, width: '3.66%'
			},
			{ data: 'product_type', width: '24.66%' },
			{ data: 'product_code', width: '19.66%' },
			{
				data: function (data) {
					return '<span="' + data.code_description + '">' + ellipsis(data.code_description, 10) + '<span>';
				}, width: '39.66%'
			},
			{ data: 'created_at', width: '8.66%' },
		]
	});
}