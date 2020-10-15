var _with_zero = 0;

$(function () {
	getMaterials('');
	getInventory(_with_zero);
	init();

	//$('#srch_received_date').daterangepicker();

	$(document).on('shown.bs.modal', function () {
		$($.fn.dataTable.tables(true)).DataTable()
			.columns.adjust();
	});


	$("#materials_type").on('change', function () {
		getMaterialCode('','');
	});

	$("#srch_materials_type").on('change', function () {
		getMaterialCode('', 'search');
	});

	$("#materials_code").on('change', function () {
		GetMaterialCodeDetails('');
	});

	$("#srch_materials_code").on('change', function () {
		GetMaterialCodeDetails('search');
	});

	$('.custom-file-input').on('change', function () {
		let fileName = $(this).val().split('\\').pop();
		$(this).next('.custom-file-label').addClass("selected").html(fileName);
	});

	$('#btn_add').on('click', function () {
		var d = new Date();
		var month = d.getMonth() + 1;
		var day = d.getDate();
		var date = d.getFullYear() + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day
		$('#received_date').val(date);
		$('#qty_weight').val(0);
		$('#qty_pcs').val(0);
		$('#modal_material_inventory').modal('show');
		clear();
		getMaterialCode('');
	});

	$('#btn_zero').on('click', function () {
		if (_with_zero == 0) {
			_with_zero = 1;
			getInventory(_with_zero);

			$(this).removeClass('bg-blue');
			$(this).addClass('bg-red');
			$(this).html('Exclude 0 quantity');
		} else {
			_with_zero = 0;
			getInventory(_with_zero);
			$(this).removeClass('bg-red');
			$(this).addClass('bg-blue');
			$(this).html('Include 0 quantity');
		}
	});

	$('#tbl_materials_body').on('click', '.edit-mainEdit', function (e) {
		e.preventDefault();
		$('#material_id').val($(this).attr('data-id'));
		$('#receiving_no').val($(this).attr('data-receiving_no'));
		$('#materials_type').val($(this).attr('data-materials_type'));

		if ($('#materials_type').val() != '') {
			getMaterialCode($(this).attr('data-materials_code'),'');
		}

		$('#description').val($(this).attr('data-description'));
		$('#item').val($(this).attr('data-item'));
		$('#alloy').val($(this).attr('data-alloy'));
		$('#schedule').val($(this).attr('data-schedule'));
		$('#size').val($(this).attr('data-size'));
		$('#width').val($(this).attr('data-width'));
		// $('#thickness').val($(this).attr('data-thickness'));
		$('#length').val($(this).attr('data-length'));
		$('#qty_weight').val($(this).attr('data-qty_weight'));
		$('#qty_pcs').val($(this).attr('data-qty_pcs'));
		$('#heat_no').val($(this).attr('data-heat_no'));
		$('#invoice_no').val($(this).attr('data-invoice_no'));
		$('#received_date').val($(this).attr('data-received_date'));
		$('#supplier').val($(this).attr('data-supplier'));
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
								getInventory(_with_zero);
								var not_registedred = return_data.Material;
								if (not_registedred.length > 0) {
									GetMateriialsNotExisting(not_registedred);
								}
							}).fail( function(xhr,textStatus,errorThrown) {
								$('.loadingOverlay').hide();
								var response = jQuery.parseJSON(xhr.responseText);
								ErrorMsg(response);
							}).always(function () {
								$('.loadingOverlay').hide();
							});
							
							// $.ajax({
							// 	url: uploadInventory,
							// 	type: 'POST',
							// 	data: formData,
							// 	mimeType: "multipart/form-data",
							// 	contentType: false,
							// 	cache: false,
							// 	processData: false,
							// 	success: function (returnData) {
							// 		$('.loadingOverlay').hide();
							// 		var return_data = jQuery.parseJSON(returnData);

							// 		msg(return_data.msg, return_data.status);
							// 		document.getElementById('file_inventory_label').innerHTML = fileN;
							// 		getInventory(_with_zero);
							// 		var not_registedred = return_data.Material;
							// 		if (not_registedred.length > 0) {
							// 			GetMateriialsNotExisting(not_registedred);
							// 		}

							// 	}
							// });
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
							msg("Please maintain data as 1 sheet only.", "warning");
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
						var response = jQuery.parseJSON(xhr.responseText);
						ErrorMsg(response);
					}).always( function() {
						//$('.loadingOverlay').hide();
					});
					

					// $.ajax({
					// 	url: checkfile,
					// 	type: 'POST',
					// 	mimeType: "multipart/form-data",
					// 	contentType: false,
					// 	cache: false,
					// 	processData: false,
					// 	data: formData,
					// 	success: function (returns) {
					// 		var return_datas = jQuery.parseJSON(returns);
					// 		if (return_datas.status == "success") {
					// 			$.ajax({
					// 				url: uploadInventory,
					// 				type: 'POST',
					// 				data: formData,
					// 				mimeType: "multipart/form-data",
					// 				contentType: false,
					// 				cache: false,
					// 				processData: false,
					// 				success: function (returnData) {
					// 					$('.loadingOverlay').hide();
					// 					var return_data = jQuery.parseJSON(returnData);

					// 					msg(return_data.msg, return_data.status);
					// 					document.getElementById('file_inventory_label').innerHTML = fileN;
					// 					getInventory(_with_zero);
					// 					var not_registedred = return_data.Material;
					// 					if (not_registedred.length > 0) {
					// 						GetMateriialsNotExisting(not_registedred);
					// 					}

					// 				}
					// 			});
					// 		}
					// 		else if (return_datas.status == "validateRequired") {
					// 			$('.loadingOverlay').hide();
					// 			msg("Fill up correctly the record in line " + return_datas.line, "warning");
					// 			document.getElementById('file_inventory_label').innerHTML = "Select file...";
					// 		}
					// 		else if (return_datas.status == "heatnumber error") {
					// 			$('.loadingOverlay').hide();
					// 			msg(return_datas.msg, "warning");
					// 			document.getElementById('file_inventory_label').innerHTML = "Select file...";
					// 		}
					// 		else if (return_datas.status == "not num") {
					// 			$('.loadingOverlay').hide();
					// 			msg("Invalid input of Quantity", "warning");
					// 			document.getElementById('file_inventory_label').innerHTML = "Select file...";
					// 		}
					// 		else if (return_datas.status == "failed") {
					// 			$('.loadingOverlay').hide();
					// 			console.log(return_datas.fields);
					// 			msg("Please maintain data as 1 sheet only.", "warning");
					// 			document.getElementById('file_inventory_label').innerHTML = "Select file...";
					// 		}
					// 		else {
					// 			$('.loadingOverlay').hide();
					// 			msg("Upload failed", "warning");
					// 			document.getElementById('file_inventory_label').innerHTML = "Select file...";
					// 		}
					// 	},
					// 	statusCode: {
					// 		500: function(data) {
					// 			$('.loadingOverlay').hide();
					// 			console.log(data);
					// 			//msg('','error');
					// 		}
					// 	}
					// 	// error: function() {
					// 	// 	$('.loadingOverlay').hide();
					// 	// }
					// });
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
				getInventory(_with_zero);
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
		}
	});

	$('#btn_excel').on('click', function () {
		window.location.href = downloadNonexistingURL;
	});

	$('#btn_download_format').on('click', function () {
		window.location.href = downloadFormatURL;
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
		getMaterials('search');
		$('#modal_material_search').modal('show');
	});

	$('#srch_received_date_from').on('change', function() {
		var selected_date = new Date($(this).val()).toISOString().split('T')[0];
		document.getElementsByName("srch_received_date_to")[0].setAttribute('min', selected_date);
	});

	$("#frm_search").on('submit', function (e) {
		e.preventDefault();
		$('.loadingOverlay-modal').show();

		$.ajax({
			url: $(this).attr('action'),
			type: 'GET',
			dataType: 'JSON',
			data: $(this).serialize(),
		}).done(function (data, textStatus, xhr) {
			//msg(data.msg, data.status);

			InventoryTable(data);
			//$('#modal_material_search').modal('hide');
			
		}).fail(function (xhr, textStatus, errorThrown) {
			var errors = xhr.responseJSON.errors;

			console.log(errors);
			showErrors(errors);
		}).always(function () {
			$('.loadingOverlay-modal').hide();
		});
	});

	$('#btn_search_excel').on('click', function() {
		var srch_received_date_from = $('#srch_received_date_from').val();
		var srch_received_date_to = $('#srch_received_date_to').val();
		var srch_receiving_no = $('#srch_receiving_no').val();
		var srch_materials_type = $('#srch_materials_type').val();
		var srch_materials_code = $('#srch_materials_code').val();
		var srch_item = $('#srch_item').val();
		var srch_alloy = $('#srch_alloy').val();
		var srch_schedule = $('#srch_schedule').val();
		var srch_size = $('#srch_size').val();
		var srch_width = $('#srch_width').val();
		var srch_length = $('#srch_length').val();
		var srch_heat_no = $('#srch_heat_no').val();
		var srch_invoice_no = $('#srch_invoice_no').val();
		var srch_supplier = $('#srch_supplier').val();
		var srch_supplier_heat_no = $('#srch_supplier_heat_no').val();

		var url = downloadSearchExcelURL+
		"?srch_received_date_from=" + srch_received_date_from +
		"&srch_received_date_to=" + srch_received_date_to +
		"&srch_receiving_no=" + srch_receiving_no +
		"&srch_materials_type=" + srch_materials_type +
		"&srch_materials_code=" + srch_materials_code +
		"&srch_item=" + srch_item +
		"&srch_alloy=" + srch_alloy +
		"&srch_schedule=" + srch_schedule +
		"&srch_size=" + srch_size +
		"&srch_width=" + srch_width +
		"&srch_length=" + srch_length +
		"&srch_heat_no=" + srch_heat_no +
		"&srch_invoice_no=" + srch_invoice_no +
		"&srch_supplier=" + srch_supplier +
		"&srch_supplier_heat_no=" + srch_supplier_heat_no;

		window.location.href = url;
	});
});

