Class.Define('List', {
	Constructor: function() {
		var forms = document.getElementsByTagName('form'),
			form;
		for (var i = 0, l = forms.length; i < l; i += 1) {
			form = forms[i];
			if (form.className.indexOf('delete') > -1) {
				form.onsubmit = function(e) {
					e = e || window.event;
					if (!confirm("Are you sure?")) {
						if (e.preventDefault) e.preventDefault();
						return false;
					}
				}
			}
		}
	}
});