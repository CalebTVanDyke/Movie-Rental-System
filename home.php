<?php
require_once('objects/user.php');
require_once('objects/book.php');
require_once('objects/library.php');
session_start();
$user = unserialize($_SESSION['user']);

mail("andy.guibert@gmail.com",
	'[Unified Rental Service] Upcoming rental deadline',
	'One of your rentals is due today, make sure you bring that back to us!');
?>

<html>
<head>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel='stylesheet' type='text/css'>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<title>Unified Rental Service</title>
<body>
	<!-- nav bar, goes at the top of every page -->
	<nav class="navbar navbar-inverse" role="navigation">
		<div class="collapse navbar-collapse">
	    <ul class="nav navbar-nav">
	    	<li><h4 class="navbar-text"><b>Unified Rental Service</b></h4>
	    	<li class="active"><a href="#">Home</a></li>
			<li><a href="manage.php">Account Management</a></li>
		</ul>
	    <ul class="nav navbar-nav navbar-right">
			<li><button type="button" class="btn btn-danger navbar-btn" onclick="logout()">Logout <?php echo $user->getUsername() ?></button></li>
			<li><a style="padding-right:10px"></a>
		</ul>
		</div>
	</nav>
	<div class="col-md-offset-1">
		<div >
			<h1 style="">Unified Rental Service</h1>
			<div class="col-md-10 urs-container" style="padding-left:25px">
				<table id="lib" class="table">
				</table>
			</div>
		</div>
	</div>
	<!-- Modal for when a table cell is clicked -->
	<div id="mymodal" class="modal fade">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <input id="modal-copyid" type="hidden" value="">
	      <div class="modal-body" align="center">
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	        <button id="deleteBookBtn" type="button" class="btn btn-danger teacher" style="display:none" data-dismiss="modal">Delete</button>
	        <button id="checkoutBookBtn" type="button" class="btn btn-primary student" style="display:none" data-dismiss="modal">Checkout</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</body>
<script>
function logout(){
	window.location.href = "index.php";
}
function checkRentalDue(){
	if(<?php echo $user->isLib() ?>)
		return;
	var username = "<?php echo $user->getUsername() ?>";
	$.ajax({
		type : "GET",
		url  : "router.php",
		data : {"function":"checkDueToday","userID":username},
		success : function(result){
			if(result == "PASSED"){
				sendMail();
			}
		}
	});
}
function sendMail(){
	var userEmail = "<?php echo $user->getEmail() ?>";
	$.ajax({
		type : "GET",
		url  : "router.php",
		data : {"function":"email","userEmail":userEmail},
		success : function(result){
			if(result != "")
				alert(result);
		}
	});
}
function showModal(title, body, copyID){
    	// $('#mymodal .modal-title').html(title);
    	$('#mymodal .modal-body').html(body);
    	$('#modal-copyid').val(copyID);
        $('#mymodal').modal('show');
}
function getBookInfo(copyID){
	$.ajax({
		type  : "GET",
		url   : "router.php",
		data  : {"function": "getBookInfo","copyID": copyID},
		success: function(result){
			showModal("Information for Movie " + copyID, result, copyID);
		}
	});
}
function updateLib(){
	$.ajax({
		type : "GET",
		url  : "router.php",
		data : {"function":"showLib"},
		success : function(result){
			$("#lib").html(result);
			$('.book').click(function(){
				getBookInfo($(this).find("input").val());
			})
		} 
	});
};
function removeBook(){
	var input = $("#modal-copyid").val();
	$.ajax({
		type : "GET",
		url  : "router.php",
		data : {"function":"removeBook","copyID":input.trim()},
		success : function(result){
			updateLib();
		}
	});
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
$('#checkoutBookBtn').click(function(){
	var input = $("#modal-copyid").val();
	var username = "<?php echo $user->getUsername() ?>";
	$.ajax({
		type : "GET",
		url  : "router.php",
		data : {"function":"checkoutBook","copyID":input.trim(),"userID":username},
		success : function(result){
			if(result == 'FAILED')
				alert("You have already checked out book " + input + " before.");
			updateLib();
			checkOutTable();
		}
	});
});
$(document).ready(function(){
	updateLib();
	checkOutTable();
	if(<?php echo $user->isLib() ?>)
		$(".teacher").css("display","");
	else
		$(".student").css("display","");
	$('#deleteBookBtn').click(removeBook);
	checkRentalDue();
});
</script>
</html>
