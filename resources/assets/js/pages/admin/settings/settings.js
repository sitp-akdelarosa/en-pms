$( function() {
    init();
    getISO();
    checkAllCheckboxesInTable('#tbl_iso','.check_all','.check_item');

    $('.custom-file-input').on('change', function() {
       let fileName = $(this).val().split('\\').pop();
       $(this).next('.custom-file-label').addClass("selected").html(fileName);
       readPhotoURL(this);
    });

    $('#btn_clear').on('click', function() {
        clear();
    });

    $("#frm_iso").on('submit',function(e){
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
        }).done(function(data, textStatus, xhr){
            readPhotoURL("");
            msg("Successful" , "success");
            clear();
            getISO();
            $('#btn_save').removeClass('bg-green');
            $('#btn_save').addClass('bg-blue');
            $('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
        }).fail(function(xhr, textStatus, errorThrown) {
            var errors = xhr.responseJSON.errors;
            showErrors(errors);
        }).always(function() {
            $('.loadingOverlay').hide();
        });
    });

    //Edit table
    $('#tbl_iso').on('click', '.btn_edit', function(e) {
        $('#id').val($(this).attr('data-id'));
        $('#iso_name').val($(this).attr('data-iso_name'));
        $('#iso_code').val($(this).attr('data-iso_code'));
        $('#whs_photo').attr("src",'../../../'+$(this).attr('data-photo'));
        $('#btn_save').removeClass('bg-blue');
        $('#btn_save').addClass('bg-green');
        $('#btn_save').html('<i class="fa fa-check"></i> Update');
    });

    //Delete Multiple data
    $('#btn_delete').on('click', function() {
        delete_set('.check_item',deleteISO);
    });

});

function getISO() {
    $.ajax({
        url: getISOTable,
        type: 'GET',
        dataType: 'JSON',
        data: {
            _token: token
        },
    }).done(function(data, textStatus, xhr){
        makeISOdatatable(data);
    }).fail(function(xhr, textStatus, errorThrown) {
        msg(errorThrown,textStatus);
    });
}

function init() {
    if (permission_access == '2' || permission_access == 2) {
        $('.permission').prop('readonly', true);
        $('.permission-button').prop('disabled', true);
    } else {
        $('.permission').prop('readonly', false);
        $('.permission-button').prop('disabled', false);
    }

}

function makeISOdatatable(arr) {
    $('#tbl_iso').dataTable().fnClearTable();
    $('#tbl_iso').dataTable().fnDestroy();
    $('#tbl_iso').dataTable({
        data: arr,
        order: [[2,'asc']],
        columns: [
            { data: function(x) {
                return '<input type="checkbox" class="table-checkbox check_item" value="'+x.id+'">';
            }, searchable: false, orderable: false },
            { data: function(x) {
                return '<button class="btn btn-sm bg-blue btn_edit permission-button" data-id="'+x.id+'" '+
                            'data-iso_code="'+x.iso_code+'" data-iso_name="'+x.iso_name+'" '+
                            'data-photo="'+x.photo+'">'+
                            '<i class="fa fa-edit"></i>'+
                        '</button>';
            } },
            { data: 'iso_name' },
            { data: 'iso_code' },
        ]
    });
}


//Multiple Delete 
function delete_set(checkboxClass,deleteOM) {
    var chkArray = [];
    $(checkboxClass+":checked").each(function() {
        chkArray.push($(this).val());
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
                    url: deleteISO,
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        _token:token,
                        id: chkArray
                    },
                }).done(function(data, textStatus, xhr) {
                    msg(data.msg,data.status)
                    getISO();
                }).fail(function(xhr, textStatus, errorThrown) {
                    msg(errorThrown,'error');
                });
            } else {
                swal("Cancelled", "Your data is safe and not deleted.");
            }
        });
    } else {
        msg("Please select at least 1 item." ,"warning");
    }

    $('.check_all').prop('checked',false);
    clear();
    $('#btn_save').removeClass('bg-green');
    $('#btn_save').addClass('bg-blue');
    $('#btn_save').html('<i class="fa fa-plus"></i> Add');

    getISO();
}

function clear() {
    $('.clear').val('');
    $('.custom-file-input').next('.custom-file-label').addClass("selected").html("Select a photo..."); 
    $('.photo').attr('src', '../images/default_upload_photo.jpg');
    $('#btn_save').removeClass('bg-green');
    $('#btn_save').addClass('bg-blue');
    $('#btn_save').html('<i class="fa fa-plus"></i> Add');
}