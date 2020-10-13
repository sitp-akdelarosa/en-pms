var dataColumn = [
    { data: function(data) {
        return "<input type='checkbox' class='table-checkbox check_item_prod_sum'"+
                    "id='prod_sum_chk_"+data.id+"'"+
                    "data-id='"+data.id+"'"+
                    "data-sc_no='"+data.sc_no+"'"+
                    "data-prod_code='"+data.prod_code+"'"+
                    "data-description='"+data.description+"'"+
                    "data-quantity='"+data.quantity+"'"+
                    "data-status='"+data.status+"'"+
                    "data-sched_qty='"+data.sched_qty+"'>";
    }, name: 'id', 'searchable': false, 'orderable': false },
    { data: 'sc_no', name: 'ps.sc_no' },
    { data: 'prod_code', name: 'ps.prod_code' },
    { data: 'description', name: 'ps.description' },
    { data: 'quantity', name: 'ps.quantity' },
    { data: 'sched_qty', name: 'ps.sched_qty' },
    { data: 'po', name: 'ps.po' },
    { data: 'status', name: 'ps.status' },
    { data: 'date_upload', name: 'ps.date_upload' },
];
var EditMode = false;
var schedmode = false;
var MainData;
var joDetails_arr = [];
var count;
var travel_Sheet = [];
var p_pcode = "";
var msg_non_standard;

