<?php
/**
 * Copyright (C) 2019-2025 Paladin Business Solutions
 */
ob_start();
session_start();

require_once('includes/ringcentral-functions.inc');
require_once('includes/ringcentral-php-functions.inc');
require_once('includes/ringcentral-db-functions.inc');

// show_errors();

function show_form($message, $print_again = false) {
    page_header();
    ?>
    <form action="" method="post">
        <table class="EditTable">
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
            <tr>
                <td class="edit_left_col">
                    Event(s) for which to create notifications:
                </td>
                <td>
                    <input type="checkbox" name="voice_type"> Voice Mail <br/>
                    <input type="checkbox" name="sms_type"> SMS <br/>
                    <input type="checkbox" name="fax_type"> Fax <br/>
                    <input type="checkbox" name="pager_type"> Pager
                </td>
            </tr>
            <tr>
                <td colspan="2" class="EditTableFullCol">
                    <br/>
                    <input type="submit" class="submit_button" value=" Create notification event(s) "
                           name="create_event">
                </td>
            </tr>
            <tr>
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

    /* ================================================= */
    /* ====== build the events session content ========= */
    /* ================================================= */

    if ($_POST["voice_type"] == "on") {
        $_SESSION["event_types"]["voice"] = 1;
    }
    if ($_POST["sms_type"] == "on") {
        $_SESSION["event_types"]["sms"] = 1;
    }
    if ($_POST["fax_type"] == "on") {
        $_SESSION["event_types"]["fax"] = 1;
    }
    if ($_POST["pager_type"] == "on") {
        $_SESSION["event_types"]["pager"] = 1;
    }

    header("Location: create_webhook.php");
}

/* ============= */
/*  --- MAIN --- */
/* ============= */
if (isset($_POST['create_event'])) {
    check_form();
} else {
    $message = "Please provide the required information.";
    $_SESSION["event_types"] = array();
    show_form($message);
}

ob_end_flush();
page_footer();
