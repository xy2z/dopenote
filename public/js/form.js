$(document).ready(function(){

	// Check the form is saved before user leaves page.
	var form = $('.form-check-unsaved'),
		original = form.serialize()

	form.submit(function() {
		window.onbeforeunload = null
	})

	window.onbeforeunload = function() {
		if (form.serialize() !== original) {
			return 'You have unsaved changes. Are you sure you want to leave?'
		}
	}
})