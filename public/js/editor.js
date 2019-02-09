// Deprecated: Use Vue + axios instead of jquery.
// Deprecated: Use Vue + axios instead of jquery.
// Deprecated: Use Vue + axios instead of jquery.
// Deprecated: Use Vue + axios instead of jquery.

function Editor() {
}


$(function() {

	$.ajaxSetup({
	    headers: {
	        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	    }
	});


	Editor.status = function(text) {
		$('#status').text(text)
	}

	Editor.update = function(content) {
		var self = this
		self.status('Saving...')

		$.ajax({
			method: 'POST',
			url: '/note/' + note_id + '/update',
			data: {
				title: $('.note-title input').val(),
				content: content,
			}
		}).done(function() {
			self.status('Saved.')
		})
	}



	// Events
	$('.note-title').keyup(function() {
		Editor.update(tinymce.get('editor').getContent())
	})

});
