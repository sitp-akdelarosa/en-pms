var audit_arr = [];

Echo.channel('audit-trail')
    .listen('AuditTrail', (e) => {
        var audit = e.audit;
        audit_arr.push({
            action: audit.action,
            created_at: '',
            id: '',
            module: audit.module,
            updated_at: '',
            user: audit.user,
            user_type: audit.user_type
        });

        console.log(audit_arr);

        makeAuditTrailTable(audit_arr);
    });

Echo.channel('travel-sheet')
    .listen('TravelSheet', (e) => {
        console.log(e.travelsheet);
    });

Echo.channel('notification')
    .listen('Notify', (e) => {
        var noti = e.notification;
        msg('A','success');
        $.each(noti, function(i, x) {
            if (x.to == $('meta[name=user_id]').attr('content')) {
                getUnreadNotification();
                msg(x.content,'notification');
            }
        });
        
    });


// $( document ).ajaxError(function( event, jqxhr, settings, thrownError ) {
    // swal({
    //     title: "Session Expired!",
    //     text: "You'll be take to the login page.",
    //     type: "warning",
    //     showCancelButton: true,
    //     confirmButtonColor: "#f95454",
    //     confirmButtonText: "OK",
    //     closeOnConfirm: true,
    // }, function(isConfirm){
    //     if (isConfirm) {
    //         location.href = "/";
    //     }
    // }); 
// });

$( function() {
    $.ajaxPrefilter(function(options, originalOptions, xhr) {
        var token = $('meta[name="csrf_token"]').attr('content');

        if (token) {
            return xhr.setRequestHeader('X-XSRF-TOKEN', token);
        }
    });
    getUnreadNotification();

    $('.validate').on('keyup', function(e) {
        var no_error = $(this).attr('id');
        hideErrors(no_error)
    });

    $('.select-validate').on('change', function(e) {
        var no_error = $(this).attr('id');
        hideErrors(no_error)
    });

    $('#notification_list_header').on('click', '.view_notification', function() {
        readNotification($(this).attr('data-id'),$(this).attr('data-link'));
    });
});

jQuery.fn.extend({
    live: function (event, callback) {
       if (this.selector) {
            jQuery(document).on(event, this.selector, callback);
        }
    }
});

function readPhotoURL(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function(e) {
			$('.photo').attr('src', e.target.result);
		}
		reader.readAsDataURL(input.files[0]);
	}
}

function showErrors(errors) {
	$.each(errors, function(i, x) {
		switch(i) {
			case i:
				$('#'+i).addClass('is-invalid');
				if (i == 'photo') {
					var err = '';
					$.each(x, function(ii, xx) {
						err += '<li>'+xx+'</li>';
						$('#photo_feedback').append(err);
					});
					$('#photo_feedback').css('color', '#dc3545');
				} 
				if (i !== 'photo') {
					$('#'+i+'_feedback').addClass('invalid-feedback');
					$('#'+i+'_feedback').html(x);
				}
			break;
		}
	});
}

function hideErrors(error) {
	$('#'+error).removeClass('is-invalid');
	$('#'+error+'_feedback').removeClass('invalid-feedback');
	$('#'+error+'_feedback').html('');
}

function jsUcfirst(word) {
    return word.charAt(0).toUpperCase() + word.slice(1);
}

/**
 * Open Message
 * @param  {String} msg_content [message content]
 * @param  {String} status [is it 'success','failed', or 'error']
 */
