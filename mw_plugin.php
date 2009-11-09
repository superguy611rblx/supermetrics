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

require_once('owa_env.php');
require_once(OWA_BASE_CLASSES_DIR.'owa_mw.php');
require_once "$IP/includes/SpecialPage.php";

/* MEDIAWIKI GLOBALS */
global $wgCachePages, $wgDBtype, $wgDBname, $wgDBserver, $wgDBuser, $wgDBpassword, $wgUser, $wgServer, $wgScriptPath, $wgScript;
/* OWA's MEDIAWIKI CONFIGURATION OVERRIDES */

// Public folder URI
define('OWA_PUBLIC_URL', $wgServer.$wgScriptPath.'/extensions/owa/');

// Build OWA's Mediawiki specific config overrides array
$owa_config = array();
$wiki_url = $wgScriptPath;

// OWA DATABASE CONFIGURATION 
// Will use Mediawiki's config valuesunless there are values present in an OWA config file.
// OWA uses this to setup it's own DB connection seperate from the one that Mediawiki uses.
$owa_config['db_type'] = $wgDBtype;
$owa_config['db_name'] = $wgDBname;
$owa_config['db_host'] = $wgDBserver;
$owa_config['db_user'] = $wgDBuser;
$owa_config['db_password'] = $wgDBpassword;

$owa_config['report_wrapper'] = 'wrapper_mediawiki.tpl';
$owa_config['images_url'] = OWA_PUBLIC_URL.'i/';
$owa_config['images_absolute_url'] = $owa_config['images_url'];
$owa_config['main_url'] = $wgScriptPath.'/index.php?title=Special:Owa';
$owa_config['main_absolute_url'] = $wgServer.$owa_config['main_url'];
$owa_config['action_url'] = $wgServer.$wgScriptPath.'/index.php?action=owa&owa_specialAction';
$owa_config['log_url'] = $wgServer.$wgScriptPath.'/index.php?action=owa&owa_logAction=1';
$owa_config['link_template'] = '%s&%s';
$owa_config['site_id'] = md5($wgServer.$wiki_url);
$owa_config['is_embedded'] = true;
$owa_config['delay_first_hit'] = true;
$owa_config['error_handler'] = 'development';

//create instance of OWA
$owa = owa_mw::singleton($owa_config);

// Turn MediaWiki Caching Off
global $wgCachePages, $wgCacheEpoch;
$wgCachePages = false;
$wgCacheEpoch = 'date +%Y%m%d%H%M%S';

// Register Extension with MediaWiki
//$wgExtensionFunctions[] = 'owa_main';
$wgExtensionCredits['other'][] = array( 'name' => 'Open Web Analytics for MediaWiki', 
										'author' => 'Peter Adams <peter@openwebanalytics.com>', 
										'url' => 'http://www.openwebanalytics.com' );
										
$wgExtensionCredits['specialpage'][] = array('name' => 'Open Web Analytics for MediaWiki', 
  											 'author' => 'Peter Adams', 
  											 'url' => 'http://www.openwebanalytics.com',
  											 'description' => 'Open Web Analytics for MedaWiki');

//Load Special Page
$wgAutoloadClasses['SpecialOwa'] = __FILE__;
// Adds OWA's admin interface to special page list
$wgSpecialPages['Owa'] = 'SpecialOwa';
$wgHooks['LoadAllMessages'][] = 'SpecialOwa::loadMessages';
// generic action hook for OWA special actions
$wgHooks['UnknownAction'][] = 'owa_actions';
// Hook for logging various page types
$wgHooks['ArticlePageDataAfter'][] = 'owa_logArticle';
$wgHooks['SpecialPageExecuteAfterPage'][] = 'owa_logSpecialPage';
$wgHooks['CategoryPageView'][] = 'owa_logCategoryPage';
// Hooks for adding page tracking tags 
$wgHooks['ArticlePageDataAfter'][] = 'owa_footer';
$wgHooks['SpecialPageExecuteAfterPage'][] = 'owa_footer';
$wgHooks['CategoryPageView'][] = 'owa_footer';
// used to set OWA's current user
$wgHooks['UserGetRights'][] = 'owa_set_priviledges';
	
/**
 * Main Mediawiki Extension method
 *
 * sets up OWA to be triggered for various hooks/actions
 */
function owa_main() {

	global $wgHooks, $wgUser;
	//print_r($wgUser);
	// Hook for logging Article Page Views
	$wgHooks['ArticlePageDataAfter'][] = 'owa_logArticle';
	$wgHooks['SpecialPageExecuteAfterPage'][] = 'owa_logSpecialPage';
	$wgHooks['CategoryPageView'][] = 'owa_logCategoryPage';
	
	// Hooks for adding page tracking tags 
	$wgHooks['ArticlePageDataAfter'][] = 'owa_footer';
	$wgHooks['SpecialPageExecuteAfterPage'][] = 'owa_footer';
	$wgHooks['CategoryPageView'][] = 'owa_footer';
	
	//SpecialPage::addPage(new OwaSpecialPage());
	
    return;
}

/**
 * Hook for OWA special actions
 *
 * This uses mediawiki's 'unknown action' hook to trigger OWA's special action handler.
 * This is setup by adding 'action=owa' to the URLs for special actions. There is 
 * probably a better way to do this so that the OWA namespace is preserved.
 *
 * @TODO figure out how to register this method to be triggered only when 'action=owa' instead of 
 *		 for all unknown mediawiki actions.
 * @param object $specialPage
 * @url http://www.mediawiki.org/wiki/Manual:MediaWiki_hooks/UnknownAction
 * @return false
 */
function owa_actions() {
	
	global $wgOut, $owa;

	$wgOut->disable();
	$owa->handleSpecialActionRequest();
	
	return false;

}

