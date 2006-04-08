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

require_once(WA_BASE_DIR ."/wa_db.php");
require_once(WA_BASE_DIR ."/wa_settings_class.php");

/**
 * Request Event Handler
 * 
 * @author      Peter Adams <peter@openwebanalytics.com>
 * @copyright   Copyright &copy; 2006 Peter Adams <peter@openwebanalytics.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GPL v2.0
 * @category    wa
 * @package     wa
 * @version		$Revision$	      
 * @since		wa 1.0.0
 */
class Log_observer_request_logger extends wa_observer {

	/**
	 * Database Access Object
	 *
	 * @var object
	 */
	var $db;
	
	/**
	 * Configuration
	 *
	 * @var array
	 */
	var $config;
	
	/**
	 * Event
	 *
	 * @var object
	 */
	var $m;

	/**
	 * Constructor
	 *
	 * @param 	string $priority
	 * @param 	array $conf
	 * @access 	public
	 * @return 	Log_observer_request_logger
	 */
    function Log_observer_request_logger($priority, $conf) {
	
        // Call the base class constructor.
        $this->Log_observer($priority);

        // Configure the observer.
		$this->_event_type = array('new_request', 'feed_request');
		
		return;
    }

    /**
     * Notify Handler
     *
     * @access 	public
     * @param 	object $event
     */
    function notify($event) {
	
		$this->m = $event['message'];
		
		$this->config = &wa_settings::get_settings();
				
		$this->insert_request();
		$this->insert_document();
						
		return;
	}
	
	/**
	 * Log request to database
	 * 
	 * @access 	private
	 */
	function insert_request() {	
		
		// Setup databse acces object
		$this->db = &wa_db::get_instance();
	
		$request = array(
					'request_id',
					'visitor_id', 
					'session_id',
					'inbound_visitor_id', 
					'inbound_session_id',
					'user_name',
					'user_email',
					'timestamp',
					'last_req',
					'year',
					'month',
					'day',
					'dayofweek',
					'dayofyear',
					'weekofyear',
					'hour',
					'minute',
					'second',
					'msec',
					'feed_subscription_id',
					'referer',
					'referer_id',
					'page_type',
					'page_id',
					'page_title',
					'document_id',
					'site',
					'site_id',
					'uri',
					'ip_address',
					'host',
					'host_id',
					'os',
					'os_id',
					'ua',
					'ua_id',
					'browser_type',
					'is_new_visitor',
					'is_repeat_visitor',	
					'is_comment',
					'is_entry_page',
					'is_browser',
					'is_robot',
					'is_feedreader'
					);
					
			foreach ($request as $key => $value) {
			
				$sql_cols = $sql_cols.$value;
				$sql_values = $sql_values."'".$this->m->properties[$value]."'";
				
				if (!empty($request[$key+1])):
				
					$sql_cols = $sql_cols.", ";
					$sql_values = $sql_values.", ";
					
				endif;	
			}
						
			$this->db->query(
				sprintf(
					"INSERT into %s (%s) VALUES (%s)",
					$this->config['ns'].$this->config['requests_table'],
					$sql_cols,
					$sql_values
				)
			);	
				
		return;
	}
	
	/**
	 * Adds document data to documents table.
	 * 
	 * @access private
	 */
	function insert_document() {
	
		$this->db->query(
				sprintf(
					"INSERT into %s (id, url, page_title, page_type) VALUES ('%s', '%s', '%s', '%s')",
					$this->config['ns'].$this->config['documents_table'],
					$this->m->properties['document_id'],
					$this->m->properties['uri'],
					$this->m->properties['page_title'],
					$this->m->properties['page_type']
				)
			);	
		return;
	}
	
}

?>
