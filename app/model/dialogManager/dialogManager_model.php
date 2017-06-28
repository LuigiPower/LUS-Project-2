<?php 
class  DIALOGMANAGER_MODEL {
	
    private $slu;
    private $classifier;

    private $sentence;

    private $decision;
    private $intents;
    private $concepts;

    private $prevConfiguration;

	public function __construct(){
      
        $this->slu = load_model('slu');
        $this->classifier = load_model('classifier');

        $this->decision[final_intent] = null;
        $this->decision[final_concept] = null;

    }

    public function getSentence(){
        return $this->sentence;
    }
    public function getDecision(){
        return $this->decision;
    }
    public function getIntets(){
        return $this->intents;
    }
    public function getConcepts(){
        return $this->concepts;
    }
    public function getPrevConfiguration(){
        return $this->prevConfiguration;
    }

    public function loadPrevConfiguration($json_state){
        $this->prevConfiguration = $json_state;
    }
    public function setDecision($key, $value){
        $this->decision[$key] = $value;
    }

    public function getState(){
        $result=array();
        $result['sentence'] = $this->sentence;
        $result['intents'] = $this->intents;
        $result['concepts'] = $this->concepts;
        $result['decision'] = $this->decision;
        return $result;
    }


    private function getSluConcepts($slu_out, $sentence){
        $concepts = array();
        $toReturn = array();
        foreach ($slu_out as $key => $value) {
            $tag = $value[0];
            $result = $this->slu->getConcept($sentence, $tag);
            if (!empty($result)){
                $concepts[] = $result;
            }
        }
        foreach ($concepts as $key => $block) {
            $midResutl = array();
            foreach ($block as $key => $concept) {

                foreach ($concept as $conceptName => $word) {
                    $tmp = array();
                    $tmp[]= $word;
                    $tmp[]= $conceptName;
                    $midResutl[] = $tmp;
                }
            }
            $toReturn[] = $midResutl;
        }
        return $toReturn;
    }

    private function getUCIntents($uc_out){
        $intents = array();
        foreach ($uc_out as $key => $value) {
            $intents[] = $value[0];
        }
        return $intents;
    }
    private function getLongestConcept($concepts){
        if (empty($concepts)){
            return null;
        }
        $longestIndex = 0;
        $maxLen = 0;
        foreach ($concepts as $key => $value) {
            $length = count($value);
            if ($length > $maxLen){
                $longestIndex = $key;
            }
        }
        return $concepts[$longestIndex];
    }

     private function checkThreshold($confidence, $threshold){

        if ($confidence < $threshold){
            return false;
        }
        return true;
    }

    private function removeFoundConcept($concept){
        $newConcepts = array();
        foreach ($this->concepts as $value) {

            if ($value[0]!=$concept[0] || $value[1]!=$concept[1]){
                $newConcepts[] = $value;
            }   
        }
        $this->concepts = $newConcepts;
    }
    public function simpleAnalysis($sentence){
        $this->sentence = $sentence;

        $uc_out = $this->classifier->getClassification($sentence);
        $slu_out = $this->slu->getSlu($sentence);
        $concepts = $this->getSluConcepts($slu_out, $sentence);
        $this->intents = $this->getUCIntents($uc_out);
        $this->concepts = $this->getLongestConcept($concepts);


        if (!empty($uc_out)){
            if ($this->checkThreshold($uc_out[0][1], intent_acceptance)){
                $this->decision[final_intent] = $uc_out[0][0];
                $this->intents = [];
            }
        }
        else{

        }
        if (!empty($concepts)){
           if($this->checkThreshold($slu_out[0][1], concept_acceptance)){
               if (!empty($concepts)){
                    $this->decision[final_concept] = $concepts[0][0];
                    $this->removeFoundConcept($concepts[0][0]);
               }
           }
        }
    }

    public function intentAnalysis($sentence){
        $this->sentence = $sentence;

        $uc_out = $this->classifier->getClassification($sentence);

        if (!empty($uc_out)){
            $this->intents = $this->getUCIntents($uc_out);
        }
    }

    public function conceptAnalysis($sentence){
        $this->sentence = $sentence;

        $slu_out = $this->slu->getSlu($sentence);
        $concepts = $this->getSluConcepts($slu_out, $sentence);

        if (!empty($concepts)){
            
            if($this->checkThreshold($slu_out[0][0], $slu_out[0][1], concept_acceptance, final_concept)){
               if (!empty($concepts)){
                    $this->decision[final_concept] = $concepts[0];
               }
           }

            $this->concepts = $this->getLongestConcept($concepts);

        }
    }
}