(function () {
  'use strict';

	angular.module('dialogText', ['ngMaterial'])
	.component('dialogText',{
	    templateUrl: '/app_web/templates/dialog-text.html',
    	bindings: {
    		who:'='
    	},
    	controller: ['$scope', function($scope){
    		var self = this;
    		this.onEnterPressed = function(){
    			$scope.$ctrl.who.text = $scope.$ctrl.who.text.trim();
    			$scope.$ctrl.who.text = $scope.$ctrl.who.text.replace(/\n/g, '')+" ";
				$scope.$ctrl.who.callback($scope.$ctrl.who.text);
    		}
    	}]
  });

})();