function msg(msg_content,status) {
    if (status == '') {
        //swal("Oops!", msg_content);
        $.toast({
            heading: 'Oops!',
            text: msg_content,
            position: 'top-right',
            loaderBg: '#ff6849',
            icon: 'warning',
            hideAfter: 3000,
            stack: 6
        });
    } else {
        //swal(jsUcfirst(status)+"!", msg_content, status);
        switch(status) {
            case 'success':
               $.toast({
                    heading: jsUcfirst(status)+"!",
                    text: msg_content,
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'success',
                    hideAfter: 5000,
                    stack: 6
                });
            break;

            case 'failed':
               $.toast({
                    heading: jsUcfirst(status)+"!",
                    text: msg_content,
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'warning',
                    hideAfter: 5000,
                    stack: 6
                });
            break;

            case 'warning':
               $.toast({
                    heading: jsUcfirst(status)+"!",
                    text: msg_content,
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'warning',
                    hideAfter: 5000,
                    stack: 6
                });
            break;

            case 'error':
               $.toast({
                    heading: jsUcfirst(status)+"!",
                    text: msg_content,
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'danger',
                    hideAfter: 5000,
                    stack: 6
                });
            break;

            case 'notification':
               $.toast({
                    heading: jsUcfirst(status)+"!",
                    text: msg_content,
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'info',
                    hideAfter: 5000,
                    stack: 6
                });
            break;
        }
    }
}

function confirm_delete(id, token, ajax_url,refresh_table,tbl,tbl_url,dataColumn) {
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
        		url: ajax_url,
        		type: 'POST',
        		dataType: 'JSON',
        		data: {
        			_token:token,
        			id: id
        		},
        	}).done(function(data, textStatus, xhr) {
        		if (data.status == 'success') {
        			msg(data.msg,data.status)
                    is_confirmed_deleted = true;
        		} else {
                    msg(data.msg,data.status)
                }

                if (refresh_table == true) {
                    getDatatable(tbl,tbl_url,dataColumn,[],0);
                }

                return data.status;
        	}).fail(function(xhr, textStatus, errorThrown) {
        		msg(errorThrown,'error');
        	});
        } else {
            swal("Cancelled", "Your data is safe and not deleted.");
        }
    });
}

/**
 * Datatables
 * @param  {[String]} tbl_id       [description]
 * @param  {[String]} Url          [description]
 * @param  {[Array]} dataColumn   Data
 * @param  {Array}  aoColumnDefs  Define css styles per td
 * @param  {Number} inOrder       Define what column will in descending Order
 * @param  {Array}  unOrderable   Define what columns will not be orderable
 * @param  {Array}  unSearchable  Define what columns will not be searchable
 * @return {[Datable]}            [description]
 */
function getDatatable(tbl_id,Url,dataColumn,aoColumnDefs,inOrder,unOrderable,unSearchable) {
    var table = $('#'+tbl_id);

    table.dataTable().fnClearTable();
    table.dataTable().fnDestroy();
    table.dataTable({
        processing: true,
        serverSide: true,
        ajax: Url,
        deferRender: true,
        columns: dataColumn,
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
        // bStateSave: true,
        aoColumnDefs: aoColumnDefs,
        lengthMenu: [
            [5, 10, 15, 20, -1],
            [5, 10, 15, 20, "All"]
        ],
        pageLength: 10,
        columnDefs: [{
            orderable: false,
            targets: unOrderable
        }, {
        	searchable: false,
            targets: unSearchable
        }],
        order: [
            [inOrder, "desc"]
        ]
    });

    var tableWrapper = jQuery('#'+tbl_id+'_wrapper');

    table.find('.group-checkable').change(function () {
        var set = jQuery(this).attr("data-set");
        var checked = jQuery(this).is(":checked");
        jQuery(set).each(function () {
            if (checked) {
                $(this).prop("checked", true);
                $(this).parents('tr').addClass("active");
            } else {
                $(this).prop("checked", false);
                $(this).parents('tr').removeClass("active");
            }
        });
        jQuery.uniform.update(set);
    });

    table.on('change', 'tbody tr .checkboxes', function () {
        $(this).parents('tr').toggleClass("active");
    });
}

function checkAllCheckboxesInTable(tblID, checkAllClass, checkItemClass, deleteButtonID) {
    $(checkAllClass).on('change', function (e) {
        $('input:checkbox' + checkItemClass).not(this).prop('checked', this.checked);

        var checked = 0;
        var table = $(tblID).DataTable();

        for (var x = 0; x < table.context[0].aoData.length; x++) {
            var aoData = table.context[0].aoData[x];
            if (aoData.anCells !== null && aoData.anCells[0].firstChild.checked == true) {
                checked++;
            }
        }

        if (checked > 0) {
            $(deleteButtonID).prop('disabled', false);
        } else {
            $(deleteButtonID).prop('disabled', true);
        }
    });
}

