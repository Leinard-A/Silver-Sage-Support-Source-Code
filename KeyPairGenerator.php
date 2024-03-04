<?php

function getKey($type){
    /**
     * Reads a key from a file
     * 
     * @param string $type the type of key, either "Public" or "Private"
     * @return string|false the key read from the file, or false if the file does not exist or the key could not be read
     */
    try{
    $file = fopen($type.".pem","r");
    $data = fread($file,filesize($type.".pem" ));
    fclose($file);
    return $data;
   }
    catch (Exception $e){
    return false;
   }
}

function saveKey($key, $type){
    /**
     * Writes a given key to a file
     * 
     * @param string $key the key to be written to the file
     * @param string $type the type of key, either "Public" or "Private"
     */
    $file = fopen($type.".pem","w");
    fwrite($file,$key);
    fclose($file);
    return;
}

function generateKeyPair(){
    
    $config = array(
        "config" => "E:/XAMPP/php/extras/openssl/openssl.cnf",
        "digest_alg" => "sha256",
        "private_key_bits" => 2048,
        "private_key_type" => OPENSSL_KEYTYPE_RSA,
    );
        
    // Create the private
    $res = openssl_pkey_new($config);
    // Extract the private key from $res to $privKey
    openssl_pkey_export($res, $privKey,null,$config);
    
    // Extract the public key from $res to $pubKey
    $pubKey = openssl_pkey_get_details($res);
    $pubKey = $pubKey["key"];
    
    saveKey($pubKey,"Public");
    saveKey($privKey,"Private");
    return $pubKey;
}

if ( file_exists("Public.pem")){
    print(getKey("Public"));
}else{
    echo(generateKeyPair());
}



?>
