<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 	Frank NÃgler (typo3@naegler.net),
*
*  All rights reserved
*
*  This script is free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; version 2 of the License.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Hooks for the 'piwik' extension.
 *
 * @author	Frank NÃgler <typo3@naegler.net>
 */


class tx_piwik {

	/**
	 * main processing method
	 */
	function contentPostProc_all(&$params, &$reference){
		// process the page with these options
		$content	= $params['pObj']->content;
		$conf		= $params['pObj']->config['config']['tx_piwik.'];
		
		if (!($conf['piwik_idsite']) || !($conf['piwik_host'])) return;

		foreach ($conf as $key => $val) {
			if (preg_match('/^piwik(\_.+)$/', $key, $r)) { // a TSConfig option starts with piwik_
				$this->piwikOptions[$r[1]] = $val;
			}
		}
		
		$trackingCode = '
<!-- Piwik -->
<a href="http://piwik.org" title="Web analytics" onclick="window.open(this.href);return(false);">
<script language="javascript" src="'.$this->getPiwikHost().'piwik.js" type="text/javascript"></script>
<script type="text/javascript">
<!--
piwik_action_name = '.$this->getPiwikActionName().';
'.$this->getPiwikDownloadExtensions().$this->getPiwikHostsAlias().$this->getPiwikTrackerPause().$this->getPiwikInstallTracker().'piwik_idsite = '.$this->getPiwikIDSite().';
piwik_url = \''.$this->getPiwikHost().'piwik.php\';
piwik_log(piwik_action_name, piwik_idsite, piwik_url);
//-->
</script><object>
<noscript><p>Web analytics <img src="'.$this->getPiwikHost().'piwik.php" style="border:0" alt="piwik"/></p>
</noscript></object></a>
<!-- /Piwik --> 
		';
		
		$params['pObj']->content = str_replace('</body>', $trackingCode.'</body>', $content);

	}
	
	function getPiwikActionName() {
		if (strtoupper($this->piwikOptions['_action_name']) == 'TYPO3') {
			return "'" . $GLOBALS['TSFE']->cObj->data['title'] . "'";
		}
		
		if (strlen($this->piwikOptions['_action_name'])) {
			return $this->piwikOptions['_action_name'];
		}
		return "''";
	}
	
	function getPiwikDownloadExtensions() {
		if (strlen($this->piwikOptions['_download_extensions'])) {
			return 'piwik_download_extensions = \''.$this->piwikOptions['_download_extensions'].'\';'."\n";
		}
		return '';
	}
	
	function getPiwikHostsAlias() {
		if (strlen($this->piwikOptions['_hosts_alias'])) {
			$hosts = t3lib_div::trimExplode(',', $this->piwikOptions['_hosts_alias']);
			for ($i=0; $i<count($hosts); $i++) {
				$hosts[$i] = '"'.$hosts[$i].'"';
			}
			return 'piwik_hosts_alias = ['.implode(', ', $hosts).'];'."\n";
		}
		return '';
	}
	
	function getPiwikTrackerPause() {
		if (strlen($this->piwikOptions['_tracker_pause'])) {
			return 'piwik_tracker_pause = '.$this->piwikOptions['_tracker_pause'].';'."\n";
		}
		return '';
	}
	
	function getPiwikInstallTracker() {
		if (strlen($this->piwikOptions['_install_tracker'])) {
			return 'piwik_install_tracker = '.$this->piwikOptions['_install_tracker'].';'."\n";
		}
		return '';
	}
	
	function getPiwikIDSite() {
		return $this->piwikOptions['_idsite'];
	}
	
	function getPiwikHost() {
		return $this->piwikOptions['_host'];
	}
	

	/**
	 * a stub for backwards compatibility with extending classes that might use it
	 *
	 * @return	bool	always false
	 */
	function is_backend() {
	    return false;
	}
	
}


if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/piwik/class.tx_piwik.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/piwik/class.tx_piwik.php"]);
}

?>
