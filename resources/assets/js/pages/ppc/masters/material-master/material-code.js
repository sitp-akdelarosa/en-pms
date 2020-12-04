
$( function() {
	$(document).on('shown.bs.modal', function () {
		getAllMaterialType();
	});

	$(document).on('keydown', function (e) {
		if ($('#material_code_tab').hasClass('active')) {
			switch (e.keyCode) {

				//F1: Block F1
				case 112:
					e.preventDefault();
					window.onhelp = function () {
						return false;
					}
					if (!$('#btn_add_code').is(':disabled') && !$('#btn_add_code').is(':hidden')) {
						$('#btn_add_code').click();
					}
					break;
				//F2: SAVE
				case 113:
					e.preventDefault();
					if (!$('#btn_save').is(':disabled') && !$('#btn_save').is(':hidden')) {
						$('#btn_save').click();
					}
					break;
				//F3: UPDATE
				case 114:
					e.preventDefault();
					if (!$('#btn_save').is(':disabled') && !$('#btn_save').is(':hidden')) {
						$('#btn_save').click();
					}
					break;
				//F4: CLEAR
				case 115:
					e.preventDefault();
					if (!$('#btn_clear_code').is(':disabled') && !$('#btn_clear_code').is(':hidden')) {
						$('#btn_clear_code').click();
					}
					break;
				//F6: Block F6
				case 117:
                    e.preventDefault();
					break;
				//F8: DELETE
				case 119:
					e.preventDefault();
					if (!$('#btn_delete').is(':disabled') && !$('#btn_delete').is(':hidden')) {
						$('#btn_delete').click();
					}
					break;
				//F10: 
				case 121:
					e.preventDefault();
					
					break;
				//F12: CLOSE
				case 123:
					e.preventDefault();
					if (!$('#btn_cancel').is(':disabled') && !$('#btn_cancel').is(':hidden')) {
						$('#btn_cancel').click();
					}
					break;
				default:

			}
		}
    });

	$('body').on('keydown', '.switch_code', function(e) {
		
		var self = $(this)
			, form = self.parents('form:eq(0)')
			, focusable
			, next
			;
		if (e.keyCode == 40) {
			focusable = form.find('.switch_code').filter(':visible');
			next = focusable.eq(focusable.index(this)+1);

			if (next.is(":disabled")) {
				next = focusable.eq(focusable.index(this) + 2);
			}

			if (next.length) {
				next.focus();
			}
			return false;
		}

		if (e.keyCode == 38) {
			focusable = form.find('.switch_code').filter(':visible');
			next = focusable.eq(focusable.index(this)-1);

			if (next.is(":disabled")) {
				next = focusable.eq(focusable.index(this) - 2);
			}

			if (next.length) {
				next.focus();
			}
			return false;
		}

		if (e.keyCode === 13) {
			focusable = form.find('.switch_code').filter(':visible');
			next = focusable.eq(focusable.index(this));

			if (next.length) {
				switch (e.target.type) {
					case "submit":
						next.form.submit();
						break;
					default:
						next.click();
				}
				next.focus();
			}
			return false;
		}
		
	});

	$('#add_code').show();
	$('#save_code').hide();
	$('#cancel_code').hide();
	$('#clear_code').hide();
	$('.readonly_code').prop('disabled', true);

	$('#tbl_material_code .dt-checkboxes-select-all input[type=checkbox]').prop('disabled', false);
	$('#tbl_material_code .dt-checkboxes').prop('disabled', false);

	get_dropdown_material_type();
	
	$('#material_code').prop('readonly', true);
	$('#code_description').prop('readonly', true);
	$('#item').prop('readonly',true);
	$('#alloy').prop('readonly',true);
	$('#schedule').prop('readonly',true);
	$('#size').prop('readonly',true);
	$('#std_weight').prop('readonly', true);

	$('#material_code').mask('AAAAA-AAA-AAAAAA', {
		placeholder: '_____-___-______',
		translation: {
			A: {
				pattern: /[A-Za-z0-9.]/
			},
		}
	});

	checkAllCheckboxesInTable('#tbl_material_code','.check_all_material','.check_material_item');
	materialCodesDataTable();

	$('#material-type').on('change', function(e) {
		e.preventDefault();
		showDropdowns($(this).val())
		$('#material_type').val($(this).val());

		if ($(this).val() != '') {
			$('#material_code').prop('readonly', false);
			$('#code_description').prop('readonly', false);
			$('#item').prop('readonly',false);
			$('#alloy').prop('readonly',false);
			$('#schedule').prop('readonly',false);
			$('#size').prop('readonly',false);
			$('#std_weight').prop('readonly', false);
		} else {
			clearCode();
			clearInputs();
			$('#material_code').prop('readonly', true);
			$('#code_description').prop('readonly', true);
			$('#item').prop('readonly',true);
			$('#alloy').prop('readonly',true);
			$('#schedule').prop('readonly',true);
			$('#size').prop('readonly',true);
			$('#std_weight').prop('readonly', true);
		}
			$('.select-code').val('');
			$('#hide_5th').show();
			$('#hide_8th').show();
			$('#hide_9th').show();
			$('#hide_14th').show();
			$('#material_code').val('');
			$('#code_description').val('');
	});

	$('.select-code').on('change', function(e) {
		e.preventDefault();
		if($(this).val() == ''){
			$('#'+$(this).attr('id')+'_val').val('');
			$('#'+$(this).attr('id').replace('_val','')).val('');
		}else{
			showCode($(this).attr('id'),$('#material-type').val(),$(this).val());
		}		
	});

	$('#tbl_material_code_body').on('click', '.btn_edit_material', function() {
		//clearCode();
		$('#material_id').val($(this).attr('data-id'));
		$('#material_type').val($(this).attr('data-material_type'));
		$('#material_code').val($(this).attr('data-material_code'));
		$('#code_description').val($(this).attr('data-code_description'));
		showDropdowns($(this).attr('data-material_type'));
		$('#material-type').val($(this).attr('data-material_type'));
		$('#item').val($(this).attr('data-item'));
		$('#schedule').val($(this).attr('data-schedule'));
		$('#alloy').val($(this).attr('data-alloy'));
		$('#size').val($(this).attr('data-size'));
		$('#std_weight').val($(this).attr('data-std_weight'));
		$('#material_id').val($(this).attr('data-id'));
		$('#btn_save').html('<i class="fa fa-check"></i> Update');

		$('#material_code').prop('readonly', false);
		$('#code_description').prop('readonly', false);
		$('#item').prop('readonly',false);
		$('#alloy').prop('readonly',false);
		$('#schedule').prop('readonly',false);
		$('#size').prop('readonly',false);
		$('#std_weight').prop('readonly', false);

		$('#tbl_material_code .dt-checkboxes-select-all input[type=checkbox]').prop('disabled', true);
		$('#tbl_material_code .dt-checkboxes').prop('disabled', true);

		$('#btn_add_code').html('<i class="fa fa-pencil"></i> Edit');
		$('#clear_code').show();
	});

	$('#frm_mat_code').on('submit', function(e) {
		e.preventDefault();
		$('.loadingOverlay').show();
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			dataType: 'JSON',
			data: $(this).serialize(),
		}).done(function(data, textStatus, xhr) {
			if (data.status == 'success') {
				msg(data.msg,data.status);
				materialCodesDataTable();
				$('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
				$('#btn_add_code').html('<i class="fa fa-plus"></i> Add New');
				$('.readonly_code').prop('disabled', true);

				$('#add_code').show();
				$('#clear_code').hide();
				$('#save_code').hide();
				$('#cancel_code').hide();

				clearInputs();
				clearCode();
			}else{
				msg(data.msg,data.status);
			}
		}).fail(function(xhr, textStatus, errorThrown) {
			var errors = xhr.responseJSON.errors;
			showErrors(errors);
		}).always( function() {
			$('.loadingOverlay').hide();
		});
	});

	$('#btn_delete_material').on('click', function(e) {
		delete_material('.check_material_item',materialCodeDeleteURL);
	});

	$('#btn_cancel').on('click', function() {
		clearCode();
		showDropdowns();

		$('#add_code').show();
		$('#save_code').hide();
		$('#cancel_code').hide();

		$('.readonly_code').prop('disabled', true);

		$('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
		$('#btn_add_code').html('<i class="fa fa-plus"></i> Add New');
		$('#material_code').prop('readonly', true);
		$('#code_description').prop('readonly', true);

		$('#tbl_material_code .dt-checkboxes-select-all input[type=checkbox]').prop('disabled', false);
		$('#tbl_material_code .dt-checkboxes').prop('disabled', false);
	});

	$('#material_code').on('keyup', function() {
		var code = $(this).val();
		autoAssignSelectBox(code);
	});

	$('#material_code').on('focusout', function() {
		var code = $(this).val();
		$(this).val(code.toUpperCase());

		$('#code_description').val('');
		showDescription();
	});

	$('#btn_add_code').on('click', function() {
		$('#add_code').hide();
		$('#clear_code').hide();
		$('#save_code').show();
		$('#cancel_code').show();

		$('#tbl_material_code .dt-checkboxes-select-all input[type=checkbox]').prop('disabled', true);
		$('#tbl_material_code .dt-checkboxes').prop('disabled', true);

		$('.readonly_code').prop('disabled', false);
	});

	$('#btn_clear_code').on('click', function() {
		$('#btn_add_code').html('<i class="fa fa-plus"></i> Add New');
		$('#add_code').show();
		$('#save_code').hide();
		$('#cancel_code').hide();
		$('#clear_code').hide();
		$('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');

		$('#tbl_material_code .dt-checkboxes-select-all input[type=checkbox]').prop('disabled', false);
		$('#tbl_material_code .dt-checkboxes').prop('disabled', false);

		clearCode();
	});

	$('#tbl_material_code .dt-checkboxes-select-all').on('click', function() {
		if ($('#tbl_material_code .dt-checkboxes-select-all input[type=checkbox]').is(':checked')) {
			$('.btn_edit_material').prop('disabled', true);
			$('#tbl_material_code_body .btn_enable_disable').prop('disabled', true);
		} else {
			$('.btn_edit_material').prop('disabled', false);
			$('#tbl_material_code_body .btn_enable_disable').prop('disabled', false);
		}
	});

	$('#tbl_material_code_body').on('click', 'td:first-child',function() {
		if ($('#tbl_material_code_body .dt-checkboxes').is(':checked')) {
			$('.btn_edit_material').prop('disabled', false);
			$('#tbl_material_code_body .btn_enable_disable').prop('disabled', false);
		} else {
			$('.btn_edit_material').prop('disabled', true);
			$('#tbl_material_code_body .btn_enable_disable').prop('disabled', true);
		}

	});

	$('#tbl_material_code_body').on('change', '.dt-checkboxes',function() {
		if ($(this).is(':checked')) {
			$('.btn_edit_material').prop('disabled', true);
			$('#tbl_material_code_body .btn_enable_disable').prop('disabled', true);
		} else {
			$('.btn_edit_material').prop('disabled', false);
			$('#tbl_material_code_body .btn_enable_disable').prop('disabled', false);
		}
	});

	$('#tbl_material_code').on('click', '.btn_enable_disable',function() {
		$('.loadingOverlay').show();
		$.ajax({
			url: disabledURL,
			type: 'GET',
			dataType: 'JSON',
			data: {
				_token: token,
				id: $(this).attr('data-id'),
				disabled: $(this).attr('data-disabled')
			}
		}).done(function (data, textStatus, xhr) {
			materialCodesDataTable();
		}).fail(function (xhr, textStatus, errorThrown) {
			ErrorMsg(xhr);
		}).always( function() {
			$('.loadingOverlay').hide();
		});
	});

	$('#btn_excel_material').on('click', function() {
		$('#modal_download_excel').modal('show');
	});

	$('#btn_download_excel').on('click', function() {
		var page_url = downloadExcelFileURL;
		var param = '?_token=' + token + '&&mat_types=' + $('#mat_types').val();
		var percentage = 10;

		$('#progress').show();
		$('.progress-bar').css('width', '10%');
		$('.progress-bar').attr('aria-valuenow', percentage);

		var req = new XMLHttpRequest();

		req.open("GET", page_url + param, true);

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
			else if (req.stastus == 500) {
				console.log(req);
			}
		};
		req.send();
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

function showDropdowns(mat_type) {
	$('.loadingOverlay').show();
	$.ajax({
		url: showDropdownURL,
		type: 'GET',
		dataType: 'JSON',
		data: {_token: token, mat_type: mat_type}
	}).done(function(data, textStatus, xhr) {
		console.log(data);
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
		autoAssignSelectBox($('#material_code').val());
	}).fail(function(xhr, textStatus, errorThrown) {
		ErrorMsg(xhr);
	}).always( function() {
		$('.loadingOverlay').hide();
	});
}

function showCode(el,mat_type,code) {
	$('#'+el+'_val').val(code);
	$('#'+el.replace('_val','')).val(code);

	if (el.replace('_val','') == 'second') {
		if (code.length == 2) {
			$('#hide_3rd').hide();
			$('#hide_4th').show();
		} else if(code.length == 3){
			$('#hide_3rd').hide();
			$('#hide_4th').hide();
		}else{
			$('#hide_3rd').show();
			$('#hide_4th').show();
		}
	}

	if (el.replace('_val','') == 'fifth' && mat_type == 'S/S ROUND BAR') {
		if (code == "" && code == null) {
			$('#hide_4th').show();
			$('#hide_5th').hide();
		} else {
			$('#hide_4th').hide();
			$('#hide_5th').show();
		}
	} else {
		if (el.replace('_val','') == 'forth') {
			if (code.length == 2) {
				$('#hide_5th').hide();
			} else {
				$('#hide_5th').show();
			}
		}
	}

	

	if (el.replace('_val','') == 'seventh') {
		if (code.length == 3) {
			$('#hide_8th').hide();
			$('#hide_9th').hide();
		} else {
			$('#hide_8th').show();
			$('#hide_9th').show();
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
			$('#forteenth_val').val('');
			$('#forteenth').val('');
			$('#hide_14th').hide();
		} else {
			$('#hide_14th').show();
		}
	}
	showMaterialCode();
	showDescription();
}

function showMaterialCode() {
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
	$('#material_code').val(first_val+second_val+third_val+forth_val+fifth_val+'-'+seventh_val+eighth_val+ninth_val+'-'+eleventh_val+forteenth_val);
}

function showDescription() {
	var material_type = $('#material-type').val();
	var first = (getSelectedText('first') == null)? '':getSelectedText('first');
	var second = (getSelectedText('second') == null)? '':getSelectedText('second');
	var third = (getSelectedText('third') == null)? '':getSelectedText('third');
	var forth = (getSelectedText('forth') == null)? '':getSelectedText('forth');
	var fifth = (getSelectedText('fifth') == null)? '':getSelectedText('fifth');
	var seventh = (getSelectedText('seventh') == null)? '':getSelectedText('seventh');
	var eighth = (getSelectedText('eighth') == null)? '':getSelectedText('eighth');
	var eleventh = (getSelectedText('eleventh') == null)? '':getSelectedText('eleventh');
	
	//$('#code_description').val(forth+' '+eleventh+' '+ $('#material-type').val()+' '+seventh +$('#material-type').val());
	//$('#alloy').val(forth);

	switch (material_type) {
		case 'ALLOY STEEL ROUND':
			$('#code_description').val(fifth+' '+seventh+' '+eighth+' '+eleventh);

			$('#alloy').val(fifth);
			$('#item').val(first+' '+seventh+' '+eighth);
			$('#size').val(eleventh);
			break;

		case 'C/S HEX BAR':
			$('#code_description').val(fifth+' '+seventh+' '+eighth+' '+eleventh);

			$('#alloy').val(fifth);
			$('#item').val(first+' '+seventh+' '+eighth);
			$('#size').val(eleventh);
			break;

		case 'C/S ROUND BAR':
			$('#code_description').val(first+' '+seventh+' '+eighth+' '+eleventh);

			$('#item').val(first+' '+seventh+' '+eighth);
			$('#alloy').val(fifth);
			$('#size').val(eleventh);
			break;

		case 'C/S SMLS PIPE':
		case 'C/S WELDED PIPE':
			$('#code_description').val(first+' '+second+' '+seventh+' '+eleventh);

			$('#item').val(first+' '+second);
			$('#alloy').val(fifth);
			$('#size').val(eleventh);
			break;

		case 'S/S CAST BAR/BILLET':
			$('#code_description').val(first+' '+seventh+' '+second.replace("BAR",eighth)+' '+eleventh);

			$('#item').val(first+' '+second.replace("BAR",eighth));
			$('#alloy').val(fifth);
			$('#size').val(eleventh);
			break;

		case 'S/S PEELED BAR':
			$('#code_description').val(first+' '+seventh+' '+eighth+' '+fifth+' '+eleventh);

			$('#item').val(first+' '+seventh+' '+eighth);
			$('#alloy').val(fifth);
			$('#size').val(eleventh);
			break;

		case 'S/S PLATE':
			$('#code_description').val(first+' '+eighth+' '+fifth+' '+eleventh);

			$('#item').val(first+' '+eighth);
			$('#alloy').val(fifth);
			$('#size').val(eleventh);
			break;

		case 'S/S ROUND BAR':
			fifth = (fifth == "")? forth: fifth;
			$('#code_description').val(first+' '+seventh+' '+eighth+' '+fifth+' '+eleventh);

			$('#item').val(first+' '+seventh+' '+eighth);
			$('#alloy').val(fifth);
			$('#size').val(eleventh);
			break;

		case 'S/S SMLS PIPE':
		case 'S/S WELDED PIPE':
			$('#code_description').val(first+' '+forth+' '+second+' '+fifth+' '+seventh+' '+eleventh);

			$('#item').val(first+' '+forth+' '+second);
			$('#alloy').val(fifth);
			$('#size').val(eleventh);
			$('#schedule').val(seventh);
			break;

		default:
			fifth = (fifth == "")? forth: fifth;
			$('#code_description').val(first+' '+seventh+' '+eighth+' '+fifth+' '+eleventh);

			$('#item').val(first+' '+seventh+' '+eighth);
			$('#alloy').val(fifth);
			$('#size').val(eleventh);
			break;
	}
	

	// if (material_type.indexOf('PLATE') > -1) {
	// 	$('#code_description').val(first+' '+eighth+' '+fifth+' '+eleventh);

	// 	$('#item').val(second);
	// 	$('#alloy').val(forth+' '+fifth);
	// 	$('#size').val(eleventh);

	// } else if (material_type.indexOf('PIPE') > -1) {
	// 	$('#code_description').val(first+' '+forth+' '+second+' '+fifth+' '+seventh+' '+eleventh);

	// 	$('#item').val(second);
	// 	$('#alloy').val(forth+' '+fifth);
	// 	$('#size').val(eleventh);

	// } else if (material_type.indexOf('S/S ROUND') > -1) {
	// 	$('#code_description').val(first+' '+seventh+' '+eighth+' '+fifth+' '+eleventh);

	// 	$('#item').val(second);
	// 	$('#alloy').val(forth+' '+fifth);
	// 	$('#size').val(eleventh);

	// }
}

function delete_material(checkboxClass,deleteURL) {
	$('.loadingOverlay').show();
	var chkArray = [];
	$(checkboxClass+":checked").each(function() {
		chkArray.push($(this).attr('data-id'));
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
	                materialCodesDataTable();
	        	}).fail(function(xhr, textStatus, errorThrown) {
	        		ErrorMsg(xhr);
	        	}).always(function() {
	        		$('.loadingOverlay').hide();
	        	});
	        } else {
				$('.loadingOverlay').hide();
				$('#tbl_material_code .dt-checkboxes-select-all').click();
	            swal("Cancelled", "Your data is safe and not deleted.");
	        }
	    });


		clearCode();
		$('#material_code').prop('readonly', true);
		$('#code_description').prop('readonly', true);
		$('#material_id').val('');
		$('#material_code').val('');
		$('#code_description').val('');
		$('#material_type').val('');
		$('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');

	} else {
		$('.loadingOverlay').hide();
		msg("Please select at least 1 item.", "failed");
	}

	$('#tbl_material_code .dt-checkboxes-select-all input[type=checkbox]').prop('checked',false);
}

