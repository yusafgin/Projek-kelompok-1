<?php
// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "hasanah_cantik");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Fungsi untuk membaca kunci public dan private
function getPublicKey() {
    return file_get_contents("public.pem");
}

function getPrivateKey() {
    return file_get_contents("private.pem");
}

// Fungsi RSA Encryption
function encryptRSA($data) {
    $publicKey = openssl_pkey_get_public(getPublicKey());
    openssl_public_encrypt($data, $encrypted, $publicKey);
    return base64_encode($encrypted); // Simpan dalam format Base64
}

// Fungsi RSA Decryption
function decryptRSA($data) {
    $privateKey = openssl_pkey_get_private(getPrivateKey(), "1234"); // Password kunci private
    openssl_private_decrypt(base64_decode($data), $decrypted, $privateKey);
    return $decrypted;
}

// Fungsi registrasi dengan RSA
function registrasi($data) {
    global $conn;
    
    $username = strtolower(stripslashes($data["username"]));
    $password = $data["password"];
    $password2 = $data["password2"];

    // Cek apakah username sudah ada di database
    $result = mysqli_query($conn, "SELECT username FROM user WHERE username = '$username'");
    if (mysqli_fetch_assoc($result)) {
        echo "<script>alert('Username sudah terdaftar!');</script>";
        return false;
    }
    
    // Cek konfirmasi password
    if ($password !== $password2) {
        echo "<script>alert('Konfirmasi password tidak sesuai!');</script>";
        return false;
    }

    // Enkripsi password dengan RSA sebelum disimpan
    $encryptedPassword = encryptRSA($password);

    // Insert ke database
    $query = "INSERT INTO user (username, password) VALUES ('$username', '$encryptedPassword')";
    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
} 
?>
