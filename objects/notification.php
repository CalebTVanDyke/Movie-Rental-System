<?php 

/**
* 
*/
class Notification
{

	private $file = "notification.data.txt";
	private $data = array();
	
	function __construct()
	{
		$all = file_get_contents($this->file);
		if($all){
			$this->data = unserialize($all);
		}
	}

	public function getNotifications($bookTitle){
		if(isset($this->data[$bookTitle])){
			return $this->data[$bookTitle];
		}else{
			return array();
		}
	}

	public function storeNotification($bookTitle, $username){
		if(isset($this->data[$bookTitle])){
			array_push($this->data[$bookTitle]['userList'],$username);
		} else {
			$this->data[$bookTitle]['userList'] = array($username);
		}
		file_put_contents($this->file, serialize($this->data));
		return "Success";
	}
}

?>