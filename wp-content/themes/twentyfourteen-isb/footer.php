<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
?>

		</div><!-- #main -->
		
	<?php wp_footer(); ?>

	<script type="text/javascript">
		;(function(){
			var getWid = document.getElementById('loginform');
			var forgetPass = document.createElement("span");
			forgetPass.innerHTML = '<a href="/wp-login.php?action=lostpassword">Forgot password?</a>';
			getWid.appendChild(forgetPass);
		}());
	</script>
</body>
</html>