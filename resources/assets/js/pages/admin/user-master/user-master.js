$(function () {
	"use strict";
	userList();
	//getDivisionCode('#div_code');
	get_user_type('#user_type');

	modules(1,'');
	init();

	checkAllCheckboxesInTable('#tbl_user','.check_all_users','.check_user');
	

	$('.custom-file-input').on('change', function() {
	   let fileName = $(this).val().split('\\').pop();
	   $(this).next('.custom-file-label').addClass("selected").html(fileName);

	   readPhotoURL(this);
	});

	$('#btn_delete').on('click', function() {
		var chkArray = [];
		var table = $('#tbl_user').DataTable();

		for (var x = 0; x < table.context[0].aoData.length; x++) {
			var DataRow = table.context[0].aoData[x];
			if (DataRow.anCells !== null && DataRow.anCells[0].firstChild.checked == true) {
				chkArray.push(table.context[0].aoData[x].anCells[0].firstChild.value)
			}
		}

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
						url: userDeleteURL,
						type: 'POST',
						dataType: 'JSON',
						data: {
							_token:token,
							ids: chkArray
						},
					}).done(function(data, textStatus, xhr) {
						if (data.status == 'success') {
							msg(data.msg,data.status)
						} else {
							msg(data.msg,data.status)
						}

						userList(); // in here, the loading will close 

						return data.status;
					}).fail(function(xhr, textStatus, errorThrown) {
						msg(errorThrown,'error');
					});
				} else {
					swal("Cancelled", "Your data is safe and not deleted.");
				}
			});
		} else {
			msg('Please select at least 1 user.', 'failed');
		}
	});

	$('#tbl_user_body').on('click', '.btn_edit_user', function(e) {
		clear();
		show_user($(this).attr('data-id'));
		$('#modal_user_access').modal('show');
	});
	
	//$("#div_code").on('keyup', getdivisionsuggest);
	

	$('#btn_add_user').on('click', function() {
		clear();
		modules();
		$('#modal_user_access').modal('show');
	});

	$('#btn_upd_usertype').on('click', function() {
		$.ajax({
			url: userUpdTypeURL,
			type: 'POST',
			dataType: 'JSON',
		}).done(function(data, textStatus, xhr) {
			msg(data.msg,data.status);
		}).fail(function(xhr, textStatus, errorThrown) {
			console.log("error");
		});
	});

	$('#user_type').on('change', function() {
		modules($(this).val());
	});

	$('#frm_user').on('submit', function(e) {
		$('.loadingOverlay-modal').show();
		e.preventDefault();
		var data = new FormData(this);
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			dataType: 'JSON',
			data: data,
			mimeType:"multipart/form-data",
			contentType: false,
			cache: false,
			processData:false,
		}).done(function(data, textStatus, xhr) {
			$('.loadingOverlay-modal').hide();
			if (textStatus) {
				if(data.status == "failed"){
					msg(data.msg,data.status);
				}else{
					msg("User data was successfully saved.",textStatus);
				}
				userList();
			}
		}).fail(function(xhr, textStatus, errorThrown) {
			var errors = xhr.responseJSON.errors;
			showErrors(errors);

			if(errorThrown == "Internal Server Error"){
				msg(errorThrown,textStatus);
			}

			$('.loadingOverlay-modal').hide();
		});
	});

});

function init() {
	check_permission(code_module, function(output) {
		if (output == 1) {}
	});
}

function show_user(id) {
	$.ajax({
		url: '/admin/user-master/'+id,
		type: 'GET',
		dataType: 'JSON',
	}).done(function(data) {
		$('#photo_profile').attr("src",'../../../../'+data.photo);
		$('#id').val(data.id);
		$('#user_id').val(data.user_id);
		$('#firstname').val(data.firstname);
		$('#lastname').val(data.lastname);
		$('#user_type').val(data.user_type);
		$('#password').val(data.actual_password);
		$('#password_confirmation').val(data.actual_password);
		$('#email').val(data.email);

		var checked = false;
		if (data.is_admin) {
			checked = true;
		}
		$('#is_admin').prop('checked',checked);

		modules(data.user_type,data.id);
	}).fail(function() {
		msg(errorThrown,textStatus);
	});
}

function clear() {
	$('.clear').val('');

	hideErrors('user_id');
	hideErrors('firstname');
	hideErrors('lastname');
	hideErrors('user_type');
	hideErrors('email');
	hideErrors('password');

	$('#photo_profile').attr('src',defaultPhoto);
	$('#photo_label').html("Select a photo...");
}

