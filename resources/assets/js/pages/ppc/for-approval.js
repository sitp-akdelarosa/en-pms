$( function() {
	getTransferItems($('meta[name=user_id]').attr('content'));

	$('#transfer_item_approval').on('click', '.approve', function() {
		var id = $(this).attr('data-id');
		var status = $(this).attr('data-status');
		confirmAnswer(id,status);
	});

	$('#transfer_item_approval').on('click', '.disapprove', function() {
		var id = $(this).attr('data-id');
		var status = $(this).attr('data-status');
		confirmAnswer(id,status);
	});
});

function getTransferItems(user_id) {
	var items = '';
	$('#transfer_item_approval').html(items);

	$('#timeline-loading').show();

	$.ajax({
		url: getTransferItemsURL,
		type: 'GET',
		dataType: 'JSON',
		data: {
			_token: token,
			user_id: user_id
		},
	}).done(function(data, textStatus, xhr) {
		$('#timeline-loading').hide();
		$.each(data, function(i, x) {
			items = '<form >'+
						'<div class="from-group row">'+
							'<div class="col-md-6">'+
								'<div class="table-responsive">'+
									'<table class="table">'+
										'<thead>'+
											'<tr>'+
												'<th colspan="2"><h5>From</h5></th>'+
											'</tr>'+
										'</thead>'+
										'<tbody>'+
											'<tr>'+
												'<th>Division Code:</th>'+
												'<td>'+x.current_div_code+'</td>'+
											'</tr>'+
											'<tr>'+
												'<th>Process:</th>'+
												'<td>'+x.current_process+'</td>'+
											'</tr>'+
											'<tr>'+
												'<th>Line Leader:</th>'+
												'<td>'+x.from_user+'</td>'+
											'</tr>'+
										'</tbody>'+
									'</table>'+
								'</div>'+
							'</div>'+

							'<div class="col-md-6">'+
								'<div class="table-responsive">'+
									'<table class="table">'+
										'<thead>'+
											'<tr>'+
												'<th colspan="2"><h5>To</h5></th>'+
											'</tr>'+
										'</thead>'+
										'<tbody>'+
											'<tr>'+
												'<th>Division Code</th>'+
												'<td>'+x.div_code+'</td>'+
											'</tr>'+
											'<tr>'+
												'<th>Process</th>'+
												'<td>'+x.process+'</td>'+
											'</tr>'+
										'</tbody>'+
									'</table>'+
								'</div>'+
							'</div>'+
						'</div>'+
						'<div class="form-group">'+
							'<div class="col-md-12">'+
								'<button type="button" class="btn btn-sm bg-green approve pull-right" data-id="'+x.id+'" data-status="1">Approve</button>'+
								'<button type="button" class="btn btn-sm bg-red disapprove pull-right" data-id="'+x.id+'" data-status="3">Disapprove</button>'+
							'</div>'+
						'</div>'+
					'</form>'+
					'<br><hr>';
			$('#transfer_item_approval').append(items);
		});
	}).fail(function(xhr, textStatus, errorThrown) {
		msg(errorThrown,textStatus);
	});	
}

function confirmAnswer(id,status) {
	$.ajax({
		url: answerRequestURL,
		type: 'POST',
		dataType: 'JSON',
		data: {
			_token: token,
			id: id,
			status: status
		},
	}).done(function(data, textStatus, xhr) {
		getTransferItems($('meta[name=user_id]').attr('content'));
		if(data == 1){
			msg('Successfully Approve','success')
		}else{
			msg('Successfully Disapprove','success')
		}
		
	}).fail(function(xhr, textStatus, errorThrown) {
		msg(errorThrown,textStatus)
	});	
}