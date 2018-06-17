(function(namespace, $) {
	"use strict";

	var DemoUILists = function() {
		// Create reference to this instance
		var o = this;
		// Initialize app when document is ready
		$(document).ready(function() {
			o.initialize();
		});

	};
	var p = DemoUILists.prototype;
	
	// =========================================================================
	// INIT
	// =========================================================================

	p.initialize = function() {
		this._initNestableLists();
	};
	
	// =========================================================================
	// NESTABLE LISTS
	// =========================================================================

	p._initNestableLists = function() {
		if (!$.isFunction($.fn.nestable)) {
			return;
		}
		
		var updateOutput = function(e)
		{
			var list   = e.length ? e : $(e.target),
				output = list.data('output');
			if (window.JSON) {
				console.log(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
			} else {
				console.log('JSON browser support required for this demo.');
			}
		};

		$('.nestable-list').nestable().on('change', updateOutput);;
	};

	// =========================================================================
	namespace.DemoUILists = new DemoUILists;
}(this.materialadmin, jQuery)); // pass in (namespace, jQuery):
