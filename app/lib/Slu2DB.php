<?php
/**
 * Class for Attribute-Value & Utterance label to SQL Query Conversion
 * 
 * @author estepanov
 */
class Slu2DB {
	
	/**
	 * Map SLU concepts & utterance classes to DB columns
	 * 
	 * EXTEND!
	 */
	private $mapping = array(
		'actor' => 'actors',
		'actor_name' => 'actors',
		'award' => '',
		'award_category' => '',
		'award_category_count' => '',
		'award_ceremony' => '',
		'award_count' => '',
		'birth_date' => 'year',
		'budget' => 'budget',
		'character' => 'actors',
		'composer' => 'director',
		'country' => 'country',
		'date' => 'year',
		'director' => 'director',
		'director_name' => 'director',
		'genre' => 'genres',
		'language' => 'language',
		'media' => 'color',
		'movie' => 'title',
		'movie_count' => 'title',
		'movie_name' => 'title',
		'movie_other' => 'title',
		'organization' => 'movie_imdb_link, country',
		'other' => 'movie_imdb_link',
		'person' => 'actors',
		'person_name' => 'actors',
		'picture' => 'movie_imdb_link',
		'producer' => 'director',
		'producer_count' => 'director',
		'rating' => 'imdb_score, movie_facebook_likes',
		'release_date' => 'year',
		'revenue' => 'gross',
		'review' => 'movie_facebook_likes, imdb_score',
		'runtime' => 'duration',
		'star_rating' => 'imdb_score, movie_facebook_likes',
		'subjects' => '',
		'synopsis' => 'plot_keywords',
		'theater' => 'country',
		'trailer' => 'plot_keywords, movie_imdb_link',
		'writer' => 'director',
		'producer.name' => 'director',
        'character.name' => 'actors',
        'person.nationality' => 'country',
        'actor.nationality' => 'country',
        'movie.release_date' => 'year',
        'award.ceremony' => '',
        'movie.language' => 'language',
        'country.name' => 'country',
        'rating.name' => 'imdb_score, movie_facebook_likes',
        'movie.subject' => 'title',
        'director.nationality' => 'country',
        'actor.name' => 'actors',
        'movie.description' => 'movie_imdb_link',
        'movie.star_rating' => 'imdb_score, movie_facebook_likes',
        'movie.gross_revenue' => 'gross',
        'award.category' => '',
        'actor.type' => 'actors',
        'movie.release_region' => 'country',
        'movie.genre' => 'genres',
        'movie.location' => 'country',
        'movie.name' => 'title',
        'person.name' => 'actors',
        'director.name' => 'director'
	);
	
	/**
	 * Returns db column w.r.t. $str
	 */
	public function db_mapping($str) {
		return $this->mapping[$str];
	}
	
	/**
	 * Meta function to
	 * - map slu concepts to DB
	 * - map utterance classifier class to db
	 * - construct sql query
	 */
	public function slu2sql($concepts, $class) {
		// var_dump($concepts);
		// var_dump($class);
		$db_class    = $this->db_mapping($class);

		
		$db_concepts = array();
		foreach ($concepts as $value) {
				// var_dump($value);
				// $value[1] = str_replace(".", "_", $value[1]);
				$item=array();
				$item[]=$this->db_mapping($value[1]);
				$item[]=$value[0];
				$db_concepts[$value[1]][] = $item;

				$db_class = $db_class.", ".$this->db_mapping($value[1]);
		}
		
		
				
		// construct SQL query
		$query  = 'SELECT ';
		$query .= '*';
		$query .= ' FROM movie WHERE ';
		
		$tmp = array();
		foreach ($db_concepts as $key => $val) {
			$mid = array();
			//$tmp[] = $attr . ' LIKE "%' . $val . '%"';
			foreach ($val as $value) {

				$mid[] = $value[0] . ' LIKE "' . $value[1] . '%"';
			}

			$mid[0] = "(".$mid[0];
			$mid[count($mid)-1] = $mid[count($mid)-1].")";
			$mid = implode(' OR ', $mid);


			$tmp[] = $mid;


		}
		$query .= implode(' AND ', $tmp);
		$query .= ';';

		return $query;
	}
}
