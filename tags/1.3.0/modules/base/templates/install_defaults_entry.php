<h2>Default Site & User Information</h2>
<div id="configSettings">
	<form method="POST">
		
		<p class="form-row">
			<span class="form-label">Site Domain</span>
			<span class="form-field">
				<input type="text"size="30" name="<?php echo $this->getNs();?>domain" value="<?php echo $defaults['domain'];?>">
			</span>
			<span class="form-instructions">This is the domain of the site to track.</span>
		</p>
		
		<p class="form-row">
			<span class="form-label">Your E-mail Address</span>
			<span class="form-field">
				<input type="text"size="30" name="<?php echo $this->getNs();?>email_address" value="<?php echo $defaults['email_address'];?>">
			</span>
			<span class="form-instructions">This is the e-mail address of the admin user.</span>
		</p>
				
		<p>
			<?php echo $this->createNonceFormField('base.installBase');?>
			<input type="hidden" value="base.installBase" name="<?php echo $this->getNs();?>action">
			<input class="owa-button" type="submit" value="Continue..." name="<?php echo $this->getNs();?>save_button">
		</p>
		
	</form>
	
</div>
	