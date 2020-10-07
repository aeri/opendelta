<?php

/**************************************
aeri/opendelta is licensed under the
GNU Lesser General Public License v3.0
***************************************/

$key = $_GET[ "x" ];

// Set absolute path of our files.
$rute = '<ABSOULTE_PATH>';

// Set default domain with protocol.
$domain = "<DOMAIN>";

// Set our cipher.
$ciphering = "AES-256-CBC";

// AES password and iv vector.
$password = hex2bin( '<PASSWORD>' );
$iv = hex2bin( '<IV>' );

//Retrieve AES encoded filename from base64.
$encstr = base64_decode( $key );

//Encrypt
//$output = openssl_encrypt("<FILE_TO_ENCODE>", 'AES-256-CBC', $password, 0, $iv); 

//Decrypt process is here.
$output = openssl_decrypt( $encstr, $ciphering, $password, 0, $iv );


// Generate absolute route with filename.
$rute .= $output;

// Set default timeset.
date_default_timezone_set( 'UTC' );

// Register timestamp and IP address of client for accounting.

$browser = $_SERVER['HTTP_USER_AGENT'];

$ipAddress = 'NA';

//Check to see if the CF-Connecting-IP header exists.
if(isset($_SERVER["HTTP_CF_CONNECTING_IP"])){
    //If it does, assume that PHP app is behind Cloudflare.
    $ipAddress = $_SERVER["HTTP_CF_CONNECTING_IP"];
} else{
    //Otherwise, use REMOTE_ADDR.
    $ipAddress = $_SERVER['REMOTE_ADDR'];
}

$line = date('Y-m-d H:i:s') . " – $ipAddress" . " – $browser" . " – $output";

file_put_contents('visitors.log', $line . PHP_EOL, FILE_APPEND);

if ( file_exists( $rute ) && $output != '' ) {
	header( 'Content-Description: File Transfer' );
	header( 'Content-Type: application/octet-stream' );
	header( 'Content-Disposition: attachment; filename=' . basename( $rute ) );
	header( 'Content-Transfer-Encoding: binary' );
	header( 'Expires: 0' );
	header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	header( 'Pragma: public' );
	header( 'Content-Length: ' . filesize( $rute ) );
	ob_clean();
	flush();
	readfile( $rute );
	exit;
} else {
	// Redirect if encoded filename is not correct or file not exists.
	header( "Location: " . $domain );
}

?>