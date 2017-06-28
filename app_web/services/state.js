(function () {
  'use strict';

	angular.module('state', [])
	.service('state', [function($scope) {
		var self = this;
		this.initVariables = function(){
			initVariables();
		};
		initVariables();
		/**
			finalState: {
				concepts:[[word, concept], [word, concept], ...],
				intent: word1
			}

			disambiguationState:{
				lastSentence: "sentence",
				concepts:[
					["word", "concept"], ...
				],
				intent: word1
			}
		*/
		function initVariables(){
			self.context = "init";
			self.todo = "intent";
			self.finalState = {
				concepts:[],
				intent: null
			};
			self.disambiguationState = {
				sentence: null,
				concepts: [],
				intents: []
			};

			self.partial1 = null;
			self.partial2 = null;
			self.partialConcept = null;

			self.dbResponse = [];
		}
		this.parseInitResponse = function (response, callback){
			self.todo = response['todo'];
			console.log('response:');
			console.log(response);
			var state = response['state'];
			self.disambiguationState['sentence'] = state['sentence'];
			self.disambiguationState['concepts'] = state['concepts'];
			self.disambiguationState['intents'] = state['intents'];
			console.log('disambiguation:');
			console.log(self.disambiguationState);
			self.finalState['intent'] = state['decision']['final_intent'];
			self.addFinalConcept(state['decision']['final_concept']);
			console.log('finalState:');
			console.log(self.finalState);
			callback();
		}

		this.parseIntentResponse = function (response, callback){
			self.todo = response['todo'];
			console.log('response:');
			console.log(response);
			var state = response['state'];
			self.disambiguationState['intents'] = state['intents'];
			console.log('disambiguation:');
			console.log(self.disambiguationState);

			self.finalState['intent'] = state['decision']['final_intent'];
			console.log('finalState:');
			console.log(self.finalState);
			callback();
		}
		this.parseConceptResponse = function (response, callback){
			self.todo = response['todo'];
			console.log('response:');
			console.log(response);
			var state = response['state'];
			if (state['concepts'] && state['concepts'].length>0){
				state['concepts'].forEach(function(value){
					self.addConcept(value);
				});
			}
			
			self.addFinalConcept(state['decision']['final_concept']);
			callback();
		}
		this.parsePartialResponse = function(response, callback){
			self.todo = response['todo'];
			console.log('response:');
			console.log(response);
			var state = response['partial'];

			self.partialConcept = state['concept'];
			if (state['firstList'] && Object.keys(state['firstList']).length>0){
				self.partial1 = state['firstList'];
			}
			if (state['secondList'] && Object.keys(state['secondList']).length>0){
				self.partial2 = state['secondList'];
			}
			console.log(self.partialConcept);
			console.log(self.partial1);
			console.log(self.partial2);
			callback();
		}

		this.parseDBResponse = function(response, callback){
			console.log('response:');
			console.log(response);
			self.dbResponse = response;
			callback();
		}
		this.getNextConcept = function(){
			if (self.disambiguationState['concepts'].length == 0) return null;
			return self.disambiguationState['concepts'][0];
		}
		this.getNextIntent = function(){
			if (self.disambiguationState['intents'].length == 0) return null;
			return self.disambiguationState['intents'][0];
		}
		this.existsFinalConcept = function(concept){
			if (concept==null || concept.length==0)	return false;
			var found = false;
			self.finalState['concepts'].forEach(function(value){
				if (value[0]==concept[0] && value[1]==concept[1]){
					found = true;
					return;
				}
			});
			return found;
		}
		this.existsConcept = function (concept){
			if (concept==null || concept.length==0)	return false;
			var found = false;
			if (self.disambiguationState['concepts'] == null ||
					self.disambiguationState['concepts'].length==0)	return false;
			self.disambiguationState['concepts'].forEach(function(value){
				if (value[0]==concept[0] && value[1]==concept[1]){
					found = true;
					return;
				}
			});
			return found;
		}

		this.shiftConcept = function(){
			if (self.disambiguationState['concepts'] && self.disambiguationState['concepts'].length>0){
				return self.disambiguationState['concepts'].shift();
			}
		}
		this.addConcept = function(concept){
			console.log("adding concept");
			if (self.disambiguationState['concepts']==null){
				self.disambiguationState['concepts']=[];
			}

			if (concept==null || concept.length==0)	return;
			if (self.existsConcept(concept)==false){
				self.disambiguationState['concepts'].unshift(concept);
			}
		}
		this.addFinalConcept = function addFinalConcept(concept){
			console.log("adding final concept");
			if (concept==null || concept.length==0)	return;
			if (self.existsFinalConcept(concept)==false){
				self.finalState['concepts'].push(concept);
				self.shiftConcept();
			}
			console.log("finalStare");
			console.log(self.finalState);
		}
		this.addFinalIntent = function(intent){
			if (intent){
				self.finalState['intent'] = intent;
			}
		}

		this.getIntentsAndConcepts = function(){
			var finalIntent = self.finalState['intent'];
			var concepts = self.finalState['concepts'];
			var intentConcepts=[]

			if (finalIntent){
				var item=[];
				item.push(finalIntent);
				item.push('intent');
				intentConcepts.push(item);
			}
			if (concepts && concepts.length>0){
				intentConcepts = intentConcepts.concat(concepts);
			}
			return intentConcepts;
		}

		this.getTempIntentsAndConcepts = function(){
			var intents = self.disambiguationState['intents'];
			var concepts = self.disambiguationState['concepts'];
			var intentConcepts=[]
			console.log("update concepts temp chip");

			if (intents){
				var items =[]
				if (intents && intents.length>0){
					intents.forEach(function(value){
						var item=[];
						item.push(value);
						item.push('intent');
						items.push(item);
					});
				if (items.length>0)
					intentConcepts.push(items);
				}
				
			}
			if (concepts && concepts.length>0){
				intentConcepts = intentConcepts.concat(concepts);
			}
			return intentConcepts;
		}

		this.updateCurrentConcept = function(conceptString){
			var concept = self.shiftConcept();
			if (concept==null) return null;

			concept[1] = conceptString;
			self.addConcept(concept);
			self.partial1 = null;
			self.partial2 = null;
			self.partialConcept = null;
		}
		this.removeFinalIntent = function(){
			self.finalState['intent'] = null;
		}
		this.removeFinalConcept = function(concept){
			console.log("deleting final concept");
			var newConcepts=[];
			if (concept==null || concept.length==0)	return;

			if (self.finalState['concepts'] == null ||
					self.finalState['concepts'].length==0)	return false;
			self.finalState['concepts'].forEach(function(value){
				if (value[0]!=concept[0] || value[1]!=concept[1]){
					newConcepts.push(value);
				}
			});
			console.log(self.finalState['concepts']);
			self.finalState['concepts'] = newConcepts;
		}

		this.readyForDB = function(){
			return (self.finalState['concepts'] && self.finalState['concepts'].length>0 
				&& self.finalState['intent'] && self.finalState['intent'].length>0);
		}
		

	}]);

})();