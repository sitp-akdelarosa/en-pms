var dataColumn = [
    {data: function(data) {
        return '<input type="checkbox" class="table-checkbox check_item" value="'+data.id+'">';
    }, name: 'id', orderable: false, searchable: false},
    {data: 'action', name: 'action', orderable: false, searchable: false},
    {data: 'operator_id', name: 'operator_id'},
    {data: 'firstname', name: 'firstname'},
    {data: 'lastname', name: 'lastname'},
    {data: 'created_at', name: 'created_at'}
];

$( function() {
    getDatatable('tbl_operator',getOutputsURL,dataColumn,[],5);
    init();
    checkAllCheckboxesInTable('#tbl_operator','.check_all','.check_item');

    $('#btn_clear').on('click', function() {
        clear();
        $('#btn_save').removeClass('bg-green');
        $('#btn_save').addClass('bg-blue');
        $('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
    });

    //Add and save update
    $( function() {
        $("#frm_operator").on('submit',function(e){
            e.preventDefault();
            var form_action = $(this).attr("action");
            $.ajax({
                dataType: 'json',
                type:'POST',
                url: form_action,
                data:  $(this).serialize(),
            }).done(function(data, textStatus, xhr){
                if(data.status == 'success'){
                    getDatatable('tbl_operator',getOutputsURL,dataColumn,[],0);
                    $('#btn_save').removeClass('bg-green');
                    $('#btn_save').addClass('bg-blue');
                    $('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');

                    clear();
                }

                msg(data.msg,data.status);
            }).fail( function(xhr, textStatus, errorThrown) {
                var errors = xhr.responseJSON.errors;
                showErrors(errors);
            });
        });
    });

    //Edit table
    $('#tbl_operator').on('click', '.btn_edit', function(e) {
        $('#operator_id').val($(this).attr('data-operator_id'));
        $('#id').val($(this).attr('data-id'));
        $('#firstname').val($(this).attr('data-firstname'));
        $('#lastname').val($(this).attr('data-lastname'));
        $('#btn_save').removeClass('bg-blue');
        $('#btn_save').addClass('bg-green');
        $('#btn_save').html('<i class="fa fa-check"></i> Update');
    });

    //Delete Multiple data
    $('#btn_delete').on('click', function() {
            delete_set('.check_item',deleteOM);
    });

});

function init() {
    check_permission(code_permission, function(output) {
        if (output == 1) {}
    });
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