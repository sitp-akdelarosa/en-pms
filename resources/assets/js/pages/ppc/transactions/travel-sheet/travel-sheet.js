var jo_details = [];
var prod_arr = [];
var cut_sched = [];
var scno_arr = [];
var sched_qty_arr = [];

$( function() {
	
	init();

	$('#btn_cutting_schedule').on('click', function() {
		$('#prepared_by').val(auth_user);
		$('#modal_cutting_schedule').modal('show');
	});

	$('#tbl_jo_details_body').on('click', '.open_travel_sheet_modal', function() {
		$('#idJO').val($(this).attr('data-idJO'));
		$('#travel_sheet_id').val($(this).attr('data-id'));
		$('#jo_no').val($(this).attr('data-jo_no'));
		$('#prod_code').val($(this).attr('data-prod_code'));

		$('#ship_date').val($(this).attr('data-ship_date'));
		$('#ts_remarks').val($(this).attr('data-remarks'));

		$('#cancel_process').hide();
		

		if ($(this).attr('data-id') !== '') {
			$('#issued_qty').val( $(this).attr('data-issued_qty'));
			$('#qty_per_sheet').val( $(this).attr('data-qty_per_sheet'));
			$('#iso_no').val( $(this).attr('data-iso_code'));
			$('#issued_qty').val( $(this).attr('data-issued_qty'));
			
			getPreTravelSheetData($(this).attr('data-id'), $(this).attr('data-jo_no'));
			getSC($(this).attr('data-idJO'));
		} else {
			$('#issued_qty').val($(this).attr('data-sched_qty'));
			// $('#ship_date').val($(this).attr('data-ship_date'));
			// $('#ts_remarks').val($(this).attr('data-remarks'));
			$('#qty_per_sheet').val('');
			$('#iso_no').val('');
			makeProdTable([]);
			$('#set').val('None');
			showProcessList('None', $(this).attr('data-prod_code'), null,$(this).attr('data-jo_no'));
		}
		if($(this).attr('data-status') == 2 || $(this).attr('data-status') == 5){
			$('.disableOnProduction').prop('disabled',true);
		}else if($(this).attr('data-status') == 0){
			$('.disableOnProduction').prop('disabled',false);
			$('#btn_travel_sheet_preview').prop('disabled',true);
		}else{
			$('#btn_travel_sheet_preview').prop('disabled',false);
			$('.disableOnProduction').prop('disabled',false);
		}
		$('#modal_travel_sheet').modal('show');
	});

	$('#set').on('change', function() {
		showProcessList($(this).val(), $('#prod_code').val(), null, $('#jo_no').val());
	});

	$('#tbl_product').on('change', '.issued_qty_per_sheet', function(e) {
		getTotalIssuedQty();
	});
	
	$('#btn_add_prod').on('click', function() {
		var issued_qty = parseInt($('#issued_qty').val());
		var qty_per_sheet = parseInt($('#qty_per_sheet').val());
		var idJO = $('#idJO').val();
		if (issued_qty < qty_per_sheet) {
			msg("Qty per sheet is greater than issued qty." , 'failed');
		}else if (issued_qty < 0 || qty_per_sheet < 0 || issued_qty == 0 || qty_per_sheet == 0) {
			msg("Please input a valid number." , 'failed');
		} else {

			$('.loadingOverlay-modal').show();
			$.ajax({
				url: getSc_noURL,
				type: 'POST',
				dataType: 'JSON',
				data: {_token:token,id:idJO},
			}).done(function(data, textStatus, xhr) {
				var scnum = data.sc_no.join();
				$('#sc_no').val(scnum);

				scno_arr = data.sc_no;
				sched_qty_arr = data.back_order_qty;
				// var sc_no = sc.split(',');
				prod_arr = [];
				var count = 1;
				var index = 0;
				if ($('#qty_per_sheet').val() == '' || $('#qty_per_sheet').val() == 0) {
					prod_arr.push({
						id: count,
						index: index,
						prod_code: $('#prod_code').val(),
						issued_qty: $('#issued_qty').val(),
						sc_no: scno_arr
					});
					count++;
					index++;
				} else {
					var count = 1;
					var index = 0;
					var quo = issued_qty / qty_per_sheet;

					if (quo % 1 != 0) {
						var num = quo.toString().split('.');
						var times = parseFloat(num[0]);
						var remainder = parseFloat('0.'+num[1]);

						for (var i = 0; i < times; i++) {
							prod_arr.push({
								id: count,
								index: index,
								prod_code: $('#prod_code').val(),
								issued_qty: qty_per_sheet,
								sc_no: scno_arr
							});
							count++;
							index++;
						}

						prod_arr.push({
							id: count,
							index: index,
							prod_code: $('#prod_code').val(),
							issued_qty: parseInt(qty_per_sheet*remainder),
							sc_no: scno_arr
						});

					} else {
						var count = 1;
						var index = 0;
						for (var i = 0; i < quo; i++) {
							prod_arr.push({
								id: count,
								index: index,
								prod_code: $('#prod_code').val(),
								issued_qty: qty_per_sheet,
								sc_no: scno_arr
							});
							count++;
							index++;
						}
					}
				}
				makeProdTable(prod_arr,scno_arr);

			}).fail(function(xhr, textStatus, errorThrown) {
				var errors = xhr.responseJSON.errors;
				showErrors(errors);
			}).always( function() {
				$('.loadingOverlay-modal').hide();
			});

			$('#issued_qty_table').val(issued_qty);
		}
	});

	$('#btn_remove_prod').on('click', function() {
		var chkArray = [];
		$(".prod_check:checked").each(function() {
			chkArray.push($(this).val());
		});

		$.each(chkArray, function(i, x) {
			for (var i = prod_arr.length - 1; i >= 0; --i) {
				if (prod_arr[i].id == x) {
					prod_arr.splice(i,1);
				}
			}
		});

		makeProdTable(prod_arr,scno_arr);
	});

	$('#status').on('change', function() {
		var param = {
			_token: token,
			status: $(this).val(),
			fromvalue: null,
			tovalue: null
		};

		TravelSheetDataTable(joDetailsListURL, param);
	});

	$('#frm_travel_sheet').on('submit', function(e) {
		e.preventDefault();
		var validate = validateInput();
	 	var totalIssued = parseInt($('#issued_qty_table').val());
		var issued_qty = parseInt($('#issued_qty').val());
		 
		if (validate == 'invalid') {
			msg("Please input valid number.",'warning');
		} else if (validate == 'morethan') {
			msg('Issued quantity per sheet must not more than SC quantity.','warning');
		} else if (validate == 'no issuance') {
			msg('No issuance was prepared.','warning');
		} else {
			if (totalIssued != issued_qty) {
				swal({
				title: "Are you sure to save?",
				text: "The total issued qty is not equal to issued qty",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#f95454",
				confirmButtonText: "Yes",
				cancelButtonText: "No",
				closeOnConfirm: true,
				closeOnCancel: false
				}, function(isConfirm) {
					if (isConfirm) {
						SaveTravelSheet();
					} else {
						swal.close();
					}
				});
			} else {
				SaveTravelSheet();
			}
		}
	});

	$('#tbl_jo_details_body').on('change', '.jo_check', function(e) {
    	var status = $(this).attr('data-status');
    	 if ($(this).is(":checked") ) {
			 
    	 	if(status == 0 ){
				msg("No Preparation for Travel Sheet .",'failed');
				$(this).prop('checked',false);
			}
    	 }
	});

	$('#btn_travel_sheet_preview').on('click', function() {
		var travel_sheet = pdfTravelSheetURL+'?jo_no='+$('#jo_no').val();
		window.open(travel_sheet,'_tab');
	});

	$('#btn_travel_sheet_all_print_preview').on('click', function() {
		var jo_no = [];

		$(".jo_check:checked").each(function() {
			jo_no.push($(this).val());
		});

		if (jo_no.length > 0) {
			var travel_sheet = pdfTravelSheetURL+'?iso_control='+$('#iso_no').val()+'&&jo_no='+jo_no;
			window.open(travel_sheet,'_tab');
		} else {
			msg("Select a J.O. number.",'failed');
		}
	});

	$('#searchPS').on('click',function() {
        if ($('#from').val() != "" && $('#to').val() != "") {
			var param = {
				_token: token,
				status: $('#status').val(),
				fromvalue: $('#from').val(),
				tovalue: $('#to').val()
			};

			TravelSheetDataTable(joDetailsListURL, param);
	   } 

	    else if ($('#from').val() != "") {

			var param = {
				_token: token,
				status: $('#status').val(),
				fromvalue: $('#from').val(),
				tovalue: ''
			};

			TravelSheetDataTable(joDetailsListURL, param);
	    }
	    else{
	        msg("From Input is required","warning");
	    }
    });

    $('#tbl_product_body').on('change', '.scno', function() {
    	var sc = $(this).val();
    	var qty = 0;

    	$.each(scno_arr, function(i, x) {
    		if (sc.includes(x[0])) {
    			qty = parseFloat(qty) + parseFloat(sched_qty_arr[i]);
    		}
    	});

    	$('#sc_qty_'+$(this).attr('data-id')).val(qty);
    });

    $('#tbl_product_body').on('change', '.issued_qty_per_sheet', function() {
    	var issued_qty = parseFloat($(this).val());
    	var sc_qty = parseFloat($('#sc_qty_'+$(this).attr('data-id')).val());

    	// if (issued_qty > sc_qty) {
    	// 	msg('Issued quantity per sheet must not more than SC quantity.','failed');
    	// 	// $(this).val($(this).attr('data-old_qty'));
    	// }
	});
	
	$('#btn_proceed').on('click', function() {
		var id = [];
		var jo = [];
		var status = [];
		var error = 0;
		var table = $('#tbl_jo_details').DataTable();

		for (var x = 0; x < table.context[0].aoData.length; x++) {
			var DataRow = table.context[0].aoData[x];
			if (DataRow.anCells !== null && DataRow.anCells[0].firstChild.checked == true) {
				var checkbox = table.context[0].aoData[x].anCells[0].firstChild;
				id.push($(checkbox).attr('data-id'));
				jo.push($(checkbox).attr('data-jo'));
				status.push($(checkbox).attr('data-status'));
			}
		}

		if (id.length > 0) {
			for (let i = 0; i < jo.length; i++) {
				if (status[i] == '2' || status[i] == '4' || status[i] == '5' || status[i] == '6') {
					error++;
				}
			}

			if (error > 0) {
				msg("Please select only J.O. # with status of 'Ready to Issue'.", 'failed');
			} else {
				swal({
					title: "Proceed to Production",
					text: "Ready to proceed this Travel sheet to Production?",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#f95454",
					confirmButtonText: "Yes",
					cancelButtonText: "No",
					closeOnConfirm: true,
					closeOnCancel: false
				}, function (isConfirm) {
					if (isConfirm) {
						var param = {
							_token: token,
							id: id,
							jo: jo
						}
						ProceedToProduction(param);
					} else {
						swal.close();
					}
				});
			}
		} else {
			msg("Select at least 1 J.O. number to proceed to Production.", 'failed');
		}
		
	});

	$('#btn_add_process').on('click', function () {
		var sameProcess = 0;
		$.each(process_array, function (i, x) {
			if (x.process == $('#process').val()) {
				sameProcess = 1
			}
		});
		//if (sameProcess == 0) {
			if ($('#process').val() == '') {
				msg("Please Select a Process.", 'warning');
			} else {
				var div_code;
				getProcessDiv($('#process').val(), function (output) {
					div_code = output;

					if ($('#process_id').val() !== '') {
						var id = $('#process_id').val();
						id--;
						process_array[id] = {
							id: $('#process_id').val(),
							sequence: $('#sequence').val(),
							remarks: '',
							process: $('#process').val(),
							sets: $('#set').val(),
							div_code: div_code
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

						process_array.splice(seq, 0, {
							id: '',
							sequence: $('#sequence').val(),
							prod_code: $('#prod_code').val(),
							remarks: '',
							process: $('#process').val(),
							sets: $('#set').val(),
							div_code: div_code
						});

						var sequence = process_array.length + 1;
						$('#sequence').val(sequence);
					}
					makeProcessList(process_array,null);
				});
			}
		// } else {
		// 	msg("The Process already existing.", "failed");
		// }
		$('#cancel_process').hide();
	});

	$('#btn_cancel_process').on('click', function () {
		$('#process').val('');
		$('#process_id').val('');
		$('#btn_add_process').html('<i class="fa fa-plus"></i> Add Process');
		$('#btn_add_process').removeClass('bg-navy');
		$('#btn_add_process').addClass('bg-green');

		$('#cancel_process').hide();

		$('#sequence').prop('readonly', false);

		$(this).hide();

		//showProcessList($('#set').val(), $('#prod_code').val(), null, $('#jo_no').val());
	});

	$('#tbl_process_body').on('click', '.btn_proc_delete', function () {
		var id = $(this).attr('data-id');
		$('#' + id).remove();
		id--;
		process_array.splice(id, 1);

		makeProcessList(process_array,null);

		// if ($('#tbl_process_body > tr').length < 1) {
		// 	$('#tbl_process_body').html('<tr id="no_data">'+
		//                                     '<td colspan="4" class="text-center">No data available.</td>'+
		//                                 '</tr>');
		// }
	});

	$('#tbl_process_body').on('click', '.btn_proc_edit', function () {
		var id = $(this).attr('data-id');
		$('#process_id').val(id);
		$('#sequence').val(id);
		$('#set').val($(this).attr('data-sets'));
		$('#sequence').prop('readonly', true);

		id--;
		var proc = process_array[id].process;
		$('#process').val(proc);


		$('#btn_add_process').html('<i class="fa fa-check"></i> Update Process');
		$('#btn_add_process').removeClass('bg-green');
		$('#btn_add_process').addClass('bg-navy');
		$('#cancel_process').show();
		$('#btn_cancel_process').show();
	});

	$('#tbl_product_body').on('click','.btn_remove_issue', function() {
		var index = $(this).attr('data-index');
		$('#' + index).remove();
		prod_arr.splice(index, 1);

		console.log(prod_arr);
		// makeProdTable(prod_arr);
	});

	$('#ship_date').on('change', function(e) {
		hideErrors('ship_date');
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

	var param = {
		_token: token,
		status: $('#status').val(),
		fromvalue: $('#from').val(),
		tovalue: $('#to').val()
	};

	TravelSheetDataTable(joDetailsListURL, param);

	get_set();
	getISO('#iso_no');

	get_dropdown_items_by_id(1, '#process');

	$('#sortable_process').sortable({
		multiDrag: true,
		selectedClass: 'selected',
		fallbackTolerance: 3, // So that we can select items on mobile
		animation: 150
	});
}

function getTotalIssuedQty(){
	var totalIssued = 0;
	var rows = $('#tbl_product')["0"].rows.length - 1;
 	if(rows != 0){
        for(var x=0;x<rows;x++){
        	totalIssued += parseInt($('#tbl_product')["0"].children[1].children[x].cells[1].children["0"].value);
        }
	}
	$('#issued_qty_table').val(totalIssued);	
}

function validateInput(){
	var rows = $('#tbl_product')["0"].rows.length - 1;
 	if(rows > 0){
        for(var x=0;x<rows;x++){
			if ($('#tbl_product')["0"].children[1] !== undefined) {
				if ($('#tbl_product')["0"].children[1].children[x].cells[1] == undefined) {
					return "no issuance";
				} else {
					var issued = parseInt($('#tbl_product')["0"].children[1].children[x].cells[1].children["0"].value);
					var sched = parseInt($('#tbl_product')["0"].children[1].children[x].cells[2].children["0"].value);
					if (issued == 0 || issued < 0) {
						return "invalid";
					}
				}
			}
        }
	} else {
		msg('Please prepare your travel sheet properly.', 'failed')
		return "invalid";
	}
	return  "NONE";
}

function SaveTravelSheet(){
	$('.loadingOverlay-modal').show();
	$.ajax({
		url: $('#frm_travel_sheet').attr('action'),
		type: 'POST',
		dataType: 'JSON',
		data: $('#frm_travel_sheet').serialize(),
	}).done(function(data, textStatus, xhr) {
		if (data.status == "success") {
			msg(data.msg,data.status);
			$('#travel_sheet_id').val(data.travel_sheet_id);

			var param = {
				_token: token,
				status: $('#status').val(),
				fromvalue: '',
				tovalue: ''
			};

			TravelSheetDataTable(joDetailsListURL, param);

			$('#btn_travel_sheet_preview').prop('disabled',false);
		}else if (data.status == "warning"){
			msg(data.msg,data.status);
		}
	}).fail(function(xhr, textStatus, errorThrown) {
		if (xhr.status == 422) {
			var errors = xhr.responseJSON.errors;
			showErrors(errors);
		} else {
			ErrorMsg(xhr);
		}
	}).always(function() {
		$('.loadingOverlay-modal').hide();
	});		
}

function getProcess(set,prod_code,old_data) {
	
	var pros = '<tr>'+
                    '<td colspan="4">No data</td>'+
                '</tr>';
	$('#tbl_process_body').html(pros);

	$.ajax({
		url: getProcessURL,
		type: 'GET',
		dataType: 'JSON',
		data: {
			_token: token,
			sets: set,
			prod_code: prod_code
		},
	}).done(function(data, textStatus, xhr) {
		if (data.length > 0) {

			// if (arr.length > 0) {
				
			// } else {
			// 	$('#sortable_process').html('<div class="list-group-item process_name" data-process="">No Process selected.</div>');
			// 	$('.loadingOverlay').hide();
			// }

			var list = '';
			$('#sortable_process').html(list);

			$.each(data, function (i, x) {
				var remarks = (x.process[3] == null) ? '' : x.process[3];

				list += '<div class="form-group row process_name" data-process="' + x.process[0] + '" data-remarks="' + remarks + '">' +
					'<div class="col-sm-3">' + x.process[0] + 
						'<input type="hidden" name="processes[]" value="' + x.process[0] + '">' +
						'<input type="hidden" name="sequence[]" value="'+x.process[2]+'">'+
					'</div>' +
					'<div class="col-sm-5">' +
					'<input type="text" class="form-control form-control-sm" name="remarks[]" placeholder="Remarks.." value="' + remarks + '">' +
					'</div>' +
					'<div class="col-sm-3">' +
						'<select class="form-control form-control-ms" name="div_code[]">';
							if (old_data !== undefined) {
								list += "<option value='" + old_data[i].div_code + "' hidden>" + old_data[i].div_code + "</option>";
							}
							if (x.process[1].length > 0) {
								selected = '';
								$.each(x.process[1], function (ii, xx) {
									if (old_data !== undefined) {
										if (old_data[i].process_name == x.process[0]) {
											selected = 'selected';
										}
									}

									list += "<option value='" + xx.div_code + "'>" + xx.div_code + "</option>";
								});
							} else {
								list += "<option value=''></option>";
							}
				list += '</select><div id="division_feedback"></div>' +
					'</div>' +
					'<button type="button" class="btn btn-sm bg-red delete col-sm-1 btn-block" data-count="' + x.count + '">' +
					'<i class="fa fa-times" ></i> ' +
					'</button> ' +
					'</div>';
			});
			$('#sortable_process').html(list);
			$('.loadingOverlay').hide();



			// pros = '<tr>' +
			// 			'<td colspan="4">Loading...</td>' +
			// 		'</tr>';
			// $('#tbl_process_body').html(pros);

			// pros = '';
			// $.each(data, function(i, x) {
			// 	var remarks = (x.process[3] == null) ? '' : x.process[3];
			// 	pros += '<tr>'+
			// 				'<td>'+x.process[0]+
			// 					'<input type="hidden" name="processes[]" value="'+x.process[0]+'">'+
			// 					'<input type="hidden" name="sequence[]" value="'+x.process[2]+'">'+
			// 				'</td>'+
			// 				'<td>' + remarks +
			// 					'<input type="text" class="form-control form-control-sm" name="remarks[]" value="' + remarks + '">' +
			// 				'</td>' +
			// 				'<td>'+
			// 					'<select class="form-control form-control-ms" name="div_code[]">';
			// 						if (old_data !== undefined) {
			// 							pros += "<option value='"+old_data[i].div_code+"' hidden>"+old_data[i].div_code+"</option>";
			// 						}
			// 						if (x.process[1].length > 0) {
			// 							selected = '';
			// 							$.each(x.process[1], function(ii, xx) {
			// 								if (old_data !== undefined) {
			// 									if (old_data[i].process_name == x.process[0]) {
			// 										selected = 'selected';
			// 									}
			// 								}
											
			// 								pros += "<option value='"+xx.div_code+"'>"+xx.div_code+"</option>";
			// 							});
			// 						} else {
			// 							pros += "<option value=''></option>";
			// 						}
									
			// 			pros += '</select><div id="division_feedback"></div>'+
			// 				'</td>'+
			// 				'<td>' + 
			// 					'<button class="btn btn-sm bg-red btn_remove_process">' +
			// 						'<i class="fa fa-times"></i>'+
			// 					'</button>'+
			// 				'</td>' +
			// 			'</tr>';
			// 	$('#tbl_process_body').append(pros);
			// });

			// $('#tbl_process_body').html(pros);
		} else {
			$('#sortable_process').html('<div class="list-group-item process_name" data-process="">No Process selected.</div>');
			// $('#tbl_process_body').html('<tr>'+
			// 			                    '<td colspan="4">No data displayed.</td>'+
			// 			                '</tr>');
		}
	}).fail(function(xhr, textStatus, errorThrown) {
		ErrorMsg(xhr);
	}).always( function() {
		//$('.loadingOverlay-modal').hide();
	});
}

function showProcessList(set, prod_code, old_data, jo) {
	$('.loadingOverlay-modal').show();
	$.ajax({
		url: getProcessURL,
		type: 'GET',
		dataType: 'JSON',
		data: {
			_token: token,
			sets: set,
			prod_code: prod_code,
			jo_no: jo
		}
	}).done(function (data, textStatus, xhr) {
		if (textStatus == 'success') {
			process_array = [];
			$.each(data, function (i, x) {
				process_array.push({
					id: x.id,
					sequence: x.sequence,
					remarks: (x.remarks == null) ? '' : x.remarks,
					process: x.process,
					sets: x.set,
					div_code: x.div_code
				});
			});

			console.log(data);
			console.log(process_array);

			var seq = process_array.length + 1;
			$('#sequence').val(seq);
		}
		makeProcessList(process_array, old_data);
	}).fail(function (xhr, textStatus, errorThrown) {
		msg(errorThrown, textStatus);
	}).always( function() {
		$('.loadingOverlay-modal').hide();
	});
}

function makeProcessList(arr, old_data) {
	var row = 1;
	var i = 0;

	$('.loadingOverlay-modal').show();

	$('#tbl_process').dataTable().fnClearTable();
	$('#tbl_process').dataTable().fnDestroy();
	$('#tbl_process').dataTable({
		data: arr,
		order: [[0, 'desc']],
		bLengthChange: false,
		scrollY: "400px",
		paging: false,
		searching: false,
		processing: true,
		scrollCollapse: true,
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
					var old = (old_data == null)? undefined: old_data[i];
					var list = '<select class="form-control form-control-ms" name="div_code[]">';
								if (old !== undefined) {
									list += "<option value='" + old.div_code + "' hidden>" + old.div_code + "</option>";
								}

								if (x.div_code.length > 0) {
									selected = '';
									$.each(x.div_code, function (ii, xx) {
										if (old !== undefined) {
											if (old.process_name == x.process) {
												selected = 'selected';
											}
										}

										list += "<option value='" + xx.div_code + "'>" + xx.div_code + "</option>";
									});
								} else {
									list += "<option value=''></option>";
								}
					list += '</select><div id="division_feedback"></div>';

					return list;
				}, orderable: false, searchable: false
			},
			{
				data: function (x) {
					return '<button type="button" class="btn btn-sm bg-blue btn_proc_edit" data-id="' + row + '" data-sets="' + x.sets + '">' +
						'<i class="fa fa-edit"></i>' +
						'</button>' +
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
			i++;
		},
		fnDrawCallback: function (oSettings) {
            $('.dataTables_scrollBody').slimScroll({
                height: '400px'
            });
        },
		initComplete: function() {
			$('.loadingOverlay-modal').hide();
		}
	});
}

function getProcessDiv(process, handleData) {
	$.ajax({
		url: getProcessDivURL,
		type: 'GET',
		dataType: 'JSON',
		data: {
			process: process 
		}
	}).done(function (data, textStatus, xhr) {
		handleData(data);
	}).fail(function (xhr, textStatus, errorThrown) {
		ErrorMsg(xhr);
	}).always(function () {
		console.log("complete");
	});

}

function TravelSheetDataTable(ajax_url, object_data) {
    var tbl_jo_details = $('#tbl_jo_details').DataTable();

    tbl_jo_details.clear();
    tbl_jo_details.destroy();
    tbl_jo_details = $('#tbl_jo_details').DataTable({
        ajax: {
            url: ajax_url,
            data: object_data,
            error: function(xhr,textStatus, errorThrown) {
                ErrorMsg(xhr);
            }
        },
        serverSide: true,
        processing: true,
        order: [[10,'desc']],
        columns: [ 
            { data: function(data) {
				return "<input type='checkbox' value='"+data.jo_no+"' data-jo='"+data.jo_no+"' "+
					"data-id='"+data.travel_sheet_id+"' data-status='"+data.status+"' class='table-checkbox jo_check'>";
			}, name: 'id', orderable: false, searchable: false, width: '3.33%'},
			{ data: 'action', name: 'action', orderable: false, searchable: false, width: '3.33%' },
			{ data: 'jo_no', name: 'jo_no', width: '10.33%' },
			{ data: 'product_code', name: 'prod_code', width: '10.33%' },
			{ data: 'description', name: 'description', width: '14.33%' },
			{ data: 'back_order_qty', name: 'order_qty', width: '6.33%' },
			{ data: 'sched_qty', name: 'sched_qty', width: '6.33%' },
			{ data: 'issued_qty', name: 'ts.issued_qty', width: '6.33%' },
			{ data: 'material_used', name: 'material_used', width: '14.33%' },
			{ data: 'material_heat_no', name: 'material_heat_no', width: '8.33%' },
			{ data: 'updated_at', name: 'updated_at', width: '8.33%' },
		    { data: function(data) {
				switch (data.status) {
					case 0:
						return 'No quantity issued'; //No quantity issued
						break;
					case '1':
						return 'Ready to Issue';
						break;
					case '2':
						return 'On-going Process';
						break;
					case '3':
						return 'Cancelled';
						break;
					case '4':
						return 'Proceeded to Production';
						break;
					case '5':
						return 'Closed';
						break;
					case '6':
						return 'In Production';
						break;
					default:
						return data.status;
						break;
				}
			}, name: 'ts.status', width: '8.33%' }
		],
        createdRow: function(row, data, dataIndex) {
			var dataRow = $(row);
			var checkbox = $(dataRow[0].cells[0].firstChild);
			var details_button = $(dataRow[0].cells[1].firstChild);

			if (data.status == 2) {
                $(row).css('background-color', '#001F3F'); // NAVY
				$(row).css('color', '#fff');
				checkbox.prop('disabled', false);
				details_button.prop('disabled', false);
            }

            if (data.status == 3) {
                $(row).css('background-color', '#ff6266'); // RED
				$(row).css('color', '#fff');
				checkbox.prop('disabled', true);
				details_button.prop('disabled', true);
			}
			
			if (data.status == 4) {
                $(row).css('background-color', '#7460ee'); // PURPLE
				$(row).css('color', '#fff');
				checkbox.prop('disabled', false);
				details_button.prop('disabled', false);
            }

            if (data.status == 5) {
                $(row).css('background-color', 'rgb(139 241 191)'); // GREEN
				$(row).css('color', '#000000');
				checkbox.prop('disabled', false);
				details_button.prop('disabled', false);
			}
			
			if (data.status == 6) {
                $(row).css('background-color', 'rgb(121 204 241)'); // BLUE
				$(row).css('color', '#000000');
				checkbox.prop('disabled', false);
				details_button.prop('disabled', false);
            }
        },
        fnDrawCallback: function() {
            $("#tbl_jo_details").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        initComplete: function() {
            $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
			$('.loadingOverlay').hide();
        }
    });
}

function get_set() {
	$('.loadingOverlay').show();
	var set = '<option value="None"></option>';
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
		ErrorMsg(xhr);
	}).always( function() {
		//$('.loadingOverlay').hide();
	});
}

function getPreTravelSheetData(id,jo) {
	prod_arr = [];

	$('.loadingOverlay-modal').show();
	$.ajax({
		url: getPreTravelSheetDataURL,
		type: 'GET',
		dataType: 'JSON',
		data: {
			_token: token,
			id: id
		},
	}).done(function(data, textStatus, xhr) {
		prod_arr = data.prod;
		makeProdTable(prod_arr,scno_arr);
		$('#set').val(data.sets);
		showProcessList(data.sets,data.prod_code,data.process,jo)
	}).fail(function(xhr, textStatus, errorThrown) {
		ErrorMsg(xhr);
	}).always(function () {
		//$('.loadingOverlay-modal').hide();
	});
}

function makeProdTable(arr,all_sc) {
	var index = 0;
	$('#tbl_product').dataTable().fnClearTable();
    $('#tbl_product').dataTable().fnDestroy();
    $('#tbl_product').dataTable({
        data: arr,
        bLengthChange : false,
        scrollY: "250px",
	    paging: false,
		searching: false,
		processing: true,
        columns: [
            { data: function(x) {
                return x.prod_code;
            }, searchable: false, orderable: false },

            { data: function(x) {
                return '<input type="number" step="1" name="issued_qty_per_sheet[]" id="issued_qty_'+x.id+'" data-id="'+x.id+'" data-old_qty="'+x.issued_qty+'" class="form-control issued_qty_per_sheet" value="'+x.issued_qty+'">';
            }, searchable: false, orderable: false },

            { data: function(x) {
            	var scnos = x.sc_no;
            	var qty = 0;

            	$.each(all_sc, function(i, x) {
            		if (scnos.includes(x)) {
            			qty = parseFloat(qty) + parseFloat(sched_qty_arr[i]);
            		}
            	});

                return '<input type="number" step="1" name="sc_qty[]" id="sc_qty_'+x.id+'" class="form-control sc_qty" value="'+qty+'" readonly>';
            }, searchable: false, orderable: false },

            { data: function(x) {
            	var scnos = x.sc_no;
            	// var scnos = scno.split(',');

            	var options = '';
            	// all_sc = sc.split(',');
            	$.each(all_sc, function(i, x) {
            		if (scnos.includes(x)) {
            			options += '<option value="'+x+'" selected>'+x+'</option>';
            		} else {
            			options += '<option value="'+x+'">'+x+'</option>';
            		}
            		
            	});
            	var index = parseFloat(x.id)-1;

            	if (index == NaN) {
            		index = 0;
            	}
                return '<select name="scno['+index+'][]" data-id="'+x.id+'" class="form-control form-control-sm scno" multiple="multiple">'+
                			options+
                		'</select>';
			}, searchable: false, orderable: false },

			{ data: function(x) {
				var idx = (x.index == undefined)? index : x.index;
				return '<button type="button" class="btn btn-sm bg-red btn_remove_issue" data-index="'+idx+'">'+
							'<i class="fa fa-times"></i>' +
						'</button>';
            }, searchable: false, orderable: false },

		],
		initComplete: function() {
		},
		fnDrawCallback: function (oSettings) {
            $('.dataTables_scrollBody').slimScroll({
                height: '250px'
            });
        },
		createdRow: function (nRow, aData, iDataIndex) {
			$(nRow).attr('id', index);
			index++;
		},
    });
    if(arr.length > 0){
		getTotalIssuedQty();
	}else{
		$('#issued_qty_table').val(0);
	}
    $('.scno').select2();
}

function getSC(idJO) {
	$.ajax({
		url: getSc_noURL,
		type: 'POST',
		dataType: 'JSON',
		data: {_token:token,id: idJO},
	}).done(function(data, textStatus, xhr) {
		scno_arr = data.sc_no;
		sched_qty_arr = data.back_order_qty;
		makeProdTable(prod_arr,scno_arr);
	}).fail(function(xhr, textStatus, errorThrown) {
		ErrorMsg(xhr);
	});
}

function ProceedToProduction(param) {
	$('.loadingOverlay').show();
	$.ajax({
		url: ProceedToProductionURL,
		type: 'POST',
		dataType: 'JSON',
		data: param,
	}).done(function(data, textStatus, xhr) {
		var param = {
			_token: token,
			status: $('#status').val(),
			fromvalue: $('#from').val(),
			tovalue: $('#to').val()
		};

		TravelSheetDataTable(joDetailsListURL, param);
		msg(data.msg,data.status);
	}).fail(function(xhr, textStatus, errorThrown) {
		ErrorMsg(xhr);
	}).always( function() {
		$('.loadingOverlay').hide();
	});
}
