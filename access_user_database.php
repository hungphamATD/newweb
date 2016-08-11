<?php
require_once("./../db.php");
require_once("./session.php");

$dbobj=new db_gafam;
$userobj=new user_gafam;
$servername = "localhost";
$username = "root";
$password = "";
$dbname="gafam";

//connect to database, if failed connection then make new database
$dbobj->connectDB($servername, $username, $password, $dbname);

$table_name="user_tb";
$insert_vars="type,username,email,password,salt,first_name,last_name,last_login,last_activity,activation,time_created";
//user columns
$u_type='';
$u_name='';
$u_email='';
$u_pass='';
$u_salt='';
$u_fname='';
$u_lname='';
$u_lastlogin='';
$u_lastactiv='';
$u_acti='';
$u_time_cre='';
//

$insert_vals="
				'$u_type',
				'$u_name',
				'$u_email',
				'$u_pass',
				'$u_salt',
				'$u_fname',
				'$u_lname',
				'$u_lastlogin',
				'$u_lastactiv',
				'$u_acti',
				'$u_time_cre'
			";
//insert data to user table
//$dbobj->insertToTable($table_name,$insert_vars,$insert_vals);

$userobj->init();
$userobj->ssobj->start();
$userobj->dbobj->connectDB($servername, $username, $password, $dbname);
//$userobj->addUser();
//$user=$userobj->getUser();
if($user=$userobj->login()){
	echo "LOGINED";
}
else{
	echo "INLOGIN";
}


//echo count($user);
echo "<br>";
foreach($user as $i => $value){
	echo $value."<br>";
}
//echo $user;
class user_gafam {
	var $u_id='';
	var $u_type='';
	var $u_name='hung11';
	var $u_email='';
	var $u_pass='4334rt';
	var $u_salt='';
	var $u_fname='';
	var $u_lname='';
	var $u_lastlogin='';
	var $u_lastactiv='';
	var $u_acti='';
	var $u_time_cre='';
	
	function init() {
		$this->dbobj = new db_gafam;
		$this->ssobj = new session_gafam;
	}
	
	function addUser() {
			if(empty($this->u_type)) {
					$this->u_type = 'normal';
			}
			$user = $this->getUser();

			if(empty($user['a_name']) && $this->isPassword() && $this->isUsername()) {
					$this->u_salt            = $this->generateSalt();
					$password              = $this->generatePassword($this->u_pass, $this->u_salt);
					$activation            = md5($this->u_pass . time() . rand());
					$creattime=time();
					//$this->sendactiviation = ossn_call_hook('user', 'send:activation', false, $this->sendactiviation);
					//$this->validated       = ossn_call_hook('user', 'create:validated', false, $this->validated);
					$this->validated = true;//hack later
					if($this->validated === true) {
							//don't set null , set empty value for users created by admin
							$activation = '';
					}
					$insert_vars="type,username,email,password,salt,first_name,last_name,last_login,last_activity,activation,time_created";
					$insert_vals="

							'$this->u_type',
							'$this->u_name',
							'$this->u_email',
							'$password',
							'$this->u_salt',
							'$this->u_fname',
							'$this->u_lname',
							'0',
							'0',
							'$activation',
							'$creattime'
					";
					$table_name="user_tb";
					//insert data to user table
					if($this->dbobj->insertToTable($table_name,$insert_vars,$insert_vals)){
							return true;
					}
			}
			return false;
	}


	function getUser() {
			$user='';
			if(!empty($this->u_email)) {				
					$params['from']   = 'user_tb';
					$params['wheres'] = array(
							"email='{$this->u_email}'"
					);
					$this->dbobj->select_ex($params);
					$user=$this->fetchUserData();
			}
			if(empty($user) && !empty($this->u_name)) {
					$params['from']   = 'user_tb';
					$params['wheres'] = array(
							"username='{$this->u_name}'"
					);
					$this->dbobj->select_ex($params);
					$user=$this->fetchUserData();
			}
			if(empty($user) && !empty($this->u_id)) {
					$params['from']   = 'user_tb';
					$params['wheres'] = array(
							"id='{$this->u_id}'"
					);
					$this->dbobj->select_ex($params);
					$user=$this->fetchUserData();

			}
			if(!count($user)) {
					return false;
			}

			return $user;

	}
	
	function isPassword() {
			if(strlen($this->u_pass) > 5) {
					return true;
			}
			return false;
	}
	