// function delete_material(checkboxClass,deleteURL) {
// 	var chkArray = [];
// 	$(checkboxClass+":checked").each(function() {
// 		chkArray.push($(this).val());
// 	});

// 	if (chkArray.length > 0) {
// 		confirm_delete(chkArray,token,deleteURL,true,'tbl_material_code',matCodeListURL,material_dataColumn);
// 		clearCode();
// 		$('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
// 	} else {
// 		msg("Please select at least 1 item." , "failed");
// 	}

// 	$('.check_all_product').prop('checked',false);
// }

function autoAssignSelectBox(code) {
	if (code != '') {
		var first = jsUcfirst(code.charAt(0));
		var second1 = jsUcfirst(code.charAt(1));
		var second2 = jsUcfirst(code.charAt(1))+jsUcfirst(code.charAt(2));
		var second3 = jsUcfirst(code.charAt(1))+jsUcfirst(code.charAt(2))+jsUcfirst(code.charAt(3));
		var third = jsUcfirst(code.charAt(2));
		var forth1 = jsUcfirst(code.charAt(3));
		var forth2 = jsUcfirst(code.charAt(3))+jsUcfirst(code.charAt(4));
		var fifth = jsUcfirst(code.charAt(3)) + jsUcfirst(code.charAt(4));//jsUcfirst(code.charAt(4));

		var seventh = jsUcfirst(code.charAt(6));//+jsUcfirst(code.charAt(7))+jsUcfirst(code.charAt(8));
		var eighth = jsUcfirst(code.charAt(7)) + jsUcfirst(code.charAt(8));
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
			$('#hide_4th').show();
				$('#forth_val').val(forth1);
				$('#forth').val(forth1);
				$('#hide_4th').show();
				if ($('#forth_val').val() != null) {
					$('#fifth_val').val(fifth);
					
					console.log($('#fifth_val').val());

					if ($('#fifth_val').val() == null) {
						fifth = jsUcfirst(code.charAt(4));
						$('#fifth_val').val(fifth);
					}
					$('#fifth').val(fifth);
					$('#hide_5th').show();
				}else{
					$('#forth_val').val(forth2);
					$('#forth').val(forth2);

					if ($('#forth_val').val() == null) {
						$('#hide_4th').hide();
						$('#hide_5th').show();
						$('#fifth_val').val(fifth);
						$('#fifth').val(fifth);
					} else {
						$('#hide_5th').hide();
					}
				}
		}else{
			$('#second_val').val(second2);
			$('#second').val(second2);
			$('#hide_3rd').hide();
			$('#hide_4th').show();
			if ($('#second_val').val() != null) {
				$('#forth_val').val(forth1);
				$('#forth').val(forth1);
				$('#hide_4th').show();
				if ($('#forth_val').val() != null) {
					$('#fifth_val').val(fifth);

					console.log($('#fifth_val').val());

					if ($('#fifth_val').val() == null) {
						fifth = jsUcfirst(code.charAt(4));
						$('#fifth_val').val(fifth);
					}

					$('#fifth').val(fifth);
					$('#hide_5th').show();
				}else{
					$('#forth_val').val(forth2);
					$('#forth').val(forth2);
					if ($('#forth_val').val() == null) {
						$('#hide_4th').hide();
						$('#hide_5th').show();
						$('#fifth_val').val(fifth);
						$('#fifth').val(fifth);
					} else {
						$('#hide_5th').hide();
					}
				}
			}
			else{
				$('#hide_5th').show();
				$('#fifth_val').val(fifth);

				console.log($('#fifth_val').val());

				if ($('#fifth_val').val() == null) {
					fifth = jsUcfirst(code.charAt(4));
					$('#fifth_val').val(fifth);
				}

				$('#fifth').val(fifth);
				$('#second_val').val(second3);
				$('#second').val(second3);
				$('#hide_4th').hide();
			}
		}

		
		$('#seventh_val').val(seventh);
		if ($('#seventh_val').val() == null) {
			seventh = jsUcfirst(code.charAt(6))+jsUcfirst(code.charAt(7))+jsUcfirst(code.charAt(8));
			$('#seventh_val').val(seventh);
		}
		$('#seventh').val(seventh);

		if ($('#seventh_val').val() != null) {
			var seventh_val = $('#seventh_val').val();
			if (seventh_val.length == 3) {
				$('#hide_8th').hide();
				$('#hide_9th').hide();
			} else {
				$('#hide_8th').show();
				$('#eighth_val').val(eighth);
				$('#eighth').val(eighth);

				$('#hide_9th').show();
			}
		}

		if ($('#eighth_val').val() != null) {
			var eighth_val = $('#eighth_val').val();
			if (eighth_val.length == 2) {
				$('#hide_9th').hide();
			}
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
			$('#hide_14th').hide();
		}
	}
}

