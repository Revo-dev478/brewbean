<?php
$url = "http://localhost/project.2/ongkir.php?action=get_provinces";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Mimic a browser just in case
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
$res = curl_exec($ch);
curl_close($ch);

file_put_contents('captured_html_output.html', $res);
echo "Captured " . strlen($res) . " bytes. Check captured_html_output.html";
