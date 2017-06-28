<?php 
class  DATABASE {
	private $db;

	public function __construct(){
		$this->db = load_model('database');

    }
    
	public function dbRequest($intent, $concepts){
		return $this->db->ask($concepts, $intent);
	}
}