// Note title: on key down.
document.querySelector('.note-title input').addEventListener('keyup', function(e) {
	if (e.key === 'Enter') {
		// Focus on editor
		tinyMCE.get('editor').focus()
	}
});