function init() {
	check_permission(code_permission, function(output) {
		if (output == 1) {}
	});
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
		var code = '<option></option>';
		mat_type.html(code);

		$.each(data.type, function (i, x) {
			code = '<option value="' + x.material_type + '">' + x.material_type + '</option>';
			mat_type.append(code);
		});
	}).fail(function (xhr, textStatus, errorThrown) {
		msg(errorThrown, textStatus);
	}).always( function() {
		$('.loadingOverlay-modal').hide();
	});
}

function getMaterialCode(mat_code,state) {

	
	var hideErr = 'materials_code';
	var material_type = $('#materials_type');
	var material_code = $('#materials_code')
	var code = "<option value=''></option>";
	

	if (state == 'search') {
		material_type = $('#srch_materials_type');
		material_code = $('#srch_materials_code');
		hideErr = 'srch_materials_code';
	}
	hideErrors(hideErr);
	material_code.html(code);
	

	$('.loadingOverlay-modal').show();
	$.ajax({
		url: GetMaterialCode,
		type: 'GET',
		dataType: 'JSON',
		data: {
			_token: token,
			mat_type: material_type.val()
		},
	}).done(function (data, textStatus, xhr) {
		if (data.length > 0) {
			$.each(data, function (i, x) {
				code = "<option value='" + x.material_code + "'>" + x.material_code + "</option>";
				material_code.append(code);
			});

			material_code.val(mat_code);
		} else {
			if (material_type.val() !== '') {
				showErrors({materials_code:["No Materials registered to " + material_type.val()]});
			}
		}
			
	}).fail(function (xhr, textStatus, errorThrown) {
		msg(errorThrown, textStatus);
	}).always( function() {
		$('.loadingOverlay-modal').hide();
	});
}

