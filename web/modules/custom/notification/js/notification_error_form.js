(function ($, Drupal) {
  Drupal.behaviors.notificationErrorForm = {
    attach: function (context, settings) {
      $('#notification-errors-form', context).once('notificationErrorForm').each(function () {
        $('#notification-errors-form a.edit').click(function () {
					$('#date').text($(this).attr('date'));
					$('#reference').text($(this).attr('reference'));
					$('#form').text($(this).attr('form'));
					$('#status').text($(this).attr('status'));
					$('#guid').text($(this).attr('guid'));
					var pretty = '';
					var obj = null;
					var json = $('[data-drupal-selector=edit-data-' + $(this).attr('id') + ']').val();
					try {
						obj = JSON.parse(json);
						pretty = JSON.stringify(obj, undefined, 4);
					}catch(err) {
						pretty = json;
					}
					$('#edit-data').val(pretty);
					json = $('[data-drupal-selector=edit-error-' + $(this).attr('id') + ']').val();
					try {
						obj = JSON.parse(json);
						pretty = JSON.stringify(obj, undefined, 4);
					}catch(err) {
						pretty = json;
					}
					$('#error').html(pretty);
					$('#edit-display').show();
					$('#edit-submit').show();
					$('[data-drupal-selector="edit-current-id"]').val($(this).attr('id'));
					$([document.documentElement, document.body]).animate({
							scrollTop: $("#edit-display").offset().top
					}, 500);
					return false;
				});
				$('#notification-errors-form a.delete').click(function () {
					if (confirm('Delete this ' + $(this).attr('reference') + ' submission?')) {
						$.ajax({
							url: "/notify-errors-delete/" + $(this).attr('id'),
							type: "GET",
							dataType: "html",
							cache: false,
							timeout: 60000,
							success: function(){
								location.reload();
							}
						});
					}
				});
				$('#edit-submit').click(function () {
					$(this).prop('disabled', 'true');
					$('.ajax-progress-throbber').show();
					var json = $('#edit-data').val();
					$.ajax({
							url: "/notify-errors-submit/" + $('[data-drupal-selector="edit-current-id"]').val() + "?json=" + encodeURIComponent(json),
							type: "GET",
							dataType: "html",
							cache: false,
							timeout: 60000,
							success: function(){
								location.reload();
							}
						});
					return false;
				});
      });
    }
  };
})(jQuery, Drupal);
