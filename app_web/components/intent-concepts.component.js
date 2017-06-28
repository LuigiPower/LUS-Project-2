(function () {
  'use strict';

	angular.module('intentConcepts', ['ngMaterial'])
	.component('intentConcepts', {
	    templateUrl: '/app_web/templates/intent-concepts.html',
    	bindings:{
    		list: '=',
    		onRemoved: '='
    	}
  });

})();