function getDivisionCode(el) {
    var opt = "<option value=''></option>";
    $(el).html(opt);
    $.ajax({
        url: '../../../../helpers/div-code',
        type: 'GET',
        dataType: 'JSON',
        data: {_token: token},
    }).done(function(data, textStatus, xhr) {
        $.each(data, function(i, x) {
            opt = "<option value='"+x.id+"'>"+x.div_code+"</option>";
            $(el).append(opt);
        });
    }).fail(function(xhr, textStatus, errorThrown) {
        console.log(errorThrown);
    });
}

function get_dropdown_items_by_id(id,el) {
    var opt = "<option value=''></option>";
    $(el).html(opt);
    $.ajax({
        url: '../../../../helpers/dropdown-item-id',
        type: 'GET',
        dataType: 'JSON',
        data: {_token: token, id:id},
    }).done(function(data, textStatus, xhr) {
        $.each(data, function(i, x) {
            opt = "<option value='"+x.dropdown_item+"'>"+x.dropdown_item+"</option>";
            $(el).append(opt);
        });
    }).fail(function(xhr, textStatus, errorThrown) {
        console.log("error");
    });
}

function get_dropdown_items_by_name(name,el) {
    var opt = "<option value=''></option>";
    $(el).html(opt);
    $.ajax({
        url: '../../../../helpers/dropdown-item-name',
        type: 'GET',
        dataType: 'JSON',
        data: {_token: token, name:name},
    }).done(function(data, textStatus, xhr) {
        $.each(data, function(i, x) {
            opt = "<option value='"+x.dropdown_item+"'>"+x.dropdown_item+"</option>";
            $(el).append(opt);
        });
    }).fail(function(xhr, textStatus, errorThrown) {
        console.log("error");
    });
}

function get_user_type(el) {
    var opt = "<option value=''></option>";
    $(el).html(opt);
    $.ajax({
        url: '../../../../helpers/user-type',
        type: 'GET',
        dataType: 'JSON',
        data: {_token: token},
    }).done(function(data, textStatus, xhr) {
        $.each(data, function(i, x) {
            opt = "<option value='"+x.id+"'>"+x.description+"</option>";
            $(el).append(opt);
        });
    }).fail(function(xhr, textStatus, errorThrown) {
        console.log("error");
    });
}

function get_leader(el) {
    var opt = "<option value=''></option>";
    $(el).html(opt);
    $.ajax({
        url: '../../../../helpers/leader',
        type: 'GET',
        dataType: 'JSON',
        data: {_token: token},
    }).done(function(data, textStatus, xhr) {
        $.each(data, function(i, x) {
            opt = "<option value='"+x.name+"'>"+x.name+"</option>";
            $(el).append(opt);
        });
    }).fail(function(xhr, textStatus, errorThrown) {
        console.log("error");
    });
}

function ellipsis(string,string_count){
    if (string.length > string_count)
        return string.substring(0,string_count)+'...';
    else
        return string;
};



function getAuditTrailData() {
    $.ajax({
        url: getAuditTrailDataURL,
        type: 'GET',
        dataType: 'JSON',
    }).done(function(data, textStatus, xhr) {
        audit_arr = [];
        audit_arr = data;
        makeAuditTrailTable(audit_arr);
    }).fail(function(xhr, textStatus, errorThrown) {
        console.log(xhr+' '+errorThrown);
    });
}

function makeAuditTrailTable(arr) {
    $('#tbl_audit').dataTable().fnClearTable();
    $('#tbl_audit').dataTable().fnDestroy();
    $('#tbl_audit').dataTable({
        data: arr,
        bLengthChange : false,
        bDestroy: true,
        order: [[ 0, "desc" ]],
        columns: [
            { data: 'id', orderable: false},
            { data: 'module', orderable: false},
            { data: 'action', orderable: false},
            { data: 'user', orderable: false},
            { data: 'created_at', orderable: false}
        ]
    });
}

