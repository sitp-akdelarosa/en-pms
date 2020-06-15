var get_count = 5;
var timeline;
$( function() {
	getTimeline($('#user_id').val());
	$('#show-more').on('click', function() {
		getTimeline($('#user_id').val());
	});
});

function getTimeline(user_id) {
	$('#timeline-loading').show();
	$('#show-more-row').hide();

	setTimeout(function() {
		$.ajax({
			url: '../timeline',
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
				timeline = '<li class="time-label">'+
			                    '<span class="bg-red">'+
			                        i+
			                    '</span>'+
			                '</li>';

			        $.each(x, function(ii, xx) {
			        	timeline += '<li>'+
				                    	// '<i class="ion ion-email bg-blue"></i>'+

					                    '<div class="timeline-item">'+
					                        '<span class="time"><i class="fa fa-clock-o"></i> '+xx.time+'</span>'+

					                        '<h3 class="timeline-header">'+xx.module+'</h3>'+

					                        '<div class="timeline-body">'+
					                        	xx.action
					                        '</div>'+
					                    '</div>'+
					                '</li>';
			        });

			                
			    $('#timeline').append(timeline);
			});
			get_count = get_count + 5;
			console.log(get_count);
		}).fail(function(xhr, textStatus, errorThrown) {
			console.log("error");
		});
	}, 1000);
}