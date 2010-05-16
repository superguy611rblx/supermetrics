<?php

//
// Open Web Analytics - An Open Source Web Analytics Framework
//
// Copyright 2006 Peter Adams. All rights reserved.
//
// Licensed under GPL v2.0 http://www.gnu.org/copyleft/gpl.html
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.
//
// $Id$
//

require_once(OWA_BASE_DIR.'/owa_module.php');

/**
 * Base Package Module
 * 
 * @author      Peter Adams <peter@openwebanalytics.com>
 * @copyright   Copyright &copy; 2006 Peter Adams <peter@openwebanalytics.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GPL v2.0
 * @category    owa
 * @package     owa
 * @version		$Revision$	      
 * @since		owa 1.0.0
 */

class owa_baseModule extends owa_module {
	
	/**
	 * PHP 4 constructor
	 * Remains for backwards compatibility
	 */
	function owa_baseModule() {
		
		return owa_baseModule::__construct();
	}
	
	/**
	 * Constructor
	 * 
	 */
	function __construct() {
		
		$this->name = 'base';
		$this->display_name = 'Open Web Analytics';
		$this->group = 'Base';
		$this->author = 'Peter Adams';
		$this->version = 5;
		$this->description = 'Base functionality for OWA.';
		$this->config_required = false;
		$this->required_schema_version = 5;
		
		// register filters
		$this->registerFilter('operating_system', $this, 'determineOperatingSystem', 0);
		$this->registerFilter('ip_address', $this, 'setIp', 0);
		$this->registerFilter('full_host', $this, 'resolveHost', 0);
		$this->registerFilter('host', $this, 'getHostDomain', 0);
		
		//Clean Query Strings 
		if (owa_coreAPI::getSetting('base', 'clean_query_string')) {
			$this->registerFilter('page_url', $this, 'makeUrlCanonical',0);
			$this->registerFilter('prior_page', $this, 'makeUrlCanonical',0);
			$this->registerFilter('target_url', $this, 'makeUrlCanonical',0);
		}
		
		// register metrics
		$this->registerMetric('pageViews', 'base.pageViews');
		$this->registerMetric('uniqueVisitors', 'base.uniqueVisitors');
		$this->registerMetric('visits', 'base.visits');
		$this->registerMetric('newVisitors', 'base.newVisitors');
		$this->registerMetric('repeatVisitors', 'base.repeatVisitors');
		$this->registerMetric('bounces', 'base.bounces');
		$this->registerMetric('visitDuration', 'base.visitDuration');
		$this->registerMetric('uniquePageViews', 'base.uniquePageViews');
		$this->registerMetric('bounceRate', 'base.bounceRate');
		$this->registerMetric('pagesPerVisit', 'base.pagesPerVisit');
		$this->registerMetric('actions', 'base.actions');
		$this->registerMetric('uniqueActions', 'base.uniqueActions');
		$this->registerMetric('actionsValue', 'base.actionsValue');
		$this->registerMetric('actionsPerVisit', 'base.actionsPerVisit');
		
		// register dimensions
		$this->registerDimension('browserVersion', 'base.ua', 'browser', 'Browser Version', 'visitor', 'The browser version of the visitor.');
		$this->registerDimension('browserType', 'base.ua', 'browser_type', 'Browser Type', 'visitor', 'The browser type of the visitor.');
		$this->registerDimension('ipAddress', 'base.host', 'ip_address', 'IP Address', 'visitor', 'The IP address of the visitor.');
		$this->registerDimension('hostName', 'base.host', 'full_host', 'Host Name', 'visitor', 'The host name used by the visitor.');
		$this->registerDimension('city', 'base.host', 'city', 'City', 'visitor', 'The city of the visitor.');
		$this->registerDimension('country', 'base.host', 'country', 'Country', 'visitor', 'The country of the visitor.');
		$this->registerDimension('latitude', 'base.host', 'latitude', 'Latitude', 'visitor', 'The latitude of the visitor.');
		$this->registerDimension('longitude', 'base.host', 'longitude', 'Longitude', 'visitor', 'The longitude of the visitor.');
		$this->registerDimension('timeSinceLastVisit', 'base.session', 'time_sinse_priorsession', 'Time Since Last Visit', 'visitor', 'The time since the last visit.', '', true);
		$this->registerDimension('isRepeatVisitor', 'base.session', 'is_repeat_visitor', 'Repeat Visitor', 'visitor', 'Repeat Site Visitor.', '', true);
		$this->registerDimension('isNewVisitor', 'base.session', 'is_new_visitor', 'New Visitor', 'visitor', 'New Site Visitor.', '', true);
		
		$this->registerDimension('siteDomain', 'base.site', 'domain', 'Site Domain', 'visitor', 'The domain of the site.');
		$this->registerDimension('siteName', 'base.site', 'name', 'Site Name', 'visitor', 'The name of the site.');
		$this->registerDimension('userName', 'base.visitor', 'user_name', 'User Name', 'visitor', 'The name or ID of the user.');
		$this->registerDimension('userEmail', 'base.visitor', 'user_email', 'Email Address', 'visitor', 'The email address of the user.');
		
		// denormalized date dimensions
		$this->registerDimension('date', 'base.session', 'yyyymmdd', 'Date', 'visit', 'The date.', '', true, 'yyyymmdd');
		$this->registerDimension('day', 'base.session', 'day', 'Day', 'visit', 'The day.', '', true);
		$this->registerDimension('month', 'base.session', 'month', 'Month', 'visit', 'The month.', '', true);
		$this->registerDimension('year', 'base.session', 'year', 'Year', 'visit', 'The year.', '', true);
		$this->registerDimension('dayofweek', 'base.session', 'dayofweek', 'Day of Week', 'visit', 'The day of the week.', '', true);
		$this->registerDimension('dayofyear', 'base.session', 'dayofyear', 'Day of Year', 'visit', 'The day of the year.', '', true);
		$this->registerDimension('weekofyear', 'base.session', 'weekofyear', 'Week of Year', 'visit', 'The week of the year.', '', true);
		
		$this->registerDimension('date', 'base.request', 'yyyymmdd', 'Date', 'visit', 'The date.', '', true, 'yyyymmdd');
		$this->registerDimension('day', 'base.request', 'day', 'Day', 'visit', 'The day.', '', true);
		$this->registerDimension('month', 'base.request', 'month', 'Month', 'visit', 'The month.', '', true);
		$this->registerDimension('year', 'base.request', 'year', 'Year', 'visit', 'The year.', '', true);
		$this->registerDimension('dayofweek', 'base.request', 'dayofweek', 'Day of Week', 'visit', 'The day of the week.', '', true);
		$this->registerDimension('dayofyear', 'base.request', 'dayofyear', 'Day of Year', 'visit', 'The day of the year.', '', true);
		$this->registerDimension('weekofyear', 'base.request', 'weekofyear', 'Week of Year', 'visit', 'The week of the year.', '', true);
		
		$this->registerDimension('actionName', 'base.action_fact', 'action_name', 'Action Name', 'actions', 'The name of the action.', '', true);
		$this->registerDimension('actionGroup', 'base.action_fact', 'action_group', 'Action Group', 'actions', 'The group that an action belongs to.', '', true);
		$this->registerDimension('actionLabel', 'base.action_fact', 'action_label', 'Action Label', 'actions', 'The label associated with an action.', '', true);
		
		// visit
		$this->registerDimension('entryPageUrl', 'base.document', 'url', 'Entry Page URL', 'visit', 'The URL of the entry page.', 'first_page_id');
		$this->registerDimension('entryPageTitle', 'base.document', 'page_title', 'Entry Page Title', 'visit', 'The title of the entry page.', 'first_page_id');
		$this->registerDimension('entryPageType', 'base.document', 'page_type', 'Entry Page Type', 'visit', 'The page type of the entry page.', 'first_page_id');
		$this->registerDimension('exitPageUrl', 'base.document', 'url', 'Entry Page URL', 'visit', 'The URL of the exit page.', 'last_page_id');
		$this->registerDimension('exitPageTitle', 'base.document', 'page_title', 'Entry Page Title', 'visit', 'The title of the exit page.', 'last_page_id');
		$this->registerDimension('exitPageType', 'base.document', 'page_type', 'Entry Page Type', 'visit', 'The page type of the exit page.', 'last_page_id');
		
		// traffic sources
		$this->registerDimension('referralPageUrl', 'base.referer', 'url', 'Referral Page URL', 'traffic sources', 'The url of the referring web page.');
		$this->registerDimension('referralPageTitle', 'base.referer', 'page_title', 'Referral Page Title', 'traffic sources', 'The title of the referring web page.');
		$this->registerDimension('referralSearchTerms', 'base.referer', 'query_terms', 'Search Terms', 'traffic sources', 'The referring search terms.');
		$this->registerDimension('referralLinkText', 'base.referer', 'refering_anchortext', 'Referral Link Text', 'traffic sources', 'The text of the referring link.');
		$this->registerDimension('isSearchEngine', 'base.referer', 'is_searchengine', 'Search Engine', 'traffic sources', 'Is traffic source a search engine.');
		$this->registerDimension('referralWebSite', 'base.referer', 'site', 'Referral Web Site', 'traffic sources', 'The full domain of the referring web site.');
		
		// content
		$this->registerDimension('pageUrl', 'base.document', 'url', 'Entry Page URL', 'content', 'The URL of the web page.');
		$this->registerDimension('pageTitle', 'base.document', 'page_title', 'Entry Page Title', 'content', 'The title of the web page.');
		$this->registerDimension('pageType', 'base.document', 'page_type', 'Entry Page Type', 'content', 'The page type of the web page.');
		
		// register CLI commands
		$this->registerCliCommand('update', 'base.updatesApplyCli');
		$this->registerCliCommand('build', 'base.build');
		
		// register API methods
		$this->registerApiMethod('getResultSet', array($this, 'getResultSet'), array('metrics', 'dimensions', 'constraints', 'sort', 'limit', 'page', 'offset', 'period', 'startDate', 'endDate', 'startTime', 'endTime', 'format'));
		
		return parent::__construct();
	}
	