$(function () {
    initializePage();
    autoComplete("#jono", getjosuggest, "jo_no");

    $('#tbl_prod_sum_body').on('change', '.check_item_prod_sum', function(e) {
        e.preventDefault();
        if (schedmode == false) {
                if ($(this).is(":checked")) {
                    var Exist = 0;
                    var sc_no = $(this).attr('data-sc_no');
                    var prod_code = $(this).attr('data-prod_code');
                    var quantity = $(this).attr('data-quantity');
                    $.each(joDetails_arr, function(i, x) {
                        if(x.sc_no == sc_no && x.prod_code == prod_code && x.quantity == quantity ){ 
                            Exist = "The item already on the table";
                        }
                        if(EditMode == true && x.prod_code != prod_code){
                            Exist = "Just choose same product code of "+x.prod_code;
                        }
                    });
                    if(Exist == 0){ 
                    $('#no_data').remove();
                        count = joDetails_arr.length;
                        joDetails_arr.push({
                            count: count,
                            dataid: $(this).attr('data-id'),
                            id: count,
                            sc_no: sc_no,
                            prod_code: prod_code,
                            description: $(this).attr('data-description'),
                            quantity:quantity,
                            sched_qty:0,
                            totalsched_qty:$(this).attr('data-sched_qty'),
                            material_used:"",
                            material_heat_no:"",
                            uom: "",
                            lot_no: ""
                        });
                    }else{
                        msg(Exist,'warning');
                        $(this).prop('checked',false);
                    }
                } else {
                    var count = 0;
                    var id = $(this).attr('data-id');
                    $.each(joDetails_arr, function(i, x) {
                        if(x.dataid == id  ){ 
                           joDetails_arr.splice(count,1);
                           return false;
                        }
                        count++;
                    });
                    var counts = 0;
                    $.each(joDetails_arr, function(i, x) {
                         joDetails_arr.push({
                                    count: counts,
                                    dataid: x.dataid,
                                    id: x.id,
                                    sc_no: x.sc_no,
                                    prod_code: x.prod_code,
                                    description: x.description,
                                    quantity:x.quantity,
                                    sched_qty:x.sched_qty,
                                    totalsched_qty:x.totalsched_qty,
                                    material_used:x.material_used,
                                    material_heat_no:x.material_heat_no,
                                    uom: x.uom,
                                    lot_no:x.lot_no
                        });
                        counts++;
                    });
                    while (counts != 0){
                        joDetails_arr.splice(0,1);
                        counts--;
                    }

                    if ($('#tbl_jo_details_body > tr').length < 1) {
                        $('#tbl_jo_details_body').html('<tr id="no_data">'+
                                                        '<td colspan="9" class="text-center">No data available.</td>'+
                                                    '</tr>');
                    }
                    if (joDetails_arr.length < 1) {
                        $('#btn_save').hide();
                        $('#btn_edit').show();
                        $('#btn_cancel').hide();
                    }
                }
            makeJODetailsList(joDetails_arr);
            getMaterialHeatNo($('#rmw_no').val());
        }
        else{
            msg("Not Allowed!!!","warning");
            $('.check_item_prod_sum').prop('checked',false);
            joDetails_arr = [];
        }
        console.log(joDetails_arr);
    });

    $('#jono').on('change', function() {
        $('.check_item_prod_sum').prop('checked',false);
        if($(this).val() == ""){
            $(".check_item_prod_sum").prop("disabled", true);
            clear();
        }else{
            $(".check_item_prod_sum").prop("disabled", false);  
        }

        getTables();
    });

    // $("#jono").on('keyup', getJO);
    // $("#jono").on('keyup', getTables);
    $("#status").on('change', changestatus);
       

    $('#tbl_jo_details_body').on('click', '.remove_jo_details', function() {
        var count = $(this).attr('data-count');
        var id = $(this).attr('data-dataid');
        joDetails_arr.splice(count,1);
        $('#prod_sum_chk_'+id).prop('checked',false);
        var counts = 0;
        $.each(joDetails_arr, function(i, x) {
             joDetails_arr.push({
                        count: counts,
                        dataid: x.dataid,
                        id: x.id,
                        sc_no: x.sc_no,
                        prod_code: x.prod_code,
                        description: x.description,
                        quantity:x.quantity,
                        sched_qty:x.sched_qty,
                        totalsched_qty:x.totalsched_qty,
                        material_used:x.material_used,
                        material_heat_no:x.material_heat_no,
                        lot_no:x.lot_no

            });
            counts++;
        });
        while (counts != 0){
            joDetails_arr.splice(0,1);
            counts--;
        }
        getMaterialHeatNo($('#rmw_no').val());
        makeJODetailsList(joDetails_arr);
        if ($('#tbl_jo_details_body > tr').length < 1) {
            $('#tbl_jo_details_body').html('<tr id="no_data">'+
                                            '<td colspan="10" class="text-center">No data selected.</td>'+
                                        '</tr>');
        }
        if (joDetails_arr.length < 1 && $('#jono').val() == '') {
            $('#btn_save').hide();
            $('#btn_edit').show();
            $('#btn_cancel').hide();
        }
    });

    $('#tbl_jo_details_body').on('change', '.material_heat_no', function() {
        $('.loadingOverlay').show();
        p_pcode = $(this).attr('data-pcode');

        var rmw_issued_qty = $(this).find('option:selected').attr('data-rmw_issued_qty');
        var uom = $(this).find('option:selected').attr('data-uom');
        var material_heat_no = $(this).val();
        
        if ($('#is_same').is(':checked')) {
            
            $.each(joDetails_arr, function(i, x) {
                $('#material_heat_no_'+x.count).val(material_heat_no);
                $('#rmw_issued_qty_'+x.count).val(rmw_issued_qty);
                $('#uom_' + x.count).val(uom);
                getMaterialused(material_heat_no,x.count);

                if ($('#sched_qty_'+x.count).val() > rmw_issued_qty) {
                    error = "Over issuance";
                    $('.material_heat_no_select').addClass('is-invalid');
                    $('.material_heat_no_feedback').addClass('invalid-feedback');
                    $('.material_heat_no_feedback').html(error);
                } else {
                    $('.material_heat_no_select').removeClass('is-invalid');
                    $('.material_heat_no_feedback').removeClass('invalid-feedback');
                    $('.material_heat_no_feedback').html('');
                }

                
            });
        } else {
            $('#rmw_issued_qty_'+$(this).attr('data-count')).val(rmw_issued_qty);
            getMaterialused($(this).val(),$(this).attr('data-count'));

            if ($('#sched_qty_'+$(this).attr('data-count')).val() > rmw_issued_qty) {
                error = "Over issuance";
                $('#material_heat_no_'+$(this).attr('data-count')).addClass('is-invalid');
                $('#material_heat_no_'+$(this).attr('data-count')+'_feedback').addClass('invalid-feedback');
                $('#material_heat_no_'+$(this).attr('data-count')+'_feedback').html(error);
            } else {
                $('#material_heat_no_'+$(this).attr('data-count')).removeClass('is-invalid');
                $('#material_heat_no_'+$(this).attr('data-count')+'_feedback').removeClass('invalid-feedback');
                $('#material_heat_no_'+$(this).attr('data-count')+'_feedback').html('');
            }
                
        }

        var inputs = $(".rmw_issued_qty");
        var total = 0;

        if ($('#is_same').is(':checked')) {
            $('#total_heat_qty').val($('#rmw_issued_qty_'+$(this).attr('data-count')).val()); 
        } else {
            for(var i = 0; i < inputs.length; i++){
                if ($(inputs[i]).val() == '') {
                    total = parseInt(total) + 0;
                } else {
                    total = parseInt(total) + parseInt($(inputs[i]).val());
                }
            }

            if (total === NaN) {
                total = 0;
            }
            $('#total_heat_qty').val(total);
        }

        $('#btn_save').show();
        $('#btn_edit').hide();
        $('#btn_cancel').show();
        $('.loadingOverlay').hide();
    });

    $('#tbl_jo_details_body').on('change', '.material_used', function() {
        var standard_material_used;
        var material_used = $(this).val();
        if ($('#is_same').is(':checked')) {
            var count = $(this).attr('data-count');
            $.each(joDetails_arr, function(i, x) {
                $('#material_used_'+x.count).val(material_used);
            });
        } 
        compareStandardMaterialUsed(count,$(this).attr('data-pcode'),material_used);
       
        $('#btn_save').show();
        $('#btn_edit').hide();
        $('#btn_cancel').show();
    });

    $('#tbl_jo_details_body').on('keyup', '.lot_no', function() {
        if ($('#is_same').is(':checked')) {
            var lot_no = $(this).val();
            var count = $(this).attr('data-count');
            $.each(joDetails_arr, function(i, x) {
                $('#lot_no_'+x.count).val(lot_no);
            });
        }
    });

    $('#tbl_jo_details_body').on('keyup', '.sched_qty', function(event) {
        var inputs = $(".sched_qty");
        var total = 0;

        for(var i = 0; i < inputs.length; i++){
            if ($(inputs[i]).val() == '') {
                total = parseInt(total) + 0;
            } else {
                total = parseInt(total) + parseInt($(inputs[i]).val());
            }
        }

        if (total === NaN) {
            total = 0;
        }
        $('#total_sched_qty').val(total);

        var rmw_issued_qty = parseFloat($('#rmw_issued_qty_'+$(this).attr('data-count')).val());
        var sched_qty = parseFloat($(this).val());
        var pcode = $(this).attr('data-pcode');
        var heat_no = $('#material_heat_no_'+$(this).attr('data-count')).val();

        if ($(this).val() == "") {

        } else {
            if ($('#is_same').is(':checked')) {
                if (total > rmw_issued_qty) {
                    error = "Over issuance";
                    $('.material_heat_no_select').addClass('is-invalid');
                    $('.material_heat_no_feedback').addClass('invalid-feedback');
                    $('.material_heat_no_feedback').html(error);
                } else {
                    sum_sched_qty();
                    $('.material_heat_no_select').removeClass('is-invalid');
                    $('.material_heat_no_feedback').removeClass('invalid-feedback');
                    $('.material_heat_no_feedback').html('');
                }
            } else {
                if (sched_qty > rmw_issued_qty) {
                    error = "Over issuance";
                    $('#material_heat_no_'+$(this).attr('data-count')).addClass('is-invalid');
                    $('#material_heat_no_'+$(this).attr('data-count')+'_feedback').addClass('invalid-feedback');
                    $('#material_heat_no_'+$(this).attr('data-count')+'_feedback').html(error);
                } else {
                    sum_sched_qty();
                    $('#material_heat_no_'+$(this).attr('data-count')).removeClass('is-invalid');
                    $('#material_heat_no_'+$(this).attr('data-count')+'_feedback').removeClass('invalid-feedback');
                    $('#material_heat_no_'+$(this).attr('data-count')+'_feedback').html('');
                }
            }
                
        }
    });

    $('#btn_save').on('click',function() {
        if (joDetails_arr.length  != 0 ) {
            var validate = "NONE";
            valid = validateTable();

            if (parseInt($('#total_sched_qty').val()) > parseInt($('#total_heat_qty').val())) {
                msg("You still have an over issuance." ,"warning");
            } else if (valid == "false") {
                msg("All fields are required.","warning");
            } else if (valid == "true") {
                for (var y = 0; y < joDetails_arr.length; y++) {
                    var quantity = parseInt($('#quantity_'+y).val());
                    var sched_qty = parseInt($('#sched_qty_'+y).val());
                    var totalsched_qty = parseInt($('#totalsched_qty'+y).val()) + sched_qty;
                    if (quantity < sched_qty) {
                        validate = "Some of Sched Qty is greater than quantity!";
                    } else if (quantity < totalsched_qty) {
                        validate = "Some of Total Sched Qty already reach the total quantity!";
                    }
                }

                if(validate == "NONE"){
                    $(".check_item_prod_sum").prop("disabled", false);
                    SaveJODetails();
                } else {
                    swal({
                    title: "Are you sure to save?",
                    text: validate,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#f95454",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No",
                    closeOnConfirm: true,
                    closeOnCancel: false
                    }, function(isConfirm){
                        if (isConfirm) {
                            $(".check_item_prod_sum").prop("disabled", false);
                            SaveJODetails();
                        } else {
                            swal("Cancelled", "Saving production schedule is cancelled.");
                        }
                    });
                }
            } else {
                msg("Please input valid number in Sched Qty." ,"warning");
            }




        } else {
            msg("Please input production schedule." , 'warning');
        }
    });

    $('#btn_cancel').on('click',function() {
        joDetails_arr = [];
        EditMode = false;
        makeJODetailsList(joDetails_arr);
        $('.check_item_prod_sum').prop('checked',false);
        clear();

        $('#btn_save').hide();
        $('#btn_edit').show();
        $('#btn_cancel').hide();

        $('#jono').prop('readonly',true);
        $(".check_item_prod_sum").prop("disabled", false);
    });

    $('#btn_edit').on('click', function() {
        joDetails_arr = [];
        makeJODetailsList(joDetails_arr);
        $('#btn_save').show();
        $('#btn_edit').hide();
        $('#btn_cancel').show();
        $('.check_item_prod_sum').prop('checked',false);
        $('#jono').prop('readonly',false);
        $(".check_item_prod_sum").prop("disabled", true);
    });

    $('#tbl_travel_sheet').on('click', '.cancel_travel_sheet', function() {
       var id = $(this).attr('data-id');
       var jo_no = $(this).attr('data-jo_no');
       var idJTS = $(this).attr('data-idJO');
       
        swal({
        title: "Are you sure to cancel the travel sheet?",
        text: "All of the process in this travel sheet will be cancelled!",
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
                    url: cancelTravelSheetURL,
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        _token:token,
                        id: id,
                        jo_no:jo_no,
                        idJTS:idJTS
                    },
                }).done(function(data, textStatus, xhr) {
                    if(data.status == 'success'){
                        swal("Successful", "The travel sheet has been cancelled.");
                        ProdSummaries(prodSummariesURL);
                        travel_Sheet = [];
                        getTravelSheet();
                    }else{ 
                        swal("Failed", "The travel sheet has been processing of goods.");
                    }
                }).fail(function(xhr, textStatus, errorThrown) {
                    msg(errorThrown,'error');
                });
            } else {
                swal("Cancelled", "The travel sheet is safe and not cancelled.");
            }
        });
    });

    $('#btn_search_withdrawal').on('click', function() {
        getMaterialHeatNo($('#rmw_no').val());
    });

});

