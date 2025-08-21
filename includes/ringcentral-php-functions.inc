<?php
/**
 * Copyright (C) 2019-2025 Paladin Business Solutions
 *
 */

/* ========================== */
/* Call HTML wrappers section */
/* ========================== */
function page_header() {
    echo "<html class='SiteWide'><head >";
    echo "<title >" . app_name() . "</title></head>";
    echo "<link rel='stylesheet' href='css/ringcentral-custom.css'>";
}
function page_footer() {
    echo "</html>";
}
function place_logo() {
echo <<<SHOWLOGO
		<tr class="CustomTable">
          <td colspan="3" class="CustomTableFullCol">
             <img src="images/rc-logo.png"/>
          </td>
        </tr>
SHOWLOGO;
}
/* =================== */
/* Turn on Error codes */
/* =================== */
function show_errors() {
     error_reporting();
//     error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    ini_set('display_errors', 1);
}
function echo_spaces($text, $value="", $lines=0) {
    echo "<br /><strong><p style='color: red; display: inline'>$text";
	if ($value !== "") { echo ": " ; }
	echo "</p></strong> ";
    if (is_string($value) || is_int($value))  { echo $value; }
    if (is_object($value) || is_array($value)) { ringcentral_dump_obj($value); }
    if (is_bool($value)) { if ($value) echo "TRUE / Success"; else echo 'FALSE / Failure'; }
    $i = 1 ;
    while ($i <= $lines) {
        echo "<br />" ;
        $i++;
    }
}
/* ======================== */
/* Dump the provided object */
/* ======================== */
function ringcentral_dump_obj($object) {
    echo "<pre>";
    var_dump($object);
    echo "</pre>";
}

function echo_plain_text($text, $color, $font_size="large", $lines=0) {
    echo "<br /><p style='color: $color; display: inline; font-size: $font_size'>$text</p>" ;
    if ($lines > 0 ) {
        $i = 0;
        while ($i <= $lines) {
            echo "<br />";
            $i++;
        }
    }
}
function echo_link($text, $url) {
	if ($url == "HOME_PAGE") {
		require('includes/vendor/autoload.php');
		$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
		$dotenv->load();
		$url = $_ENV['RC_HOME_ADDRESS'];
	}
	echo "<br /><br /><a href='$url'>$text</a>" ;
}
function required_field() {
	echo "<p style='color: red; display: inline'>*</p>" ;
}

function upload_file ($target_file) {

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		// Directory where the file will be uploaded
		$target_dir = "uploads/";

		// Path to the target file
		$file_to_attach = $target_dir . basename($target_file);

		// Variable to keep track of upload status
		$uploadOk = 1;

		// Get file extension
		$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

		// Check file size (limit to 1.5MB)
		if ($_FILES["file_to_mms"]["size"] > 1500000) {
			$_SESSION['message'] = "Sorry, your file is too large.";
			$uploadOk = 0;
		}

		// Check for allowed file types by extension
		$allowedFileTypes = get_allowed_file_types();

		if (!in_array($fileType, $allowedFileTypes)) {
			$_SESSION['message'] = "Sorry, only specific file types are allowed. ";
			$uploadOk = 0;
		}

		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			$_SESSION['message'] .= "Your file was not uploaded.";
			$result = 0 ;
		} else {
			if (move_uploaded_file($_FILES["file_to_mms"]["tmp_name"], $target_file)) {
				$filename = htmlspecialchars(basename($_FILES["file_to_mms"]["name"])) ;
				$result = $target_file ;
			} else {
				$_SESSION['message'] = "Sorry, there was an error uploading your file.";
				$result = 0 ;
			}
		}
	}
	return $result ;
}
function get_allowed_file_types() {
	$allowed_image_file_types = array(
		"jpg", "png", "jpeg", "pdf", "bmp", "gif", "tiff", "tif", "svg", "webp",
	);
	$allowed_video_file_types = array(
		"3gp", "mp4", "m4v", "mpeg", "mpg", "avi", "flv", "wmv", "mov", "qt", "webm", "ogg",
	);
	$allowed_audio_file_types = array(
		"mp3", "m4a", "oga", "wav", "arm","3gp",
	);
	$allowed_misc_file_types = array(
		"vcf", "vcard", "zip", "gzip", "rtf", "pdf", "html", "txt", "csv", "ics",
	);
	return array_unique(array_merge($allowed_image_file_types, $allowed_video_file_types,
		$allowed_audio_file_types, $allowed_misc_file_types));
}