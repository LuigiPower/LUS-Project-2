<?php 
class  CLASSIFIER_MODEL {
	
	private $UC;
	
	public function __construct(){
       $this->UC = new FstClassifier(classifier, cilex, colex, unk);
    }

    public function getClassification($utterance, $nbest = n_best){
    	return $this->UC->predict($utterance, TRUE, $nbest);
    }
}
