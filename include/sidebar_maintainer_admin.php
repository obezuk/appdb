<?php
/*****************/
/* sidebar_admin */
/*****************/

function global_maintainer_admin_menu() {

    $g = new htmlmenu("Maintainer Admin");
    
    $g->add("View App Queue (".$_SESSION['current']->getQueuedVersionCount().")", BASE."admin/adminAppQueue.php");
    $g->add("View App Data Queue (".$_SESSION['current']->getQueuedAppDataCount().")", BASE."admin/adminAppDataQueue.php");
    $g->done();
}

?>
