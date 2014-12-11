<?php 

/**
* 
*/
class genre
{

	private $file = "genre.data.txt";
	private $data = array();
	
	function __construct()
	{
		$all = file_get_contents($this->file);
		if($all){
			$this->data = unserialize($all);
		}
	}

	public function getGenre($bookID){
		if(isset($this->data[$bookID])){
			return $this->data[$bookID];
		}else{
			$data['bookID'] = $bookID;
			$data['genre'] = "";
			return $data;
		}
	}

	public function updateGenre($bookID, $genre){
		$this->data[$bookID]['genre'] = $genre;
		file_put_contents($this->file, serialize($this->data));
		return $this->getGenre($bookID);
	}
}

?>