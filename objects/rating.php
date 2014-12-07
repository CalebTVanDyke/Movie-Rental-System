<?php 

/**
* 
*/
class rating
{

	private $file = "ratings.data.txt";
	private $data = array();
	
	function __construct()
	{
		$all = file_get_contents($this->file);
		if($all){
			$this->data = unserialize($all);
		}
	}

	public function getRatings($bookID){
		if(isset($this->data[$bookID])){
			return $this->data[$bookID];
		}else{
			$data['bookID'] = $bookID;
			$data['numRatings'] = 0;
			$data['points'] = 0;
			$data['avg'] = 0;
			return $data;
		}
	}

	public function updateRating($bookID, $score){
		if($this->data[$bookID]){
			$this->data[$bookID]['numRatings'] += 1;
			$this->data[$bookID]['points'] += $score;
		} else {
			$this->data[$bookID]['numRatings'] = 1;
			$this->data[$bookID]['points'] = $score;
		}
		$this->data[$bookID]['avg'] = round( $this->data[$bookID]['points'] / $this->data[$bookID]['numRatings']);
		file_put_contents($this->file, serialize($this->data));
		return $this->getRatings($bookID);
	}
}

?>