	/**
	 * Registers Admin panels
	 *
	 */
	function registerAdminPanels() {
		
		$this->addAdminPanel(array('do' 			=> 'base.optionsGeneral', 
									'priviledge' 	=> 'admin', 
									'anchortext' 	=> 'Main Configuration',
									'group'			=> 'General',
									'order'			=> 1));
		
		
		
		$this->addAdminPanel(array('do' 			=> 'base.users', 
										'priviledge' 	=> 'admin', 
										'anchortext' 	=> 'User Management',
										'group'			=> 'General',
										'order'			=> 2));
									
		
									
		$this->addAdminPanel(array('do' 			=> 'base.sites', 
									'priviledge' 	=> 'admin', 
									'anchortext' 	=> 'Site Roster',
									'group'			=> 'General',
									'order'			=> 3));
								
		$this->addAdminPanel(array('do' 			=> 'base.optionsModules', 
									'priviledge' 	=> 'admin', 
									'anchortext' 	=> 'Modules Admin',
									'group'			=> 'General',
									'order'			=> 3));
									
		return;
		
	}
	
	function registerNavigation() {
		
		$this->addNavigationLink('Reports', '', 'base.reportDashboard', 'Dashboard', 1);
		$this->addNavigationLink('Reports', '', 'base.reportVisitors', 'Visitors', 3);
		$this->addNavigationLink('Reports', '', 'base.reportTraffic', 'Traffic', 2);
		$this->addNavigationLink('Reports', '', 'base.reportContent', 'Content', 4);
		$this->addNavigationLink('Reports', 'Content', 'base.reportClicks', 'Click Map Report', 1);
		$this->addNavigationLink('Reports', 'Content', 'base.reportFeeds', 'Feeds', 2);
		$this->addNavigationLink('Reports', 'Content', 'base.reportEntryExits', 'Entry & Exit Pages', 3);
		$this->addNavigationLink('Reports', 'Content', 'base.reportDomstreams', 'Domstreams', 4);
		$this->addNavigationLink('Reports', 'Visitors', 'base.reportVisitsGeolocation', 'Geo-location', 1);
		$this->addNavigationLink('Reports', 'Visitors', 'base.reportHosts', 'Domains', 2);								
		$this->addNavigationLink('Reports', 'Visitors', 'base.reportVisitorsLoyalty', 'Visitor Loyalty', 3);
		$this->addNavigationLink('Reports', 'Traffic', 'base.reportKeywords', 'Keywords', 1);								
		$this->addNavigationLink('Reports', 'Traffic', 'base.reportAnchortext', 'Inbound Link Text', 2);
		$this->addNavigationLink('Reports', 'Traffic', 'base.reportSearchEngines', 'Search Engines', 3);
		$this->addNavigationLink('Reports', 'Traffic', 'base.reportReferringSites', 'Referring Web Sites', 4);
		$this->addNavigationLink('Reports', 'Dashboard', 'base.reportDashboardSpy', 'Latest Visits Spy', 1);
		$this->addNavigationLink('Reports', '', 'base.reportActionTracking', 'Action Tracking', 5);		
	}
	
