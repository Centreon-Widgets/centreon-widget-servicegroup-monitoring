<?php
/**
 * Copyright 2005-2015 CENTREON
 * Centreon is developped by : Julien Mathis and Romain Le Merlus under
 * GPL Licence 2.0.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation ; either version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses>.
 *
 * Linking this program statically or dynamically with other modules is making a
 * combined work based on this program. Thus, the terms and conditions of the GNU
 * General Public License cover the whole combination.
 *
 * As a special exception, the copyright holders of this program give CENTREON
 * permission to link this program with independent modules to produce an executable,
 * regardless of the license terms of these independent modules, and to copy and
 * distribute the resulting executable under terms of CENTREON choice, provided that
 * CENTREON also meet, for each linked independent module, the terms  and conditions
 * of the license of that module. An independent module is a module which is not
 * derived from this program. If you modify this program, you may extend this
 * exception to your version of the program, but you are not obliged to do so. If you
 * do not wish to do so, delete this exception statement from your version.
 *
 * For more information : contact@centreon.com
 *
 */

require_once "../require.php";
require_once $centreon_path . 'www/class/centreon.class.php';
require_once $centreon_path . 'www/class/centreonSession.class.php';
require_once $centreon_path . 'www/class/centreonDB.class.php';
require_once $centreon_path . 'www/class/centreonWidget.class.php';

session_start();
if (!isset($_SESSION['centreon']) || !isset($_REQUEST['widgetId'])) {
    exit;
}
$centreon = $_SESSION['centreon'];
$widgetId = $_REQUEST['widgetId'];

try {
    $db = new CentreonDB();
    $widgetObj = new CentreonWidget($centreon, $db);
    $preferences = $widgetObj->getWidgetPreferences($widgetId);
    $autoRefresh = 0;
    if (isset($preferences['refresh_interval'])) {
        $autoRefresh = $preferences['refresh_interval'];
    }
} catch (Exception $e) {
    echo $e->getMessage() . "<br/>";
    exit;
}
?>
<html>
    <style type="text/css">
         body{ margin:0; padding: 0; }
        .ListHeader {background: #cfedf9 none repeat scroll 0 0;}
        .ListTable {font-size:11px;border-color: #BFD0E2;}
        * html body { overflow:hidden; }
        * html div#hostMonitoringTable { height:100%; overflow:auto; }
    </style>
    <head>
    	<title>Servicegroup Monitoring</title>
    	
    	<link href="../../Themes/Centreon-2/jquery-ui/jquery-ui.css" rel="stylesheet" type="text/css"/>
    	<link href="../../Themes/Centreon-2/jquery-ui/jquery-ui-centreon.css" rel="stylesheet" type="text/css"/>
    	<link href="../../include/common/javascript/jquery/plugins/pagination/pagination.css" rel="stylesheet" type="text/css"/>
        <link href="../../Themes/Centreon-2/style.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo '../../Themes/Centreon-2/Color/blue_css.php';?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo '../../Themes/Centreon-2/Color/green_css.php';?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo '../../Themes/Centreon-2/Color/red_css.php';?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo '../../Themes/Centreon-2/Color/yellow_css.php';?>" rel="stylesheet" type="text/css"/>
        
    	<link href="../../include/common/javascript/jquery/plugins/treeTable/jquery.treeTable.css" rel="stylesheet" type="text/css"/>
    	<script type="text/javascript" src="../../include/common/javascript/jquery/jquery.js"></script>
    	<script type="text/javascript" src="../../include/common/javascript/jquery/jquery-ui.js"></script>
    	<script type="text/javascript" src="../../include/common/javascript/jquery/plugins/pagination/jquery.pagination.js"></script>
		<script type="text/javascript" src="../../include/common/javascript/widgetUtils.js"></script>
		<script type="text/javascript" src="../../include/common/javascript/jquery/plugins/treeTable/jquery.treeTable.min.js"></script>
    </head>
    <body>
        <div id='actionBar'>
            <span id='toolBar'></span>
            <span id='pagination' class='pagination' style='float:left;width:50%'></span>
            <span id='nbRows' style='float:left;width:14%;text-align:right;font-weight: bold;'></span>
        </div><br/><br/>
        <div id='sgMonitoringTable'></div>
    </body>

<script type="text/javascript">
var widgetId = <?php echo $widgetId; ?>;
var autoRefresh = <?php echo $autoRefresh;?>;
var timeout;
var itemsPerPage = <?php echo $preferences['entries'];?>;
var pageNumber = 0;

jQuery(function() {
	loadPage();
});

/**
 * Load page
 */
function loadPage()
{
    jQuery.ajax("./src/index.php?widgetId="+widgetId+"&page="+pageNumber, {        
        success : function(htmlData) {
            jQuery("#sgMonitoringTable").html("");
            jQuery("#sgMonitoringTable").html(htmlData);
            var h = document.getElementById("sgMonitoringTable").scrollHeight + 30;
            parent.iResize(window.name, h);
            jQuery("#sgMonitoringTable").find("img, style, script, link").load(function(){
                var h = document.getElementById("sgMonitoringTable").scrollHeight + 30;
                parent.iResize(window.name, h);
            });

        }
    });
    if (autoRefresh) {
        if (timeout) {
            clearTimeout(timeout);
        }
        timeout = setTimeout(loadPage, (autoRefresh * 1000));
    }
}
</script>
</html>