function initializePage() {
    check_permission(code_permission, function(output) {
        if (output == 1) {}
    });

    ProdSummaries(prodSummariesURL);
    $('#searchPS').on('click', getDatatablesearch)
    checkAllCheckboxesInTable('.check-all_prod_sum','.check_item');
    makeJODetailsList(joDetails_arr);
    getTravelSheet();

    $('.material_heat_no').prop('disabled', true);

    $('#btn_save').hide();
    $('#btn_edit').show();
    $('#btn_cancel').hide();
}

function changestatus() {
    if($('#status').val() == "")
    {
        $('#status').val("");
        $('#tbl_jo_details').dataTable().fnClearTable();
        $('#tbl_jo_details').dataTable().fnDestroy();
        joDetails_arr = [];
        makeJODetailsList(joDetails_arr);
        var showform = document.getElementById('formbaba').style.display = 'inline';
        schedmode = false;
        joDetails_arr = [];
        $('#sched_qty').attr('disabled',false);
        $('.material_heat_no').attr('disabled',false);
        $('.material_used').attr('disabled',false);
        $('.lot_no').attr('disabled',false);
        $(".remove_jo_details").css("visibility", "visible");
        clear();
    }
    else{
        var hideform = document.getElementById('formbaba').style.display = 'none';
        getTablesAll();
        schedmode = true;
        joDetails_arr = [];
        $('.check_item_prod_sum').prop('checked',false);
    }
}

