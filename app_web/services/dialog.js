(function () {
  'use strict';

	angular.module('dialog', ['state', 'asr'])
	.service('dialog',[ 'state', 'asr', function (state, asr) {
		var self = this;
		this.yups = ['yes', 'yep', 'yea', 'okey','ok', 'dokey', 'affirmative', 'aye', 'roger', 'ten four', 'uh', 'huh', 'righto', 'well', 'yup', 'yuppers', 'yups', 'right', 'ja', 'ia', 'surely', 'sure', 'amen', 'totally', 'yessir', 'of course'];
		this.nope = ["nary", "none", "nope", "not", "no", 'nah', 'na'];
		this.wrong = ["wrong", "cancel", "abort", "delete it", "delete", "nothing"];
		this.imOK = ['ok', 'all right', 'fine', 'done', 'got it'];
		this.mapping = {
			title_actors: 'was interpreted by',
			title_director: 'was directed by',
			title_genres: 'is',
			title_country: 'was recorded in',
			title_year: 'was released in',
			title_language: 'original language is',
			title_duration: 'is about minutes',
			title_color: 'it was',
			title_budget: 'had a budget of',
			title_plot_keywords: 'keywords are',
			title_gross: 'made about',
			title_imdb_score: 'has a imdb score of',
			title_movie_facebook_likes: 'has a lot of facebook likes, about',

			actors_title: 'acted in',
			actors_director: 'worked with',

			director_title: 'directed',
			director_actors: 'directed',

			genres_title: 'is the genre of',
			genres_director: 'films were directed by',

			country_title: 'gave birth to',
			
			year_title: 'was the release date of',

			language_title: 'is the original language of',
			
			duration_title: 'is the duration of',
			
			budget_title: 'was the buget of',
			
			plot_keywords_title: 'are the plot key of',
			
			gross_title: 'is the revenue of',
			
			imdb_score_title: 'is the imbd score of',
			
			movie_facebook_likes_title: 'are the facebook likes of',
		
			movie_imdb_link_title: 'is the image of',
		};
		function checkPartials (doCheck){
			console.log(doCheck);


			if (doCheck && (state.context=='concept' || state.context=='partial')){
				console.log('requesting');
				ajax.doRequest('partial', self.user.text, self.onLUSResult, self.onRecognitionResult);
				return true;
			}
			else{
				console.log('no partials');
				if (state.context == 'partial'){
					state.context = 'concept';
				}
				return false;
			}

		}

		this.anyMatch = function(sentence1, concept){
			if (concept && concept.length>0){
				sentence1 = concept[0];
			}
			if (sentence1 == undefined || sentence2==undefined)	return false;
			if (sentence1 == sentence2)	return false;
			console.log("try any match");
			var sentence1 = sentence1.split(" ");
			var sentence2 = sentence2.split(" ");
			var found = false;
			sentence1.forEach(function(value){
				if (sentence2.includes(value)) found = true;
			});
			if (found) return true;
			sentence2.forEach(function(value){
				if (sentence1.includes(value)) found = true;
			});
			if (found) return true;

			return false;
		}
		this.isAllFine = function(sentence){
			var found = false;
			if(sentence){
				self.imOK.forEach(function(value){
				if (sentence.indexOf(value) != -1){
					found = true;
					return;
				}
			});
			}
			return found;
			
		}
		this.isYes = function(sentence){
			var found = false;
			if(sentence){

				self.yups.forEach(function(value){
					if (sentence.indexOf(value) != -1){
						found = true;
						return;
					}
				});
			}

			return found;
		}
		this.isNO = function(sentence){
			var found = false;
			if(sentence) {
				self.nope.forEach(function(value){
					if (sentence.indexOf(value) != -1){
						found = true;
						return;
					}
				});
			}

			
			return found;
		}
		this.isWrong = function(sentence){
			var found = false;
			if(sentence){
				self.wrong.forEach(function(value){
				if (sentence==value){
					found = true;
					console.log(found);
					return;
				}
			});
			}

			
			return found;
		}
		this.isOnlyNO = function(sentence){
			var found = false;
			if(sentence){

			self.nope.forEach(function(value){
				if (sentence==value){
					found = true;
					console.log(found);
					return;
				}
			});
			}

			return found;
		}

		function speak (textToSpeak, callback){
			textToSpeak = textToSpeak.replace(".", " ");
			textToSpeak = textToSpeak.replace("_", " ");
			asr.speakText(textToSpeak, callback);
		}
		this.noIntent = function (sync){
			var sentence = "What are you searching for?";
			sync(sentence);
			speak(sentence);
		}
		this.gotIntent = function(intent, sync, callback){
			var sentence = self.imOK[Math.floor(Math.random() * 5)];// + ", you are searching for "+intent;
			sync(sentence);
			speak(sentence, callback);
		}
		this.askIntentConfirmation = function(intents, sync){
			var sentence = "Are you searching for "+intents[0]+"?";
			sentence += " If not, try with ";

			(intents.slice(-2)).forEach(function(value){
				sentence += value+", ";
			});
			sentence += "or someting else."
			sync(sentence);
			speak(sentence);
		}
		this.noConcept = function (sync){
			var sentence = "It is ok? ";//If i'm wrong, try to remove or add something"
			sync(sentence);
			speak(sentence);
		}
		this.gotConcepts = function(intent, conceptList, sync, callback){
			var sentence = self.imOK[Math.floor(Math.random() * 5)]+", ";
			if (intent){
				sentence += " you want the "+intent+" of ";
			}
			var len = conceptList.length;
			var index = 1;
			conceptList.forEach(function(value){
				sentence += value[0];

				if (index != len)
					sentence += " and ";
				else if (len!=1)
					sentence += ", ";
				index++;
			});
			sync(sentence);
			speak(sentence, callback);
		}
		this.askConceptConfirmation = function(concept, sync){
			var sentence = "Is "+concept[0]+" a "+concept[1]+"?";
			sync(sentence);
			speak(sentence);
		}
		this.sayOK = function (sync, callback){
			var sentence = self.imOK[Math.floor(Math.random() * 5)]+" ";
			sync(sentence);
			speak(sentence, callback);
		}
		this.whatIs = function(concept, sync){
			var sentence = "What is "+concept[0]+"?";
			sync(sentence);
			speak(sentence);
		}
		this.sayPartial = function(partial, concept, sync){
			var key = Object.keys(partial)[0];
			var sentence = concept[0]+" can be a "+key+": ";
			partial[key].forEach(function(value){
				sentence += value+", "
			});
			sync(sentence);
			speak(sentence);
		}

		// this.dbResult = function(intent, concepts, sync){

		// 	var sentence = "";
		// 	intent.forEach(function(value){
		// 		console.log(value);
		// 		sentence += value[1]+" is the "+value[0]+", ";
		// 	});
		// 	sentence = sentence.replace(new RegExp(", " + '$'), '');
		// 	sentence+=" of ";
		// 	concepts.forEach(function(value){
		// 		sentence+= value[1]+", "
		// 	});
		// 	sentence += "do you want to search something else?"
		// 	sentence = sentence.replace(new RegExp("|/g"), ', ');
		// 	sentence = sentence.replace(new RegExp("_/g"), ' ');

		// 	sync(sentence);
		// 	speak(sentence);
		// }

		this.dbResult = function(db, intents, concepts, sync){

			var sentence = "";


			db.slice(0, 4).forEach(function(value){
				console.log(value);
				intents.forEach(function(intent){
					sentence+=value[intent.trim()];
					concepts.forEach(function(concept){
						sentence += " "+self.mapping[intent+"_"+concept]+" "+value[concept]+".\n";
						console.log(sentence);
						sentence = sentence.replace(/\|/g, ", ");
						console.log(sentence);
					});

				});
			});
			if (db.length>4)
				sentence+=" and others.";
			
			// sync(sentence);
			speak(sentence);
		}
		this.dbResultFail = function(intent, concepts, sync){
			var sentence = "I have no results for the "+intent+" of ";
			concepts.forEach(function(value){
				console.log(value);
				sentence += value[0]+", ";
			});
			sentence = sentence.replace(new RegExp(", " + '$'), '');
			sync(sentence);
			speak(sentence);
		}
	}]);
})();