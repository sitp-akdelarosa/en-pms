$( function() {
    init();
    $('#btn_clear').on('click', function() {
        clear();
        $('#btn_save').removeClass('bg-green');
        $('#btn_save').addClass('bg-blue');
        $('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
    });

    $(document).on('keydown', function (e) {
		if ($('#product_code_tab').hasClass('active')) {
			switch (e.keyCode) {
				//F1: Block F1
				case 112:
					e.preventDefault();
					window.onhelp = function () {
						return false;
					}
					if (!$('#btn_add').is(':disabled') && !$('#btn_add').is(':hidden')) {
						$('#btn_add').click();
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
					if (!$('#btn_clear').is(':disabled') && !$('#btn_clear').is(':hidden')) {
						$('#btn_clear').click();
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

	$('body').on('keydown', '.switch', function(e) {
		
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

    //Add and save update
    $("#frm_operator").on('submit',function(e){
		e.preventDefault();
		$('.loadingOverlay').show();
        var form_action = $(this).attr("action");
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: form_action,
            data:  $(this).serialize(),
        }).done(function(data, textStatus, xhr){
            if(data.status == 'success'){
                getOperators();
                $('#btn_save').removeClass('bg-green');
                $('#btn_save').addClass('bg-blue');
                $('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');

                viewState('');
                clear();
            }

            msg(data.msg,data.status);
        }).fail( function(xhr, textStatus, errorThrown) {
            if (xhr.status == 422) {
                var errors = xhr.responseJSON.errors;
                showErrors(errors);
            } else {
                ErrorMsg(xhr);
            }
            
        }).always( function() {
			$('.loadingOverlay').hide();
		});
    });

    //Edit table
    $('#tbl_operator').on('click', '.btn_edit', function(e) {
        viewState('show');
        $('#operator_id').val($(this).attr('data-operator_id'));
        $('#id').val($(this).attr('data-id'));
        $('#firstname').val($(this).attr('data-firstname'));
		$('#lastname').val($(this).attr('data-lastname'));
		$('#nickname').val($(this).attr('data-nickname'));
		$('#position').val($(this).attr('data-position'));
        $('#btn_save').removeClass('bg-blue');
        $('#btn_save').addClass('bg-green');
        $('#btn_save').html('<i class="fa fa-check"></i> Update');
    });

    //Delete Multiple data
    $('#btn_delete').on('click', function() {
        delete_operators('.dt-checkboxes',deleteOM);
    });

    $('#btn_add').on('click', function() {
		if ($('#id').val() == '') {
			viewState('addnew');
		} else {
			viewState('edit');
		}
        
    });

    $('#btn_clear').on('click', function() {
        clear();
    });

    $('#btn_cancel').on('click', function() {
        clear();
        viewState('');
    });

    $('.dt-checkboxes-select-all').on('click', function() {
		if ($('.dt-checkboxes-select-all input[type=checkbox]').is(':checked')) {
			$('.btn_edit').prop('disabled', true);
			$('.btn_enable_disable').prop('disabled', true);
		} else {
			$('.btn_edit').prop('disabled', false);
			$('.btn_enable_disable').prop('disabled', false);
		}
	});

	$('tbl_operator_body').on('click', 'td:first-child',function() {
		if ($('.dt-checkboxes').is(':checked')) {
			$('.btn_edit').prop('disabled', false);
			$('.btn_enable_disable').prop('disabled', false);
		} else {
			$('.btn_edit').prop('disabled', true);
			$('.btn_enable_disable').prop('disabled', true);
		}

	});

	$('tbl_operator_body').on('change', '.dt-checkboxes',function() {
		if ($(this).is(':checked')) {
			$('.btn_edit').prop('disabled', true);
			$('.btn_enable_disable').prop('disabled', true);
		} else {
			$('.btn_edit').prop('disabled', false);
			$('.btn_enable_disable').prop('disabled', false);
		}
	});

	$('#tbl_operator_body').on('click', '.btn_enable_disable',function() {
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
			getOperators();
		}).fail(function (xhr, textStatus, errorThrown) {
			ErrorMsg(xhr);
		}).always( function() {
			$('.loadingOverlay').hide();
		});
	});


});

function init() {
	viewState('');

	if (permission_access == '2' || permission_access == 2) {
        $('.permission').prop('readonly', true);
        $('.permission-button').prop('disabled', true);
    } else {
        $('.permission').prop('readonly', false);
        $('.permission-button').prop('disabled', false);
    }
	
	getOperators();
	checkAllCheckboxesInTable('#tbl_operator','.check_all','.check_item');
}

//Multiple Delete 
function delete_set(checkboxClass,deleteOM) {
    var chkArray = [];
    $(checkboxClass+":checked").each(function() {
        chkArray.push($(this).val());
    });
    if (chkArray.length > 0) {
        confirm_delete(chkArray,token,deleteOM,true,'tbl_operator',getOutputsURL,dataColumn);
    } else {
        msg("Please select at least 1 item." ,"failed");
    }

    $('.check_all').prop('checked',false);
    clear();
    $('#btn_save').removeClass('bg-green');
    $('#btn_save').addClass('bg-blue');
    $('#btn_save').html('<i class="fa fa-plus"></i> Add');
}

//Clear Textbox
function clear() {
    $('.clear').val('');
}

function viewState(state) {
    switch (state) {
        case 'addnew':
            $('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
            $('#div_add').hide();
            $('#div_save').show();
            $('#div_clear').show();
            $('#div_cancel').show();
            $('#div_delete').hide();
            $('.readonly_op').prop('disabled', false);
            $('.dt-checkboxes').prop('disabled', true);
            $('.dt-checkboxes-select-all input[type=checkbox]').prop('disabled', true);
			break;
			
		case 'show':
            $('#btn_add').html('<i class="fa fa-edit"></i> Edit');
            $('#div_add').show();
            $('#div_save').hide();
            $('#div_clear').hide();
            $('#div_cancel').show();
            $('#div_delete').hide();
            $('.readonly_op').prop('disabled', true);
            $('.dt-checkboxes').prop('disabled', true);
            $('.dt-checkboxes-select-all input[type=checkbox]').prop('disabled', true);
            break;

        case 'edit':
            $('#btn_save').html('<i class="fa fa-pencil"></i> Update');
            $('#div_add').hide();
            $('#div_save').show();
            $('#div_clear').hide();
            $('#div_cancel').show();
            $('#div_delete').hide();
            $('.readonly_op').prop('disabled', false);
            $('.dt-checkboxes').prop('disabled', true);
            $('.dt-checkboxes-select-all input[type=checkbox]').prop('disabled', true);
            break;
    
        default:
			$('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
			$('#btn_add').html('<i class="fa fa-plus"></i> Add New');
            $('#div_add').show();
            $('#div_save').hide();
            $('#div_clear').hide();
            $('#div_cancel').hide();
            $('#div_delete').show();
            $('.readonly_op').prop('disabled', true);
            $('.dt-checkboxes').prop('disabled', false);
            $('.dt-checkboxes-select-all input[type=checkbox]').prop('disabled', false);

            hideErrors('operator_id');
            hideErrors('firstname');
			hideErrors('lastname');
			hideErrors('nickname');
			hideErrors('position');
            break;
    }
}

function getOperators() {
	$('#tbl_operator').dataTable().fnClearTable();
	$('#tbl_operator').dataTable().fnDestroy();
	$('#tbl_operator').dataTable({
		ajax: {
			url: getOperatorsURL,
			error: function(xhr,textStatus,errorThrown) {
				ErrorMsg(xhr);
			}
		},
		serverSide: true,
		processing: true,
		deferRender: true,
		stateSave: true,
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
			selector: 'td:not(:nth-child(2)):not(:nth-child(3)):not(:nth-child(4)):not(:nth-child(5)):not(:nth-child(6)):not(:nth-child(7))',
			style: 'multi'
		},
		order: [[5, 'desc']],
		columns: [
			{
                data: function(data) {
                    return data.id;//'<input type="checkbox" class="table-checkbox check_item" value="'+data.id+'">';
                }, name: 'id', orderable: false, searchable: false, width: '5.5%'
            },
            { data: 'action', name: 'action', orderable: false, searchable: false, width: '5.5%' },
            { data: 'operator_id', name: 'operator_id', width: '16.5%' },
            { data: 'firstname', name: 'firstname', width: '16.5%' },
			{ data: 'lastname', name: 'lastname', width: '16.5%' },
			{ data: 'nickname', name: 'nickname', width: '12.5%' },
			{ data: 'position', name: 'position', width: '21.5%' },
            { data: 'updated_at', name: 'updated_at', width: '12.5%' },
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
							'data-content="This Button is to Disable / Enable '+data.firstname+' '+data.lastname+'" '+
							'data-placement="right" '+
							'>' + enable_disable + '</button>';
                }, name: 'disabled', orderable: false, searchable: false, width: '5.5%'
            }
		],
		initComplete: function() {
			$('.btn_edit').popover({
				trigger: 'hover focus'
            });
            
			$('.btn_enable_disable').popover({
				trigger: 'hover focus'
			});

			$('#tbl_operator .dt-checkboxes-select-all input[type=checkbox]').addClass('table-checkbox');
			$("#tbl_operator").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
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
			checkbox.addClass('table-checkbox check_item');
		},
		
	});
}

function delete_operators(checkboxClass,deleteURL) {
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
	                getOperators();
	        	}).fail(function(xhr, textStatus, errorThrown) {
	        		msg(errorThrown,'error');
	        	}).always(function() {
	        		$('.loadingOverlay').hide();
	        	});
	        } else {
				$('.loadingOverlay').hide();
				$('#tbl_product_code .dt-checkboxes-select-all').click();
	            swal("Cancelled", "Your data is safe and not deleted.");
	        }
	    });


		$('.check_all').prop('checked',false);
        clear();
        $('#btn_save').removeClass('bg-green');
        $('#btn_save').addClass('bg-blue');
        $('#btn_save').html('<i class="fa fa-plus"></i> Add');

	} else {
		$('.loadingOverlay').hide();
		msg("Please select at least 1 item.", "failed");
	}
}