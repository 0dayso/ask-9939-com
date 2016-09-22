<?php
/**
 * The arrayf adapter is a filtered mySQL adapter riggged 
 * to only transmit certain column names. Must be typed manually.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright (c) 2003 amfphp.org
 * @package flashservices
 * @subpackage adapters
 * @version $Id: mysqlfAdapter.php,v 1.1 2005/07/05 07:56:29 pmineault Exp $
 */

require_once(AMFPHP_BASE . "shared/adapters/RecordSetAdapter.php");

class arrayfAdapter extends RecordSetAdapter {
	/**
	 * Constructor method for the adapter.  This constructor implements the setting of the
	 * 3 required properties for the object.
	 * 
	 * @param resource $d The datasource resource
	 */
	 
	function arrayfAdapter($d) {
		
		$f = $d->filter;
		$d = $d->data;
		
		parent::RecordSetAdapter($d);
		
		$this->columns = $f;
		foreach($d as $key => $val)
		{
			$row = array();
			foreach($f as $key2 => $val2)
			{
				$row[$key2] = $val[$val2];
			}
			$this->rows[] = $row;
		}
	}
}

?>