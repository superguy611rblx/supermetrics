<P>
<div>

<?php if ($visit['session_is_new_visitor'] == true): ?>
New Visitor
<?php else: ?>
Returning Visitor <span class="info_text">(<a href="<?=$this->makeLink(array('session_id' => $visit['session_prior_session_id'], 'do' => 'base.reportVisit'), true,'',true);?>">Last visit was</a>	<?=round($visit['session_time_sinse_priorsession']/(3600*24));?> 
<?php if (round($visit['session_time_sinse_priorsession']/(3600*24)) == 1): ?>
day ago.
<?php else: ?>
days ago.
<?php endif; ?>
)</span>
<?php endif;?>
<?=$this->choose_browser_icon($visit['ua_browser_type']);?><P>

<span class="inline_h2"><?=$visit['host_host'];?> - <?=$visit['session_month'];?>/<?=$visit['session_day'];?> at <?=$visit['session_hour'];?>:<?=$visit['session_minute'];?></span>
<P>

<?php if ($visit['host_city']):?>
<?=$visit['host_city'];?>, <?=$visit['host_country'];?> 
<?php endif;?>
<P>			
<table cellpadding="0" cellspacing="0" width="250" border="0" class="visit_summary">
	<TR>
		<TD class="visit_icon" align="left" valign="top" width="20">
			<img src="<?=$this->makeImageLink('base/i/user_icon_small.gif', true);?>" alt="Visitor"> 
		</TD>	
		<TD valign="top">
			<a href="<?=$this->makeLink(array('do' => 'base.reportVisitor', 'visitor_id' => $visit['visitor_id']), true,'',true);?>">
			<span class="inline_h2"><? if (!empty($visit['visitor_user_name'])):?><?=$visit['visitor_user_name'];?><?php elseif (!empty($visit['visitor_user_email'])):?><?=$visit['visitor_user_email'];?><? else: ?><?=$visit['visitor_id'];?><? endif; ?></span></a>
			
		</TD>
	</TR>							
	<TR>					
		<TD class="visit_icon" align="left" width="20" valign="top"><span class="h_label">
			<img src="<?=$this->makeImageLink('base/i/document_icon.gif', true);?>" alt="Entry Page"> </span>
		</TD>
		<TD valign="top">
			<a href="<?=$visit['document_url'];?>"><span class="inline_h4"><?=$this->escapeForXml($visit['document_page_title']);?></span></a><? if($visit['document_page_type']):?> (<?=$visit['document_page_type'];?>)<? endif;?><BR> 
			<span class="info_text"><?=$visit['document_url'];?></span>
		</TD>							
	</TR>
	<? if (!empty($visit['referer_url'])):?>					
	<TR>
		<TD class="visit_icon" rowspan="2" align="left" width="20" valign="top">
			<span class="h_label"><img src="<?=$this->makeImageLink('base/i/referer_icon.gif', true);?>" alt="Refering URL"> </span>
		</TD>
		<TD valign="top" colspan="2">
			<a href="<?=$visit['referer_url'];?>"><? if (!empty($visit['referer_page_title'])):?><span class="inline_h4"><?=$this->escapeForXml($this->truncate($visit['referer_page_title'], 80, '...'));?></span></a> <span class="info_text"><?=$this->truncate($visit['referer_url'], 35, '...');?></span><? else:?><?=$this->truncate($visit['referer_url'], 50, '...');?><? endif;?></a>
		</TD>													
	</TR>								
	<?endif;?>		
</table>
		
<P><a href="<?=$this->makeLink(array('session_id' => $visit['session_id'], 'do' => 'base.reportVisit'), true,'',true);?>"><span class="">View Visit Details</a></P>

</div>