	/**
	 * Registers Event Handlers with queue queue
	 *
	 */
	function _registerEventHandlers() {
		
		// User management
		$this->registerEventHandler(array('base.set_password', 'base.reset_password', 'base.new_user_account'), 'userHandlers');
		// Page Requests
		$this->registerEventHandler(array('base.page_request', 'base.first_page_request'), 'requestHandlers');
		// Sessions
		$this->registerEventHandler(array('base.page_request_logged', 'base.first_page_request_logged'), 'sessionHandlers');
		// Clicks
		$this->registerEventHandler('dom.click', 'clickHandlers');
		// Documents
		$this->registerEventHandler(array('base.page_request_logged', 'base.first_page_request_logged', 'base.feed_request_logged'), 'documentHandlers');
		// Referers
		$this->registerEventHandler('base.new_session', 'refererHandlers');
		// User Agents
		$this->registerEventHandler(array('base.feed_request', 'base.new_session'), 'userAgentHandlers');
		// Hosts
		$this->registerEventHandler(array('base.feed_request', 'base.new_session'), 'hostHandlers');
		// Hosts
		$this->registerEventHandler('base.feed_request', 'feedRequestHandlers');
		// User management
		$this->registerEventHandler('base.new_session', 'visitorHandlers');
		// Nofifcation handlers
		$this->registerEventHandler('base.new_session', 'notifyHandlers');
		// install complete handler
		$this->registerEventHandler('install_complete', $this, 'installCompleteHandler');
		// domstreams
		$this->registerEventHandler('dom.stream', 'domstreamHandlers');
		// actions
		$this->registerEventHandler('track.action', 'actionHandler');
	}
	
