var material_dataColumn = [
    {data: function(data) {
    	return '<input type="checkbox" class="table-checkbox check_material_item" value="'+data.id+'">';
    }, name: 'pmc.id', orderable: false, searchable: false},
    {data: 'action', name: 'action', orderable: false, searchable: false},
    {data: 'material_type', name: '.pmc.material_type'},
    {data: 'material_code', name: 'pmc.material_code'},
    {data: function(data) {
    	return '<span title="'+data.code_description+'">'+ellipsis(data.code_description,10)+'</span>';
    }, name: 'pmc.code_description'},
    {data: 'create_user', name: 'pmc.create_user'},
    {data: 'created_at', name: 'pmc.created_at'}
];

$( function() {
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
	getDatatable('tbl_material_code',matCodeListURL,material_dataColumn,[],6);

	init();

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
	});

	$('#frm_mat_code').on('submit', function(e) {
		e.preventDefault();
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			dataType: 'JSON',
			data: $(this).serialize(),
		}).done(function(data, textStatus, xhr) {
			if (data.status == 'success') {
				msg(data.msg,data.status);
				getDatatable('tbl_material_code',matCodeListURL,material_dataColumn,[],6);
				$('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
				clearInputs();
				clearCode();
			}else{
				msg(data.msg,data.status);
			}
		}).fail(function(xhr, textStatus, errorThrown) {
			var errors = xhr.responseJSON.errors;
			showErrors(errors);
		});
	});

	$('#btn_delete_material').on('click', function(e) {
		delete_material('.check_material_item',materialCodeDeleteURL);
	});

	$('#btn_cancel').on('click', function() {
		clearCode();
		showDropdowns();
		$('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
		$('#material_code').prop('readonly', true);
		$('#code_description').prop('readonly', true);
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
});

function init() {
	check_permission(code_permission, function(output) {
		if (output == 1) {}
	});
}

function showDropdowns(mat_type) {
	$.ajax({
		url: showDropdownURL,
		type: 'GET',
		dataType: 'JSON',
		data: {_token: token, mat_type: mat_type}
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
		autoAssignSelectBox($('#material_code').val());
	}).fail(function(xhr, textStatus, errorThrown) {
		msg(errorThrown,textStatus);
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

	if (el.replace('_val','') == 'forth') {
		if (code.length > 1) {
			$('#hide_5th').hide();
		} else {
			$('#hide_5th').show();
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
	var second = (getSelectedText('second') == null)? '':getSelectedText('second');
	var third = (getSelectedText('third') == null)? '':getSelectedText('third');
	var forth = (getSelectedText('forth') == null)? '':getSelectedText('forth');
	var eleventh = (getSelectedText('eleventh') == null)? '':getSelectedText('eleventh');
	var seventh = (getSelectedText('seventh') == null)? '':getSelectedText('seventh');
	//$('#code_description').val(forth+' '+eleventh+' '+ $('#material-type').val()+' '+seventh +$('#material-type').val());
	//$('#alloy').val(forth);
	var fifth = (getSelectedText('fifth') == null)? '':getSelectedText('fifth');

	if (material_type.indexOf('PLATE') > -1) {
		$('#code_description').val(fifth+' '+forth+' '+second+' '+seventh );
	} else if (material_type.indexOf('PIPE') > -1) {
		$('#code_description').val(forth+' '+eleventh+' '+second );
	} else {
		$('#code_description').val(fifth+' '+forth+' '+eleventh+' '+second+' '+seventh );
	}

	$('#alloy').val(forth+' '+fifth);
	$('#item').val(second);
	$('#size').val(eleventh);
}

function delete_material(checkboxClass,deleteURL) {
	var chkArray = [];
	$(checkboxClass+":checked").each(function() {
		chkArray.push($(this).val());
	});

	if (chkArray.length > 0) {
		confirm_delete(chkArray,token,deleteURL,true,'tbl_material_code',matCodeListURL,material_dataColumn);
		clearCode();
		$('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
	} else {
		msg("Please select at least 1 item." , "failed");
	}

	$('.check_all_product').prop('checked',false);
}

function autoAssignSelectBox(code) {
	if (code != '') {
		var first = jsUcfirst(code.charAt(0));
		var second1 = jsUcfirst(code.charAt(1));
		var second2 = jsUcfirst(code.charAt(1))+jsUcfirst(code.charAt(2));
		var second3 = jsUcfirst(code.charAt(1))+jsUcfirst(code.charAt(2))+jsUcfirst(code.charAt(3));
		var third = jsUcfirst(code.charAt(2));
		var forth1 = jsUcfirst(code.charAt(3));
		var forth2 = jsUcfirst(code.charAt(3))+jsUcfirst(code.charAt(4));
		var fifth = jsUcfirst(code.charAt(4));

		var seventh = jsUcfirst(code.charAt(6))+jsUcfirst(code.charAt(7))+jsUcfirst(code.charAt(8));

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
					$('#fifth').val(fifth);
					$('#hide_5th').show();
				}else{
					$('#forth_val').val(forth2);
					$('#forth').val(forth2);
					$('#hide_5th').hide();
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
					$('#fifth').val(fifth);
					$('#hide_5th').show();
				}else{
					$('#forth_val').val(forth2);
					$('#forth').val(forth2);
					$('#hide_5th').hide();
				}
			}
			else{
				$('#hide_5th').show();
				$('#fifth_val').val(fifth);
				$('#fifth').val(fifth);
				$('#second_val').val(second3);
				$('#second').val(second3);
				$('#hide_4th').hide();
			}
		}

		
		$('#seventh_val').val(seventh);
		$('#seventh').val(seventh);

		if ($('#seventh_val').val() != null) {
			var seventh_val = $('#seventh_val').val();
			if (seventh_val.length == 3) {
				$('#hide_8th').hide();
				$('#hide_9th').hide();
			} else {
				$('#hide_8th').show();
				$('#hide_9th').show();
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
        msg(errorThrown,textStatus);
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
