<? if($status_msg):?>
<DIV class="status_msg">
	<?=$status_msg;?>
</DIV>
<? endif;?>

<h1><?=$page_h1;?></h1>
    
The next few screens will guide you through installing and configuraing the Open Web Analytics framework. If at any time you
need help, please consult the <a href="http://wiki.openwebanalytics.com">OWA Wiki</a>.

<DIV class="centered_buttons">	
    <? if($db_state == true):?><a href="<?=$_SERVER['PHP_SELF'];?>?action=env_check">Next >> Step 1: Environment Check</a><?endif;?>
</DIV>
    

 