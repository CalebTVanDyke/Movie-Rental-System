<?php
require_once('objects/user.php');
session_start();
$user = unserialize($_SESSION['user']);
?>
<html>
<head>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel='stylesheet' type='text/css'>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
<!-- nav bar, goes at the top of every page -->
	<nav class="navbar navbar-inverse" role="navigation">
		<div class="collapse navbar-collapse">
	    <ul class="nav navbar-nav">
	    	<li><h4 class="navbar-text"><b>Unified Rental Service</b></h4>
	    	<li><a href="home.php">Home</a></li>
			<li class="active"><a href="#">Account Management</a></li>
		</ul>
	    <ul class="nav navbar-nav navbar-right">
			<li><button type="button" class="btn btn-danger navbar-btn" onclick="logout()">Logout <?php echo $user->getUsername() ?></button></li>
			<li><a style="padding-right:10px"></a>
		</ul>
		</div>
	</nav>
<!-- main content -->
<div class="col-md-offset-1">
	<div class="urs-container col-md-10">
		<div class="row student" style="display:none">
			<div class="col-md-5">
				<h2>User use cases</h2>
				<input id="returnBookText" type="text" placeholder="Copyid"> <button id="returnBookBtn" type="button" class="btn btn-success">Return a Movie</button><br>
			</div>
			<div class="col-md-5">
				<h3>Outstanding rentals</h3>
				<table id="checkOutTable" class="table table-condensed" style="background-color:#ffffff">
					<TR><TH>Copy ID</TH><TH>Due Date</TH></TR>
				</table>
			</div>
		</div>

		<div class="row teacher" style="display:none">
			<div class="col-md-3">
				<h3><u>Add a Movie</u></h3>
				<input id="addBookName" name="addBookName" type="text" placeholder="MovieName"> <br>
				<input id="addAuthor" name="addAuthor" type="text" placeholder="Director"><br>
				<input id="addQty" name="addQty" type="text" placeholder="Qty"><br><br>
				<button id="addBookBtn" type="submit" value="addBook" class="btn btn-success">Add a Movie</button>
				<hr>
				<h3><u>Query Rental History</u></h3>
				<input id="viewUserHistory" class="form-control" type="text" placeholder="Username"> <br>
				<button id="viewLoansBtn" type="button" class="btn btn-success">View history</button>
			</div>
			<div class="col-md-7">
				<h2 id="loanHeader">View Rental History by User</h2>
				<table id="historyTable" class="table table-condensed" style="background-color:#ffffff">
					<TR class='info'><TH >Copy ID</TH><TH>Username</TH><TH >Due Date</TH><TH >Date Returned</TH><TR>
				</table>
			</div>
		</div>
	</div>
</div>
</body>
</html>
<script type="text/javascript">
function logout(){
	window.location.href = "index.php";
}
function checkOutTable(){
	var username = "<?php echo $user->getUsername() ?>";
	$.ajax({
		type : "GET",
		url	 : "router.php",
		data : {"function" :"viewCheckOut", "userID"	:username},
		success	: function(result){
			$('#checkOutTable').html(result);
		}
	});
}
$('#viewLoansBtn').click(function(){
	var input = $('#viewUserHistory').val();
	$.ajax({
		type : "GET",
		url	 : "router.php",
		data : {"function" :"viewLoans", "user"	:input, "exact":"true"},
		success	: function(result){
			$('#historyTable').html(result);
		}
	});
	$('#viewUserHistory').val("");
});
$('#viewUserHistory').keyup(function() {
	var input = $('#viewUserHistory').val();
	$.ajax({
		type : "GET",
		url	 : "router.php",
		data : {"function" :"viewLoans", "user"	:input, "exact":"false"},
		success	: function(result){
			$('#historyTable').html(result);
		}
	});
});
$('#addBookBtn').click(function(){
	var bookName = $("#addBookName").val();
	var author 	 = $("#addAuthor").val();
	var qty      = $("#addQty").val();
	var validated = false;
	$.ajax({
		type : "GET",
		url  : "router.php",
		data : {"function":"validate","bookName":bookName,"author":author,"qty":qty},
		async:   false,
		success : function(result){
			if(result == "PASSED")
				validated = true;
			else
				alert(result);
		}
	})
	if(!validated)
		return;
	$.ajax({
		type : "GET",
		url  : "router.php",
		data : {"function":"addBook","title":bookName,"author":author,"qty":qty},
		success : function(result){
			updateLib();
		}
	});
	$("#addBookName").val("");
	$("#addAuthor").val("");
	$("#addQty").val("");
});
$('#returnBookBtn').click(function(){
	var input = $("#returnBookText").val();
	var username = "<?php echo $user->getUsername() ?>";
	$.ajax({
		type : "GET",
		url  : "router.php",
		data : {"function":"returnBook","copyID":input.trim(),"userID":username},
		success : function(result){
			checkOutTable();
		}
	});
	$("#returnBookText").val("");
});
$(document).ready(function(){
	checkOutTable();
	if(<?php echo $user->isLib() ?>)
		$(".teacher").css("display","");
	else
		$(".student").css("display","");
	$('#deleteBookBtn').click(removeBook);
});
</script>