	function _registerEventProcessors() {
		
		$this->addEventProcessor('base.page_request', 'base.processRequest');
		$this->addEventProcessor('base.first_page_request', 'base.processFirstRequest');
		//$this->addEventProcessor('base.feed_request', 'base.processFeedRequest');
	}
	
	function _registerEntities() {
		
		//$this->_addEntity('testtable');
								
		$this->registerEntity(array('request', 
								'session', 
								'document', 
								'feed_request', 
								'click', 
								'ua', 
								'referer', 
								'site', 
								'visitor', 
								'host',
								'exit',
								'os',
								'impression', 
								'configuration',
								'user',
								'domstream',
								'action_fact'));
		
	}
	
	function installCompleteHandler($event) {
		
		//owa_coreAPI::debug('test handler: '.print_r($event, true));
	}
	
	/**
	 * Determine the operating system of the browser making the request
	 *
	 * @param string $user_agent
	 * @return string
	 */
	function determineOperatingSystem($os = '', $ua) {
		
		if (empty($os)) {
		
			$matches = array(
				'Win.*NT 5\.0'=>'Windows 2000',
				'Win.*NT 5.1'=>'Windows XP',
				'Win.*(Vista|XP|2000|ME|NT|9.?)'=>'Windows $1',
				'Windows .*(3\.11|NT)'=>'Windows $1',
				'Win32'=>'Windows [prior to 1995]',
				'Linux 2\.(.?)\.'=>'Linux 2.$1.x',
				'Linux'=>'Linux [unknown version]',
				'FreeBSD .*-CURRENT$'=>'FreeBSD -CURRENT',
				'FreeBSD (.?)\.'=>'FreeBSD $1.x',
				'NetBSD 1\.(.?)\.'=>'NetBSD 1.$1.x',
				'(Free|Net|Open)BSD'=>'$1BSD [unknown]',
				'HP-UX B\.(10|11)\.'=>'HP-UX B.$1.x',
				'IRIX(64)? 6\.'=>'IRIX 6.x',
				'SunOS 4\.1'=>'SunOS 4.1.x',
				'SunOS 5\.([4-6])'=>'Solaris 2.$1.x',
				'SunOS 5\.([78])'=>'Solaris $1.x',
				'Mac_PowerPC'=>'Mac OS [PowerPC]',
				'Mac OS X'=>'Mac OS X',
				'X11'=>'UNIX [unknown]',
				'Unix'=>'UNIX [unknown]',
				'BeOS'=>'BeOS [unknown]',
				'QNX'=>'QNX [unknown]',
			);
			
			$uas = array_map(create_function('$a', 'return "#.*$a.*#";'), array_keys($matches));
			
			$os = preg_replace($uas, array_values($matches), $ua);
		}
			
		return $os;
	}
	