function getSelectedText(elementId) {
    var elt = document.getElementById(elementId);

    if (elt.selectedIndex == -1)
        return null;

    return elt.options[elt.selectedIndex].text;
}

function check_permission(code) {
    $.ajax({
        url: '../../../../helpers/check-permission',
        type: 'GET',
        dataType: 'JSON',
        data: { code:code }
    }).done(function(data, textStatus, xhr) {
        if (data.access == 2) {
            $('.permission').prop('readonly', true);
            $('.permission-button').prop('disabled', true);
        } else {
            $('.permission').prop('readonly', false);
            $('.permission-button').prop('disabled', false);
        }
    }).fail(function(xhr, textStatus, errorThrown) {
        msg(errorThrown,textStatus);
    }).always(function() {
        console.log("complete");
    });
    
}

function getUnreadNotification() {
    $('#notification_bell').html('<i class="fa fa-bell"></i>');
    $.ajax({
        url: '../../../notification/get-unread',
        type: 'GET',
        dataType: 'JSON'
    }).done(function(data, textStatus, xhr) {
        if (data.noti_count > 0) {
            $('#notification_bell').append('<span class="label label-danger">'+data.noti_count+'</span>');
        }
        notiList(data.noti_list);
    }).fail(function(xhr, textStatus, errorThrown) {
        //msg(errorThrown,textStatus);
    });
}

function readNotification(id,link) {
    $('#notification_bell').html('<i class="fa fa-bell"></i>');
    $.ajax({
        url: '../../../notification/read',
        type: 'POST',
        dataType: 'JSON',
        data: {
            _token: $('meta[name=csrf-token]').attr('content'),
            id: id
        }
    }).done(function(data, textStatus, xhr) {
        console.log(data);
        if (data.noti_count > 0) {
            $('#notification_bell').append('<span class="label label-danger">'+data.noti_count+'</span>');
        } else {
            $(this).html('<i class="fa fa-bell"></i>');
        }

        if (link !== undefined) {
            window.location.href=link;
        }
    }).fail(function(xhr, textStatus, errorThrown) {
        msg(errorThrown,textStatus);
    });
}

function notiList(data) {
    var notilist = '';
    $('#notification_list_header').html(notilist);

    notilist += '<ul class="menu inner-content-div">';

    $.each(data, function(i, x) {

        notilist += '<li data-link="'+x.link+'" data-id="'+x.id+'" class="view_notification">'+
                        '<a href="javascript:;">'+
                            '<div class="pull-left">'+
                                '<img src="../../../'+x.photo+'" class="rounded-circle" alt="User Image">'+
                            '</div>'+
                            '<div class="mail-contnet">'+
                                '<h4>'+
                                    x.title+
                                    '<small><i class="fa fa-clock-o"></i>'+timeago().format(x.created_at)+'</small>'+
                                '</h4>'+
                                '<span>'+ellipsis(x.content,30)+'</span>'+
                            '</div>'+
                        '</a>'+
                    '</li>';
    });
    notilist += '</ul>';
    $('#notification_list_header').html(notilist);
}

function getISO(elem) {
    var options = '<option value=""></option>';
    $(elem).html(options);
    $.ajax({
        url: '../../../helpers/iso',
        type: 'GET',
        dataType: 'JSON',
        data: {
            _token: token
        },
    }).done(function(data, textStatus, xhr) {
        $.each(data, function(i, x) {
            options = '<option value="'+x.iso_code+'">'+x.iso_name+'</option>';
            $(elem).append(options);
        });
    }).fail(function(xhr, textStatus, errorThrown) {
        msg(errorThrown,textStatus);
    });
}
function autoComplete(id,url,text) {
	$.ajax({
		url: url,
		dataType: "JSON",
		type: "GET",
	}).done(function (data, txtStatus, xhr) {
        var operators = [];
        var val = "x."+text;
		$.each(data, function (i, x) {
			operators[i] = eval(val);
		});
		$(id).autocomplete({
			source: operators
		});
	}).fail(function (xhr, txtStatus, errorThrown) {
		console.log(errorThrown);
	});
}