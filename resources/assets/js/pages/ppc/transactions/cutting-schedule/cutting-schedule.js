$(function () {
	initialisePage();

	$('#btn_search_withdrawal').on('click', function() {
		var withdrawal_slip = $.trim($('#withdrawal_slip_no').val());
		if (withdrawal_slip !== '') {
			JOListDataTable(JOListURL, { _token: token, withdrawal_slip: withdrawal_slip });
		} else {
			msg('Fill out Withdrawal Slip No.', 'failed');
		}
	});

	$('#withdrawal_slip_no').on('keydown', function(e) {
        if (e.keyCode === 13) {

			var withdrawal_slip = $.trim($(this).val());

			if (withdrawal_slip !== '') {
				JOListDataTable(JOListURL, { _token: token, withdrawal_slip: withdrawal_slip });
			}
        }
	});
	
	$('#btn_cancel').on('click', function() {
		$('#withdrawal_slip_no').val('');
		$('#leader').select2().val(null).trigger('change.select2');
		$('#iso_control_no').val('');
		JOListDataTable(JOListURL, { _token: token, withdrawal_slip: $('#withdrawal_slip_no').val() });
	});

	$('#btn_save').on('click', function() {
		var jo_no = [];
		var table = $('#tbl_jo').DataTable();
		var error = 0;

		for (var x = 0; x < table.context[0].aoData.length; x++) {
			var DataRow = table.context[0].aoData[x];
			if (DataRow.anCells !== null && DataRow.anCells[0].firstChild.checked == true) {
				var checkbox = table.context[0].aoData[x].anCells[0].firstChild;
				jo_no.push($(checkbox).attr('data-jo'))
			}
		}

		if ($('#leader').val() == '') {
			msg('Please select a Leader input.', 'failed');
		} else if ($('#iso_control_no').val() == '') {
			msg('Please select a ISO Control # input.', 'failed');
		} else if (jo_no.length < 1) {
			msg('Please select at least 1 item of data.', 'failed');
		} else {
			var param = {
				_token: token,
				withdrawal_slip: $('#withdrawal_slip_no').val(),
				date_issued: $('#date_issued').val(),
				prepared_by: $('#prepared_by').val(),
				leader: $('#leader').val(),
				iso_control_no: $('#iso_control_no').val(),
				jo_no: jo_no // array
			}
			saveCuttSched(param);
		}
	});

	$('#btn_print_preview').on('click', function() {
		var jo_no = [];
		var table = $('#tbl_jo').DataTable();

		for (var x = 0; x < table.context[0].aoData.length; x++) {
			var DataRow = table.context[0].aoData[x];
			if (DataRow.anCells !== null && DataRow.anCells[0].firstChild.checked == true) {
				var checkbox = table.context[0].aoData[x].anCells[0].firstChild;
				jo_no.push($(checkbox).attr('data-jo'))
			}
		}

		jo_string = jo_no.join();

		if ($('#leader').val() == '') {
			msg('Please select a Leader input.', 'failed');
		} else if ($('#iso_control_no').val() == '') {
			msg('Please select a ISO Control # input.', 'failed');
		} else if (jo_no.length < 1) {
			msg('Please select at least 1 item of data.', 'failed');
		} else {
			param = "?iso_control_no=" + $('#iso_control_no').val() +
					"&&withdrawal_slip=" + $('#withdrawal_slip_no').val() +
					"&&jo_no=" + jo_string +
					"&&leader=" + $('#leader').val() +
					"&&date_issued=" + $('#date_issued').val() +
					"&&prepared_by=" + $('#prepared_by').val() +
					"&&type=";
			
			var print_preview = pdfCuttingScheduleURL + param;

			window.open(print_preview, '_tab');
		}
	});

	$('#tbl_cut_sched').on('click', '.btn_reprint',function() {
		var tbl_cut_sched = $('#tbl_cut_sched').DataTable();
		var data = tbl_cut_sched.row( $(this).parents('tr') ).data();

		var print_preview = pdfCuttingScheduleReprintURL + "?id=" + data.id;

		window.open(print_preview, '_tab');
	});
});

function initialisePage() {
	if (permission_access == '2' || permission_access == 2) {
        $('.permission').prop('readonly', true);
        $('.permission-button').prop('disabled', true);
    } else {
        $('.permission').prop('readonly', false);
        $('.permission-button').prop('disabled', false);
    }

	checkAllCheckboxesInTable('#tbl_jo', '.chk_all_jo', '.chk_jo', '');

	getISO('#iso_control_no');
	getLeaders();

	JOListDataTable(JOListURL, { _token: token, withdrawal_slip: $('#withdrawal_slip_no').val() });
	CuttSchedListDataTable(CuttSchedListURL, { _token: token })
}