function GetMaterialCodeDetails(state) {
	$('.loadingOverlay-modal').show();

	var material_code = $('#materials_code');
	if (state == 'search') {
		material_code = $('#srch_materials_code');
	}

	$.ajax({
		url: GetMaterialCodeDetailsurl,
		type: 'GET',
		dataType: 'JSON',
		data: {
			_token: token,
			mat_code: material_code.val()
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
		order: [[5,'asc']],
		columns: [
			{ data: 'materials_type', name: 'materials_type' },
			{ data: 'materials_code', name: 'materials_code' },
			{ data: 'qty_weight', name: 'qty_weight' },
			{ data: 'qty_pcs', name: 'qty_pcs' },
			{ data: 'heat_no', name: 'heat_no' },
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
}

function InventoryTable(arr) {
	$('#tbl_materials').dataTable().fnClearTable();
	$('#tbl_materials').dataTable().fnDestroy();
	$('#tbl_materials').dataTable({
		data: arr,
		order: [[14,'asc']],
		scrollX: true,
		columns: [
			{
				data: function (data) {
					return "<button type='button' name='edit-mainEdit' class='btn btn-sm btn-primary edit-mainEdit'" +
						"id='editinventory'" +
						"data-id= '" + data.id + "' " +
						"data-receiving_no='" + data.receiving_no + "' " +
						"data-materials_type='" + data.materials_type + "' " +
						"data-materials_code='" + data.materials_code + "'" +
						"data-description='" + data.description + "'" +
						"data-item='" + data.item + "'" +
						"data-alloy='" + data.alloy + "'" +
						"data-schedule='" + data.schedule + "'" +
						"data-size='" + data.size + "'" +
						"data-qty_weight='" + data.qty_weight + "'" +
						"data-qty_pcs='" + data.qty_pcs + "'" +
						"data-current_stock='" + data.current_stock + "'" +
						"data-heat_no='" + data.heat_no + "' " +
						"data-invoice_no='" + data.invoice_no + "'" +
						"data-received_date='" + data.received_date + "'" +
						"data-width='" + data.width + "' " +
						// "data-thickness='" + data.thickness + "' " +
						"data-length='" + data.length + "' " +
						"data-supplier_heat_no='" + data.supplier_heat_no + "' " +
						"data-supplier='" + data.supplier + "'>" +
						"<i class='fa fa-edit'></i> " +
						"</button>";
				}, searchable: false, orderable: false
			},
			{ data: 'receiving_no' },
			{ data: 'materials_type' },
			{ data: 'materials_code' },
			{ data: 'description' },
			{ data: 'item' },
			{ data: 'alloy' },
			{ data: 'schedule' },
			{ data: 'size' },
			{ data: 'width' },
			// { data: 'thickness' },
			{ data: 'length' },
			// {
			// 	data: function (data) {
			// 		var wxl = $.trim(data.wxl);
			// 		var w = $.trim(data.width);
			// 		var l = $.trim(data.length);
			// 		if (w != "" && l == "") {
			// 			return data.width
			// 		} else if (w == "" && l != "") {
			// 			return data.length
			// 		} else {
			// 			if (wxl == "x") {
			// 				return "";
			// 			} else {
			// 				return data.wxl;
			// 			}
			// 		}
			// 	}
			// },
			{ data: 'qty_weight' },
			{ data: 'qty_pcs' },
			{ data: 'current_stock' },
			{ data: 'heat_no' },
			{ data: 'invoice_no' },
			{ data: 'received_date' },
			{ data: 'supplier' },
			{ data: 'supplier_heat_no' }
		],
		createdRow: function (row, data, dataIndex) {
			if (data.description == "N/A") {
				$(row).css('background-color', '#ff6266');
				$(row).css('color', '#fff');
			}
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
			{ data: 'materials_code' },
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