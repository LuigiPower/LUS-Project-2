<?php 
class  DATABASE_MODEL {
	private $QC;
    private $DB;
	
	public function __construct(){
        $this->QC  = new Slu2DB();
        $this->DB  = new QueryDB();
    }

    public function ask($concepts, $intent){

        $query = $this->QC->slu2sql($concepts, $intent);
        $db_results = $this->DB->query($query);

        $result = array();
        $intent_mapped = explode( ', ', $this->QC->db_mapping($intent));
        $concept_mapped =[];
        foreach ($concepts as $value) {
            $tmp = explode( ', ', $this->QC->db_mapping($value[1]));
            foreach ($tmp as $concept) {
                $concept_mapped[$concept] = $concept;
            }
        }
        $concept_mapped = array_keys($concept_mapped);

        $intent_keys=array();
        $concepts_keys=array();

        foreach ($db_results as $value) {
            $keys = array_keys($value);
            foreach ($keys as $key) {
                // var_dump($key);
                // var_dump($intent_mapped);
                if (in_array($key, $intent_mapped)){
                    // print_r("\nin\n");
                    $intent_keys[$key]=$key;
                }
                else if (in_array($key, $concept_mapped)){
                    $concepts_keys[$key]=$key;
                }
            }
        }
        // foreach ($db_results as $value) {
        //     foreach ($intent_keys as $key) {
        //         $result['intent'][] = array($key,$value[$key]);
        //     }
        //     foreach ($concepts_keys as $key) {
        //         $result['concepts'][] = array($key,$value[$key]);
        //     }
        // }

         // var_dump($query);
        // var_dump($db_results);
        // var_dump($intent_mapped);
        // var_dump($intent_keys);
        // var_dump($concepts_keys);
        // var_dump($result);

        $result['query'] = $query;
        $result['intent'] = array_keys($intent_keys);
        $result['concepts'] = array_keys($concepts_keys);
        $result['db'] = $db_results;
        return $result;
    }
}
