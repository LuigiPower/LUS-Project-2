(function () {
  'use strict';


	angular.module('inputOutput', ['ajax', 'state', 'asr'])
	.controller('inputOutput', ['$scope', 'ajax', 'state', 'asr', 'dialog', function($scope, ajax, state, asr, dialog){
		var self = this;
		this.speechIcon = "/app_web/icons/mic.svg";
		this.computeIcon = "/app_web/icons/compute.svg";

		this.db = null;
		this.intents = null;
		this.concepts = null;

		this.sync = function(text){
			self.system.text = text;
			if(!$scope.$$phase){
    			$scope.$apply();
			}
		}
		this.textReceived = function(sentence){
			asr.stopSpeech();
			console.log(state.context);
			self.user.text = sentence;
			if (!sentence) sentence = "";
			if (sentence.trim() != ""){
				self.processStep(sentence);
			}
		};
		this.onMicPressed = function(){
			asr.startRecognition(self.textReceived);
		}
		this.onDBPressed = function(){

			if (state.readyForDB()){
				state.context = 'database';
				self.processStep();
			}
		}
		this.openLink = function(dbkey){
			console.log("click");
			console.log(dbkey);
			window.open(encodeURI(self.db[dbkey]['movie_imdb_link']), '_system');
		}
		
		this.dummy = function(dummy){};
		this.user = {
			label: "User",
			text: "",
			placeholder: "Write a sentence or click on the microphone button",
			callback: self.textReceived,
			readonly: false,
			focus: true
		};
		this.system = {
			label: "System",
			text: "",
			placeholder: "",
			callback: self.dummy,
			readonly: true,
			focus: false
		};

		this.chips = state.getIntentsAndConcepts();
		this.tempChips = state.getTempIntentsAndConcepts();

		this.onTempChipsRemoved = function(chip){
			console.log( 'onTempChipsRemoved');

		}

		this.onChipsRemoved = function(chip){
			console.log(chip);
			if (chip[1] == 'intent'){
				state.context = 'intent';
				state.removeFinalIntent();
				self.user.text = "";
				state.context = 'onIntentResponse';

			}
			else{
				state.context = 'concept';
				var concept = [];
				concept.push(chip[0]);
				concept.push(chip[1]);
				state.removeFinalConcept(concept);
				self.user.text = "";

				if (state.finalIntent==null){
					state.context = 'onIntentResponse';
				}
			}
			self.processStep();
		}

		this.processStep = function(sentence){
			if (!sentence)	sentence = self.user.text;
			self.chips = state.getIntentsAndConcepts();
			if(!$scope.$$phase){
    			$scope.$apply();
			}
			var context = state.context;
			switch(context) {
				case 'init':
					init(sentence);
				break;
    			case 'intent':
    				intent(sentence);
    			break;
    			case 'checkpartial':
    				self.checkPartial(sentence);
    			break;
    			case 'somethingElse':
    				self.somethingElse(sentence);
    			break;
    			case 'gotConcept':
    				self.gotConcept(sentence);
    			break;
    			case 'onIntentResponse':
    				self.onIntentResponse(sentence);
    			break;
    			case 'intentConfirmation':
    				self.onIntentConfirmation(sentence);
    			break;
    			case 'concept':
    				self.concept(sentence);
    			break;
    			case 'conceptConfirmation':
    				self.onConceptConfirmation(sentence);
    			break;
    			case 'partial':
    				partial(sentence);
    			break;
    			case "askDatabase":
    				self.askDatabase(sentence);
    			break;
    			case 'database':
    				self.doDatabaseQuery(sentence);
    			break;
    		}
		}
		

		this.onIntentConfirmation = function(sentence){
			console.log('onIntentConfirmation');
			if (dialog.isYes(sentence)){
				console.log('he said yes');
				state.addFinalIntent(state.getNextIntent());
				state.context = 'gotConcept';
				dialog.gotIntent(state.getNextIntent(), self.sync, self.processStep);

			}else{
				state.context = 'intent';
				self.processStep(sentence);
			}
		}
		this.onIntentResponse = function(){
			console.log("on intent response");
			//chech if final intent is found
			var finalIntent = state.finalState['intent'];
			var intentList = state.disambiguationState['intents'];
			if (finalIntent){
				state.context = 'gotConcept';
				dialog.gotIntent(finalIntent, self.sync, self.processStep);
			}
			else if (intentList && intentList.length>0){
				// ask for possibilities
				state.context = 'intentConfirmation';
				dialog.askIntentConfirmation(intentList, self.sync);
			}else{
				//no clue
				state.context = 'init';
				dialog.noIntent(self.sync);
			}
		}

		function init(sentence){
			self.db = null;
			self.intents = null;
			self.concepts = null;
			ajax.initRequest(state.context, sentence, self.onIntentResponse);
			return;
		}
		function intent(sentence){
			ajax.intentRequest(state.context, sentence, self.onIntentResponse);
			return;
		}

		this.gotConcept = function(){
			console.log("got concept");
			var finalConceptList = state.finalState['concepts'];
			var finalIntent = state.finalState['intent'];
			state.context = 'concept';

			if (finalConceptList && finalConceptList.length>0){
				dialog.gotConcepts(finalIntent, finalConceptList, self.sync, self.processStep);
			}
			else{
				self.processStep();
			}
		}


		this.onPartialResponse = function(){
			console.log("onPartialResponse");
			var concept = state.getNextConcept();
			if (state.partialConcept){
				state.context = 'concept';

				state.updateCurrentConcept(state.partialConcept);
				self.processStep();
			}
			else if (state.partial1 && Object.keys(state.partial1).length>0){
				state.context = 'checkpartial';
				dialog.sayPartial(state.partial1, concept, self.sync);
			}
			else if (state.partial2 && Object.keys(state.partial2).length>0){
				state.context = 'checkpartial';
				dialog.sayPartial(state.partial1, concept, self.sync);
			}
			else{
				state.context = "somethingElse"
				self.processStep();

			}
		}
		this.checkPartial = function(sentence){
			console.log("checkpartial");
			ajax.partialRequest('partial', sentence, self.onPartialResponse);
		}
		this.onConceptConfirmation = function(sentence){
			console.log('onConceptConfirmation');
			if (dialog.isYes(sentence)){
				console.log('he said yes');
				state.addFinalConcept(state.getNextConcept());
				state.context = 'concept';
				dialog.sayOK(self.sync, self.processStep);

			}else if (dialog.isOnlyNO(sentence.trim())){
				//test for partials
				state.context="checkpartial";
				dialog.whatIs(state.getNextConcept(), self.sync);
			}else{
				state.context="checkpartial";
				self.processStep(self.user.text);
			}

		}
		this.onConceptResponse = function(){
			console.log("onConceptResponse");
			state.context = 'concept';
			self.processStep();
		}

		this.askDatabase = function(sentence){
			console.log('askDatabase');
			console.log(sentence);
			if (dialog.isAllFine(sentence) || dialog.isYes(sentence) ){
				console.log('he said is fine');
				// state.addFinalConcept(state.getNextConcept());
				state.context = 'database';
				dialog.sayOK(self.sync, self.processStep);
			}
			else{
				state.context = 'somethingElse';
				self.processStep();
			}
		}
		this.somethingElse = function (sentence){
			console.log('somethingElse');
			//nothing => database

			if (dialog.anyMatch(sentence, state.shiftConcept()) == true){
				var concept = state.shiftConcept();
				concept[0] = sentence;
				state.addConcept(concept);
				state.context = 'gotConcept';
				self.processStep();
			}
			else if (dialog.isOnlyNO(sentence) || dialog.isWrong(sentence)){
				state.context = 'gotConcept';
				state.shiftConcept();
				self.processStep();
			}
			else{
				state.context = 'gotConcept';
				state.shiftConcept();
				console.log("concept request");
				ajax.conceptRequest('concept', sentence, self.onConceptResponse);
			}
		}
		self.concept = function(){
			var conceptList = state.disambiguationState['concepts'];
			
			if (conceptList && conceptList.length>0){
				state.context = 'conceptConfirmation';
				var nextConcept = state.getNextConcept();
				dialog.askConceptConfirmation(nextConcept, self.sync);
			}else{
				state.context = 'askDatabase';
				dialog.noConcept(self.sync);
				// state.context = 'database';
				// dialog.sayOK(self.sync, self.processStep);
			}
		}
		function partial(){
		
		}


		this.onDBResult = function(){
			// var intent = state.dbResponse['intent'];
			// var concepts = state.dbResponse['concepts'];
			// state.context = 'intent';

			// if (intent && intent.length>0 && concepts && concepts.length>0){
				
			// 	dialog.dbResult(intent, concepts, self.sync);
			// 	state.initVariables();
			// }
			self.db = state.dbResponse['db'];
			if (self.db.length>20){
				self.db = self.db.slice(0,21);
			}
			self.intents = state.dbResponse['intent'];
			self.concepts = state.dbResponse['concepts'];
			state.context = 'intent';

			if (self.db && self.db.length>0){
				
				// dialog.dbResult(intent, concepts, self.sync);
				dialog.dbResult(self.db, self.intents, self.concepts, self.sync);
				state.initVariables();
			}
			else{
				dialog.dbResultFail(state.finalState['intent'], state.finalState['concepts'], self.sync);
			}

		}
		this.doDatabaseQuery = function(){
			var intent = state.finalState['intent'];
			var concepts = state.finalState['concepts'];
			if (state.readyForDB()){
				ajax.doDBRequest('database', intent, concepts, self.onDBResult);
			}
		}

	}]);

})();