	/**
	 * Get IP address from request
	 *
	 * @return string
	 * @access private
	 */
	function setIp($ip) {
	
		$HTTP_X_FORWARDED_FOR = owa_coreAPI::getServerParam('HTTP_X_FORWARDED_FOR');
		$HTTP_CLIENT_IP = owa_coreAPI::getServerParam('HTTP_CLIENT_IP');
		
		// check for a non-unknown proxy address
		if (!empty($HTTP_X_FORWARDED_FOR) && strpos(strtolower($HTTP_X_FORWARDED_FOR), 'unknown') === false) {
			
			// if more than one use the last one
			if (strpos($HTTP_X_FORWARDED_FOR, ',') === false) {
				$ip = $HTTP_X_FORWARDED_FOR;
			} else {
				$ips = array_reverse(explode(",", $HTTP_X_FORWARDED_FOR));
				$ip = $ips[0];
			}
		
		// or else just use the remote address	
		} else {
		
			if ($HTTP_CLIENT_IP) {
		    	$ip = $HTTP_CLIENT_IP;
			}
			
		}
		
		return $ip;
	
	}
	
	/**
	 * Resolve hostname from IP address
	 * 
	 * @access public
	 */
	function resolveHost($remote_host = '', $ip_address = '') {
	
		// See if host is already resolved
		if (empty($remote_host)) {
			
			// Do the host lookup
			if (owa_coreAPI::getSetting('base', 'resolve_hosts')) {
				$remote_host = @gethostbyaddr($ip_address);
			}
			
		}
		
		return $remote_host;
	}
	
	function getHostDomain($fullhost = '', $ip_address = '') {
	
		$host = '';
		
		if (!empty($fullhost)) {
		
			// Sometimes gethostbyaddr returns 'unknown' or the IP address if it can't resolve the host
			if ($fullhost != $ip_address) {
		
				$host_array = explode('.', $fullhost);
				
				// resort so top level domain is first in array
				$host_array = array_reverse($host_array);
				
				// array of tlds. this should probably be in the config array not here.
				$tlds = array('com', 'net', 'org', 'gov', 'mil');
				
				if (in_array($host_array[0], $tlds)) {
					$host = $host_array[1].".".$host_array[0];
				} else {
					$host = $host_array[2].".".$host_array[1].".".$host_array[0];
				}
					
			} elseif ($fullhost === 'unknown') {
				;
			}
				
		}
		
		return $host;
	
	}
	