function get_dropdown_material_type() {
    var opt = "<option value=''></option>";
    $('#material-type').html(opt);
    $.ajax({
        url: getMaterialTypeURL,
        type: 'GET',
        dataType: 'JSON',
        data: {_token: token},
    }).done(function(data, textStatus, xhr) {
        $.each(data, function(i, x) {
            opt = "<option value='"+x.material_type+"'>"+x.material_type+"</option>";
            $('#material-type').append(opt);
        });
    }).fail(function(xhr, textStatus, errorThrown) {
        ErrorMsg(xhr);
    });
}

function clearCode() {
	$('.select-code').val('');
	$('#material-type').val('');
	$('#hide_3rd').show();
	$('#hide_5th').show();
	$('#hide_4th').show();
	$('#hide_8th').show();
	$('#hide_9th').show();
	$('#hide_14th').show();
	$('#material_code').val('');
	$('#code_description').val('');
	$('#material_id').val('');
	$('#item').val('');
	$('#alloy').val('');
	$('#size').val('');
	$('#std_weight').val('');
	$('#schedule').val('');
}

function clearInputs() {
	$('#code_description').val('');
	$('#item').val('');
	$('#alloy').val('');
	$('#size').val('');
	$('#std_weight').val('');
	$('#schedule').val('');
	$('#material-type').val('');
	$('#material_code').val('');
	$('#material_type').val('');
	$('#material_id').val('');
}

