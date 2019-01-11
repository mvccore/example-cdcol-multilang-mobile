Class.Define('Front',{
	Extend: Module,
	Constructor: function () {
		this.parent();
		if (typeof (List) != 'undefined') new List();
	}
});

// run all declared javascripts after <body>, after all elements are declared
window.front = new Front();