	function isUsername() {
			if(preg_match("/^[a-zA-Z0-9]+$/", $this->u_name) && strlen($this->u_name) > 4) {
					return true;
			}
			return false;
	}
	
	function generateSalt() {
			return substr(uniqid(), 5);
	}
	
	function generatePassword($password = '', $salt = '') {
			return md5($password . $salt);
	}
	
	function isEmail() {
				if(filter_var($this->u_email, FILTER_VALIDATE_EMAIL)) {
						return true;
				}
				return false;
	}
	
	function login() {
				$user     = $this->getUser();
				$salt     = $user['a_salt'];
				$password = $this->generatePassword($this->u_pass . $salt);
				if($password == $user['a_pass'] && $user['a_acti'] == NULL) {
						unset($user['a_pass']);
						unset($user['a_salt']);
						
						$this->ssobj->assign('GAFAM_USER', $user);
						$this->updateLastLogin();

						return $user;
				}
				return false;
	}

	function updateLastLogin() {
		
				$user = $this->returnUserLoggedin();
				$id   = $user['a_id'];
				$table_name = "user_tb";
				$setvalues = "last_login='{time}'";
				$condition = "id='{$id}'";
				if($id > 0 && $this->updateTable($table_name,$setvalues,$condition)) {
						return true;
				}
				return false;
	}
	
	function returnUserLoggedin() {
		if($this->isUserLoggedin()) {
				return forceObject($_SESSION['GAFAM_USER']);
		}
		return false;
	}
	
	
	function isUserLoggedin() {
			$user = $this->forceObject($_SESSION['GAFAM_USER']);
			if(isset($user) && is_array($user) && $user instanceof user_gafam) {
					return true;
			}
			return false;
	}	
	
	
	function arrayObject($array, $class = 'stdClass') {
		$object = new $class;
		if (empty($array)) {
			return false;
		}
		foreach ($array as $key => $value) {
			if (strlen($key)) {
				if (is_array($value)) {
					$object->{$key} = arrayObject($value, $class);
				} else {
					$object->{$key} = $value;
				}
			}
		}
		return $object;
	}	
	
	function forceObject($array) {
		if (!is_array($array) && gettype($array) == 'array')
			//return ($array = unserialize(serialize($array)));
			return $array;
		return $array;
	}
	
	function gafam_route() {
		$root = str_replace("\\", "/", dirname(dirname(__FILE__)));
		$defaults = array(
			'www' => "$root/",
			'libs' => "$root/libraries/",
			'classes' => "$root/classes/",
			'actions' => "$root/actions/",
			'locale' => "$root/locale/",
			'sys' => "$root/system/",
			'configs' => "$root/configurations/",
			'themes' => "$root/themes/",
			'pages' => "$root/pages/",
			'com' => "$root/components/",
			'admin' => "$root/admin/",
			'forms' => "$root/forms/",
			'upgrade' => "$root/upgrade/",
			'cache' => "{$root}/cache/",
			'js' => "$root/javascripts/",
		'system' => "$root/system/",
		'components' => "$root/components",
		);
		return arrayObject($defaults);
	}
	
	/**
	* Get current url
	*/
	function current_url($uport = '') {
		$protocol = 'http';
		$uri = $_SERVER['REQUEST_URI'];
		if ($uport == true) {
			$uri = substr($uri, 0, $uri);
		}
		if (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
			$protocol = 'https';
		}
		$port = ':' . $_SERVER["SERVER_PORT"];
		if ($port == ':80' || $port == ':443') {
			if ($uport == true) {
				$port = '';
			}
		}
		$url = "$protocol://{$_SERVER['SERVER_NAME']}$port{$uri}";
		return $url;
	}	
	
	function fetchUserData(){
		//$this->result_data=null;
		$user_arr='';
		while($row = $this->dbobj->result->fetch_assoc()) {
			//$this->result_data=$row;
			$user_arr['a_id']=$row['id'];
			$user_arr['a_type']=$row['type'];
			$user_arr['a_name']=$row['username'];
			$user_arr['a_email']=$row['email'];
			$user_arr['a_pass']=$row['password'];
			$user_arr['a_salt']=$row['salt'];
			$user_arr['a_fname']=$row['first_name'];
			$user_arr['a_lname']=$row['last_name'];
			$user_arr['a_lastlogin']=$row['last_login'];
			$user_arr['a_lastactiv']=$row['last_activity'];
			$user_arr['a_acti']=$row['activation'];
			$user_arr['a_time_cre']=$row['time_created'];
		}
		return $user_arr;
	}
}
?>