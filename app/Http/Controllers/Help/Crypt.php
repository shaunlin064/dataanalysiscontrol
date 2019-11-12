<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-07-02
	 * Time: 17:20
	 */
	
	namespace App\Http\Controllers\Help;
	
	
	class Crypt
	{
		public static $key = "FAA2C53CA77AEF2F77C6E3C83C81B798";
		/**
		 * handle decrypt function
		 *
		 * @param string $data : the data want to decrypt
		 * @param string $key : the key use to decrypt
		 * @param string $method : decrypt method , AES-256-CBC
		 * @return item after decrypt
		 */
		static function decrypt(string $data, string $method)
		{
			$data = base64_decode($data);
			$ivSize = openssl_cipher_iv_length($method);
			$iv = substr($data, 0, $ivSize);
			$data = openssl_decrypt(substr($data, $ivSize), $method, self::$key, OPENSSL_RAW_DATA, $iv);
			
			return $data;
		}
		
		/**
		 * handle encrypt function
		 *
		 * @param string $data : the data want to encrypt
		 * @param string $key : the key use to encrypt
		 * @param string $method : decrypt method , AES-256-CBC
		 * @return string after encrypt
		 */
		static function encrypt(string $data, string $method)
		{
			$ivSize = openssl_cipher_iv_length($method);
			$iv = openssl_random_pseudo_bytes($ivSize);
			
			$encrypted = openssl_encrypt($data, $method, self::$key, OPENSSL_RAW_DATA, $iv);
			
			// For storage/transmission, we simply concatenate the IV and cipher text
			$encrypted = base64_encode($iv . $encrypted);
			
			return $encrypted;
		}
		
		/**
		 * handle return encrypt key function
		 *
		 * @return string $key
		 */
		public function getKey(){
			return self::$key;
		}
	}