	/**
	 * Filter function Strips a URL of certain defined session or tracking params
	 *
	 * @return string
	 */
	function makeUrlCanonical($url) {
		
		if (owa_coreAPI::getSetting('base', 'query_string_filters')) {
			$filters = str_replace(' ', '', owa_coreAPI::getSetting('base', 'query_string_filters'));
			$filters = explode(',', $filters);
		} else {
			$filters = array();
		}
			
		// OWA specific params to filter
		array_push($filters, owa_coreAPI::getSetting('base', 'ns').owa_coreAPI::getSetting('base', 'source_param'));
		array_push($filters, owa_coreAPI::getSetting('base', 'ns').owa_coreAPI::getSetting('base', 'feed_subscription_id'));
		
		//print_r($filters);
		
		foreach ($filters as $filter => $value) {
			
		  $url = preg_replace(
			'#\?' .
			$value .
			'=.*$|&' .
			$value .
			'=.*$|' .
			$value .
			'=.*&#msiU',
			'',
			$url
		  );
		  
		}
	        
	        
	    //check for dangling '?'. this might occure if all params are stripped.
	        
	    // returns last character of string
		$test = substr($url, -1);   		
		
		// if dangling '?' is found clean up the url by removing it.
		if ($test == '?') {
			$url = substr($url, 0, -1);
		}
			
     	return $url;
		
	}
	
	/**
	 * Convienence method for generating a data result set
	 *
	 * Takes an array of values that contain necessary params to compute the results set.
	 * Strings use ',' to seperate their values if needed. Array name/value pairs include:
	 * 
	 * array(metrics => 'foo,bar'
	 *      , dimensions => 'dim1,dim2,dim3'
	 *      , period => 'today'
	 *      , startDate => 'yyyymmdd'
	 *      , endDate => 'yyyymmdd'
	 *      , startTime => timestamp
	 *      , endTime => timestamp
	 *      , constraints => 'con1=foo, con2=bar'
	 *      , page => 1
	 *      , offset => 0
	 *      , limit => 10
	 *      , sort => 'dim1,dim2'
	 *
	 *
	 * @param $params array
	 * @return paginatedResultSet obj
	 * @link http://wiki.openwebanalytics.com/index.php?title=REST_API
	 */
	function getResultSet($metrics, $dimensions = '', $constraints = '', $sort = '', $limit = '', $page = '', $offset = '', $period = '', $startDate = '', $endDate = '', $startTime = '', $endTime = '', $format = '') {
		
		//print_r(func_get_args());
		// create the metric obj for the first metric
		require_once(OWA_BASE_CLASS_DIR.'resultSetManager.php');
		$rsm = new owa_resultSetManager;
		
		if ($metrics) {
			$metrics = $rsm->metricsStringToArray($metrics);
		} else {
			return false;
		}
		
		// count how many metrics there are
		$count = count($metrics);
		
		//loop through the rest of the metrics and merge them into the first
		if ($metrics) {
			
			for($i = 0; $i < $count; ++$i) {
				
				$rsm->addMetric($metrics[$i]);
			}
		}

		// set dimensions
		if ($dimensions) {
			$rsm->setDimensions($rsm->dimensionsStringToArray($dimensions));
		}
			
		// set period
		if (!$period) {
			$period = 'today';
		}
		
		$rsm->setTimePeriod($period, 
						  $startDate, 
						  $endDate, 
						  $startTime, 
						  $endTime); 
		
		// set constraints
		if ($constraints) {
			
			$rsm->setConstraints($rsm->constraintsStringToArray($constraints));
		}
		
		// set sort order
		if ($sort) {
			$rsm->setSorts($rsm->sortStringToArray($sort));
		}
				
		// set limit
		if ($resultsPerPage) {
			$rsm->setLimit($resultsPerPage);
		}
		
		// set limit  (alt key)
		if ($limit) {
			$rsm->setLimit($limit);
		}
		
		// set page
		if ($page) {
			$rsm->setPage($page);
		}
		
		// set offset
		if ($offset) {
			$rsm->setOffset($offset);
		}
		
		// set format
		if ($format) {
			$rsm->setOffset($format);
		}
		
		// get results
		$rs = $rsm->getResults();
		
		if ($format) {
			owa_lib::setContentTypeHeader($format);
			return $rs->formatResults($format);		
		} else {
			return $rs;
		}
	}
	
}


?>