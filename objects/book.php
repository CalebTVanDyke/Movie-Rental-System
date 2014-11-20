<?php
require_once('dbutil.php');
require_once('library.php');
require_once('shelf.php');

class Book
{
	private $_title;
	private $_author;
	private $_copyID;
	private $_bookID;

	function __construct($title, $author, $copyID, $bookID){
		$this->_title  = $title;
		$this->_author = $author;
		$this->_copyID = $copyID;
		$this->_bookID = $bookID;
	}	

	public function getTitle(){
		return $this->_title;
	}

	public function getAuthor(){
		return $this->_author;
	}

	public function getCopyID(){
		return $this->_copyID;
	}

	public function getID(){
		return $this->_bookID;
	}

	public static function getBookId($bookTitle){
		$conn = DB::getConnection();

		$result = mysqli_query($conn, "SELECT Bookid from books where Groupnumber=10 and Booktitle='".$bookTitle."'");
		$row = mysqli_fetch_array($result);
		$bookId = $row['Bookid'];

		return $bookId;
	}

	public static function getNextCopyId(){
		$conn = DB::getConnection();
		$result = mysqli_query($conn, "SELECT Copyid from bookscopy where Groupnumber = 10 ORDER BY Copyid DESC");
		$row = mysqli_fetch_array($result);
		$nextId = $row['Copyid'];

		return $nextId + 1;
	}

	public static function getNextBookId(){
		$conn = DB::getConnection();
		$result = mysqli_query($conn, "SELECT Bookid from books where Groupnumber = 10 ORDER BY Bookid DESC");
		$row = mysqli_fetch_array($result);
		$nextId = $row['Bookid'];

		return $nextId + 1;
	}

	public static function getBookFromDB($bookID){
		$conn = DB::getConnection();

		$result = mysqli_query($conn, 
			"SELECT * from books ".
			"INNER JOIN bookscopy ".
			"ON books.Bookid=bookscopy.Bookid ".
			"WHERE Groupnumber=10");

		$book = null;
		if($row = mysqli_fetch_array($result)){
			$title  = $row['Booktitle'];
			$author = $row['Author'];
			$copyID = $row['Copyid'];
			$bookID = $row['Bookid'];
			$book = new Book($title, $author, $copyID, $bookID);
		}
		else
			echo "COULDNT GET BOOK IN DB<BR>";

		return $book;
	}
}
?>