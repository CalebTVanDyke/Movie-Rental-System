<?php
require_once('objects/library.php');
require_once('objects/user.php');

session_start();

if(isset($_GET['function'])){
	$function = $_GET['function'];

	if($function === 'showLib'){
		Library::showLib();
		return;
	}
	if($function === 'addBook'){
		Library::addBook($_GET['title'], $_GET['author'], $_GET['qty']);
		return;
	}
	if($function === 'removeBook'){
		Library::deleteCopy($_GET['copyID']);
		return;
	}
	if($function == 'getBookInfo'){
		$book = Library::getBook($_GET['copyID']);
		echo "<h1 style='color:#333333'>".$book->getTitle()."</h1>".
		     "<img src='images/".$book->getTitle().".jpg' alt='".$book->getTitle()."' border=10 style='width:500;'/>".
			 "<BR><BR>Director:\t".$book->getAuthor().
			 "<BR>Movie ID:\t".$book->getID().
			 "<BR>Copy ID:\t".$book->getCopyID();
	 	return;
	}
	if($function == 'checkoutBook'){
		User::checkoutBook($_GET['userID'], $_GET['copyID']);
		return;
	}
	if($function == 'returnBook'){
		User::returnBook($_GET['userID'], $_GET['copyID']);
		return;
	}
	if($function == 'viewLoans'){
		$userName = $_GET['user'];
		$exact = $_GET['exact'];
		User::viewLoanHistory($userName, $exact);
		return;
	}
	if($function == 'viewCheckOut'){
		$userName = $_GET['userID'];
		User::viewCheckedOutBook($userName);
		return;
	}
	if($function == 'validate'){
		$bookName = $_GET['bookName'];
		$author   = $_GET['author'];
		$qty      = $_GET['qty'];
		if(!ctype_digit($qty)){
			echo "Invalid qty: " .$qty;
			return;
		}
		echo "PASSED";
		return;
	}
	if($function == 'email'){
		if(!isset($_SESSION['notified'])){
			$userEmail = $_GET['userEmail'];
			if(mail($userEmail,
					'[Unified Rental Service] Upcoming rental deadline',
					'One of your rentals is due today, make sure you bring that back to us!'))
			{
				$_SESSION['notified'] = true;
				echo "You have a rental due today!\nAn email reminder has been sent to:\n".$userEmail;
				return;
			}
			echo "Unable to send an email reminder to your email address at\n".$userEmail;
			return;
		} else {
			return;
		}
	}
	if($function == 'checkDueToday'){
		$userName = $_GET['userID'];
		User::hasRentalDueToday($userName);
		return;
	}	
}
?>