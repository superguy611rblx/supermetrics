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

/**
 * Abstract Module Class
 * 
 * @author      Peter Adams <peter@openwebanalytics.com>
 * @copyright   Copyright &copy; 2006 Peter Adams <peter@openwebanalytics.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GPL v2.0
 * @category    owa
 * @package     owa
 * @version		$Revision$	      
 * @since		owa 1.0.0
 */

class owa_module extends owa_base {
	
	/**
	 * Name of module
	 *
	 * @var string
	 */
	var $name;
	
	/**
	 * Description of Module
	 *
	 * @var string
	 */
	var $description;
	
	/**
	 * Version of Module
	 *
	 * @var string
	 */
	var $version;
	
	/**
	 * Schema Version of Module
	 *
	 * @var string
	 */
	//var $schema_version = 1;
	
	/**
	 * Name of author of module
	 *
	 * @var string
	 */
	var $author;
	
	/**
	 * URL for author of module
	 *
	 * @var unknown_type
	 */
	var $author_url;
	
	/**
	 * Wiki Page title. Used to generate link to OWA wiki for this module.
	 * 
	 * Must be unique or else it will could clobber another wiki page.
	 *
	 * @var string
	 */
	var $wiki_title;
	
	/**
	 * name used in display situations
	 *
	 * @var unknown_type
	 */
	var $display_name;
	
	/**
	 * Array of event names that this module has handlers for
	 *
	 * @var array
	 */
	var $subscribed_events;
	
	/**
	 * Array of link information for admin panels that this module implements.
	 *
	 * @var array
	 */
	var $admin_panels;
	
	/**
	 * Array of navigation links that this module implements
	 *
	 * @var unknown_type
	 */
	var $nav_links;
	
	/**
	 * Array of metric names that this module implements
	 *
	 * @var unknown_type
	 */
	var $metrics;
	
	/**
	 * Array of graphs that are implemented by this module
	 *
	 * @var array
	 */
	var $graphs;
	
	/**
	 * The Module Group that the module belongs to. 
	 * 
	 * This is used often to group a module's features or functions together in the UI
	 * 
	 * @var string 
	 */
	var $group;
	
	/**
	 * Array of Entities that are implmented by the module
	 * 
	 * @var array 
	 */
	var $entities = array();
	
	/**
	 * Required Schema Version
	 * 
	 * @var array 
	 */
	var $required_schema_version;
	
	/**
	 * Available Updates
	 * 
	 * @var array 
	 */
	var $updates = array();
	
	/**
	 * Event Processors Map
	 * 
	 * @var array 
	 */
	var $event_processors = array();
	
	/**
	 * Constructor
	 *
	 * @return owa_module
	 */
	function owa_module() {
		
		return owa_module::__construct();
		
	}
	
	function __construct() {
		
		parent::__construct();
		
		// register event handlers unless OWA is operating in async handling mode
		if ($this->config['async_db'] == false):
			$this->_registerEventHandlers();
		endif;
		$this->_registerEventProcessors();
		$this->_registerEntities();
		
		return;
	}
	
	/**
	 * Method for registering event processors
	 *
	 */
	function _registerEventProcessors() {
		
		return false;
	}
	
	/**
	 * Returns array of admin Links for this module to be used in navigation
	 * 
	 * @access public
	 * @return array
	 */
	function getAdminPanels() {
		
		return $this->admin_panels;
	}
	
	/**
	 * Returns array of report links for this module that will be 
	 * used in report navigation
	 *
	 * @access public
	 * @return array
	 */
	function getNavigationLinks() {
		
		return $this->nav_links;
	}
		
	/**
	 * Abstract method for registering event handlers
	 *
	 * Must be defined by a concrete module class for any event handlers to be registered
	 * 
	 * @access public
	 * @return array
	 */
	function _registerEventHandlers() {
		
		return;
	}
	
