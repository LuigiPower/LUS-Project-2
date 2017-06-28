<?php 
class  SLU_MODEL {
	private $SLU;
    private $SR;
	
	public function __construct(){
       $this->SLU = new FstSlu(wfst, lm, sluilex, sluolex, unk);
       $this->SR  = new SluResults();
    }

    public function getSlu($utterance, $nbest = n_best){
    	return $this->SLU->runSlu($utterance, TRUE, $nbest);
    }

    public function getConcept($sentence, $tag_string){
        return $this->SR->getConcepts($sentence, $tag_string);
    }
}