function modules(user_type,id = '') {
	$('.loadingOverlay-modal').show();
	tbl = '';
	$('#tbl_modules_body').html(tbl);
	var d = {
		user_type: user_type,
		id: id,
	};

	$.ajax({
		url: '/admin/user-mod',
		type: 'GET',
		dataType: 'JSON',
		data: d,
	}).done(function(data, textStatus, xhr) {
		$('.loadingOverlay-modal').hide();
		if (data.length < 1) {
			tbl = 	'<tr>'+
						'<td colspan="4">No data displayed.</td>'+
					'</tr>';
			$('#tbl_modules_body').append(tbl);
		} else {
			$.each(data, function(i, x) {
				if (x.access == 1) {
					var checked_rw = 'checked';
				}

				if (x.access == 2) {
					var checked_ro = 'checked';
				}

				tbl = 	'<tr>'+
							'<td>'+x.code+
								'<input type="hidden" name="code[]" value="'+x.code+'">'+
							'</td>'+
							'<td>'+x.title+
								'<input type="hidden" name="title[]" value="'+x.title+'">'+
							'</td>'+
							'<td>'+
								'<input type="checkbox" class="table-checkbox access" name="rw[]" value="'+x.id+'" '+checked_rw+'>'+
							'</td>'+
							'<td>'+
								'<input type="checkbox" class="table-checkbox access" name="ro[]" value="'+x.id+'" '+checked_ro+'>'+
							'</td>'+
						'</tr>';
				$('#tbl_modules_body').append(tbl);
			});
		}
	}).fail(function(xhr, textStatus, errorThrown) {
		msg(errorThrown,textStatus);
	});
}

function userList() {
	$('.loadingOverlay').show();

	$.ajax({
		url: userListURL,
		type: 'GET',
		dataType: 'JSON',
		data: {
			_token: token
		},
	}).done(function(data, textStatus, xhr) {
		var table = $('#tbl_user');

		table.dataTable().fnClearTable();
		table.dataTable().fnDestroy();
		table.dataTable({
			data: data,
			processing: true,
			deferRender: true,
			responsive: true,
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
					"previous":"Prev",
					"next": "Next",
					"last": "Last",
					"first": "First"
				}
			},
			pageLength: 10,
			order: [
				[8, "desc"]
			],
			columns: [
				{data: function(x) {
					return '<input type="checkbox" class="table-checkbox check_user" value="'+x.id+'">';
				}, name: 'id', searchable: false, orderable: false, width: '3.11%' },
				{data: function (x) {

					return '<button type="button" class="btn btn-sm bg-blue btn_edit_user" data-id="'+x.id+'">'+
								'<i class="fa fa-edit"></i>'+
							'</button>';
							// '<button type="button" class="btn btn-sm bg-red btn_delete_user permission-button" data-id="'+x.id+'">'+
							// 	'<i class="fa fa-trash"></i>'+
							// '</button>';
				}, name: 'action', orderable: false, searchable: false, width: '3.11%' },
				{data: 'user_id', name: 'user_id', width: '13.11%' },
				{data: 'firstname', name: 'firstname', width: '15.11%' },
				{data: 'lastname', name: 'lastname', width: '15.11%' },
				{data: 'email', name: 'email', width: '14.11%' },
				{data: 'user_type', name: 'user_type', width: '14.11%' },
				{data: 'actual_password', name: 'actual_password', width: '13.11%' },
				{data: 'created_at', name: 'created_at', width: '11.11%' },
			],
			createdRow: function (row, data, dataIndex) {
				if (data.del_flag === 1) {
					$(row).css('background-color', '#ff6266');
					$(row).css('color', '#fff');
				}
			},
			"initComplete": function () {
				$('.loadingOverlay').hide();
			},
			"fnDrawCallback": function () {
			},
		});
	}).fail(function(xhr, textStatus, ErrMsg) {
		var msgErr = xhr.responseJSON.message;
		msg(msgErr,textStatus);
	}).always(function() {
		console.log("complete");
	});
}

// function getdivisionsuggest(){
//     var options = '';
//     var datas = $("#div_code").val();
//     $.ajax({
//         url: divCodeURL,
//         type: 'POST',
//         datatype: "json",
//         loadonce: true,
//         data: {_token: token, data:datas},
//         rowNum: 1000,
//         success: function (returnData) {
//             options = "";
//             if (returnData.length > 20) {
//                 l = 10;
//             }
//             else {
//                 l = returnData.length;
//             }
//             for (var i = 0; i < l; i++) {
//                 options += '<option value="' + returnData[i].div_code + '" />';
//             }
//             $("#divcode").empty().append(options);
//             document.getElementById('divcode').innerHTML = options;
//         },
//         error: function (xhr, ajaxOptions, thrownError) {
//             alert(xhr.status);
//             alert(thrownError);
//         }
//     });
// }
