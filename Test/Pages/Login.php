<?php



?>

<!DOCTYPE html>
<html>
    <head>
    	<meta charset="utf-8">
    	<title>Authoring Login</title>
    	<script type="text/javascript" src="../JS/jquery-3.3.1.min.js"></script>
    	<script type="text/javascript" src="../JS/md5.js"></script>
    	<script type="text/javascript">
			function pwdHandler(){
				if($('#password').prop('value') != ''){
    				$('#md5Password').prop('value', md5($('#password').prop('value')));
    				$('#password').prop('value', '');
					return true;
				}
				return false;
			}
        </script>
    </head>
	<body>
		<form id='LoginForm'  method='post' onsubmit="return pwdHandler();" action="../PHP/loginAction.php" >
    		<label for='username'>Username:</label>
    		<input type='text' name='username' id='username'/><br>
    		<label for='password'>Password:</label>
    		<input type='password' name='password' id='password'/><br>
    		<input type='hidden' name='md5Password' id='md5Password'/>
    		<input type="submit" value='Login'/>
		</form>

	</body>

</html>
