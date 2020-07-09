var get_count = 10;
var noti;
$( function() {
	getNotification($('meta[name=user_id]').attr('content'));
	$('#show-more').on('click', function() {
		getNotification($('meta[name=user_id]').attr('content'));
	});
});

function getNotification(user_id) {
	$('#timeline-loading').show();
	$('#show-more-row').hide();

	setTimeout(function() {
		$.ajax({
			url: '../notification/all',
			type: 'GET',
			dataType: 'JSON',
			data: {
				_token: token,
				user_id: user_id,
				take: get_count
			},
		}).done(function(data, textStatus, xhr) {
			$('#timeline-loading').hide();
			$('#show-more-row').show();
			$.each(data, function(i, x) {
				noti = '<div class="item">'+
							'<div class="pull-left mr-10">'+
								'<img src="../'+x.photo+'" alt="user image" class="img-fluid" width="50px">'+
							'</div>'+
							
							'<div class="ml-5">'+
								'<a href="#" class="name text-aqua">'+
									'<strong>'+x.from+'</strong>'+
								'</a>'+

								'<small class="text-muted pull-right"><i class="fa fa-clock-o"></i> '+
									timeago().format(x.created_at)+
								'</small>'+

								'<p class="message">'+x.content+'</p>'+
								'<a href="'+x.link+'">Show Item</a>'+
							'</div>'+
						'</div><br><hr>';

			                
			    $('#noti_items').append(noti);
			});
			get_count = get_count + 10;
			getUnreadNotification();
		}).fail(function(xhr, textStatus, errorThrown) {
			console.log("error");
		});
	}, 1000);
}