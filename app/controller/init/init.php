<?php 
class  INIT {
    private $dialog;
	private $response;

	public function __construct(){
        $this->dialog = load_model('dialogManager');
        $this->response = load_model('responseManager');
    }
    
	public function analizeUtterance($sentence){

		$this->dialog->simpleAnalysis($sentence);
		return $this->response->generateResponse($this->dialog);
	}
}
