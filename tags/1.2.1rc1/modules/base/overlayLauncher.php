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

require_once(OWA_BASE_DIR.'/owa_reportController.php');

/**
 * Overlay Launcher Controller
 * 
 * @author      Peter Adams <peter@openwebanalytics.com>
 * @copyright   Copyright &copy; 2006 Peter Adams <peter@openwebanalytics.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GPL v2.0
 * @category    owa
 * @package     owa
 * @version		$Revision$	      
 * @since		owa 1.0.0
 */

class owa_overlayLauncherController extends owa_reportController {

	function owa_overlayLauncherController($params) {
	
		return owa_overlayLauncherController::__construct($params);
	}
	
	function __construct($params) {
		
		return parent::__construct($params);
	}
	
	function action() {
		
		// setup overlay cookiestate
		owa_coreAPI::setState('overlay', '', urldecode($this->getParam('overlay_params')), 'cookie');
				
		// load entity for document id to get URL
		$d = owa_coreAPI::entityFactory('base.document');
		$d->load($this->getParam('document_id'));
		
		// redirect browser
		$this->redirectBrowserToUrl($d->get('url'));
		
		return;	
		
	}
	
}

?>