<?php
/*******************/
/* delete comments */
/*******************/

/*
 * application environment
 */
include("path.php");
require(BASE."include/incl.php");
require(BASE."include/application.php");
require(BASE."include/mail.php");

$aClean = array(); //array of filtered user input

$aClean['sWhy'] = makeSafe($_REQUEST['sWhy']);
$aClean['iCommentId'] = makeSafe($_REQUEST['iCommentId']);
$aClean['iDeleteIt'] = makeSafe($_REQUEST['iDeleteIt']);

$oComment = new Comment($aClean['iCommentId']);

/* if we aren't an admin or the maintainer of this app we shouldn't be */
/* allowed to delete any comments */
if (!$_SESSION['current']->hasPriv("admin") 
 && !$_SESSION['current']->isMaintainer($oComment->iVersionId)
 && !$_SESSION['current']->isSuperMaintainer($oComment->iAppId))
{
    util_show_error_page("You don't have sufficient privileges to delete this comment.");
}

if($_SESSION['current']->getPref("confirm_comment_deletion") != "no" && !isset($aClean['iDeleteIt']))
{
    apidb_header("Delete Comment");
    $mesTitle = "<b>Please state why you are deleting the following comment</b>";
    echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";
    echo html_frame_start($mesTitle,500,"",0);
    echo "<br />";
    echo html_frame_start($oComment->sSubject,500);
    echo htmlify_urls($oComment->sBody), "<br /><br />\n";
    echo html_frame_end();
    echo '<table width="100%" border=0 cellpadding=0 cellspacing=1>',"\n";
    echo "<tr class=color1><td colspan=2><textarea name=\"sWhy\" cols=\"70\" rows=\"15\" wrap=\"virtual\"></textarea></td></tr>\n";
    echo "<tr class=color1><td colspan=2 align=center>\n";
    echo "  <input type=\"submit\" value=\"Delete Comment\" class=\"button\" />\n";
    echo "</td></tr>\n";
    echo "</table>\n";
    echo html_frame_end();
    echo "<input type=\"hidden\" name=\"iDeleteIt\" value=\"1\" />\n";
    echo "<input type=\"hidden\" name=\"iCommentId\" value=\"".$oComment->iCommentId."\" />";
    echo "</form>";

    apidb_footer();
} else
{
    $oComment->delete($aClean['sWhy']);
    redirect(apidb_fullurl("appview.php?iVersionId=".$oComment->iVersionId));
}
?>
