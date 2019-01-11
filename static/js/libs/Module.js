Class.Define('Module',{
	Static: {
		instance: null,
		GetInstance: function () {
			return this.self.instance;
		}
	},
	Constructor: function () {
		this.self.instance = this;
		this.initErrorLogging();
	},
	errorFingerPrints: {},
	initErrorLogging: function () {
		return; // comment this line to show uncaught errors in browser console
		window.onerror = function (message, file, line, col, error) {
			var errorFingerPrint = this.convertStringToHexadecimalValue(file) + '_' + String(line);
			if (typeof(this.errorFingerPrints[errorFingerPrint]) != 'undefined') {
				return false;
			} else {
				this.errorFingerPrints[errorFingerPrint] = message;
				var data = {
					message: this.convertStringToHexadecimalValue(message),
					uri: this.convertStringToHexadecimalValue(location.href),
					file: this.convertStringToHexadecimalValue(file),
					line: line,
					column: col,
					callstack: error.stack ? this.convertStringToHexadecimalValue(error.stack) : '',
					browser: this.convertStringToHexadecimalValue(navigator.userAgent),
					platform: navigator.platform
				};
				Ajax.load({
					url: '?controller=system&action=js-errors-log',
					method: 'post',
					data: data
				});
				return true;
			}
		}.bind(this)
	},
	convertStringToHexadecimalValue: function (input) {
		var inputStr = String(input),
			chars = '0123456789ABCDEF', 
			output = '',
			x;
		for (var i = 0; i < inputStr.length; i++) {
			x = inputStr.charCodeAt(i);
			output += chars.charAt((x >>> 4) & 0x0F) + chars.charAt(x & 0x0F);
		}
		return output;
	}
});