	/**
	 * Attaches an event handler to the event queue
	 *
	 * @param array $event_name
	 * @param string $handler_name
	 * @return boolean
	 */
	function registerEventHandler($event_name, $handler_name, $method = 'notify', $dir = 'handlers') {
		
		if (!is_object($handler_name)) {
			
			//$handler = &owa_lib::factory($handler_dir,'owa_', $handler_name);
			$handler_name = owa_coreAPI::moduleGenericFactory($this->name, $dir, $handler_name, $class_suffix = null, $params = '', $class_ns = 'owa_');	
		}
				
		$eq = &eventQueue::get_instance();
		$eq->attach($event_name, array($handler_name, $method));
		
		return;
	
	}
	
	/**
	 * Attaches an event handler to the event queue
	 *
	 * @param array $event_name
	 * @param string $handler_name
	 * @return boolean
	 * @depricated
	 */
	function _addHandler($event_name, $handler_name) {
		
		return $this->registerEventHandler($event_name, $handler_name); 
				
	}
	
	/**
	 * Abstract method for registering administration/settings page
	 * 
	 * @access public
	 * @return array
	 */
	function _registerAdminPanels() {
		
		return;
	}
	
	/**
	 * Registers an admin panel with this module 
	 * 
	 */
	function registerSettingsPanel($panel) {
	
		$this->admin_panels[] = $panel;
		
		return true;
	}
	
	/**
	 * Registers an admin panel with this module 
	 * @depricated
	 */
	function addAdminPanel($panel) {
		
		return $this->registerSettingsPanel($panel);
	}
		
	/**
	 * Registers Group Link with a particular View
	 * 
	 */
	function addNavigationLink($group, $subgroup = '', $ref, $anchortext, $order = 0, $priviledge = 'viewer') {
		
		$link = array('ref' => $ref, 
					'anchortext' => $anchortext, 
					'order' => $order, 
					'priviledge' => $priviledge);
					
		if (!empty($subgroup)):
			$this->nav_links[$group][$subgroup]['subgroup'][] = $link;
		else:
			$this->nav_links[$group][$anchortext] = $link;			
		endif;

		return;
	}
	
	/**
	 * Abstract method for registering a module's entities
	 *
	 * This method must be defined in concrete module classes in order for entities to be registered.
	 */
	function _registerEntities() {
		
		return false;
	}
	
	
	/**
	 * Registers an Entity
	 *
	 * Can take an array of entities or just a single entity as a string.
	 * Will add an enetiy to the module's entity array. Required for entity installation, etc.
	 *
	 * @param $entity_name array or string 
	 */
	function registerEntity($entity_name) {
	
		if (is_array($entity_name)) {
			$this->entities = array_merge($this->entities, $entity_name);
		} else {
			$this->entities[] = $entity_name;
		}
	}
	
	/**
	 * Registers Entity
	 *
	 * Depreicated see registerEntity
	 *
	 * @depricated 
	 */ 
	function _addEntity($entity_name) {
		
		return $this->registerEntity($entity_name);
	}
	
	
	function getEntities() {
		
		return $this->entities;
	}
	
	/**
	 * Installation method
	 * 
	 * Creates database tables and sets schema version
	 * 
	 */
	function install() {
		
		$this->e->notice('Starting installation of module: '.$this->name);

		$errors = '';

		// Install schema
		if (!empty($this->entities)):
		
			foreach ($this->entities as $k => $v) {
			
				$entity = owa_coreAPI::entityFactory($this->name.'.'.$v);
				//$this->e->debug("about to  execute createtable");
				$status = $entity->createTable();
				
				if ($status != true):
					$this->e->notice("Entity Installation Failed.");
					$errors = true;
					//return false;
				endif;
				
			}
		
		endif;
		
		// activate module and persist configuration changes 
		if ($errors != true):
			
			// run post install hook
			$ret = $this->postInstall();
			
			if ($ret == true):
				$this->e->notice("Post install proceadure success.");;
			else:
				$this->e->notice("Post install proceadure failed.");
			endif;
			
			// save schema version to configuration
			$this->c->setSetting($this->name, 'schema_version', $this->getRequiredSchemaVersion());
			//activate the module and save the configuration
			$this->activate();
			$this->e->notice("Installation complete.");
			return true;
			
		else:
			$this->e->notice("Installation failed.");
			return false;
		endif;

	}
	
	/**
	 * Post installation hook
	 *
	 */
	function postInstall() {
	
		return false;
	}
		
