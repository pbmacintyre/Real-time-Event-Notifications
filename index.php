<?php
/**
 * Copyright (C) 2019-2025 Paladin Business Solutions
 */
ob_start();
session_start();

require_once('includes/ringcentral-functions.inc');
require_once('includes/ringcentral-php-functions.inc');
require_once('includes/ringcentral-db-functions.inc');

//show_errors();

function show_form($message, $print_again = false) {
    page_header();
    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <table class="EditTable" >
            <?php place_logo(); ?>
            <tr>
                <td colspan="3" class="EditTableFullCol">
                    <?php
                    if ($print_again == true) {
                        echo "<p class='msg_bad'>" . $message . "</strong></font>";
                    } else {
                        echo "<p class='msg_good'>" . $message . "</p>";
                    } ?>
                    <hr>
                </td>
            </tr>
            <form action="" method="post">
            <tr>
                <td class="edit_left_col">
                    Event(s) for which to create notifications:
                </td>
                <td>
                    <input type="checkbox" name="voice_type" > Voice Mail <br/>
                    <input type="checkbox" name="sms_type" > SMS <br/>
                    <input type="checkbox" name="fax_type" > Fax <br/>
                    <input type="checkbox" name="pager_type" > Pager
                </td>
            </tr>
            <tr >
                <td colspan="2" class="EditTableFullCol">
                    <br/>
                    <input type="submit" class="submit_button" value=" Create notification event(s) " name="create_event">
                </td>
            </tr>
            </form>
            <tr >
                <td colspan="2" class="EditTableFullCol">
                    <a href="list_kill_webhooks.php"> List existing webhooks</a>
                </td>
            </tr>

            <tr class="CustomTable">
                <td colspan="2" class="CustomTableFullCol">
                    <hr>
                    <br/>
                    <?php echo app_name(); ?>
                </td>
            </tr>

        </table>
    </form>
    <?php
}

function check_form() {

    /* ============================================ */
    /* ====== START data integrity checks ========= */
    /* ============================================ */

    $voice_type = $_POST["voice_type"] == "on" ? 1 : 0;
    $sms_type = $_POST["sms_type"] == "on" ? 1 : 0;
    $fax_type = $_POST["fax_type"] == "on" ? 1 : 0;
    $pager_type = $_POST["pager_type"] == "on" ? 1 : 0;

    // build event string for the session
    $_SESSION["event_types"] = array(
        "voice" => $voice_type,
        "sms" => $sms_type,
        "fax" => $fax_type,
        "pager" => $pager_type
    );

    header("Location: create_webhook.php");

}

/* ============= */
/*  --- MAIN --- */
/* ============= */
if (isset($_POST['create_event'])) {
    check_form();
} else {
    $message = "Please provide the required information.";
    show_form($message);
}

ob_end_flush();
page_footer();