function materialCodesDataTable() {
	$('#tbl_material_code').dataTable().fnClearTable();
	$('#tbl_material_code').dataTable().fnDestroy();
	$('#tbl_material_code').dataTable({
		ajax: {
			url: matCodeListURL,
			error: function (xhr, textStatus, errorThrown) {
				ErrorMsg(xhr);
			}
		},
		stateSave: true,
		serverSide: true,
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
		columnDefs: [
			{
				targets: 0,
				checkboxes: {
					selectRow: true
				}
			}
		],
		select: {
			selector: 'td:not(:nth-child(2)):not(:nth-child(3)):not(:nth-child(4)):not(:nth-child(5)):not(:nth-child(6)):not(:nth-child(7)):not(:nth-child(8))',
			style: 'multi'
		},
		order: [[6, 'desc']],
		columns: [
			{
				data: function (data) {
					return data.id//'<input type="checkbox" class="table-checkbox check_material_item" value="' + data.id + '">';
				}, name: 'pmc.id', orderable: false, searchable: false, width: '3.5%'
			},
			{ data: 'action', name: 'action', orderable: false, searchable: false, width: '3.5%' },
			{ data: 'material_type', name: '.pmc.material_type',width: '18.5%' },
			{ data: 'material_code', name: 'pmc.material_code',width: '19.5%' },
			{ data: 'code_description', name: 'pmc.code_description', width: '25.5%' },
			{ data: 'create_user', name: 'pmc.create_user',width: '12.5%' },
			{ data: 'updated_at', name: 'pmc.updated_at',width: '13.5%' },
			{
				data: function (data) {
					var enable_disable;
					var bg_color = "";
					if (data.disabled == 0) {
						enable_disable = "<i class='fa fa-ban'></i>";
						bg_color = "btn-danger";
					} else {
						enable_disable = "<i class='fa fa-toggle-on'></i>";
						bg_color = "btn-primary";
					}
					return '<button type="button" class="btn ' + bg_color + ' btn_enable_disable" data-id="' + data.id + '" '+
							'data-disabled="' + data.disabled+'" '+
							'data-toggle="popover" '+
							'data-content="This Button is to Disable / Enable '+data.material_code+'" '+
							'data-placement="right" '+
							'>' + enable_disable + '</button>';
				}, name: 'pc.disabled', orderable: false, searchable: false, width: '3.5%' 
			},
		],

		initComplete: function() {
			$('.btn_edit_material').popover({
				trigger: 'hover focus'
			});

			$('.btn_enable_disable').popover({
				trigger: 'hover focus'
			});
			
			$('#tbl_material_code .dt-checkboxes-select-all input[type=checkbox]').addClass('table-checkbox');
		},
		fnDrawCallback: function() {
		},
		createdRow: function (row, data, dataIndex) {
			if (data.disabled == 1) {
				$(row).css('background-color', '#ff6266');
				$(row).css('color', '#fff');
			}
			var dataRow = $(row);
			var checkbox = $(dataRow[0].cells[0].firstChild);

			checkbox.attr('data-id', data.id);
			checkbox.addClass('table-checkbox check_material_item');
		},
	});
}

function getAllMaterialType() {
	$('.loadingOverlay-modal').show();
	$.ajax({
		url: AllMaterialTypeURL,
		type: 'GET',
		dataType: 'JSON',
		data: { _token: token },
	}).done(function (data, textStatus, xhr) {
		$('#mat_types').select2({
			allowClear: true,
			placeholder: 'Select Material Types',
			data: data
		}).val(data).trigger('change.select2');

	}).fail(function (xhr, textStatus, errorThrown) {
		ErrorMsg(xhr);
	}).always(function () {
		$('.loadingOverlay-modal').hide();
	});
}