function JOListDataTable(ajax_url, object_data) {
	$('.loadingOverlay').show();
    var tbl_jo = $('#tbl_jo').DataTable();

    tbl_jo.clear();
    tbl_jo.destroy();
    tbl_jo = $('#tbl_jo').DataTable({
        ajax: {
            url: ajax_url,
            data: object_data,
            error: function(xhr,textStatus, errorThrown) {
                ErrorMsg(xhr);
            }
        },
        serverSide: true,
        processing: true,
        order: [[13,'desc']],
        columns: [
			{ data: 'action', name: 'action', orderable: false, searchable: false, width: '2.88%' },
			{ data: 'jo_no', name: 'jo_no', orderable: false, searchable: false, width: '5.88%' },
			{ data: 'alloy', name: 'alloy', orderable: false, searchable: false, width: '5.88%' },
			{ data: 'size', name: 'size', orderable: false, searchable: false, width: '5.88%' },
			{ data: 'item', name: 'item', orderable: false, searchable: false, width: '5.88%' },
			{ data: 'class', name: 'class', orderable: false, searchable: false, width: '5.88%' },
			{ data: 'lot_no', name: 'lot_no', orderable: false, searchable: false, width: '5.88%' },
			{ data: 'sc_no', name: 'sc_no', orderable: false, searchable: false, width: '5.88%' },
			{ data: 'sched_qty', name: 'sched_qty', orderable: false, searchable: false, width: '5.88%' },
			{ data: 'cut_weight', name: 'cut_weight', orderable: false, searchable: false, width: '5.88%' },
			{ data: 'cut_length', name: 'cut_length', orderable: false, searchable: false, width: '5.88%' },
			{ data: 'cut_width', name: 'cut_width', orderable: false, searchable: false, width: '5.88%' },
			{ data: 'material_used', name: 'material_used', orderable: false, searchable: false, width: '8.88%' },
			{ data: 'material_heat_no', name: 'material_heat_no', orderable: false, searchable: false, width: '5.88%' },
			{ data: 'supplier_heat_no', name: 'supplier_heat_no', orderable: false, searchable: false, width: '5.88%' },
			{ data: 'assign_qty', name: 'assign_qty', orderable: false, searchable: false, width: '5.88%' },
			{ data: 'status', name: 'status', orderable: false, searchable: false, width: '5.88%' }
        ],
        createdRow: function(row, data, dataIndex) {
            if (data.status == 'In Production') {
                $(row).css('background-color', 'rgb(121 204 241)'); // BLUE
				$(row).css('color', '#000000');
            }

            if (data.status == 'Cancelled') {
                $(row).css('background-color', '#ff6266'); // RED
                $(row).css('color', '#fff');
            }

            if (data.status == 'CLOSED') {
                $(row).css('background-color', 'rgb(139 241 191)'); // GREEN
				$(row).css('color', '#000000');
            }
        },
        fnDrawCallback: function() {
            $("#tbl_jo").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        initComplete: function() {
            $('.loadingOverlay').hide();
        }
    });
}

function getLeaders() {
	$.ajax({
		url: getLeaderURL,
		type: 'GET',
		dataType: 'JSON',
		data: {_token: token},
	}).done(function(data, textStatus, xhr) {
		$('#leader').select2({
			allowClear: true,
			placeholder: 'Select a Leader',
			data: data
		}).val(null).trigger('change.select2');
	}).fail(function(xhr, textStatus, errorThrown) {
		ErrorMsg(xhr);
	});
}

function saveCuttSched(param) {
	$('.loadingOverlay').show();
	$.ajax({
		url: saveCuttSchedURL,
		type: 'POST',
		dataType: 'JSON',
		data: param,
	}).done(function(data, textStatus, xhr) {

		$('#withdrawal_slip_no').val('');
		$('#leader').select2().val(null).trigger('change.select2');
		$('#iso_control_no').val('');
		JOListDataTable(JOListURL, { _token: token, withdrawal_slip: $('#withdrawal_slip_no').val() });
		CuttSchedListDataTable(CuttSchedListURL, { _token: token })

		msg(data.msg,data.status);
	}).fail(function(xhr, textStatus, errorThrown) {
		ErrorMsg(xhr);
	}).always( function() {
		$('.loadingOverlay').hide();
	});
}

function CuttSchedListDataTable(ajax_url, object_data) {
	$('.loadingOverlay').show();
    var tbl_cut_sched = $('#tbl_cut_sched').DataTable();

    tbl_cut_sched.clear();
    tbl_cut_sched.destroy();
    tbl_cut_sched = $('#tbl_cut_sched').DataTable({
        ajax: {
            url: ajax_url,
            data: object_data,
            error: function(xhr,textStatus, errorThrown) {
                ErrorMsg(xhr);
            }
        },
        serverSide: true,
        processing: true,
        order: [[7,'desc']],
        columns: [
			{ data: 'action', name: 'action', orderable: false, searchable: false, width: '5.5%' },
			{ data: function (x) {
				if (x.jo_no !== null) {
					var jono = (x.jo_no).split(",");
					var jo="";

					jono.sort();

					$.each(jono, function (i, x) {
						jo += x+"<br>";
					});
					return jo;
				}
				
				return x.jo_no;
			}, name: 'jo_no', orderable: false, searchable: false, width: '14.5%' },
			{ data: 'withdrawal_slip_no', name: 'withdrawal_slip_no', orderable: false, searchable: false, width: '15.5%' },
			{ data: 'iso_control_no', name: 'iso_control_no', orderable: false, searchable: false, width: '14.5%' },
			{ data: 'date_issued', name: 'date_issued', orderable: false, searchable: false, width: '12.5%' },
			{ data: 'leader', name: 'leader', orderable: false, searchable: false, width: '12.5%' },
			{ data: 'prepared_by', name: 'prepared_by', orderable: false, searchable: false, width: '12.5%' },
			{ data: 'created_at', name: 'created_at', orderable: false, searchable: false, width: '12.5%' }
        ],
        fnDrawCallback: function() {
            $("#tbl_cut_sched").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        initComplete: function() {
            $('.loadingOverlay').hide();
        }
    });
}