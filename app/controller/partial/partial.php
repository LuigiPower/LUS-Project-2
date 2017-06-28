<?php 
class  PARTIAL {
	private $response;

	public function __construct(){
		$this->response = load_model('responseUtil');

    }
    
	public function analizeUtterance($sentence, $partial1, $partial2){
		return $this->response->isHeDefiningNewConcept($sentence, $partial1, $partial2);
	}
}