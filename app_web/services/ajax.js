(function () {
  'use strict';

	angular.module('ajax', ['state'])
	.service('ajax',[ '$http', 'state', function($http, state) {
		var self = this;
		this.data= {};

		this.initRequest = function (context, sentence, callback){
			self.data["CONTEXT"] = context;
			self.data['ACTION'] = "analizeUtterance";
			self.data['sentence'] = sentence;
			$http({
			    method: 'POST',
			    url: 'index.php',
			    data: "REQUEST_DATA="+JSON.stringify(self.data),
			    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			}).then(function(response) {
	            // success
	            state.parseInitResponse(response.data['RESULT_DATA'], callback);
		    }, 
		    function(response) {
	            // failed
	            console.log(response);
		    });
		};
		this.intentRequest = function (context, sentence, callback){
			self.data["CONTEXT"] = context;
			self.data['ACTION'] = "analizeUtterance";
			self.data['sentence'] = sentence;
			$http({
			    method: 'POST',
			    url: 'index.php',
			    data: "REQUEST_DATA="+JSON.stringify(self.data),
			    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			}).then(function(response) {
	            // success
	            state.parseIntentResponse(response.data['RESULT_DATA'], callback);
		    }, 
		    function(response) {
	            // failed
	            console.log(response);
		    });
		};

	   	this.conceptRequest = function(context, sentence, callback){

			self.data["CONTEXT"] = context;
			self.data['ACTION'] = "analizeUtterance";
			self.data['sentence'] = sentence;

		   	$http({
			    method: 'POST',
			    url: 'index.php',
			    data: "REQUEST_DATA="+JSON.stringify(self.data),
			    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			}).then(function(response) {
	            // success
	            // console.log(response.data['RESULT_DATA']);
	            state.parseConceptResponse(response.data['RESULT_DATA'], callback);
		    }, 
		    function(response) {
	            // failed
	            console.log(response);
		    });
	   };
	   this.partialRequest = function(context, sentence, callback){

			self.data["CONTEXT"] = context;
			self.data['ACTION'] = "analizeUtterance";
			self.data['sentence'] = sentence;
			var partial1 = "";
			var partial2 = "";
			if (state.partial1){
				if (Object.keys(state.partial1)){
					partial1 = Object.keys(state.partial1)[0];
				}	
			}
			if (state.partial2){
				if (Object.keys(state.partial2)){
					partial2 = Object.keys(state.partial2)[0];
				}
			}
			
			self.data['partial1'] = partial1;
			self.data['partial2'] = partial2;

		   	$http({
			    method: 'POST',
			    url: 'index.php',
			    data: "REQUEST_DATA="+JSON.stringify(self.data),
			    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			}).then(function(response) {
	            // success
	            // console.log(response.data['RESULT_DATA']);
	            state.parsePartialResponse(response.data['RESULT_DATA'], callback);
		    }, 
		    function(response) {
	            // failed
	            console.log(response);
		    });
	   };

	   	this.doDBRequest = function(context, intent, concepts, onResult){

			self.data["CONTEXT"] = context;
			self.data['ACTION'] = "dbRequest";
			self.data['intent'] = intent;
			self.data['concepts'] = concepts;

			console.log(self.data);
		   	$http({
			    method: 'POST',
			    url: 'index.php',
			    data: "REQUEST_DATA="+JSON.stringify(self.data),
			    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			}).then(function(response) {
	            // success
	            // console.log(response.data['RESULT_DATA']);
	            state.parseDBResponse(response.data['RESULT_DATA'], onResult);
		    }, 
		    function(response) {
	            // failed
	            console.log(response);
		    });
	   };

	}]);
})();