function ProdSummaries(PRODURL) {
    $('.loadingOverlay').show();
    $.ajax({
        url: PRODURL,
        type: 'GET',
        dataType: 'JSON',
    }).done(function(data, textStatus, xhr) {
        ProdSummariesTable(data);
    }).fail(function(xhr, textStatus, errorThrown) {
        msg(errorThrown,textStatus);
    }).always(function() {
        $('.loadingOverlay').hide();
    });
}

function ProdSummariesTable(arr) {
    $('#tbl_prod_sum').dataTable().fnClearTable();
    $('#tbl_prod_sum').dataTable().fnDestroy();
    $('#tbl_prod_sum').dataTable({
        data: arr,
        processing: true,
        deferRender: true,
        order: [[1,'asc']],
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


        columns: [
            { data: function(data) {
                return "<input type='checkbox' class='table-checkbox check_item_prod_sum'"+
                            "id='prod_sum_chk_"+data.id+"'"+
                            "data-id='"+data.id+"'"+
                            "data-sc_no='"+data.sc_no+"'"+
                            "data-prod_code='"+data.prod_code+"'"+
                            "data-description='"+data.description+"'"+
                            "data-quantity='"+data.quantity+"'"+
                            "data-status='"+data.status+"'"+
                            "data-sched_qty='"+data.sched_qty+"'>";
            }, name: 'id', 'searchable': false, 'orderable': false },
            { data: 'sc_no', name: 'ps.sc_no' },
            { data: 'prod_code', name: 'ps.prod_code' },
            { data: 'description', name: 'ps.description' },
            { data: 'quantity', name: 'ps.quantity' },
            { data: 'sched_qty', name: 'ps.sched_qty' },
            { data: 'po', name: 'ps.po' },
            { data: 'status', name: 'ps.status' },
            { data: 'date_upload', name: 'ps.date_upload' }
        ]
    });
}

function getDatatablesearch() {
    if($('#from').val() != "" && $('#to').val() != ""){
        ProdSummaries(prodSummariesURL + '?fromvalue='+ $('#from').val()+'&tovalue='+ $('#to').val());
        // getDatatable('tbl_prod_sum',prodSummariesURL + '?fromvalue='+ $('#from').val()+'&tovalue='+ $('#to').val(),dataColumn,[],0);
    }
    else if($('#from').val() != ""){
        ProdSummaries(prodSummariesURL + '?fromvalue='+ $('#from').val());
        // getDatatable('tbl_prod_sum',prodSummariesURL + '?fromvalue='+ $('#from').val(),dataColumn,[],0);
    }
    else{
        msg("From Input is required","warning");
    }
}

