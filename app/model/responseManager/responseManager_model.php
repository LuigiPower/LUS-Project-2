<?php 
class  RESPONSEMANAGER_MODEL {

    private $util;

	public function __construct(){
        $this->util = load_model('responseUtil');
    }

    public function generateResponse($dialog){
        $result = array();

        $caller = "INIT";
        $trace = debug_backtrace();
        if (isset($trace[1])) {$caller = $trace[1]['class'];}

        return $this->$caller($dialog);
    }
   

    private function INIT($dialog){
        $result = array();
        $result['state'] = $dialog->getState();    
        $decision = $dialog->getDecision();

        if (empty($decision[final_intent])){
            $result['todo'] = "intent";
            return $result;
        }

        if (empty($decision[final_concept])){
            $result['todo'] = "concept";
            return $result;
        }

        $result['todo'] = "concept";
        return $result;
        
    }
    private function INTENT($dialog){
        $final_intent = null;
        $result = array();
        $decision = $dialog->getDecision();

        $prev = $dialog->getPrevConfiguration();


        if (empty($decision[final_intent])){

            $sentence = $dialog->getSentence();
            $possibleIntent = $this->util->findIntent($sentence);

            if (!empty($possibleIntent)){
                $final_intent = $possibleIntent;
            }
                        
            $dialog->setDecision(final_intent, $final_intent);

        }
        $result['state'] = $dialog->getState();
        $result['todo'] = "intent";
        return $result;

    }

    private function CONCEPT($dialog){
        $result = array();
        $result['state'] = $dialog->getState();
        $result['todo'] = "concept";
        return $result;
    }
}
