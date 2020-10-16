var for_overwrite_arr = [];
var primaryOverwrite = [];

$(function () {
    initializePage();
    getUploadedProducts();

    init();

    $('#btn_yes').on('click', function() {
    	var table = $("#tbl_overwrite");
    	for_overwrite_arr = [];
    	$.each(primaryOverwrite, function(i, x) {
			for_overwrite_arr.push({
				id: x.id,
	            sc_no: x.sc_no,
	            prod_code: x.prod_code,
	            description: x.description,
	            quantity: x.quantity,
	            po: x.po,
			});
		});

    	var indexremove = [];
    	for(var x=0;x<table[0].children[1].children.length;x++)
    	{
    		if(table[0].children[1].children[x].cells[0].children[0].checked == false)
    		{
	    		indexremove.push(x);
    		}
    	}
    	if(indexremove.length > 0){
			for (var i = indexremove.length -1; i >= 0; i--){
			   for_overwrite_arr.splice(indexremove[i],1);
			}
		}
		if(for_overwrite_arr.length > 0){
	    	$.ajax({
	    		url: overwriteURL,
	    		type: 'POST',
	    		dataType: 'JSON',
	    		data: {_token: token, data: for_overwrite_arr},
	    		success:function(data){
	    				msg('Successfully overwritten','success');
	    				$('#modal_upload_orders').modal('hide');
	    				location.reload();
	    		}
	    	});
	    }
	    else{
	    	msg("Please select an item.","warning");
	    }
    });

    $('#btn_excel').on('click', function() {
    	window.location.href = downloadNonexistingURL;
    });

    $('#btn_check_unregistered').on('click', function(event) {
    	$.ajax({
    		url: getNonexistingURL,
    		type: 'GET',
    		dataType: 'JSON',
    	}).done(function(data,textStatus,xhr) {
    		notRegistered(data);
    	}).fail(function(xhr,textStatus,errorThrown) {
    		msg('Unregistered Products: '+ errorThrown);
    	});
    	
	});
	
	$('#btn_filter_search').on('click', function () {
		$('.srch-clear').val('');
		$('#modal_order_search').modal('show');
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
			uploadedProductsTable(data);

		}).fail(function (xhr, textStatus, errorThrown) {
			var errors = xhr.responseJSON.errors;

			console.log(errors);
			showErrors(errors);
		}).always(function () {
			$('.loadingOverlay-modal').hide();
		});
	});

	$('#btn_search_excel').on('click', function () {
		window.location.href = excelSearchFilterURL + "?srch_date_upload_from=" + $('#srch_date_upload_from').val() +
			"&srch_date_upload_to=" + $('#srch_date_upload_to').val() +
			"&srch_sc_no=" + $('#srch_sc_no').val() +
			"&srch_prod_code=" + $('#srch_prod_code').val() +
			"&srch_description=" + $('#srch_description').val() +
			"&srch_po=" + $('#srch_po').val();
	});
});

function init() {
    check_permission(code_permission, function(output) {
        if (output == 1) {}
    });
}

function initializePage(){
	$("#btn_delete").on('click', removeUploadByID);

	$('#frm_upload_order').on('submit', function(e) {
		var formObj = $('#frm_upload_order');
		var formURL = formObj.attr("action");
		var formData = new FormData(this);
		var fileName = $("#fileupload").val();
		var ext = fileName.split('.').pop();
		var pros = $('#fileupload').val().replace("C:\fakepath", "");
	    var fileN = pros.substring(12, pros.length);
		e.preventDefault();
		if ($("#fileupload").val() == '') {
			msg("No File","failed"); 
		} else {
			if (fileName != ''){
				if (ext == 'xls' || ext == 'xlsx' || ext == 'XLS' || ext == 'XLSX' || ext == 'Xls') {
					$('.myprogress').css('width', '0%');
					$('#progress-msg').html('Uploading in progress...');
					var percent = 0;

					$('.loadingOverlay').show();
					
					$.ajax({
						url: checkfile,
						type: 'POST',
						mimeType:"multipart/form-data",
						contentType: false,
						cache: false,
						processData:false,
						data: formData,
						success:function(returns){
							
							var return_datas = jQuery.parseJSON(returns);

							if (return_datas.status == "warning") {
								$('.loadingOverlay').hide();
								msg(return_datas.msg,"warning");
							} else {

								if (return_datas["0"].scno != null && return_datas["0"].productcode != null &&
									return_datas["0"].quantity != null && return_datas["0"].pono != null) {
										$.ajax({
											url: formURL,
											type: 'POST',
											data:  formData,
											mimeType:"multipart/form-data",
											contentType: false,
											cache: false,
											processData:false,
											success:function(returnData)
											{
												$('.loadingOverlay').hide();
												var return_data = jQuery.parseJSON(returnData);
												if (return_data.status == "success") {
													if(return_data.countAddedRow == 0){
														msg('No record of data added','failed'); 
													}else{
														msg(return_data.msg,return_data.status); 	
													}

													primaryOverwrite = return_data.for_overwrite;
													document.getElementById('filenamess').innerHTML = fileN;
													getUploadedProducts();


													var not_registedred = return_data.not_registered;
													if (not_registedred.length > 0) {
														notRegistered(not_registedred);
													}

													var Schedule = return_data.Schedule;
													if (Schedule.length > 0) {
														scheduletable(Schedule);
													}
													
													var for_overwrite = return_data.for_overwrite
													if (for_overwrite.length > 0) {
														overwrite(for_overwrite);
													}

												} else {
													msg(returnData.msg,"warning"); 
													document.getElementById('filenamess').innerHTML = "Select file...";
												}
											},
											error:function(returnData)
											{
												$('.loadingOverlay').hide();
												var return_data = jQuery.parseJSON(returnData.responseText);
											}
										});
								} else {
									$('.loadingOverlay').hide();
									msg("File is not applicable.","warning");
									document.getElementById('filenamess').innerHTML = "Select file...";
								}
							}

						},
					});
				} else {
					$('.loadingOverlay').hide();
					msg("File Format not supported.","warning");
				}
			}
		}
	});

	$('.custom-file-input').on('change', function() {
	   let fileName = $(this).val().split('\\').pop();
	   $(this).next('.custom-file-label').addClass("selected").html(fileName);
	   
	});
}

