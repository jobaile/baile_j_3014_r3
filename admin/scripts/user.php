<?php 
	function createUser($fname,$username,$password,$email){
			include('connect.php');
			
		$create_user_query = 'INSERT INTO tbl_user(user_fname,user_name,user_pass,user_email, user_firstlogin)';
		$create_user_query .= ' VALUES(:fname,:username,:password,:email, "1")';

		$create_user_set = $pdo->prepare($create_user_query);
		$create_user_set->execute(
			array(
				':fname'=>$fname,
				':username'=>$username,
				':password'=>$password,
				':email'=>$email
			)
		);
		if($create_user_set->rowCount()){
			redirect_to('index.php');
		}else{
			$message = 'Your hiring practices have failed you.. this individual sucks...';
			return $message;
		}

}

function editUser($id, $fname, $username, $password, $email) {
include('connect.php');
	$update_user_query = 'UPDATE tbl_user SET user_fname=:fname, user_name=:username, user_pass=:password, user_email=:email, user_firstlogin = "2" WHERE user_id = :id';

		$update_user_set = $pdo->prepare($update_user_query);
		$update_user_set->execute(
			array(
				':id'=>$id,
				':fname'=>$fname,
				':username'=>$username,
				':password'=>$password,
				':email'=>$email
			)
		);

		if($update_user_set->rowCount()){
			redirect_to('index.php');
		}else{
			$message = 'Something went wrong, oops!';
			return $message;
		}

}