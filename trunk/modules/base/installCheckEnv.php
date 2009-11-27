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
		
		// check PHP version
		$version = split('\.',phpversion());
		
		if ($version[0] < 4) {
			$errors['php_version']['name'] = 'PHP Version';
			$errors['php_version']['value'] = phpversion();
			$errors['php_version']['msg'] = $this->getMsg(3301);
			$bad_environment = true;
		}
		
		// Check permissions on log directory
		
		// Check for Windows OS
		$os = php_uname("s");
		if (strtoupper(substr($os, 0, 3)) === 'WIN') {
			$errors['php_os']['value'] = 'Operating System';
			$errors['php_os']['value'] = $os;
			$errors['php_os']['msg'] = 'You are running PHP on an Operating System that OWA does not support.';
			$bad_environment = true;
		}
		
		// Check for config file and then test the db connection
		if ($this->c->isConfigFilePresent()) {
			$config_file_present = true;
			$conn = $this->checkDbConnection();
			if ($conn != true) {
				$errors['db']['name'] = 'Database Connection';
				$errors['db']['value'] = 'Connection failed';
				$errors['db']['msg'] = 'Check the connection settings in your configuration file.' ;
				$bad_environment = true;
			}
		}
		
		// if the environment is good
		if ($bad_environment != true) {
			// and the config file is present
			if ($config_file_present === true) {
				//skip to defaults entry step
				$this->setRedirectAction('base.installDefaultsEntry');
				return;		
			} else {
				// otherwise show config file entry form
				$this->setView('base.install');
				$this->setSubview('base.installConfigEntry');
				return;
			}
		// if the environment is bad, then show environment error details.
		} else {
			$this->set('errors', $errors);
			$this->setView('base.install');
			$this->setSubview('base.installCheckEnv');
		}

		return;
	}
}

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
		$this->t->set('page_title', 'Server Environment Check');
		$this->body->set('errors', $this->get('errors'));
		// load body template
		$this->body->set_template('install_check_env.tpl');
		
		return;
	}
	
	
}


?>