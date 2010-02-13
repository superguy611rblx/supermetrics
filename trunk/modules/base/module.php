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
		$this->version = 4;
		$this->description = 'Base functionality for OWA.';
		$this->config_required = false;
		$this->required_schema_version = 4;
		
		// register filters
		$this->registerFilter('operating_system', $this, 'determineOperatingSystem', 0);
		
		return parent::__construct();
	}
	
	function osFilter($os, $ua) {
		print $ua;
		return 'MacOS XI';
	}
	
	function osFilter2($os, $ua) {
		print $ua;
		return 'MacOS XII';
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
		$this->registerEventHandler('base.new_comment', 'commentHandlers');
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
	}
	
	function _registerEventProcessors() {
		
		$this->addEventProcessor('base.page_request', 'base.processRequest');
		$this->addEventProcessor('base.first_page_request', 'base.processFirstRequest');
		$this->addEventProcessor('base.page_request', 'base.processRequest');
		$this->addEventProcessor('base.feed_request', 'base.processFeedRequest');
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
								'domstream'));
		
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

}


?>