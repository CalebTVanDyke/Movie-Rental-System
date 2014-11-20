<?php
require_once('dbutil.php');
require_once('book.php');
require_once('shelf.php');

class Library
{
	const SHELF_COUNT = 10;

	public static function showLib(){
		// for each shelf
		$done = false;
		$shelfIDs = self::getShelfIDs();
		for($i = 0; !$done && $i < count($shelfIDs) && $i < self::SHELF_COUNT; $i++){
			$shelfID = $shelfIDs[$i];
			// for each book
			$books = Shelf::getBooksOnShelf($shelfID);
     		// print book name
     		$rowStr = "<TR>";
			for($j = 0; $j < Shelf::MAX_SIZE; $j++){
				if(count($books) > $j && $book = $books[$j]){
					// $rowStr .= "<TD class='book'>". $book->getCopyID() ."</TD>";
					$rowStr .= "<TD class='book' style='border-top:none'>";
					$rowStr .= "<img class='span1' src='images/". $book->getTitle() .".jpg' alt='". $book->getTitle() ."' />"; // width=50% height=175
					$rowStr .= "<input type='hidden' value='". $book->getCopyID() ."'>";
					$rowStr .= "</TD>";
				} else
					$rowStr .= "<TD style='border-top:none'></TD>";
			}
			$rowStr .= "</TR>";
			// Only print row if there is content
			if(strpos($rowStr, "class='book'") == true)
				echo $rowStr;
		}
	}

	public static function doesBookExist($booktitle){
		$exists = false;
		$conn = DB::getConnection();

		$result = mysqli_query($conn, "SELECT * from books where Groupnumber=10 and Booktitle='".$booktitle."'");
		if($row = mysqli_fetch_array($result)){
			$exists = true;
		}

		return $exists;
	}

	public static function addBook($bookTitle, $author, $num){
		$conn = DB::getConnection();

		// Create book if it doesn't exist
		if(!self::doesBookExist($bookTitle)){
			mysqli_query($conn, "INSERT INTO books VALUES (10, ".Book::getNextBookId().",'".$bookTitle."', '".$author."')");
		}

		for($i = 0; $i < $num; $i++){
			// Create the correct number of book copies in copy table
			$copyID = Book::getNextCopyId();
			mysqli_query($conn, "INSERT INTO bookscopy VALUES(10, ".$copyID.", ".Book::getBookId($bookTitle).")");

			// Add copies to shelves
			self::addCopyToShelf($copyID);
		}
	}

	public static function addCopyToShelf($copyID){
		$conn = DB::getConnection();
		$shelfID = self::getNonFullShelfID();
		mysqli_query($conn, "INSERT INTO shelves VALUES(10, ".$shelfID.", ". $copyID .")");
	}

	public static function deleteCopy($copyID){
		$conn = DB::getConnection();
		mysqli_query($conn, "DELETE FROM bookscopy WHERE Groupnumber=10 and Copyid=".$copyID);
		self::deleteCopyFromShelf($copyID);
	}

	public static function deleteCopyFromShelf($copyId){
		$conn = DB::getConnection();
		mysqli_query($conn, "DELETE FROM shelves where Groupnumber=10 and Copyid=".$copyId);
	}

	public static function getNonFullShelfID(){
		$conn = DB::getConnection();
		$result = mysqli_query($conn, "SELECT * from shelves where Groupnumber=10 ORDER BY Shelfid");

		$shelfs = self::getShelfIDs();
		$curShelf = 0; 
		$booksOnShelf = -1;
		while($row = mysqli_fetch_array($result)){
			$booksOnShelf++;
			// When shelf is not full
			if($shelfs[$curShelf] < $row['Shelfid'] && $booksOnShelf < Shelf::MAX_SIZE){
				return $shelfs[$curShelf];            	
			}
			// If max size reached, move to next shelf
			if($booksOnShelf >= Shelf::MAX_SIZE) {
				$curShelf++;
				$booksOnShelf = 0;
			}
		}
		if(count($shelfs) == 0){
			return 0; // Will get auto incremented by default;
		}
		if($booksOnShelf >= Shelf::MAX_SIZE-1){
			return $shelfs[$curShelf]+1;
		} else{
			return $shelfs[$curShelf];
		}
	}

	public static function getBook($copyID){
		$conn = DB::getConnection();
		$result = mysqli_query($conn, 
       		"SELECT * FROM shelves JOIN bookscopy ON shelves.Copyid=bookscopy.Copyid ".
       		"JOIN books ON bookscopy.Bookid=books.Bookid WHERE shelves.Groupnumber=10 ".
       		"and bookscopy.Groupnumber=10 and books.Groupnumber=10 and bookscopy.Copyid=".$copyID
       	);
		if($row = mysqli_fetch_array($result)){
			return new Book($row['Booktitle'], $row['Author'], $row['Copyid'], $row['Bookid']);
		}else
			return null;
	}

	public static function getShelfIDs(){
		$conn = DB::getConnection();
		$result = mysqli_query($conn, "SELECT * from shelves WHERE Groupnumber=10 ORDER BY Shelfid");
		$shelves = array();
		while($row = mysqli_fetch_array($result)){
			if(!in_array($row['Shelfid'], $shelves))
				$shelves[] = $row['Shelfid'];
		}
		return $shelves;
	}
}
?>