	/**
	 * Checks for and applies schema upgrades for the module
	 *
	 */
	function update() {
		
		// list files in a directory
		$files = owa_lib::listDir(OWA_DIR.'modules'.DIRECTORY_SEPARATOR.$this->name.DIRECTORY_SEPARATOR.'updates', false);
		//print_r($files);
		
		$current_schema_version = $this->c->get($this->name, 'schema_version');
		
		// extract sequence
		foreach ($files as $k => $v) {
			// the use of %d casts the sequence number as an int which is critical for maintaining the 
			// order of the keys in the array that we are going ot create that holds the update objs
			//$n = sscanf($v['name'], '%d_%s', $seq, $classname);
			$seq = substr($v['name'], 0, -4);
			
			settype($seq, "integer");
			
			if ($seq > $current_schema_version):
			
				if ($seq <= $this->required_schema_version):
					$this->updates[$seq] = owa_coreAPI::updateFactory($this->name, substr($v['name'], 0, -4));
					// set schema version from sequence number in file name. This ensures that only one update
					// class can ever be in use for a particular schema version
					$this->updates[$seq]->schema_version = $seq;
				endif;
			endif;	
			
		}
		
		// sort the array
		ksort($this->updates, SORT_NUMERIC);
		
		//print_r(array_keys($this->updates));
		
		foreach ($this->updates as $k => $obj) {
			
			$this->e->notice(sprintf("Applying Update %d (%s)", $k, get_class($obj)));
			
			$ret = $obj->apply();
			
			if ($ret == true):
				$this->e->notice("Update Suceeded");
			else:
				$this->e->notice("Update Failed");
				return false;
			endif;
		}
		
		return true;
	}
	
	/**
	 * Deactivates and removes schema for the module
	 * 
	 */
	function uninstall() {
		
		return;
	}
	
	/**
	 * Places the Module into the active module list in the global configuration
	 * 
	 */
	function activate() {
		
		//if ($this->name != 'base'):
		
			$this->c->setSetting($this->name, 'is_active', true);
			$this->c->save();
			$this->e->notice("Module $this->name activated");
			
		//endif;
		
		return;
	}
	
	/**
	 * Deactivates the module by removing it from 
	 * the active module list in the global configuration
	 * 
	 */
	function deactivate() {
		
		if ($this->name != 'base'):
			
			$this->c->setSetting($this->name, 'is_active', false);
			$this->c->save();
			
		endif;
		
		return;
	}
		
	/**
	 * Checks to se if the schema is up to date
	 *
	 */
	function isSchemaCurrent() {
		
		$current_schema = $this->getSchemaVersion();
		$required_schema = $this->getRequiredSchemaVersion(); 
		
		owa_coreAPI::debug("$this->name Schema version is $current_schema");
		owa_coreAPI::debug("$this->name Required Schema version is $required_schema");
		
		if ($current_schema >= $required_schema):
			return true;
		else:
			return false;
		endif;
	}
	
	function getSchemaVersion() {
		
		$current_schema = owa_coreAPI::getSetting($this->name, 'schema_version');
		
		if (empty($current_schema)) {
			$current_schema = 1;
			
			// if this is the base module then we need to let filters know to install the base schema
			if ($this->name === 'base') {
				$s = owa_coreAPI::serviceSingleton();
				$s->setInstallRequired();
			}
			
		}
		
		return $current_schema;
	}
	
	function getRequiredSchemaVersion() {
		
		return $this->required_schema_version;
	}
	
	/**
	 * Registers updates
	 *
	 */
	function _registerUpdates() {
		
		return;
	
	}
	
	/**
	 * Adds an update class into the update array.
	 * This should be used to within the _registerUpdates method or else
	 * it will not get called.
	 *
	 */
	function _addUpdate($sequence, $class) {
		
		$this->updates[$sequence] = $class;
		
		return true;
	}
	
	/**
	 * Adds an event processor class to the processor array. This is used to determin
	 * which class to use to process a particular event
	 */
	function addEventProcessor($event_type, $processor) {
		$this->event_processors[$event_type] = $processor;
		return;
	}
	
}

?>