function makeJODetailsList(arr) {
    var data = arr.sort(function(a,b) {
            return ((a.prod_code < b.prod_code) ? -1 : ((a.prod_code > b.prod_code) ? 1 : 0));
    });
    $('#tbl_jo_details').dataTable().fnClearTable();
    $('#tbl_jo_details').dataTable().fnDestroy();
    $('#tbl_jo_details').dataTable({
        data: data,
        bLengthChange : false,
        paging: false,
        order: [[1,'asc']],
        columns: [

            { data: function(x) {
                return "<span class='remove_jo_details' data-count='"+x.count+"' data-id='"+x.id+"' data-dataid='"+x.dataid+"'>"+
                            "<i class='text-red fa fa-times'></i>"+
                        "</span>"+
                "<input type='hidden' name='dataid[]' value='"+x.dataid+"'>";
            }, searchable: false, orderable: false },

            { data: function(x) {
                return x.sc_no+"<input type='hidden' class='form-control form-control-sm' name='sc_no[]' value='"+x.sc_no+"'>";
            }, searchable: true, orderable: false },

            { data: function(x) {
                return x.prod_code+"<input type='hidden' class='form-control form-control-sm' name='prod_code[]' value='"+x.prod_code+"'>";
            }, searchable: true, orderable: false },

            { data: function(x) {
                return x.description+"<input type='hidden' class='form-control form-control-sm' name='description[]' value='"+x.description+"'>";
            }, searchable: true, orderable: false },

            { data: function(x) {
                return x.quantity+"<input type='hidden' id='quantity_"+x.count+"' class='form-control form-control-sm' name='quantity[]' value='"+x.quantity+"'>";
            }, searchable: true, orderable: false },

            { data: function(x) {
                return "<select id='material_heat_no_"+x.count+"' data-pcode='"+x.prod_code+"' data-count='"+x.count+"' class='form-control form-control-sm material_heat_no material_heat_no_select' name='material_heat_no[]'>"+
                            "<option class='"+x.material_heat_no+"'>"+x.material_heat_no+"</option>"+
                        "</select><div class='material_heat_no_feedback' id='material_heat_no_"+x.count+"_feedback'></div>"+
                        "<input type='text' id='rmw_issued_qty_"+x.count+"' data-pcode='"+x.prod_code+"' data-count='"+x.count+"'  class='form-control form-control-sm rmw_issued_qty' readonly>";
            }, searchable: false, orderable: false },

            {
                data: function (x) {
                    return "<input type='text' id='uom_" + x.count + "' class='form-control form-control-sm' style='width:50%;' name='uom[]' value='" + x.uom + "' readonly>";
                }, searchable: true, orderable: false
            },

            { data: function(x) {
                return "<select id='material_used_"+x.count+"' data-count='"+x.count+"' data-pcode='"+x.prod_code+"' class='form-control form-control-sm material_used' name='material_used[]'>"+
                           "<option class='"+x.material_used+"'>"+x.material_used+"</option>"+
                        "</select><div id='material_used_"+x.count+"_feedback'></div>";
            }, searchable: false, orderable: false },

            { data: function(x) {
                var totalsched_qty = (x.totalsched_qty == null || x.totalsched_qty == 'null')? 0 : x.totalsched_qty;
                return "<input type='text' id='lot_no_"+x.count+"' data-count='"+x.count+"' class='form-control form-control-sm lot_no' name='lot_no[]' value='"+x.lot_no+"'>"+
                "<input type='hidden' id='totalsched_qty"+x.count+"' name='totalsched_qty[]' value='"+totalsched_qty+"'>";
            }, searchable: false, orderable: false },

            { data: function(x) {
                return "<input type='number' step='1' id='sched_qty_"+x.count+"' data-pcode='"+x.prod_code+"' data-count='"+x.count+"' class='form-control form-control-sm sched_qty' min='0' name='sched_qty[]' value='"+x.sched_qty+"'>";
            }, searchable: false, orderable: false },

            
        ],
        columnDefs: [
            { "width": "5%", "targets": 0 },
            { "width": "5%", "targets": 1 },
            { "width": "10%", "targets": 2 },
            { "width": "15%", "targets": 3 },
            { "width": "10%", "targets": 4 },
            { "width": "15%", "targets": 5 },
            { "width": "8%", "targets": 6 },
            { "width": "17%", "targets": 7 },
            { "width": "10%", "targets": 8 },
            { "width": "10%", "targets": 9 }
            
        ],
        initCompelete: function() {
            $('.material_heat_no').prop('disabled', true);
        }
    });
}

function getMaterialHeatNo(withdrawal_slip_no) {
    var op = "<option value=''></option>";
    $.each(joDetails_arr, function(i, x) {
        $('.material_heat_no').html(op);
    });
    $.ajax({
        url: getMaterialHeatNoURL,
        type: 'GET',
        dataType: 'JSON',
        data: { _token: token, rmw_no: withdrawal_slip_no, state: 'add'},
        success:function(returnData){

            var materials = returnData.materials;

            if (materials.length > 0) {
                if ($('#rmw_no').val() == "" && returnData.msg == "") {
                    $('.material_heat_no').prop('disabled', true);
                    $('#rmw_no').prop('readonly', false);
                }
                if ($('#rmw_no').val() !== "" && returnData.msg == "") {
                    $('#rmw_no').prop('readonly', true);
                    $('.material_heat_no').prop('disabled', false);
                }
                
            } else {
                $('.material_heat_no').prop('disabled', true);
                $('#rmw_no').prop('readonly', false);

                if ($('#rmw_no').val() !== "" && returnData.msg !== "") {
                    $('#rmw_no').prop('readonly', false);
                    $('.material_heat_no').prop('disabled', true);

                    msg(returnData.msg, returnData.status);
                }
            }

            for(var y = 0; y < joDetails_arr.length; y++){
                for(var x = 0; x < materials.length; x++){
                    op += "<option value='" + materials[x].heat_no + "' data-rmw_issued_qty='" + materials[x].rmw_issued_qty + "' data-uom='" + materials[x].uom +"'>" + materials[x].heat_no+"</option>";
                }
                
                $('#material_heat_no_' + y).html(op);
                var materialval = (joDetails_arr[y].material_heat_no != "")?joDetails_arr[y].material_heat_no:"";
                $('#material_heat_no_'+y).val(materialval);
                getMaterialused(materialval,y);
                op = "<option value=''></option>";
            }   
        },
        error: function (xhr, textStatus, thrownError) {
            msg(thrownError,textStatus);
        }
    });
}

