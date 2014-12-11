<?php
require_once('dbutil.php');
require_once('book.php');
require_once('library.php');

class Shelf
{
	const MAX_SIZE = 10;
	// array of book id

	function __construct(){
	}

	public static function getBooksOnShelf($shelfId){
		$conn = DB::getConnection();
		$books = array();
        $result = mysqli_query($conn, 
       		"SELECT * FROM shelves JOIN bookscopy ON shelves.Copyid=bookscopy.Copyid ".
       		"JOIN books ON bookscopy.Bookid=books.Bookid WHERE shelves.Groupnumber=10 ".
       		"and bookscopy.Groupnumber=10 and books.Groupnumber=10 and shelves.Shelfid=".$shelfId
       	);
		for($i = 0; $i < 10 && ($row = mysqli_fetch_array($result)); $i++)
			$books[] = new Book($row['Booktitle'], $row['Author'], $row['Copyid'], $row['Bookid']);

		return $books;
	}
}
?>