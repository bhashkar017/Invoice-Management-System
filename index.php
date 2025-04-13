<?php
include('header-login.php');
include('functions.php');
?>
<script>
$(document).ready(function() {
    $("#btn-login").click(function(e) {
        e.preventDefault();
        var username = $("#username").val();
        var password = $("#password").val();
        
        if(username == "" || password == "") {
            $("#response").removeClass("alert-success").addClass("alert-danger");
            $("#response .message").html("Please enter both username and password");
            $("#response").slideDown();
            setTimeout(function() {
                $("#response").slideUp();
            }, 3000);
            return;
        }

        $.ajax({
            type: "POST",
            url: "login.php",
            data: {
                username: username,
                password: password
            },
            dataType: "json",
            success: function(response) {
                if (response == 1) {
                    window.location.href = "dashboard.php";
                } else {
                    $("#response").removeClass("alert-success").addClass("alert-danger");
                    $("#response .message").html("Invalid username or password");
                    $("#response").slideDown();
                    setTimeout(function() {
                        $("#response").slideUp();
                    }, 3000);
                }
            },
            error: function(xhr, status, error) {
                $("#response").removeClass("alert-success").addClass("alert-danger");
                $("#response .message").html("An error occurred: " + error);
                $("#response").slideDown();
                setTimeout(function() {
                    $("#response").slideUp();
                }, 3000);
            }
        });
    });
});
</script>

<div class="row vertical-offset-100">
	<div id="response" class="alert alert-success" style="display:none;">
		<a href="#" class="close" data-dismiss="alert">&times;</a>
		<div class="message"></div>
	</div>

	<div class="col-md-4 col-md-offset-4">
		<div class="panel panel-default login-panel">
		  	<div class="panel-heading panel-login">
		  		<h1 class="text-center">
					<img src="<?php echo COMPANY_LOGO ?>" class="img-responsive">
				</h1>
		    	
		 	</div>
		  	<div class="panel-body">
		    	<form accept-charset="UTF-8" role="form" method="post" id="login_form">
		    		<input type="hidden" name="action" value="login">
	                <fieldset>
			    	  	<div class="input-group form-group">
			    	  		<div class="input-group-addon"><i class="glyphicon glyphicon-user"></i></div>
			    		    <input class="form-control required" name="username" id="username" type="text" placeholder="Enter Username">
			    		</div>
			    		<div class="input-group form-group">
			    		 	<div class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></div>
			    			<input class="form-control required" placeholder="Password" name="password" id="password" type="password" placeholder="Enter Password">
			    		</div>
			    		<div class="checkbox">
			    	    	<label>
			    	    		<input name="remember" type="checkbox" value="Remember Me"> Remember Me
			    	    	</label>
			    	    	<!--a href="forgot.php" class="float-right">Forgot password?</a-->
			    	    </div>
			    		<button type="button" id="btn-login" class="btn btn-danger btn-block">Login</button><br>
			    	</fieldset>
		      	</form>
		    </div>
		</div>
	</div>
</div>

<?php
	include('footer.php');
?>