function getMaterialHeatNoEdit() {
    var op = "<option value=''></option>";
    $.ajax({
        url: getMaterialHeatNoURL,
        type: 'GET',
        dataType: 'JSON',
        data: { _token: token, rmw_no: $('#rmw_no').val(), state: 'edit'},
        success:function(returnData){
            // $.each(MainData, function(i, x) {
            //     console.log(x);
            // });

            var materials = returnData.materials;

            console.log(MainData);
            for(var y = 0; y < MainData.length; y++){
                //op = "<option value=''></option>";
                //$('#material_heat_no_'+y).val(MainData[y].material_heat_no);

                for(var x = 0; x < materials.length; x++){

                    if (MainData[y].material_heat_no == materials[x].heat_no) {
                        op += "<option value='" + materials[x].heat_no + "' data-uom='" + materials[x].uom +"' data-rmw_issued_qty='"+materials[x].rmw_issued_qty+"' selected>"+materials[x].heat_no+"</option>";
                        
                    } else {
                        op += "<option value='" + materials[x].heat_no + "' data-uom='" + materials[x].uom +"' data-rmw_issued_qty='"+materials[x].rmw_issued_qty+"'>"+materials[x].heat_no+"</option>";
                        
                    }
                }

                $('#material_heat_no_'+y).html(op);

                var rmw_issued_qty = $('#material_heat_no_'+y).find('option:selected').attr('data-rmw_issued_qty');
                var uom = $('#material_heat_no_' + y).find('option:selected').attr('data-uom');
                $('#rmw_issued_qty_'+y).val(rmw_issued_qty);
                $('#uom_' + y).val(uom);
                getMaterialusedEdit(MainData[y].material_heat_no,y);
            }
        },
        error: function (xhr, textStatus, thrownError) {
            msg(thrownError,textStatus);
        }
    });
}

function getMaterialused(heat_no,count) {
    var op = "<option value=''></option>";
    $.ajax({
        url: getMaterialUsedURL,
        type: 'GET',
        dataType: 'JSON',
        data: {_token: token, heat_no: heat_no},
    }).done(function(data, textStatus, xhr) {
        if (joDetails_arr.length > 0) {
            if ($('#is_same').is(':checked')) {
                // $.each(joDetails_arr, function(i, x) {
                    $('#material_used_'+count).html(op);
                // });
                // $.each(joDetails_arr, function(index, val) {
                    $.each(data, function(i, x) {
                        compareStandardMaterialUsed(count,p_pcode,x); //x = description,schedule,size
                        op = "<option value='"+x.description+"' selected>"+x.description+"</option>";
                        $('#material_used_'+count).append(op);

                    });
                // });
            }else{
                $.each(joDetails_arr, function(i, x) {
                    $('#material_used_'+count).html(op);
                });
                $.each(data, function(i, x) {
                    compareStandardMaterialUsed(count,p_pcode,x); //x = description,schedule,size
                    op = "<option value='"+x.description+"' selected>"+x.description+"</option>";
                    $('#material_used_'+count).append(op);
                });
            }

        } else {
            $('#material_used_'+count).html(op);
            $.each(data, function(i, x) {
                compareStandardMaterialUsed(count,p_pcode,x); //x = description,schedule,size
                op = "<option value='"+x.description+"' selected>"+x.description+"</option>";
                $('#material_used_'+count).append(op);
            });
        }
    }).fail(function(xhr, textStatus, errorThrown) {
        msg(errorThrown,textStatus);
    });
}

function getMaterialusedEdit(heat_no,count = 0) {
    var op = "<option value=''></option>";
    $.ajax({
        url: getMaterialUsedURL,
        type: 'GET',
        dataType: 'JSON',
        data: {_token: token, heat_no: heat_no},
        success:function(returnData){
            for(var x = 0; x < returnData.length; x++){
                op = "<option value='"+returnData[x].description+"'>"+returnData[x].description+"</option>";
                $('#material_used_'+$(this).attr('data-count')).append(op);
                compareStandardMaterialUsed($(this).attr('data-count'),$(this).attr('data-p_code'),x); //x = description,schedule,size
            }
        },
    });
}

