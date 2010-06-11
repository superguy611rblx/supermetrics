<div class="owa_reportSectionContent" style="width:auto;">
<div class="owa_reportSectionHeader">Site Metrics</div>

	<div id="trend-chart" style="height:125px;"></div>
	<div id="trend-metrics" style="height:;"></div>
	<script>
	//OWA.setSetting('debug', true);
	var aurl = '<?php echo $this->makeApiLink(array('do' => 'getResultSet', 
													'metrics' => 'visits,pageViews,bounces,pagesPerVisit,visitDuration,actionsPerVisit', 
													'dimensions' => 'date', 
													'sort' => 'date',
													'format' => 'json'
													), true);?>';
													  
	rsh = new OWA.resultSetExplorer('site-trend');
	rsh.asyncQueue.push(['makeAreaChart', [{x: 'date', y: 'visits'}], 'trend-chart']);
	rsh.options.metricBoxes.width = '150px';
	rsh.asyncQueue.push(['makeMetricBoxes' , 'trend-metrics']);
	//rsh.options.areaChart.series.push({x:'date',y:'visits'});
	//rsh.setView('areaChart');
	rsh.load(aurl);
	
	</script>

</div>

<table style="padding:0px;">
	<TR>
		<TD style="width:25%" valign="top">
			
			
			<div class="owa_reportSectionContent">
				<div class="owa_reportSectionHeader">Visitor Types</div>	
				<div id="visitor-types" style="width:200px;text-align:center;margin-top:-15px;"></div>
				<script>
					//OWA.setSetting('debug', true);
					var aurl = '<?php echo $this->makeApiLink(array('do' => 'getResultSet', 
																	'metrics' => 'repeatVisitors,newVisitors', 
																	'dimensions' => '', 
																	'sort' => 'visits',
																	'format' => 'json',
																	'constraints' => urlencode($this->substituteValue('siteId==%s,','siteId'))),true);?>';
																	  
					var vt = new OWA.resultSetExplorer('visitor-types');
					vt.options.pieChart.metrics = ['repeatVisitors', 'newVisitors'];
					vt.setView('pie');
					vt.load(aurl);
				
				</script>
			</div>
			
			<div class="owa_reportSectionContent">
				<div class="owa_reportSectionHeader">Traffic Sources</div>
				<div id="visitor-mediums" style="width:200px;text-align:center;"></div>	
				<script>
					var vmurl = '<?php echo $this->makeApiLink(array('do' => 'getResultSet', 
																	'metrics' => 'visits', 
																	'dimensions' => 'medium', 
																	'sort' => 'visits-',
																	'format' => 'json',
																	'constraints' => urlencode($this->substituteValue('siteId==%s,','siteId'))),true);?>';
																	  
					var vm = new OWA.resultSetExplorer('visitor-mediums');
					vm.options.pieChart.metric = 'visits';
					vm.options.pieChart.dimension = 'medium';
					vm.setView('pie');
					vm.load(vmurl);
				</script>
			</div>
			
		</TD>
		
		<TD style="width:50%" valign="top">
			
			<div class="owa_reportSectionContent">
				<div class="owa_reportSectionHeader">Top Content</div>
				
				<div id="top-pages" style="min-width:350px"></div>
				<div class="owa_moreLinks">
					<a href="<?php echo $this->makeLink(array('do' => 'base.reportPages'), true);?>">View Full Report &raquo;</a>
				</div>
				<script>
				//OWA.setSetting('debug', true);
				var tcurl = '<?php echo $this->makeApiLink(array('do' => 'getResultSet', 
																'metrics' => 'pageViews', 
																'dimensions' => 'pageTitle,pageUrl', 
																'sort' => 'pageViews-',
																'format' => 'json'
																),true);?>';
																  
				OWA.items.tc = new OWA.resultSetExplorer('top-pages');
				OWA.items.tc.options.grid.showRowNumbers = false;
				OWA.items.tc.addLinkToColumn('pageTitle', '<?php echo $this->makeLink(array('do' => 'base.reportDocument', 'pageUrl' => '%s'));?>', ['pageUrl']);
				OWA.items.tc.options.grid.excludeColumns = ['pageUrl'];
				OWA.items.tc.asyncQueue.push(['refreshGrid']);
				OWA.items.tc.load(tcurl);
				
				</script>
			</div>
			
			<div class="owa_reportSectionContent">
				<div class="owa_reportSectionHeader">Top Referers</div>
				
				<div id="top-referers" style="min-width:350px"></div>
				<div class="owa_moreLinks">
					<a href="<?php echo $this->makeLink(array('do' => 'base.reportReferringSites'), true);?>">View Full Report &raquo;</a>
				</div>
				<script>
				//OWA.setSetting('debug', true);
				var aurl = '<?php echo $this->makeApiLink(array('do' => 'getResultSet', 
																'metrics' => 'visits', 
																'dimensions' => 'referralPageTitle,referralPageUrl', 
																'sort' => 'visits-',
																'format' => 'json',
																'resultsPerPage' => 10
																),true);?>';
																  
				OWA.items.topreferers = new OWA.resultSetExplorer('top-referers');
				OWA.items.topreferers.options.grid.showRowNumbers = false;
				OWA.items.topreferers.addLinkToColumn('referralPageTitle', '<?php echo $this->makeLink(array('do' => 'base.reportReferralDetail', 'referralPageUrl' => '%s'));?>', ['referralPageUrl']);
				OWA.items.topreferers.options.grid.excludeColumns = ['referralPageUrl'];
				OWA.items.topreferers.asyncQueue.push(['refreshGrid']);
				OWA.items.topreferers.load(aurl);
				
				</script>
			</div>
			
			<div class="owa_reportSectionContent">
				<div class="section_header">Latest Visits</div>
				<?php echo $this->getWidget('base.widgetLatestVisits', array('height' => '', 'width' => '100%', 'period' => $params['period']), false);?>
			</div>
			
		</TD>
		<TD style="width:60%" valign="top">
		
			<?php if ($actions->getDataRows()):?>
			<div class="owa_reportSectionContent" style="min-width:200px; height:;">
				<div class="section_header">Actions</div>
				
				<div id="actions-trend" style="width:200px;height:;"></div>
				<script>
				
				var aturl = '<?php echo $this->makeApiLink(array('do' => 'getResultSet', 
																'metrics' => 'actions', 
																'dimensions' => 'date', 
																'sort' => 'date',
																'format' => 'json',
																'period' => 'last_seven_days',
																'constraints' => urlencode($this->substituteValue('siteId==%s,','siteId'))));?>';
																  
				at = new OWA.resultSetExplorer('actions-trend');
				at.options.areaChart.series.push({x:'date',y:'actions'});
				at.setView('areaChart');
				//at.load(aturl);
				
				
				</script>
				
				<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td valign="top">
						<?php foreach($actions->getDataRows() as $k => $row):?>
							<div class="owa_metricInfobox">
								<p class="owa_metricInfoboxLabel"><?php echo $row['actionName']['value'];?></p>
								<p class="owa_metricInfoboxLargeNumber"><?php echo $row['actions']['value'];?></p>	
							</div>
						<?php endforeach;?>
						</td>
					</tr>
				</table>
				
				
				<div class="owa_genericHorizontalList owa_moreLinks">
					<UL>
						<LI>
							<a href="<?php echo $this->makeLink(array('do' => 'base.reportActionTracking'), true);?>">View Full Report &raquo;</a>
						</LI>
					</UL>
				</div>
				<div class="clear"></div>
			</div>
			<?php endif;?>
		
			<div class="owa_reportSectionContent">
				<div class="section_header">OWA News</div>
				<?php echo $this->getWidget('base.widgetOwaNews','',false);?>
			</div>
		</TD>
	</TR>
</table>

<script type="text/x-jqote-template" id="metricInfobox">
 <![CDATA[
 
	<div class="owa_metricInfobox" style="width:<%= this.width %>;">
	<p class="owa_metricInfoboxLabel"><%= this.label %></p>
	<p class="owa_metricInfoboxLargeNumber"><%= this.value %></p>
	<p id='<%= this.dom_id %>-sparkline'></p>
	</div>

]]>
</script>