function removeUploadByID(){
	var tray = [];
	$(".check_item:checked").each(function () {
		tray.push($(this).val());
	});

	var traycount =tray.length;

	$.ajax({
		url: deleteselected,
		method: 'get',
		data:  { 
				tray : tray, 
				traycount : traycount
			},
		success:function(){
			msg("Item Deleted","success"); 
			getUploadedProducts();
		},
	});
}

function overwrite(for_overwrite) {
	var ii = 0;
	var singular = "This item will be overwritten.";
	var plural = "These items will be overwritten.";

	if (for_overwrite.length > 1) {
		$('#overwrite_msg').html(singular);
	} else {
		$('#overwrite_msg').html(plural);
	}

	var tbl_overwrite_body = '';
	$('#tbl_overwrite_body').html(tbl_overwrite_body);
	$.each(for_overwrite, function(i, x) {
		for_overwrite_arr.push({
			id: x.id,
            sc_no: x.sc_no,
            prod_code: x.prod_code,
            description: x.description,
            quantity: x.quantity,
            po: x.po,
		});
		tbl_overwrite_body = '<tr>'+
								'<td>' +
                                    '<input type="checkbox" class="checkboxes"/>' +
                                '</td>' +
								'<td>'+x.sc_no+'</td>'+
								'<td>'+x.prod_code+'</td>'+
								'<td>'+x.oldquantity+'</td>'+
								'<td>'+x.quantity+'</td>'+
								'<td>'+x.po+'</td>'+
								'<td style="display:none;">'+x.id+'</td>'+
							'</tr>';
		$('#tbl_overwrite_body').append(tbl_overwrite_body);
	});

	$('#modal_upload_orders').modal('show');
}

function notRegistered(arr) {
	$('#modal_not_registered').modal('show');
	$('#tbl_not_registered').dataTable().fnClearTable();
    $('#tbl_not_registered').dataTable().fnDestroy();
    $('#tbl_not_registered').dataTable({
    	data: arr,
    	columns: [
	    	{ data: 'sc_no', name: 'sc_no' },
	    	{ data: 'prod_code', name: 'prod_code' },
	    	{ data: 'quantity', name: 'quantity' },
	    	{ data: 'po', name: 'po' },
    	]
    });
}

function scheduletable(arr) {
	$('#modal_Schedule').modal('show');
	$('#tbl_Schedule').dataTable().fnClearTable();
    $('#tbl_Schedule').dataTable().fnDestroy();
    $('#tbl_Schedule').dataTable({
    	data: arr,
    	bLengthChange : false,
    	paging: true,
    	columns: [
    		{ data: 'sc_no', name: 'sc_no' },
	    	{ data: 'prod_code', name: 'prod_code' },
	    	{ data: 'quantity', name: 'quantity' },
	    	{ data: 'po', name: 'po' },
    	]
    });
}

function getUploadedProducts() {
	$.ajax({
		url: datatableUpload,
		type: 'GET',
		dataType: 'JSON',
	}).done(function(data,textStatus,xhr) {
		uploadedProductsTable(data);
	}).fail(function(xhr,textStatus,errorThrown) {
		msg('Unregistered Products: '+ errorThrown);
	});
}

function uploadedProductsTable(arr) {
	$('#tbl_Upload').dataTable().fnClearTable();
    $('#tbl_Upload').dataTable().fnDestroy();
    $('#tbl_Upload').dataTable({
    	data: arr,
    	columns: [
	    	{ data: 'sc_no' },
			{ data: 'prod_code' },
			{ data: 'description' },
			{ data: 'quantity' },
			{ data: 'po' },
			{ data: 'date_upload' },
    	],
    	createdRow: function (row, data, dataIndex) {
            if (data.description == "Please register this code in Product Master.") {
                $(row).css('background-color', '#ff6266');
                $(row).css('color', '#fff');
            }
        }
    });
}




