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
            <tr class="CustomTable">
                <td class="left_col">
                    <p style='display: inline;' >Receiving Mobile #:</p>
                </td>
                <td class="right_col">
                    <input type="text" name="to_sms_number" >
                </td>
            </tr>
            <tr class="CustomTable">
                <td class="left_col">
                    <p style='display: inline;' >MMS message body:</p>
                </td>
                <td class="right_col">
                    <textarea name="sms_message_body" ></textarea>
                </td>
            </tr>
            <tr class="CustomTable">
                <td class="left_col">
                    <p style='display: inline;' >Select file to attach to MMS Message:</p>
                </td>
                <td class="right_col">
                    <input type="file" name="file_to_mms" id="file_to_mms">
                </td>
            </tr>
            <tr class="CustomTable">
                <td colspan="2" class="CustomTableFullCol">
                    <br/>
                    <input type="submit" class="submit_button" value="   Send MMS   " name="send_mms">
                </td>
            </tr>
            <tr class="CustomTable">
                <td colspan="2" class="CustomTableFullCol"><hr></td>
            </tr>

        </table>
    </form>
	<?php
}
function check_form () {
    show_errors();

    $print_again = false;
    $message = "";

    /* ============================================ */
    /* ====== START data integrity checks ========= */
    /* ============================================ */

    $to_sms_number = strip_tags($_POST['to_sms_number']);
    $sms_message_body = strip_tags($_POST['sms_message_body']);
    $target_file = basename($_FILES["file_to_mms"]["name"]);

    if ($target_file == "") {
        $print_again = true;
        $message = "No file selected to be uploaded";
    }
    if ($sms_message_body == "") {
        $print_again = true;
        $message = "No sms message body has been provided";
    }
    if ($to_sms_number == "") {
        $print_again = true;
        $message = "No receiving SMS mobile Number has been provided";
    }

    /* ========================================== */
    /* ====== END data integrity checks ========= */
    /* ========================================== */

    $file_with_path = upload_file($target_file);

    if ($file_with_path == 0) {
        // a file uploading error occured
        $print_again = true;
        $message = $_SESSION['message'];
    } else {
        $mms_sent_id = send_mms($to_sms_number, $sms_message_body, $file_with_path ) ;
        if ($mms_sent_id > 0) {
            $print_again = true;
            $message = "SMS Multipart message (MMS) sent successfully (Send id): " . $mms_sent_id ;
            // clean out the file
            unlink($file_with_path) ;
        }
    }
    show_form($message, $print_again);
}
/* ============= */
/*  --- MAIN --- */
/* ============= */
if (isset($_POST['send_mms'])) {
	check_form();
} else {
	$message = "Please provide the required information.";
	show_form($message);
}

ob_end_flush();
page_footer();
