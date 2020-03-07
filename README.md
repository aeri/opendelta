<p align="center">
  <img width="600" src="https://i.imgur.com/j664FHd.png">
</p>

# opendelta
A public file retriever via HTTP to provide links to files in secure environments. This system allows you to recover a file hosted on a web server in a safe way by hiding the real path, real file name and restricting access to authorized users who haven't the encryption key.

As an encryption mechanism it is proposed to use **AES-256-CBC** through the cryptographic extension of PHP OpenSSL (PHP 5 >= 5.3.0, PHP 7).

* Protect your public files with encrypted **secure links**.
* Hide real files location.
* File access accounting.


## Engine

All files that you want to be accessible via the secure links can be located anywhere on your computer, PHP will take care of reading them and providing them if you have access to them.

1. From the name of a file to be accessed, it is encrypted using a symmetrical key algorithm. This *encrypted string* is part of the **secure link** to access the remote server file.

2. The encrypted string is converted in ```index.html``` with Javascript to Base64 in client side so that the transmission does not present coding problems. This is done in an intermediate phase.

In this phase, this coded string is automatically sent to the ```delta.php``` script using ```GET``` with x parameter.

3. Using PHP in server side, the name of the file is decoded with a symmetrical AES key. If the key is correct, the server will obtain the file and return it to the user. Otherwise, the user is redirected to a page specified by the user.

The server record the access with timestamp and IP address.


## Setup

In order to configure the retriever, a number of parameters must be set:

* The path where the files to be retrieved are located must have the proper permissions for the PHP server to access them:
```php
$rute = '<ABSOULTE_PATH>';
```
* The site to which the user will be redirected if the file cannot be found or decoded:
```php
$domain = "<DOMAIN>";
```
* Set the values of your password and initialization vector. Both must be the same as those used to generate the secure link as explained below. Note that the key must be provided in hexadecimal and 32 digits.
```php
$password 	= hex2bin('<PASSWORD>');
$iv 		= hex2bin('<IV>');
```

## Secure Links

To obtain in secure link it is necessary to generate by means of symmetric encryption the encrypted name of the file to which you want to retrieve.

* Using **openssl** for GNU/Linux

```bash
echo -n "<FILENAME_TO_ENCRYPT>" | openssl AES-256-CBC -K "<KEY>" -iv "<IV>" -a -salt
```
* Using PHP

```php
$output = openssl_encrypt("<FILENAME_TO_ENCRYPT>", 'AES-256-CBC', $password, 0, $iv);
```

> Note that the key must be provided in hexadecimal and 32 digits.

Now, with the encrypted string is possible create the secure link. The **generated secure link** must have the following structure: ```http://example.com/opendelta/?=<encrypted_string>```

## License

This project is licensed under GNU Lesser General Public License v3.0
