var _with_zero = 0;

$(function () {
	getProdLine('');
	getMaterials('');
	getWarehouse('');
	// getInventory(_with_zero);
	InventoryTable(materialDataTable,{with_zero: _with_zero});
	init();

	//$('#srch_received_date').daterangepicker();

	$(document).on('shown.bs.modal', function () {
		$($.fn.dataTable.tables(true)).DataTable()
			.columns.adjust();
	});


	$('Atbl_materials_paginate .pagination').on('click', '.paginate_button',function () {
		alert('unchecked');
		// $('input:checkbox .check_all_inventories').prop('checked', false);
	});

	$('#item_class').on('change', function() {
		if ($(this).val() == 'RAW MATERIAL') {
			$('.product_div').hide();
			$('.material_div').show();
			$('#materials_type').select2("val", "");
		} else {
			$("#product_line").select2("val", "");
			$('.product_div').show();
			$('.material_div').hide();
		}
		hideErrors('item_code');
	});

	$('#srch_item_class').on('change', function () {
		if ($(this).val() == 'RAW MATERIAL') {
			$('.srch_product_div').hide();
			$('.srch_material_div').show();
			$("#srch_material_type").select2("val", "");
		} else {
			$("#srch_product_line").select2("val", "");
			$('.srch_product_div').show();
			$('.srch_material_div').hide();
		}
		hideErrors('srch_item_code');
	});

	$("#product_line").on('change', function () {
		var $class = 'finish';

		if ($('#item_class').val() == 'CRUDE') {
			$class = 'crude';
		}

		getItemCode('', '', $class);
		
	});

	$("#srch_product_line").on('change', function () {
		var $class = 'finish';

		if ($('#srch_item_class').val() == 'CRUDE') {
			$class = 'crude';
		}

		getItemCode('', 'search', $class);
	});

	$("#materials_type").on('change', function () {
		getItemCode('', '', 'material');
	});

	$("#srch_materials_type").on('change', function () {
		getItemCode('', 'search', 'material');
	});

	$("#item_code").on('change', function () {
		var $type = 'product';
		if ($('#item_class').val() == 'RAW MATERIAL') {
			$type = 'material';
		}
		getItemCodeDetails('', $type);
	});

	$("#srch_item_code").on('change', function () {
		var $type = 'product';
		if ($('#item_class').val() == 'RAW MATERIAL') {
			$type = 'material';
		}
		getItemCodeDetails('search', $type);
	});

	$('.custom-file-input').on('change', function () {
		let fileName = $(this).val().split('\\').pop();
		$(this).next('.custom-file-label').addClass("selected").html(fileName);
	});

	$('#btn_add').on('click', function () {
		var d = new Date();
		var month = d.getMonth() + 1;
		var day = d.getDate();
		var date = (month < 10 ? '0' : '') + month + '/' + (day < 10 ? '0' : '') + day + '/' + d.getFullYear();
		$('#received_date').val(date);

		$('#qty_weight').val(0);
		$('#qty_pcs').val(0);

		$('#item_class').val('RAW MATERIAL');

		clear();
		$('#modal_material_inventory').modal('show');
		
		// getItemCode('');
	});

	$('#btn_zero').on('click', function () {
		if (_with_zero == 0) {
			_with_zero = 1;
			// getInventory(_with_zero);
			InventoryTable(materialDataTable,{ with_zero: _with_zero });

			$(this).removeClass('bg-blue');
			$(this).addClass('bg-red');
			$(this).html('Exclude 0 quantity');
		} else {
			_with_zero = 0;
			// getInventory(_with_zero);
			InventoryTable(materialDataTable,{ with_zero: _with_zero });
			$(this).removeClass('bg-red');
			$(this).addClass('bg-blue');
			$(this).html('Include 0 quantity');
		}
	});

	$('#tbl_materials_body').on('click', '.edit-mainEdit', function (e) {
		e.preventDefault();
		$('#item_id').val($(this).attr('data-id'));
		$('#item_class').val($(this).attr('data-item_class')).trigger('change');

		if ($(this).attr('data-item_class') == 'RAW MATERIAL') {
			$('#receiving_no').val($(this).attr('data-receive_jo_no'));
			$('#received_date').val($(this).attr('data-received_date'));
			$('#materials_type').val($(this).attr('data-item_type_line')).trigger('change.select2');
			getItemCode($(this).attr('data-item_code'), '', 'material');

			$('#width').val($(this).attr('data-width'));
			$('#length').val($(this).attr('data-length'));
			$('#invoice_no').val($(this).attr('data-invoice_no'));
			$('#supplier').val($(this).attr('data-supplier'));
		} else {
			$('#jo_no').val($(this).attr('data-receive_jo_no'));
			$('#product_line').val($(this).attr('data-item_type_line')).trigger('change.select2');
			getItemCode($(this).attr('data-item_code'), '', $(this).attr('data-item_class'));
			$('#lot_no').val($(this).attr('data-lot_no'));
			$('#finish_weight').val($(this).attr('data-finish_weight'));
			$('#material_used').val($(this).attr('data-material_used'));
		}

		$('#warehouse').val($(this).attr('data-warehouse')).trigger('change.select2');
		$('#description').val($(this).attr('data-description'));
		$('#item').val($(this).attr('data-item'));
		$('#alloy').val($(this).attr('data-alloy'));
		$('#schedule').val($(this).attr('data-schedule'));
		$('#size').val($(this).attr('data-size'));
		
		$('#qty_weight').val($(this).attr('data-qty_weight'));
		$('#qty_pcs').val($(this).attr('data-qty_pcs'));
		$('#heat_no').val($(this).attr('data-heat_no'));
		$('#supplier_heat_no').val($(this).attr('data-supplier_heat_no'));
		

		$('#modal_material_inventory').modal('show');		
	});

	$('#frm_update_inventory').on('submit', function (e) {
		var formObj = $('#frm_update_inventory');
		var formURL = formObj.attr("action");
		var formData = new FormData(this);
		var fileName = $("#file_inventory").val();
		var ext = fileName.split('.').pop();
		var pros = $('#file_inventory').val().replace("C:\fakepath", "");
		var fileN = pros.substring(12, pros.length);
		e.preventDefault(); //Prevent Default action.
		if ($("#file_inventory").val() == '') {
			msg("No File", "failed");
		} else if ($("#up_item_class").val() == '') {
			msg("Please select an Item Class", "failed");
		} else {
			$('.loadingOverlay').show();
			if (fileName != '') {
				if (ext == 'xls' || ext == 'xlsx' || ext == 'XLS' || ext == 'XLSX') {
					$('.myprogress').css('width', '0%');
					$('#progress-msg').html('Uploading in progress...');
					var percent = 0;

					// check file
					$.ajax({
						url: checkfile,
						type: 'POST',
						mimeType: "multipart/form-data",
						contentType: false,
						cache: false,
						processData: false,
						data: formData,
					}).done( function(returns, textStatus, xhr) {
						var return_datas = jQuery.parseJSON(returns);
						if (return_datas.status == "success") {

							formData.append("item_class", return_datas.item_class);
							// upload file
							$.ajax({
								url: uploadInventory,
								type: 'POST',
								data: formData,
								mimeType: "multipart/form-data",
								contentType: false,
								cache: false,
								processData: false,
							}).done( function(returnData,textStatus,xhr) {
								$('.loadingOverlay').hide();
								var return_data = jQuery.parseJSON(returnData);

								msg(return_data.msg, return_data.status);
								document.getElementById('file_inventory_label').innerHTML = fileN;
								// getInventory(_with_zero);
								InventoryTable(materialDataTable,{ with_zero: _with_zero });
								var not_registedred = return_data.Material;
								if (not_registedred.length > 0) {
									GetMateriialsNotExisting(not_registedred);
								}
							}).fail( function(xhr,textStatus,errorThrown) {
								$('.loadingOverlay').hide();
								ErrorMsg(xhr);
							}).always(function () {
								$('.loadingOverlay').hide();
							});
						}
						else if (return_datas.status == "validateRequired") {
							$('.loadingOverlay').hide();
							msg("Fill up correctly the record in line " + return_datas.line, "warning");
							document.getElementById('file_inventory_label').innerHTML = "Select file...";
						}
						else if (return_datas.status == "heatnumber error") {
							$('.loadingOverlay').hide();
							msg(return_datas.msg, "warning");
							document.getElementById('file_inventory_label').innerHTML = "Select file...";
						}
						else if (return_datas.status == "not num") {
							$('.loadingOverlay').hide();
							msg("Invalid input of Quantity", "warning");
							document.getElementById('file_inventory_label').innerHTML = "Select file...";
						}
						else if (return_datas.status == "failed") {
							$('.loadingOverlay').hide();
							//msg(return_datas.msg,return_datas.status);
							if (return_datas.msg == '') {
								msg("Please maintain data as 1 sheet only.", "warning");
							} else {
								msg(return_datas.msg, "warning");
							}
							
							document.getElementById('file_inventory_label').innerHTML = "Select file...";
						}
						else if (return_datas.status == "same_items") {
							$('.loadingOverlay').hide();
							console.log(return_datas.same_items)
							sameMaterialTable(return_datas.same_items)
							msg(return_datas.msg, "warning");
							$('#modal_same_material').modal('show');
							document.getElementById('file_inventory_label').innerHTML = "Select file...";
						}
						else {
							$('.loadingOverlay').hide();
							msg("Upload failed", "warning");
							document.getElementById('file_inventory_label').innerHTML = "Select file...";
						}
					}).fail( function(xhr,textStatus,errorThrown) {
						$('.loadingOverlay').hide();
						ErrorMsg(xhr);
					}).always( function() {
						//$('.loadingOverlay').hide();
					});
				} else {
					$('.loadingOverlay').hide();
					msg("File Format not supported.", "warning");
				}
			}
		}
	});

	$("#frm_material_inventory").on('submit', function (e) {
		e.preventDefault();
		if ($('#quantity').val() < 0 || $('#quantity').val() == 0) {
			showErrors({quantity:["Please Input numbers greater than 0."]});
		} else {
			$('.loadingOverlay-modal').show();
			$.ajax({
				url: $(this).attr('action'),
				type: 'POST',
				dataType: 'JSON',
				data: $(this).serialize(),
			}).done(function (data, textStatus, xhr) {
				msg(data.msg, data.status);
				$('#modal_material_inventory').modal('hide');
				// getInventory(_with_zero);
				InventoryTable(materialDataTable,{ with_zero: _with_zero });
			}).fail(function (xhr, textStatus, errorThrown) {
				var errors = xhr.responseJSON.errors;

				console.log(errors);
				showErrors(errors);
			}).always( function() {
				$('.loadingOverlay-modal').hide();
			});
		}
	});

	$('#qty_weight').on('change', function() {
		if ($(this).val() !== '') {
			hideErrors($(this).attr('id'));
		}
	});

	
	$('#qty_pcs').on('change', function () {
		if ($(this).val() !== '') {
			hideErrors($(this).attr('id'));

			var finished_weight = 0
			if ($('#item_class').val() !== 'RAW MATERIAL') {
				finished_weight = (parseFloat($(this).val()) * parseFloat($('#finish_weight').val()));
				$('#qty_weight').val(finished_weight.toFixed(2));
			}

			console.log(finished_weight);
		}		
	});

	$('#btn_excel').on('click', function () {
		window.location.href = downloadNonexistingURL;
	});

	$('#btn_download_format').on('click', function () {
		$('#modal_excel_format').modal('show');
		//window.location.href = downloadFormatURL;
	});

	$('#btn_material_format').on('click', function() {
		window.location.href = downloadMaterialFormatURL;
	});

	$('#btn_product_format').on('click', function() {
		window.location.href = downloadProductFormatURL;
	});
	
	$('#btn_check_unregistered').on('click', function (event) {
		$.ajax({
			url: getNonexistingURL,
			type: 'GET',
			dataType: 'JSON',
		}).done(function (data, textStatus, xhr) {
			GetMateriialsNotExisting(data);
		}).fail(function (xhr, textStatus, errorThrown) {
			msg('Unregistered Products: ' + errorThrown);
		});

	});

	$('#received_date').on('change', function() {
		if ($(this).val() !== "") {
			hideErrors($(this).attr('id'));
		}
	});

	$('#btn_search_filter').on('click', function() {
		$('.srch-clear').val('');
		$('#srch_materials_type').val(null).trigger('change.select2');
		$('#srch_product_line').val(null).trigger('change.select2');
		$('#srch_item_code').val(null).trigger('change.select2');
		$('#srch_warehouse').val(null).trigger('change.select2');
		getMaterials('search');
		getProdLine('search');
		getWarehouse('search');
		$('#modal_search').modal('show');
	});

	$('#srch_received_date_from').on('change', function() {
		var selected_date = new Date($(this).val()).toISOString().split('T')[0];
		document.getElementsByName("srch_received_date_to")[0].setAttribute('min', selected_date);
	});

	$("#frm_search").on('submit', function (e) {
		e.preventDefault();
		// $('.loadingOverlay-modal').show();

		//console.log(objectifyForm($(this).serializeArray()));

		var search_param = objectifyForm($(this).serializeArray());

		search_param.with_zero = _with_zero;

		InventoryTable($(this).attr('action'),search_param);

		// $.ajax({
		// 	url: $(this).attr('action'),
		// 	type: 'GET',
		// 	dataType: 'JSON',
		// 	data: $(this).serialize(),
		// }).done(function (data, textStatus, xhr) {
		// 	//msg(data.msg, data.status);

		// 	InventoryTable(data);
		// 	//$('#modal_material_search').modal('hide');
			
		// }).fail(function (xhr, textStatus, errorThrown) {
		// 	var errors = xhr.responseJSON.errors;

		// 	console.log(errors);
		// 	showErrors(errors);
		// }).always(function () {
		// 	$('.loadingOverlay-modal').hide();
		// });
	});

	$('#btn_search_excel').on('click', function() {
		var srch_item_class = $('#srch_item_class').val();
		var srch_received_date_from = $('#srch_received_date_from').val();
		var srch_received_date_to = $('#srch_received_date_to').val();
		var srch_receiving_no = $('#srch_receiving_no').val();
		var srch_jo_no = $('#srch_jo_no').val();
		var srch_materials_type = $('#srch_materials_type').val();
		var srch_product_line = $('#srch_product_line').val();
		var srch_item_code = $('#srch_item_code').val();
		var srch_item = $('#srch_item').val();
		var srch_alloy = $('#srch_alloy').val();
		var srch_schedule = $('#srch_schedule').val();
		var srch_size = $('#srch_size').val();
		var srch_width = $('#srch_width').val();
		var srch_length = $('#srch_length').val();
		var srch_heat_no = $('#srch_heat_no').val();
		var srch_lot_no = $('#srch_lot_no').val();
		var srch_invoice_no = $('#srch_invoice_no').val();
		var srch_supplier = $('#srch_supplier').val();
		var srch_supplier_heat_no = $('#srch_supplier_heat_no').val();
		var srch_warehouse = $('#srch_warehouse').val();

		var url = downloadSearchExcelURL+
		'?srch_item_class=' + srch_item_class +
		'&srch_received_date_from=' + srch_received_date_from +
		'&srch_received_date_to=' + srch_received_date_to +
		'&srch_receiving_no=' + srch_receiving_no +
		'&srch_jo_no=' + srch_jo_no +
		'&srch_materials_type=' + srch_materials_type +
		'&srch_product_line=' + srch_product_line +
		'&srch_item_code=' + srch_item_code +
		'&srch_item=' + srch_item +
		'&srch_alloy=' + srch_alloy +
		'&srch_schedule=' + srch_schedule +
		'&srch_size=' + srch_size +
		'&srch_width=' + srch_width +
		'&srch_length=' + srch_length +
		'&srch_heat_no=' + srch_heat_no +
		'&srch_lot_no=' + srch_lot_no +
		'&srch_invoice_no=' + srch_invoice_no +
		'&srch_supplier=' + srch_supplier +
		'&srch_supplier_heat_no=' + srch_supplier_heat_no +
		'&srch_warehouse=' + srch_warehouse;

		var percentage = 10;

		$('#progress').show();
		$('.progress-bar').css('width', '10%');
		$('.progress-bar').attr('aria-valuenow', percentage);

		var req = new XMLHttpRequest();

		req.open("GET", url, true);

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
				var filename = (matches != null && matches[1] ? matches[1] : 'Inventory.xlsx');

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
			else if (req.stastus == 500) {
				console.log(req);
			}
		};
		req.send();
	});

	$('#btn_delete').on('click', function () {
		var ids = [];
		var table = $('#tbl_materials').DataTable();

		for (var x = 0; x < table.context[0].aoData.length; x++) {
			var DataRow = table.context[0].aoData[x];
			if (DataRow.anCells !== null && DataRow.anCells[0].firstChild.checked == true) {
				ids.push(table.context[0].aoData[x].anCells[0].firstChild.value)
			}
		}

		if (ids.length > 0) {
			check_inventory_deletion(ids, function(output) {
				
				if (output.count > 0) {
					checkDeleteTable(output.items);
					$('#modal_check_delete').modal('show');
				} else {

					var sp = 'this';
					var item = 'Item';

					if (ids.length > 1) {
						sp = 'these';
						item = 'Items';
					}

					swal({
						title: "Delete Inventory Item",
						text: "Are you sure to delete " + sp + " Inventory " + item + "?",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#f95454",
						confirmButtonText: "Yes",
						cancelButtonText: "No",
						closeOnConfirm: true,
						closeOnCancel: false
					}, function (isConfirm) {
						if (isConfirm) {
							$('.loadingOverlay').show();

							$.ajax({
								url: inventoryDeleteURL,
								type: 'POST',
								dataType: 'JSON',
								data: {
									_token: token,
									ids: ids
								},
							}).done(function (data, textStatus, xhr) {
								if (data.status == 'success') {
									msg(data.msg, data.status)
								} else {
									msg(data.msg, data.status)
								}

								// getInventory(_with_zero);
								InventoryTable(materialDataTable, { with_zero: _with_zero}); // in here, the loading will close 

								return data.status;
							}).fail(function (xhr, textStatus, errorThrown) {
								ErrorMsg(xhr);
							});
						} else {
							swal("Cancelled", "Your data is safe and not deleted.");
						}
					});
				}
			});
		} else {
			msg('Please select at least 1 Inventory Item.', 'failed');
		}
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

function init() {
	if (permission_access == '2' || permission_access == 2) {
        $('.permission').prop('readonly', true);
        $('.permission-button').prop('disabled', true);
    } else {
        $('.permission').prop('readonly', false);
        $('.permission-button').prop('disabled', false);
    }

	checkAllCheckboxesInTable('#tbl_materials','.check_all_inventories', '.check_item_inventory');

	$('#product_line_div').hide();
	$('#materials_type_div').hide();
}

function getMaterials(state) {
	$('.loadingOverlay-modal').show();

	$.ajax({
		url: materialTypeURL,
		type: 'GET',
		dataType: 'JSON',
		data: { _token: token },
	}).done(function (data, textStatus, xhr) {
		var mat_type = $('#materials_type');
		if (state == 'search') {
			mat_type = $('#srch_materials_type');
		}

		mat_type.select2({
			allowClear: true,
			placeholder: 'Select a Material Type',
			width: 'resolve',
			data: data
		}).val(null).trigger('change');
	}).fail(function (xhr, textStatus, errorThrown) {
		msg(errorThrown, textStatus);
	}).always( function() {
		$('.loadingOverlay-modal').hide();
	});
}

function getProdLine(state) {
	$('.loadingOverlay-modal').show();

	$.ajax({
		url: productLineURL,
		type: 'GET',
		dataType: 'JSON',
		data: { _token: token },
	}).done(function (data, textStatus, xhr) {
		var prod_line = $('#product_line');
		if (state == 'search') {
			prod_line = $('#srch_product_line');
		}

		prod_line.select2({
			allowClear: true,
			placeholder: 'Select a Product Line',
			width: 'resolve',
			data: data
		}).val(null).trigger('change');
	}).fail(function (xhr, textStatus, errorThrown) {
		msg(errorThrown, textStatus);
	}).always(function () {
		$('.loadingOverlay-modal').hide();
	});
}

function getWarehouse(state) {
	$('.loadingOverlay-modal').show();

	$.ajax({
		url: warehouseURL,
		type: 'GET',
		dataType: 'JSON',
		data: { _token: token, state: state },
	}).done(function (data, textStatus, xhr) {
		var warehouse = $('#warehouse');
		
		if (state == 'search') {
			warehouse = $('#srch_warehouse');
		}

		warehouse.select2({
			allowClear: true,
			placeholder: 'Select a Warehouse',
			width: 'resolve',
			data: data
		}).val(null).trigger('change');
	}).fail(function (xhr, textStatus, errorThrown) {
		msg(errorThrown, textStatus);
	}).always(function () {
		$('.loadingOverlay-modal').hide();
	});
}

function getItemCode(code,state,item_class) {
	var hideErr = 'item_code';
	var type = $('#product_line');
	var item_code = $('#item_code')
	//var code = "<option value=''></option>";

	if (item_class == 'material') {
		type = $('#materials_type');
	}

	if (state == 'search') {
		type = $('#srch_product_line');
		item_code = $('#srch_item_code');
		hideErr = 'srch_item_code';

		if (item_class == 'material') {
			type = $('#srch_materials_type');
		}
	}

	hideErrors(hideErr);
	//item_code.html(code);
	

	$('.loadingOverlay-modal').show();
	$.ajax({
		url: GetItemCodeURL,
		type: 'GET',
		dataType: 'JSON',
		data: {
			_token: token,
			type: type.val(),
			item_class: item_class
		},
	}).done(function (data, textStatus, xhr) {
		item_code.empty();

		if (data.length > 0) {
			// $.each(data, function (i, x) {
			// 	code = "<option value='" + x.item_code + "'>" + x.item_code + "</option>";
			// 	item_code.append(code);
			// });

			item_code.select2({
				allowClear: true,
				placeholder: 'Select an Item Code',
				width: 'resolve',
				data: data
			}).val(code).trigger('change.select2');


		} else {
			if (type.val() !== '') {
				if (state == 'search') {
					if (data.length < 1) {
						showErrors({ srch_item_code: ["No Item Code registered to " + type.val()] });
					} else {
						hideErrors('srch_item_code');
					}
					
				} else {
					if (data.length < 1) {
						showErrors({ item_code: ["No Item Code registered to " + type.val()] });
					} else {
						hideErrors('item_code');
					}
				}
			}
		}
			
	}).fail(function (xhr, textStatus, errorThrown) {
		msg(errorThrown, textStatus);
	}).always( function() {
		$('.loadingOverlay-modal').hide();
	});
}

function getItemCodeDetails(state,item_class) {
	$('.loadingOverlay-modal').show();

	var item_code = $('#item_code');
	if (state == 'search') {
		item_code = $('#srch_item_code');
	}

	$.ajax({
		url: getItemCodeDetailsurl,
		type: 'GET',
		dataType: 'JSON',
		data: {
			_token: token,
			item_code: item_code.val(),
			item_class: item_class
		},
	}).done(function (data, textStatus, xhr) {
		if (state == 'search') {
			$('#srch_item').val(data.item);
			$('#srch_alloy').val(data.alloy);
			$('#srch_schedule').val(data.schedule);
			$('#srch_size').val(data.size);
		} else {
			$('#description').val(data.code_description);
			$('#item').val(data.item);
			$('#alloy').val(data.alloy);
			$('#schedule').val(data.schedule);
			$('#size').val(data.size);

			if (data.hasOwnProperty('finish_weight')) {
				$('#finish_weight').val(data.finish_weight);
			}

			$('#qty_weight').val(0);
			$('#qty_pcs').val(0);
		}
		//else {
		// 	msg("Material Code is not registered");
		// }

	}).fail(function (xhr, textStatus, errorThrown) {
		msg(errorThrown, textStatus);
	}).always(function () {
		$('.loadingOverlay-modal').hide();
	});
}

function GetMateriialsNotExisting(arr) {
	$('#modal_material_not_existing').modal('show');
	$('#tbl_material_not_existing').dataTable().fnClearTable();
	$('#tbl_material_not_existing').dataTable().fnDestroy();
	$('#tbl_material_not_existing').dataTable({
		data: arr,
		bLengthChange: false,
		paging: true,
		scrollX: true,
		order: [[5,'asc']],
		columns: [
			{ data: 'item_class', name: 'item_class' },
			{ data: 'receive_jo_no', name: 'receive_jo_no' },
			{ data: 'item_type_line', name: 'item_type_line' },
			{ data: 'item_code', name: 'item_code' },
			{ data: 'qty_weight', name: 'qty_weight' },
			{ data: 'qty_pcs', name: 'qty_pcs' },
			{ data: 'heat_no', name: 'heat_no' },
			{ data: 'lot_no', name: 'lot_no' },
			{ data: 'invoice_no', name: 'invoice_no' },
			{ data: 'received_date', name: 'received_date' },
			{ data: 'supplier', name: 'supplier' },
		]
	});
}

function getInventory(with_zero) {
	$('.loadingOverlay').show();
	$.ajax({
		url: materialDataTable,
		type: 'GET',
		dataType: 'JSON',
		data: {
			with_zero: with_zero
		},
	}).done(function (data, textStatus, xhr) {
		InventoryTable(data);
	}).fail(function (xhr, textStatus, errorThrown) {
		console.log("error");
	}).always( function() {
		$('.loadingOverlay').hide();
	});
}

function clear() {
	$('.clear').val('');
	$('#materials_type').val(null).trigger('change.select2');
	$('#product_line').val(null).trigger('change.select2');
	$('#item_code').val(null).trigger('change.select2');
	$('#warehouse').val(null).trigger('change.select2');
}

function InventoryTable(ajax_url,data_object) {
	$('#tbl_materials').dataTable().fnClearTable();
	$('#tbl_materials').dataTable().fnDestroy();
	$('#tbl_materials').dataTable({
		ajax: {
			url: ajax_url, 
			data: data_object
		},
		stateSave: true,
		order: [[27,'desc']],
		scrollX: true,
		processing: true,
		deferRender: true,
		//serverSide: true,
		scrollCollapse: true,
		columns: [
			{
				data: function (data) {
					return "<input type='checkbox' class='table-checkbox check_item_inventory' data-id= '" + data.id + "' value='" + data.id + "'/>";
				}, searchable: false, orderable: false
			},
			{
				data: function (data) {
					return "<button type='button' name='edit-mainEdit' class='btn btn-sm btn-primary edit-mainEdit'" +
						"id='editinventory'" +
						"data-id= '" + data.id + "' " +
						"data-item_class='" + data.item_class + "' " +
						"data-receive_jo_no='" + data.receive_jo_no + "' " +
						"data-item_type_line='" + data.item_type_line + "' " +
						"data-item_code='" + data.item_code + "'" +
						"data-description='" + data.description + "'" +
						"data-item='" + data.item + "'" +
						"data-alloy='" + data.alloy + "'" +
						"data-schedule='" + data.schedule + "'" +
						"data-size='" + data.size + "'" +
						"data-std_weight='" + data.std_weight + "'" +
						"data-std_weight_received='" + data.std_weight_received + "'" +
						"data-finish_weight='" + data.finish_weight + "'" +
						"data-qty_weight='" + data.qty_weight + "'" +
						"data-qty_pcs='" + data.qty_pcs + "'" +
						"data-current_stock='" + data.current_stock + "'" +
						"data-heat_no='" + data.heat_no + "' " +
						"data-lot_no='" + data.lot_no + "' " +
						"data-invoice_no='" + data.invoice_no + "'" +
						"data-received_date='" + data.received_date + "'" +
						"data-width='" + data.width + "' " +
						"data-warehouse='" + data.warehouse + "' " +
						"data-length='" + data.length + "' " +
						"data-supplier_heat_no='" + data.supplier_heat_no + "' " +
						"data-material_used='" + data.material_used + "' " +
						"data-updated_at='" + data.updated_at + "' " +
						"data-supplier='" + data.supplier + "'>" +
						"<i class='fa fa-edit'></i> " +
						"</button>";
				}, searchable: false, orderable: false
			},
			{ data: 'item_class' },
			
			{ data: 'item_type_line' },
			{ data: 'item_code' },

			{ data: 'length' },
			{
				data: function (x) {
					return (x.std_weight == null) ? '' : parseFloat(x.std_weight).toFixed(2);
				}, className: "text-right"
			},
			{
				data: 'std_weight_received', className: "text-right"
			},
			{
				data: function (x) {
					return (x.finish_weight == null) ? '' : parseFloat(x.finish_weight).toFixed(2);
				}, className: "text-right"
			},
			{
				data: function (x) {
					return (x.qty_weight == null) ? '' : formatNumber(parseFloat(x.qty_weight).toFixed(2));
				}, className: "text-right"
			},
			{ data: 'qty_pcs', className: "text-right" },
			{ data: 'current_stock', className: "text-right" },
			{ data: 'heat_no' },
			{ data: 'lot_no' },
			{ data: 'receive_jo_no' },
			{ data: 'description' },
			{ data: 'warehouse' },
			{ data: 'item' },
			{ data: 'alloy' },
			{ data: 'schedule' },
			{ data: 'size' },
			{ data: 'width' },
			{ data: 'invoice_no' },
			{ data: 'received_date' },
			{ data: 'supplier' },
			{ data: 'supplier_heat_no' },
			{ data: 'material_used' },
			{ data: 'nickname' },
			{ data: 'updated_at' }
		],
		createdRow: function (row, data, dataIndex) {
			if (data.description == "N/A") {
				$(row).css('background-color', '#ff6266');
				$(row).css('color', '#000000');
			}

			if (data.item_class == "RAW MATERIAL") {
				$(row).css('background-color', 'rgb(121 204 241)'); // BLUE
				$(row).css('color', '#000000');
			}

			if (data.item_class == "CRUDE") {
				$(row).css('background-color', '#c6bdff'); // NAVY
				$(row).css('color', '#000000');
			}

			if (data.item_class == "FINISHED") {
				$(row).css('background-color', 'rgb(139 241 191)'); // GREEN
				$(row).css('color', '#000000');
			}

			if (data.my_warehouse == 0) {
				var dataRow = $(row);
				var checkbox = $(dataRow[0].cells[0].firstChild);
				var button = $(dataRow[0].cells[1].firstChild);

				checkbox.prop('disabled', true);
				button.prop('disabled', true);
			}
		},
		fnDrawCallBack: function () {
			$('.check_all_inventories').prop('checked', false);
		},
		initComplete: function() {
			$('.loadingOverlay').hide();
		}
	});
}

function sameMaterialTable(arr) {
	$('#tbl_same_material').dataTable().fnClearTable();
	$('#tbl_same_material').dataTable().fnDestroy();
	$('#tbl_same_material').dataTable({
		data: arr,
		order: [[0, 'asc']],
		scrollX: true,
		columns: [
			{ data: 'receiving_no' },
			{ data: 'materials_type' },
			{ data: 'product_line' },
			{ data: 'item_code' },
			{ data: 'warehouse' },
			{ data: 'qty_weight' },
			{ data: 'qty_pcs' },
			{ data: 'heat_no' },
			{ data: 'length' },
			{ data: 'invoice_no' },
			{ data: 'received_date' },
			{ data: 'supplier' },
		]
	});
}

function check_inventory_deletion(ids, handleData) {
	$('.loadingOverlay').show();

	$.ajax({
		url: checkInventoryDeletionURL,
		type: 'GET',
		dataType: 'JSON',
		data: { ids: ids }
	}).done(function (data, textStatus, xhr) {
		handleData(data);
	}).fail(function (xhr, textStatus, errorThrown) {
		ErrorMsg(xhr)
	}).always(function () {
		$('.loadingOverlay').hide();
	});
}

function checkDeleteTable(arr) {
	$('#tbl_check_delete').dataTable().fnClearTable();
	$('#tbl_check_delete').dataTable().fnDestroy();
	$('#tbl_check_delete').dataTable({
		data: arr,
		order: [[0, 'asc']],
		scrollX: true,
		columns: [
			{ data: 'item_class' },
			{ data: 'item_code' },
			{ data: 'description' },
			{ data: 'lot_heat_no' },
			{ data: 'length' },
			{ data: 'warehouse' },
			{ data: 'issued_qty' },
			{ data: 'type_line' }
		]
	});
}