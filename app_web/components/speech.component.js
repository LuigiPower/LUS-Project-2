(function () {
  'use strict';

	angular.module('speech', [
   		'ngMaterial'
   	])
	.component('speech', {
	    templateUrl: '/app_web/templates/speech.html',
    	bindings: {
    		onPressed: '&',
        icon: '&'
    	}
  });

})();