/**
 * OWA Priviledges
 *
 * Populates OWA requestion container with info about the current mediawiki user.
 * This info is needed by OWA authentication system as well as to add dimensions
 * requests that are logged.
 */
function owa_set_priviledges(&$user) {
	
	global $owa;	
	
	// preemptively set the current user info and mark as authenticated so that
	// downstream controllers don't have to authenticate
	$cu = &owa_coreAPI::getCurrentUser();
	$cu->setUserData('user_id', $user->mName);
	$cu->setUserData('email_address', $user->mEmail);
	$cu->setUserData('real_name', $user->mRealName);
	$cu->setRole(owa_translate_role($user->mGroups));
	//print_r($wgUser);
	$cu->setAuthStatus(true);

	return true;
}

function owa_translate_role($level = array()) {

	if (in_array("*", $level)):
		$owa_role = 'everyone';
	elseif (in_array("user", $level)):
		$owa_role = 'viewer';
	elseif (in_array("autoconfirmed", $level)):
		$owa_role = 'viewer';
	elseif (in_array("emailconfirmed", $level)):
		$owa_role = 'viewer';
	elseif (in_array("bot", $level)):
		$owa_role = 'viewer';
	elseif (in_array("sysop", $level)):
		$owa_role = 'admin';
	elseif (in_array("bureaucrat", $level)):
		$owa_role = 'admin';
	elseif (in_array("developer", $level)):
		$owa_role = 'admin';
	else:
		$owa_role = 'everyone';
	endif;
	
	return $owa_role;

}

/**
 * Logs Special Page Views
 *
 * @param object $specialPage
 * @return boolean
 */
function owa_logSpecialPage(&$specialPage) {
	
	global $wgUser, $wgOut, $owa;
	
	$app_params['user_name']= $wgUser->mName;
    $app_params['user_email'] = $wgUser->mEmail;
    $app_params['page_title'] = $wgOut->mPagetitle;
    $app_params['page_type'] = 'Special Page';

	// Log the request
	$owa->log($app_params);
	
	return true;
}

/**
 * Logs Category Page Views
 *
 * @param object $categoryPage
 * @return boolean
 */
function owa_logCategoryPage(&$categoryPage) {
	
	global $wgUser, $wgOut, $owa;
	
	$app_params['user_name']= $wgUser->mName;
    $app_params['user_email'] = $wgUser->mEmail;
    $app_params['page_title'] = $wgOut->mPagetitle;
    $app_params['page_type'] = 'Category';
	
	// Log the request
	$owa->log($app_params);
	
	return true;
}

/**
 * Logs Article Page Views
 *
 * @param object $article
 * @return boolean
 */
function owa_logArticle(&$article) {

	global $wgUser, $wgOut, $wgTitle, $owa;
	
	$wgTitle->invalidateCache();
	$wgOut->enableClientCache(false);
	
	
	// Setup Application Specific Properties to be Logged with request
	$app_params['user_name']= $wgUser->mName;
    $app_params['user_email'] = $wgUser->mEmail;
    $app_params['page_title'] = $article->mTitle->mTextform;
    $app_params['page_type'] = 'article';
    
	// Log the request
	$owa->log($app_params);
	
	return true;
	
}

/**
 * Adds helper page tags to Article Pages if they are needed
 *
 * @param object $article
 * @return boolean
 */
function owa_footer(&$article) {
	
	global $wgOut;
	$owa = owa_mw::singleton();
	
	$tags = $owa->placeHelperPageTags(false);
	
	$wgOut->addHTML($tags);
		
	return true;
}


//////////////////////////////////////////////////////////////////////////////////


/**
 * OWA Special Page Class
 *
 * Enables OWA to be accessed through a Mediawiki special page. 
 */
class SpecialOwa extends SpecialPage {

    function SpecialOwa() {
            SpecialPage::SpecialPage('Owa','',true);
            self::loadMessages();
    }

    function execute() {
            global $wgRequest, $wgOut, $wgUser, $wgSitename, $wgScriptPath, $wgScript, $wgServer, $owa;
            
            $this->setHeaders();
            
            $params = array();
            
            // if no action is found...
            $do = owa_coreAPI::getRequestParam('do');
            if (empty($do)):
            	// check to see that owa in installed.
                if (empty($owa->config['install_complete'])):

                	$site_url = $wgServer.$wgScriptPath;
                	
                	$params = array('site_id' => md5($site_url), 
    							'name' => $wgSitename,
    							'domain' => $site_url, 
    							'description' => '',
    							'do' => 'base.installStartEmbedded');
    				$page = $owa->handleRequest($params);
    			
    			// send to daashboard
                else: 
                	$params['do'] = 'base.reportDashboard';
		           	$page = $owa->handleRequest($params);
                endif;
            // do action found on url
            else:
           		$page = $owa->handleRequestFromURL(); 
            endif;
            
           				

			// switch for output scenario
			//if (empty($owa->config['schema_version'])):
				return $wgOut->addHTML($page);					
			//else:
			//	$wgOut->disable();
			//	echo $page;
			//	return;
			//endif;
            
    }

    function loadMessages() {
    	static $messagesLoaded = false;
        global $wgMessageCache;
            
		if ( $messagesLoaded ) return;
		
		$messagesLoaded = true;
		
		// this should be the only msg defined by mediawiki
		$allMessages = array(
			 'en' => array( 
				 'owa' => 'Open Web Analytics'
				 )
			);


		// load msgs in to mediawiki cache
		foreach ( $allMessages as $lang => $langMessages ) {
			   $wgMessageCache->addMessages( $langMessages, $lang );
		}
		
		return true;
    }
        
}



?>