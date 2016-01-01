function creditdeduct(productid, price, variation) {

	var areYouSure = '<div id="creditsAreYouSure" style="display:none; cursor: default">';
	areYouSure += '<p style="margin:5px;display:inline-block;height:20px;">'+params.areYouSureMessage+'</p>';
	areYouSure += '<p style="margin:10px;">';
	areYouSure += '<input style="margin-right: 20px;" type="button" class="creditsYes" value="'+params.yes+'"/>';
	areYouSure += '<input type="button" class="creditsNo" value="'+params.no+'" />';
	areYouSure += '</p>';
	areYouSure += '</div>';

	jQuery('body').append(areYouSure);

  jQuery.blockUI({
		message: jQuery('#creditsAreYouSure'),
		css: {
				border: 'none',
				padding: '5px',
				backgroundColor: '#efefef',
				'-webkit-border-radius': '5px',
				'-moz-border-radius': '5px',
				color: '#000'
		}
	});

  jQuery('#creditsAreYouSure .creditsYes').click(function() {
		jQuery('.blockUI.blockMsg').remove();

		var emptyText = '<p style="margin:5px;display:inline-block;height:20px;">'+params.errorMessage+'</p>';
		jQuery.ajax({
			type: "post",
			url: params.homeurl+'/wp-content/plugins/simple-credits/ajax.php',
			data: 'action=buy_product&productid=' + productid + '&variationid=' + variation,
			success: function(response) {
				if(response.length>0) {
					if (response == "error") {
						jQuery.blockUI({
							message: '<div style="cursor: default">'+emptyText+'</div>',
							fadeIn: 700,
							fadeOut: 700,
							timeout: 2000,
							css: {
									border: 'none',
									padding: '5px',
									backgroundColor: '#A80000',
									'-webkit-border-radius': '5px',
									'-moz-border-radius': '5px',
									color: '#fff'
							}
					});
					} else {
						jQuery.blockUI({
							message: '<div style="cursor: default">'+response+'</div>',
							fadeIn: 700,
							fadeOut: 700,
							timeout: 0,
							onOverlayClick: jQuery.unblockUI,
							css: {
									border: 'none',
									padding: '5px',
									backgroundColor: '#efefef',
									'-webkit-border-radius': '5px',
									'-moz-border-radius': '5px',
									color: '#000000'
							}
						});
					}
				}
			},
			error: function (xhr, ajaxOptions, thrownError) {
				jQuery('#creditsAreYouSure').remove();
        alert(xhr.status);
        alert(thrownError);
      }
		});
  });

  jQuery('#creditsAreYouSure .creditsNo').click(function() {
		jQuery('#creditsAreYouSure').remove();
    jQuery.unblockUI();
    return false;
  });

}
