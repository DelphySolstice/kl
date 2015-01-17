<?php
include_once 'includes/register.inc.php';
include_once 'includes/functions.php';

sec_session_start();

if (login_check($mysqli) == true) {
    $logged = 'in';
} else {
    $logged = 'out';
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Keeper Leagues</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <link rel="stylesheet" href="js/jquery-ui-1.11.2/jquery-ui.css">
    <script src="js/prefixfree.min.js"></script>
    <script type="text/javascript" src="js/forms.js"></script>    
	<script src='js/codepen-io-jquery.js'></script>	
	<script src="js/jquery-1.11.2.js"></script>
	<script src="js/jquery-ui-1.11.2/jquery-ui.js"></script>
	<script type="text/javascript" src="js/jquery.leanModal.min.js"></script>
	<script type="text/javascript">
        $(function() {
            $('a[rel*=leanModal]').leanModal({ top : 200, closeButton: ".modal_close" });       
        });
	</script>
	 <script>
		$(function() {
			$(document).tooltip({
				position:{
					my: "left top",
					at: "right+5 top -5"
				}
			});
		});
	</script>
</head>

<body>

  <div class="body"></div>
  <img src="images/KL3.jpg" alt="KeeperLeagues" class="logo">
  		<?php
        if (isset($_GET['error'])) {
            echo '<p class="error">Error Logging In!</p>';
        }
        ?>
        <p>You are currently logged <?php echo $logged ?>.</p>
        <div class="login">
			<form action="includes/process_login.php" method="post" name="login_form">
				<input type="text" placeholder="EMAIL" name="email" title="TEST"><br>
				<input type="password" placeholder="PASSWORD" name="password"><br>
				<input type="button" value="Login" onclick="formhash(this.form, this.form.password);" />>
			</form>
			
		</div>
		<div class="login-help">
    		<a id="plus" href="#add" name="add" rel="leanModal">Sign Up</a> • <a href="#">Forgot Password</a>
  		</div>
  
	<div id="add">
		<div id="signup-ct">
			<div id="signup-header">
				<h2>Sign Up For Keeper Leagues</h2>
				<p>Fill out your information below</p>
				<?php
				if(!empty($error_msg)){
					echo $error_msg;
				}
				?>
				<a class="modal_close" href="#"></a>
			</div>
			<form action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>" method="post" name="register">
				<div class="txt-fld">
					<label for="email">Email:</label>
					<input id="email" type="text" name="email" title="Your future login"/>
				</div>
				<div class="txt-fld">
					<label for="firstName">First Name:</label>
					<input id="firstName" type="text" name="firstName" />
				</div>
				<div class="txt-fld">
					<label for="lastName">Last Name:</label>
					<input id="lastName" type="text" name="lastName" />
				</div>
				<div class="txt-fld">
					<label for="password">Password:</label>
					<input id="password" type="password" name="password" />
				</div>
				<div class="txt-fld">
					<label for="repassword">Retype Password:</label>
					<input id="repassword" type="password" name="repassword" />
				</div>
				<div class="btn-fld">
					<button onclick="return regformhash(this.form,
					                                    this.form.email,
					                                    this.form.firstName,
					                                    this.form.lastName,
					                                    this.form.password,
					                                    this.form.repassword);">Sign Up »</button>
				</div>
			</form>
		</div>
	</div>
  
</body>

</html>