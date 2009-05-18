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

require_once(OWA_BASE_DIR.'/owa_view.php');
require_once(OWA_BASE_CLASS_DIR.'installController.php');

/**
 * Installer Server Environment Setup Check View
 * 
 * @author      Peter Adams <peter@openwebanalytics.com>
 * @copyright   Copyright &copy; 2006 Peter Adams <peter@openwebanalytics.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GPL v2.0
 * @category    owa
 * @package     owa
 * @version		$Revision$	      
 * @since		owa 1.0.0
 */

class owa_installCheckEnvView extends owa_view {
	
	function owa_installCheckEnvView($params) {
	
		return owa_installCheckEnvView::__construct($params);
	}
	
	function __construct() {
		
		return parent::__construct();
	}
	
	function render($data) {
		
		//page title
		$this->t->set('page_title', 'Installer Server Environment Check');
		$this->body->set('headline', 'Server Environment Check');
		$this->body->set('errors', $data['errors']);
		$this->body->set('env', $data['env']);
		// load body template
		$this->body->set_template('install_check_env.tpl');
		
		return;
	}
	
	
}

/**
 * Server Environment Check Controller
 * 
 * @author      Peter Adams <peter@openwebanalytics.com>
 * @copyright   Copyright &copy; 2006 Peter Adams <peter@openwebanalytics.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GPL v2.0
 * @category    owa
 * @package     owa
 * @version		$Revision$	      
 * @since		owa 1.0.0
 */

class owa_installCheckEnvController extends owa_installController {
	
	function owa_installCheckEnvController($params) {
		
		return owa_installCheckEnvController::__construct($params);
	}
	
	function __construct($params) {
		
		return parent::__construct($params);
	}
	
	function action() {
		
		$errors = array();
		$warnings = array();
		$env = array();
		
		// check PHP version
		$env['php_version'] = phpversion();
		$version = split('\.',$env['php_version']);
		
		if ($version[0] < 4):
			$errors['php_version'] = $this->getMsg(3301);
			$errors['count'] = $errors['count']++;
		endif;
		
		// Check DB connection status
		$db = &owa_coreAPI::dbSingleton();
		$db->connect();
		if ($db->connection_status != true):
			$errors['count'] = $errors['count']++;
			$errors['db_status'] = $this->getMsg(3300);
			$env['db_status'] = 'Failed';
		else:
			$env['db_status'] = 'Success';
		endif;
		
		// Check socket connection
		
		// Check permissions on log directory
		
		$this->set('errors', $errors);
		$this->set('env', $env);
		$this->setView('base.install');
		$this->setSubview('base.installCheckEnv');
		
		return;
	}
	
	
}


?>