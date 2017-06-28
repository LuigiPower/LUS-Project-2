<?php 
class  RESPONSEUTIL_MODEL {
	// cat app/lib/models/slu.lex | grep "B-" | sed -e "s/B-/\'/g" | cut -d" " -f1 | sed -e "s/$/\'\,/g"
    // cat app/lib/models/classifier.lex | head -n 42 | awk '{word="\047"$1"\047"","; print word}'
	public function __construct(){
		 $this->conceptList = [
            'producer.name',
            'character.name',
            'person.nationality',
            'actor.nationality',
            'movie.release_date',
            'award.ceremony',
            'movie.language',
            'country.name',
            'rating.name',
            'movie.subject',
            'director.nationality',
            'actor.name',
            'movie.description',
            'movie.star_rating',
            'movie.gross_revenue',
            'award.category',
            'actor.type',
            'movie.release_region',
            'movie.genre',
            'movie.location',
            'movie.name',
            'person.name',
            'director.name'];
        //https://www.wordsapi.com/
        $this->classifierList = [
            'actor' => [
                "histrion",
                "player",
                "role player",
                "thespian"
                ],
            'actor_name' => [],
            'award' => [
                "accolade",
                "honor",
                "honour",
                "laurels",
                "awarding",
                "prize",
                "grant",
                "present"
                ],
            'award_category' => [],
            'award_category_count' => [],
            'award_ceremony' => [],
            'award_count' => [],
            'birth_date' => [
                "nascence",
                "nascency",
                "nativity",
                "birth"
                ],
            'budget' => [
                "monetary fund",
                "fund",
                "currency",
                "money"
                ],
            'character' => [],
            'composer' => [
                "musician"
            ],
            'country' => [
                "land",
                "state",
                "nation",
                "area"
            ],
            'date' => [],
            'director' => [
                "conductor",
                "manager"
            ],
            'director_name' => [],
            'genre' => [
                "style",
                "type"
            ],
            'language' => [
                "linguistic",
                "speech"
            ],
            'media' => [],
            'movie' => [
                "film",
                "flick",
                "show"
            ],
            'movie_count' => [],
            'movie_name' => [],
            'movie_other' => [],
            'organization' => [
                "administration",
                "brass",
                "establishment",
                "governance",
                "organize",
                "establishment"
            ],
            'other' => [],
            'person' => [
                "individual",
                "mortal",
                "somebody",
                "someone",
                "soul",
                "human"
            ],
            'person_name' => [],
            'picture' => [
                "painting",
                "depict",
                "pictural",
                "icon",
                "ikon",     
                "image"
            ],
            'producer' => [
                "manufacturer"
            ],
            'producer_count' => [],
            'rating' => [
                "evaluation",
                "rank",
                "rate"
            ],
            'release_date' => [],
            'revenue' => [
                "income"
            ],
            'review' => [
                "recap",
                "recapitulation"
            ],
            'runtime' => [
                "duration",
                "temporal",
                "movie duration",
                "length",
                "size",
                "long",
                "extent",
                "extention",
                "continuance",
                "time",
                "period"
            ],
            'star_rating' => [],
            'subjects' => [
                "bailiwick",
                "discipline",
                "field",
                "field of study",
                "study",
                "subject area",
                "subject field"
            ],
            'synopsis' => [
                "abstract",
                "outline",
                "precis"
            ],
            'theater' => [
                "dramatic art",
                "dramatics",
                "dramaturgy"
            ],
            'trailer' => [],
            'writer' => [
                "author",
                "write",
                "wrote"
            ]
        ];
        $this->initPossiblePartial();

    }

    private function initPossiblePartial(){
        $concepts = $this->conceptList;
        foreach ($concepts as $value) {
            $concept = explode('.',$value, 2);
            $this->possiblePartialsFirst[$concept[0]][]=$concept[1];
            $this->possiblePartialsSecond[$concept[1]][]=$concept[0];
        }
    }

    public function findIntent($sentence){
 
            $final_intent = null;
            $weight = 0;

            foreach ($this->classifierList as $key => $value) {
                if (empty($value)) continue;

                foreach ($value as $intent) {
                    $parsedIntent = str_replace("_", " ", $intent);

                    if (!(strpos($sentence, $parsedIntent) === false)){

                        $mweight = strlen ($parsedIntent);
                        if ($mweight >= $weight){
                            $weight = $mweight;
                            $final_intent = $key;
                        }
                    }
                }
                $intent = $key;
                $parsedIntent = str_replace("_", " ", $intent);

                if (!(strpos($sentence, $parsedIntent) === false)){
                    $mweight = strlen ($parsedIntent);
                    if ($mweight >= $weight){
                        $weight = $mweight;
                        $final_intent = $key;
                    }
                }
            }
            return $final_intent;
    }

  private function findConceptPartial($sentence, $array){
        $out = array();
        foreach ($array as $key => $value) {
            $parsedConcept = str_replace("_", " ", $key);
            if (!(strpos($sentence, $parsedConcept) === false)){
                $out[$key] = $value;
            }
        }
        return $out;
    }

    private function obvConcept($partial, $which){
    	$partialElement = [];
    	//if only has 1 value finish
        if (!empty($partial)){
            $partialElement = reset($partial);
        }
        $count = count($partialElement);
        if ($count == 1){
            if ($which==1)
                return key($partial).".".$partialElement[0];
            else
                return $partialElement[0].".".key($partial);

        }
        return null;
    }

    private function inConcept($partial, $partialList){

    	if (empty($partial) || empty($partialList)) return false;

		$key = key($partialList);
    	if (in_array($partial, $partialList[$key])){
    		return true;
    	}
    }

     public function isHeDefiningNewConcept($sentence, $partial1=null, $partial2=null){
     	$result = array();
        $result['state'] = array();
        $result['todo'] = "partial";

        $partialItem = array();
        $concept = null;

        $secondPartial = $this->findConceptPartial($sentence, $this->possiblePartialsSecond);
        $partialItem['secondList'] = $secondPartial;
        $possibleConcept = $this->obvConcept($secondPartial, 2);
        if(!empty($possibleConcept)){
        	$concept = $possibleConcept;
        }

        $firstPartial = $this->findConceptPartial($sentence, $this->possiblePartialsFirst);
        $partialItem['firstList'] = $firstPartial;
        $possibleConcept = $this->obvConcept($firstPartial, 1);
        if(!empty($possibleConcept)){
        	$concept = $possibleConcept;
        }

        if ($this->inConcept($partial1, $secondPartial)){
        	reset($secondPartial);

        	$concept = $partial1.".".key($secondPartial);
        }
        if ($this->inConcept($partial2, $firstPartial)){
        	reset($firstPartial);

        	$concept = key($firstPartial).".".$partial2;
        }


        if (!empty($firstPartial) and !empty($secondPartial)){
        	reset($firstPartial);
        	reset($secondPartial);
            if ($this->inConcept(key($firstPartial), $secondPartial)){
        		$concept = key($firstPartial).".".key($secondPartial);
	        }
	        if ($this->inConcept(key($secondPartial), $firstPartial)){
        		$concept = key($firstPartial).".".key($secondPartial);
	        }    
        }
        $partialItem['concept'] = $concept;
        $result['partial'] = $partialItem;
        return $result;

    }

}
