<?php

 
/**
 * Cryptor class
 *
 */
class Crypt {

  /**
   * Holds the encryption algorithm to use
   */
  protected const ENCRYPTION_ALGORITHM = ENC_ALGO;

  /**
   * Holds the hash algorithm to use
   */
  protected const HASHING_ALGORITHM = ENC_HASHING;

  /**
   * Holds the application encryption secret
   *
   */
   protected const SECRET = ENC_SEC;


  /**
   * Decrypts a string using the application secret.
   *
   * @param string $input hex representation of the cipher text
   *
   * @return string UTF-8 string containing the plain text input
   */
  public static function decrypt( string $input ): string {

    // we'll need the binary cipher
    $binaryInput    = hex2bin( $input );
    $iv             = substr( $binaryInput, 0, 16 );
    $hash           = substr( $binaryInput, 16, 32 );
    $cipherText     = substr( $binaryInput, 48 );
    $key            = hash( Crypt::HASHING_ALGORITHM,CRYPT::SECRET, true );


    // if the HMAC hash doesn't match the hash string, something has gone wrong
    if ( hash_hmac( Crypt::HASHING_ALGORITHM, $cipherText, $key, true ) !== $hash ) {
	    return '';
    }

    return openssl_decrypt(
        $cipherText,
        Crypt::ENCRYPTION_ALGORITHM,
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );
  }

  /**
   * Encrypts a string using the application secret. This returns a hex representation of the binary cipher text
   *
   * @param string $input plain text input to encrypt
   *
   * @return string hex representation of the binary cipher text
   */
  public static function encrypt( string $input ): string {
    $key = hash( Crypt::HASHING_ALGORITHM, Crypt::SECRET, true );
    $iv  = openssl_random_pseudo_bytes( 16 );

    $cipherText = openssl_encrypt(
        $input,
        Crypt::ENCRYPTION_ALGORITHM,
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );
    $hash = hash_hmac( Crypt::HASHING_ALGORITHM, $cipherText, $key, true );

    return bin2hex( $iv . $hash . $cipherText );
  }
}
