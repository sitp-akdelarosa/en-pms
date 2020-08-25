var _with_zero = 0;

$(function () {
	getMaterials();
	getInventory(_with_zero);
	init();


	$("#materials_type").on('change', function () {
		getMaterialCode();
	});

	$("#materials_code").on('change', function () {
		GetMaterialCodeDetails();
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
		$('#modal_material_inventory').modal('show');
		clear();
		getMaterialCode();
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
		$('#materials_type').val($(this).attr('data-materials_type'));

		if ($('#materials_type').val() != '') {
			getMaterialCode($(this).attr('data-materials_code'));
		}

		$('#description').val($(this).attr('data-description'));
		$('#item').val($(this).attr('data-item'));
		$('#alloy').val($(this).attr('data-alloy'));
		$('#schedule').val($(this).attr('data-schedule'));
		$('#size').val($(this).attr('data-size'));
		$('#width').val($(this).attr('data-width'));
		$('#length').val($(this).attr('data-length'));
		$('#quantity').val($(this).attr('data-quantity'));
		$('#uom').val($(this).attr('data-uom'));
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
									GetMateriialTypeNotExisting(not_registedred);
								}
							}).fail( function(xhr,textStatus,errorThrown) {
								$('.loadingOverlay').hide();
								var responseError = jQuery.parseJSON(xhr.responseText);
								msg("Message: "+responseError.message+"\n"+
									"Line: "+responseError.line+"\n"+
									"File: "+responseError.file, "error");
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
							// 			GetMateriialTypeNotExisting(not_registedred);
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
							console.log(return_datas.fields);
							msg("Please maintain data as 1 sheet only.", "warning");
							document.getElementById('file_inventory_label').innerHTML = "Select file...";
						}
						else {
							$('.loadingOverlay').hide();
							msg("Upload failed", "warning");
							document.getElementById('file_inventory_label').innerHTML = "Select file...";
						}
					}).fail( function(xhr,textStatus,errorThrown) {
						var responseError = jQuery.parseJSON(xhr.responseText);
						msg("Message: "+responseError.message+"\n"+
							"Line: "+responseError.line+"\n"+
							"File: "+responseError.file, "error");
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
					// 						GetMateriialTypeNotExisting(not_registedred);
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
			$.ajax({
				url: $(this).attr('action'),
				type: 'POST',
				dataType: 'JSON',
				data: $(this).serialize(),
			}).done(function (data, textStatus, xhr) {
				msg(data.msg, data.status);
				getInventory(_with_zero);
			}).fail(function (xhr, textStatus, errorThrown) {
				var errors = xhr.responseJSON.errors;

				console.log(errors);
				showErrors(errors);
			});
		}
	});

	$('#quantity').on('change', function() {
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
			GetMateriialTypeNotExisting(data);
		}).fail(function (xhr, textStatus, errorThrown) {
			msg('Unregistered Products: ' + errorThrown);
		});

	});
});

function init() {
	check_permission(code_permission, function(output) {
		if (output == 1) {}
	});
}


function getMaterials() {
	$.ajax({
		url: materialTypeURL,
		type: 'GET',
		dataType: 'JSON',
		data: { _token: token },
	}).done(function (data, textStatus, xhr) {
		var code = '<option></option>';
		$('#materials_type').html(code);

		$.each(data.type, function (i, x) {
			code = '<option value="' + x.material_type + '">' + x.material_type + '</option>';
			$('#materials_type').append(code);
		});
	}).fail(function (xhr, textStatus, errorThrown) {
		msg(errorThrown, textStatus);
	});
}

function getMaterialCode(mat_code) {
	var code = "<option value=''></option>";
	$('#materials_code').html(code);

	hideErrors('materials_code');

	var material_type = $('#materials_type').val();
	$.ajax({
		url: GetMaterialCode,
		type: 'GET',
		dataType: 'JSON',
		data: {
			_token: token,
			mat_type: material_type
		},
	}).done(function (data, textStatus, xhr) {
		if (data.length > 0) {
			$.each(data.code, function (i, x) {
				code = "<option value='" + x.material_code + "'>" + x.material_code + "</option>";
				$('#materials_code').append(code);
			});

			$('#materials_code').val(mat_code);
		} else {
			if (material_type !== '') {
				showErrors({materials_code:["No Materials registered to " + material_type]});
			}
		}
			
	}).fail(function (xhr, textStatus, errorThrown) {
		msg(errorThrown, textStatus);
	});
}

function GetMaterialCodeDetails() {
	$.ajax({
		url: GetMaterialCodeDetailsurl,
		type: 'GET',
		dataType: 'JSON',
		data: {
			_token: token,
			mat_code: $('#materials_code').val()
		},
	}).done(function (data, textStatus, xhr) {
		// if (data.length > 0) {
		$('#description').val(data.code_description);
		$('#item').val(data.item);
		$('#alloy').val(data.alloy);
		$('#schedule').val(data.schedule);
		$('#size').val(data.size);
		// } else {
		// 	msg("Material Code is not registered");
		// }

	}).fail(function (xhr, textStatus, errorThrown) {
		msg(errorThrown, textStatus);
	});
}

function GetMateriialTypeNotExisting(arr) {
	$('#modal_material_not_existing').modal('show');
	$('#tbl_material_not_existing').dataTable().fnClearTable();
	$('#tbl_material_not_existing').dataTable().fnDestroy();
	$('#tbl_material_not_existing').dataTable({
		data: arr,
		bLengthChange: false,
		paging: true,
		order: [[5,'asc']],
		columns: [
			{ data: 'materials_code', name: 'materials_code' },
			{ data: 'quantity', name: 'quantity' },
			{ data: 'uom', name: 'uom' },
			{ data: 'heat_no', name: 'heat_no' },
			{ data: 'invoice_no', name: 'invoice_no' },
			{ data: 'received_date', name: 'received_date' },
			{ data: 'supplier', name: 'supplier' },
		]
	});
}

function getInventory(with_zero) {
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
		columns: [
			{
				data: function (data) {
					return "<button type='button' name='edit-mainEdit' class='btn btn-sm btn-primary edit-mainEdit'" +
						"id='editinventory'" +
						"data-id= '" + data.id + "' " +
						"data-materials_type='" + data.materials_type + "' " +
						"data-materials_code='" + data.materials_code + "'" +
						"data-description='" + data.description + "'" +
						"data-item='" + data.item + "'" +
						"data-alloy='" + data.alloy + "'" +
						"data-schedule='" + data.schedule + "'" +
						"data-size='" + data.size + "'" +
						"data-quantity='" + data.quantity + "'" +
						"data-uom='" + data.uom + "'" +
						"data-heat_no='" + data.heat_no + "' " +
						"data-invoice_no='" + data.invoice_no + "'" +
						"data-received_date='" + data.received_date + "'" +
						"data-width='" + data.width + "' " +
						"data-length='" + data.length + "' " +
						"data-supplier_heat_no='" + data.supplier_heat_no + "' " +
						"data-supplier='" + data.supplier + "'>" +
						"<i class='fa fa-edit'></i> " +
						"</button>";
				}, searchable: false, orderable: false
			},
			{ data: 'materials_type' },
			{ data: 'materials_code' },
			{ data: 'description' },
			{ data: 'item' },
			{ data: 'alloy' },
			{ data: 'schedule' },
			{ data: 'size' },
			{ data: 'width' },
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
			{ data: 'quantity' },
			{ data: 'uom' },
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
		}
	});
}