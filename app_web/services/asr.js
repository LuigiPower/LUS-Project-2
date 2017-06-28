(function () {
  'use strict';

	angular.module('asr', [])
	.service('asr', function() {
		var self = this;

		var recognizing = false;
		var ASR = new webkitSpeechRecognition();
		var TTS = new SpeechSynthesisUtterance();
		var voices = window.speechSynthesis.getVoices();

		ASR.interimResults = false;
		ASR.lang='en-US';
		ASR.maxAlternatives=10;
		ASR.continuous = false;

		self.onResult = null;

		this.stopSpeech = function(){
			if (window.speechSynthesis.speaking){
		    	window.speechSynthesis.cancel();
			}
		}
		this.startRecognition = function(onResult){
			
			self.onResult = onResult;
			self.stopSpeech();
			ASR.start();
		}

		ASR.onstart = function() {
		    recognizing = true;
		    // console.log('started recognition');
		};


		ASR.onend = function() {
		    recognizing = false;
		    // console.log('stopped recognition');
		};

		ASR.onerror = function(event) {
		    console.log(event);
		};

		ASR.onresult = function(event) {
			var max_confidence = 0;
			var best_transcript;
		    // console.log(event);
		    for (var i = 0; i < event.results.length; ++i) {
		        if (event.results[i].isFinal) {
		            for (var j = 0; j < event.results[i].length; ++j) {
		                var transcript=event.results[i][j].transcript;
		                var confidence=event.results[i][j].confidence;
		                if (confidence > max_confidence){
		                	max_confidence = confidence;
		                	best_transcript = transcript;
		                }
		            }
		            // best_transcript=event.results[0][0].transcript;
	                console.log('result: '+transcript+' conf:'+max_confidence);    
		            // if (max_confidence > 0.4){
		            	self.onResult(best_transcript);
		            // }
		            // else{
		            // 	self.speakText("sorry I did not understand.");
		            // }
		        }
		    }
		};

		this.onSpeechEnd = function(callback){
			if(window.speechSynthesis.speaking){
		    	setTimeout(function(){ self.onSpeechEnd(callback); }, 1000);
		    	console.log("speaking");
			}
			else{
				console.log('on end speech');
				callback();
			}
		}
		this.speakText = function (textToSpeak, callback){
			
		  	voices = window.speechSynthesis.getVoices();
		    // for(var i = 0; i < voices.length; i++ ) {
		    //     console.log(voices);
		    // }
		    TTS.lang = 'en-US';
		    TTS.pitch = 1; //0 to 2
		    TTS.voice = voices[32]; //Not all supported
		    TTS.voiceURI = 'native';
		    TTS.volume = 1; // 0 to 1
		    TTS.rate = 1; // 0.1 to 10
		    TTS.text = textToSpeak;
		    self.stopSpeech();
		    window.speechSynthesis.speak(TTS);
		    if (callback){
		    	self.onSpeechEnd(callback);
			}
		}

	});

})();