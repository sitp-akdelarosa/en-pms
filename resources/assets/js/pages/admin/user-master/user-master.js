var dataColumn = [
    {data: 'user_id', name: 'user_id'},
    {data: 'firstname', name: 'firstname'},
    {data: 'lastname', name: 'lastname'},
    {data: 'email', name: 'email'},
    {data: 'user_type', name: 'user_type'},
    // {data: 'div_code', name: 'div_code'},
    {data: 'created_at', name: 'created_at'},
    {data: 'action', name: 'action', orderable: false, searchable: false},
];

$(function () {
	"use strict";
    getDatatable('tbl_user',userListURL,dataColumn,[],0);
    //getDivisionCode('#div_code');
    get_user_type('#user_type');

    modules(1,'');
	check_permission(code_module);

    $('.custom-file-input').on('change', function() {
       let fileName = $(this).val().split('\\').pop();
       $(this).next('.custom-file-label').addClass("selected").html(fileName);

       readPhotoURL(this);
    });

    $('#tbl_user_body').on('click', '.btn_delete_user', function(e) {
        confirm_delete($(this).attr('data-id'),token,userDeleteURL,true,'tbl_user',userListURL,dataColumn);
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
        $('.loadingOverlay').show();
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
            $('.loadingOverlay').hide();
			if (textStatus) {
                if(data.status == "failed"){
                    msg(data.msg,data.status);
                }else{
				    msg("User data was successfully saved.",textStatus);
                }
                getDatatable('tbl_user',userListURL,dataColumn,[],0);
			}
		}).fail(function(xhr, textStatus, errorThrown) {
			var errors = xhr.responseJSON.errors;
			showErrors(errors);

            if(errorThrown == "Internal Server Error"){
                msg(errorThrown,textStatus);
            }

            $('.loadingOverlay').hide();
		});
   	});

});

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
		//$('#div_code').val(data.div_code);
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
	$('#photo_profile').attr('src',defaultPhoto);
	$('#photo_label').html("Select a photo...");
}

function modules(user_type,id = '') {
    console.log(user_type);
    $('.loadingOverlay').show();
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
        $('.loadingOverlay').hide();
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