function compareStandardMaterialUsed(count,product_code,material_used) {
    $.ajax({
        url: getStandardMaterialUsedURL,
        type: 'GET',
        dataType: 'JSON',
        data: {
            _token: token,
            product_code: product_code
        },
    }).done(function(data, textStatus, xhr) {
        var standard_material_used = data.standard_material_used;
        var selected_mat_used = "";
        //console.log(standard_material_used +" = "+material_used);

        if ((typeof material_used) == 'string') {
            selected_mat_used = material_used;
        } else {
            if (standard_material_used == material_used.size) {
                selected_mat_used = material_used.size
            } else if (standard_material_used = material_used.schedule) {
                selected_mat_used = material_used.schedule
            } else {
                selected_mat_used = material_used.description
            }
        }

        if (selected_mat_used !== null || selected_mat_used !== '') {
            if (standard_material_used != selected_mat_used) {
                var error = "This is not the Product's Standard Material";

                $('#material_used_'+count).addClass('is-invalid');
                $('#material_used_'+count+'_feedback').addClass('invalid-feedback');
                $('#material_used_'+count+'_feedback').html(error);
            }else{
                $('#material_used_'+count).removeClass('is-invalid');
                $('#material_used_'+count+'_feedback').removeClass('invalid-feedback');
                $('#material_used_'+count+'_feedback').html('');
            }
        } else {
            //msg("Please assign a standard material for "+product_code, 'warning');
        }
            
    }).fail(function(xhr, textStatus, errorThrown) {
        msg(errorThrown,textStatus);
    });
}

function sum_sched_qty() {
    var rows = $('#tbl_jo_details')["0"].rows.length - 1;
    var sum = 0;
    for(var x=0;x<rows;x++){
        if($('#tbl_jo_details')["0"].children[1].children[x].cells[8].children["0"].value != ""){
            sum += parseFloat($('#tbl_jo_details')["0"].children[1].children[x].cells[8].children["0"].value);
        }
    }
    $('#total_sched_qty').val(sum);
}

function SaveJODetails() {
    $('.loadingOverlay').show();
    var rows = $('#tbl_jo_details')["0"].rows.length - 1;
    var ss = "";
    if (EditMode) {
        ss = $('#jono').val();
    }

    $.ajax({
        url: savejodetailsURL,
        type: 'POST',
        dataType: 'JSON',
        data: {
            _token: token,
            id: $('input[name="dataid[]"]').map(function(){return $(this).val();}).get(),
            sc_no: $('input[name="sc_no[]"]').map(function(){return $(this).val();}).get(),
            prod_code: $('input[name="prod_code[]"]').map(function(){return $(this).val();}).get(),
            description: $('input[name="description[]"]').map(function(){return $(this).val();}).get(),
            quantity: $('input[name="quantity[]"]').map(function(){return $(this).val();}).get(),
            sched_qty: $('input[name="sched_qty[]"]').map(function(){return $(this).val();}).get(),
            material_used: $('.material_used').map(function(){return $(this).val();}).get(),
            material_heat_no: $('.material_heat_no').map(function(){return $(this).val();}).get(),
            uom: $('.uom').map(function () { return $(this).val(); }).get(),
            lot_no: $('input[name="lot_no[]"]').map(function(){return $(this).val();}).get(),
            totalsched_qty:$('input[name="totalsched_qty[]"]').map(function(){return $(this).val();}).get(),
            filtercount:rows,
            total_sched_qty:$('#total_sched_qty').val(),
            rmw_no: $('#rmw_no').val(),
            jo_no:ss
        }
    }).done(function(data, textStatus, xhr) {
        EditMode = false;
        clear();
        msg("Scheduled Successful \n JO Number:"+data.jocode,"success");
        // if(data.result == 'warning'){
        //     msg("Some of Schedule Quantity is greater than Quantity","warning");
        // }
        $('#jono').prop('readonly',true);
        $('#tbl_jo_details').dataTable().fnClearTable();
        $('#tbl_jo_details').dataTable().fnDestroy();
        $('#total_sched_qty').val('');
        $('#btn_save').hide();
        $('#btn_edit').show();
        $('#btn_cancel').hide();
        ProdSummaries(prodSummariesURL);
        joDetails_arr = [];
        makeJODetailsList(joDetails_arr);
        getTravelSheet();
    }).fail(function(xhr, textStatus, errorThrown) {
        msg(errorThrown,textStatus);
    }).always(function(xhr, textStatus) {
        $('#rmw_no').prop('readonly', false);
        $('.loadingOverlay').hide();
    });
}

function validateTable(){
     var rows = $('#tbl_jo_details')["0"].rows.length - 1;
     var checkvalues = [];
        for(var x=0;x<rows;x++){
            if($('#tbl_jo_details')["0"].children[1].children[x].cells[1].textContent == ""){return "false";}
            if($('#tbl_jo_details')["0"].children[1].children[x].cells[3].textContent == ""){return "false";}
            if($('#tbl_jo_details')["0"].children[1].children[x].cells[2].textContent == ""){return "false";}
            if($('#tbl_jo_details')["0"].children[1].children[x].cells[4].textContent == ""){return "false";}
            if($('#tbl_jo_details')["0"].children[1].children[x].cells[6].children["0"].value == ""){return "false";}
            if($('#tbl_jo_details')["0"].children[1].children[x].cells[7].children["0"].value == ""){return "false";}
            if($('#tbl_jo_details')["0"].children[1].children[x].cells[8].children["0"].value == ""){return "false";}
            if($('#tbl_jo_details')["0"].children[1].children[x].cells[5].children["0"].value == "0" || $('#tbl_jo_details')["0"].children[1].children[x].cells[5].children["0"].value < 0){return "validate_sched";}
        }
            return "true";
}

