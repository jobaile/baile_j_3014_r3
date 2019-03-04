<?php 

function login($username, $password, $ip){
	require_once('connect.php');
	//Check if username exists

	$check_exist_query = 'SELECT COUNT(*) FROM tbl_user';
	$check_exist_query .= ' WHERE user_name = :username';

	$user_set = $pdo->prepare($check_exist_query);
	$user_set->execute(
		array(
			':username'=>$username
		)
	);

	if($user_set->fetchColumn()>0){
		$get_user_query = 'SELECT * FROM tbl_user WHERE user_name = :username';
		$get_user_query .= ' AND user_pass = :password';


		$get_user_set = $pdo->prepare($get_user_query);

		//TODO: don't forget to bind the placeholders in here!
		$get_user_set->execute(
			array(
				':username'=>$username,
				':password'=>$password
			)
		);

		while($found_user = $get_user_set->fetch(PDO::FETCH_ASSOC)){
			//Checks if user is logging into account too late after account creation
			  // account will be suspended if user has not logged in after 30 mins of creation
			$suspended_query="SELECT * FROM tbl_user WHERE user_date < DATE_SUB(NOW(), INTERVAL 30 MINUTE) AND user_login = '0000-00-00 00:00:00' AND user_name = :user";
			$get_suspended_account = $pdo->prepare($suspended_query);
			$get_suspended_account->execute(
				array (
				":user" => $username
				)
			);

			if ($get_suspended_account->fetchColumn() > 0) {
				$message = 'Login Suspended - Please contact an administrator.';
				return $message;
			}

			//first login + checked time
			$id = $found_user['user_id'];
			$_SESSION['user_id'] = $id;
			$_SESSION['user_date'] = $found_user['user_login'];
			$_SESSION['user_name'] = $found_user['user_name'];
			$_SESSION['user_firstlogin'] = $firstlogin;

			//this will only update IF the user logged in before their 30 minutes
			$set_login_query = "UPDATE tbl_user SET user_login = NOW() WHERE user_id = :user LIMIT 1";
				$set_login = $pdo->prepare($set_login_query);
				$set_login->execute(
				array(
					":user" => $id
				)
			);

			//this makes sure that users are directed on first login
			$firstlogin = $found_user['user_firstlogin'];
              if($firstlogin === '1'){ //1 means first login
                redirect_to("admin_edituser.php");
              } else if ($firstlogin === '2') { //2 means after not first login
                redirect_to("index.php");
              }

			//Update user login IP
			$update_ip_query = 'UPDATE tbl_user SET user_ip=:ip WHERE user_id=:id';
			$update_ip_set = $pdo->prepare($update_ip_query);
			$update_ip_set->execute(
				array(
					':ip'=>$ip,
					':id'=>$id
				)
			);
		}

		if(empty($id)){
			$message = 'Login Failed! Double check your credentials.';
			return $message;
		}
		redirect_to('index.php');
	}else{
		$message = 'Login Failed!';
		return $message;
	}

}