function getTables(){
    var datas = $("#jono").val();
    $.ajax({
        url: getjotables,
        type: 'GET',
        datatype: "json",
        loadonce: true,
        data: {_token: token, JOno:datas},
        success:function(returnData){
            console.log(returnData);
            var totalsched = 0;
            joDetails_arr = [];
            MainData = returnData;
            EditMode = true;
            for (var x = 0; x < returnData.length; x++) {
                joDetails_arr.push({
                    count: x,
                    dataid:returnData[x].id,
                    id: returnData[x].id,
                    sc_no: returnData[x].sc_no,
                    prod_code: returnData[x].product_code,
                    description: returnData[x].description,
                    quantity: returnData[x].back_order_qty,
                    sched_qty:returnData[x].sched_qty,
                    totalsched_qty:0,
                    material_used:returnData[x].material_used,
                    material_heat_no:returnData[x].material_heat_no,
                    uom: returnData[x].uom,
                    lot_no: returnData[x].lot_no
                });
                totalsched += returnData[x].sched_qty;
                $('#created_by').val(returnData[x].create_user);
                $('#created_date').val(returnData[x].created_at);
                $('#updated_by').val(returnData[x].update_user);
                $('#updated_date').val(returnData[x].updated_at);
            }
            $('#rmw_no').val(returnData[0].rmw_no);
            $('#total_sched_qty').val(totalsched);
            makeJODetailsList(joDetails_arr);
            getMaterialHeatNoEdit();
            var showform = document.getElementById('formbaba').style.display = 'inline';
        },
        error: function (xhr, textStatus, errorThrown) {
            msg(errorThrown,textStatus);
        }
    });
}

function getTablesAll(){
    $.ajax({
        url: getjotablesALL,
        type: 'GET',
        datatype: "json",
        loadonce: true,
        data: {_token: token},
        success:function(returnData){
            for (var x = 0; x < returnData.length; x++) {
                joDetails_arr.push({
                    count: x,
                    id: x,
                    sc_no: returnData[x].sc_no,
                    prod_code: returnData[x].product_code,
                    description: returnData[x].description,
                    quantity: returnData[x].back_order_qty,
                    sched_qty:returnData[x].sched_qty,
                    material_used:returnData[x].material_used,
                    material_heat_no:returnData[x].material_heat_no,
                    uom: returnData[x].uom,
                    lot_no: returnData[x].lot_no
                });
            }
            makeJODetailsList(joDetails_arr);  
            $('.sched_qty').attr('disabled',true);
            $('.material_heat_no').attr('disabled',true);
            $('.material_used').attr('disabled',true);
            $('.lot_no').attr('disabled',true);
            $(".remove_jo_details").css("visibility", "hidden");
            getMaterialHeatNoEdit();
        },
        error: function (xhr, textStatus, errorThrown) {
            msg(errorThrown,textStatus);
        }
    });
}

function getTravelSheet() {
    travel_Sheet = [];
    $.ajax({
        url: getTravelSheetURL,
        type: 'GET',
        dataType: 'JSON',
        data: {
            _token: token
        },
    }).done(function(data, textStatus, xhr) {
        travel_Sheet = data;
        makeTravelSheet(travel_Sheet);
    }).fail(function(xhr, textStatus, errorThrown) {
        msg(errorThrown,textStatus);
    });   
}

function makeTravelSheet(arr) {
    $('#tbl_travel_sheet').dataTable().fnClearTable();
    $('#tbl_travel_sheet').dataTable().fnDestroy();
    $('#tbl_travel_sheet').dataTable({
        data: arr,
        order: [[1,'asc']],
        columns: [ 
            { data: function(data) {
                 return '<span class="cancel_travel_sheet"'+
                        ' data-jo_no="'+data.jo_no+'" data-prod_code="'+data.product_code+'" '+
                        ' data-issued_qty="'+data.issued_qty+'"data-id="'+data.id+'" '+
                        ' data-status="'+data.status+'"  data-sched_qty="'+data.sched_qty+'" '+
                        ' data-qty_per_sheet="'+data.qty_per_sheet+'"  data-iso_code="'+data.iso_code+'"'+
                        ' data-sc_no="'+data.sc_no+'" data-idJO="'+data.idJO+'"'+
                        ' title="Cancel Travel Sheet"><i class="text-red fa fa-times"></i> </span>';
            }, name: 'action', orderable: false, searchable: false},
            { data: 'jo_no', name: 'jt.jo_no' },
            { data: 'sc_no', name: 'jt.sc_no' },
            { data: 'product_code', name: 'jt.prod_code' },
            { data: 'description', name: 'jt.description' },
            { data: 'back_order_qty', name: 'jt.order_qty' },
            { data: 'sched_qty', name: 'jt.sched_qty' },
            { data: 'issued_qty', name: 'ts.issued_qty' },
            { data: 'material_used', name: 'jt.material_used' },
            { data: 'material_heat_no', name: 'jt.material_heat_no' },
            { data: function(data) {
                switch (data.status) {
                                case 0:
                                    return 'No quantity issued';
                                    break;
                                case 1:
                                    return 'Ready of printing';
                                    break;
                                case 2:
                                    return 'On Production';
                                    break;
                                case 3:
                                    return 'Cancelled';
                                    break;
                                case 5:
                                    return 'CLOSED';
                                    break;
                            }
            }, name: 'ts.status'}
        ]
    });
}

function clear(){
    $('.clear').val("");
    $('#total_sched_qty').val(0);
}
