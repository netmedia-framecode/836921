<?php

function handle_error($errno, $errstr, $errfile, $errline)
{
  // Create error log file path based on the file where the error occurred
  $errorLog = dirname(__FILE__) . '/error_log.log'; // Error log file location within the project folder

  // Format error message with additional information
  $error_message = "[" . date("Y-m-d H:i:s") . "] Error [$errno]: $errstr in $errfile on line $errline" . PHP_EOL;

  // Attempt to open the error log file in append mode, creating it if it doesn't exist
  $file_handle = fopen($errorLog, 'a');
  if ($file_handle !== false) {
    // Write error message to the log file
    fwrite($file_handle, $error_message);
    // Close the file handle
    fclose($file_handle);
  }

  // Save error message in session
  $_SESSION['error_message'] = $error_message;

  // Redirect user back to the same page only if there is no error
  if (!isset($_SESSION['error_flag'])) {
    // Set error flag to prevent infinite redirection loop
    $_SESSION['error_flag'] = true;
    // Redirect user back to the same page
    header("Location: {$_SERVER['REQUEST_URI']}");
    exit(); // Stop further execution
  }
}

function valid($conn, $value)
{
  $valid = htmlspecialchars(addslashes(trim(mysqli_real_escape_string($conn, $value))));
  return $valid;
}

function separateAlphaNumeric($string)
{
  $alpha = "";
  $numeric = "";
  // Mengiterasi setiap karakter dalam string
  for ($i = 0; $i < strlen($string); $i++) {
    // Memeriksa apakah karakter adalah huruf
    if (ctype_alpha($string[$i])) {
      $alpha .= $string[$i];
    }
    // Memeriksa apakah karakter adalah angka
    if (ctype_digit($string[$i])) {
      $numeric .= $string[$i];
    }
  }
  // Mengembalikan array yang berisi huruf dan angka terpisah
  return array(
    "alpha" => $alpha,
    "numeric" => $numeric
  );
}

function generateToken()
{
  // Generate a random 6-digit number
  $token = mt_rand(100000, 999999);
  return $token;
}

function compressImage($source, $destination, $quality)
{
  // mendapatkan info image
  $imgInfo = getimagesize($source);
  $mime = $imgInfo['mime'];
  // membuat image baru
  switch ($mime) {
    // proses kode memilih tipe tipe image 
    case 'image/jpeg':
      $image = imagecreatefromjpeg($source);
      break;
    case 'image/png':
      $image = imagecreatefrompng($source);
      break;
    default:
      $image = imagecreatefromjpeg($source);
  }

  // Menyimpan image dengan ukuran yang baru
  imagejpeg($image, $destination, $quality);

  // Return image
  return $destination;
}

function hapusFolderRecursively($folderPath)
{
  if (is_dir($folderPath)) {
    $files = glob($folderPath . '/*');
    foreach ($files as $file) {
      is_dir($file) ? hapusFolderRecursively($file) : unlink($file);
    }
    rmdir($folderPath);
  }
}

function currentPermissionUrl()
{
  $requestUri = strtok($_SERVER['REQUEST_URI'], '?');
  $requestUri = trim($requestUri, '/');
  $viewsPosition = strpos($requestUri, 'views/');

  if ($viewsPosition !== false) {
    $requestUri = substr($requestUri, $viewsPosition + strlen('views/'));
  }

  $requestUri = preg_replace('/\.php$/', '', $requestUri);
  return trim($requestUri, '/');
}

function canAction($action, $url = null)
{
  global $conn;

  $allowedActions = ['view', 'create', 'edit', 'delete'];
  if (!in_array($action, $allowedActions)) {
    return false;
  }

  if (!isset($_SESSION["project_ksu_st_karolus"]["users"])) {
    return false;
  }

  $id_role = valid($conn, $_SESSION["project_ksu_st_karolus"]["users"]["id_role"]);
  $role = strtolower($_SESSION["project_ksu_st_karolus"]["users"]["role"]);

  if ($id_role == 1 || $role == 'administrator') {
    return true;
  }

  $url = $url ? trim($url, '/') : currentPermissionUrl();
  $url = preg_replace('/\.php$/', '', $url);

  $urlParts = explode('/', $url);
  $page = end($urlParts);
  $folder = count($urlParts) > 1 ? $urlParts[0] : '';
  $normalizedPage = preg_replace('/^(add|edit)-/', '', $page);
  $normalizedUrl = $folder ? $folder . '/' . $normalizedPage : $normalizedPage;

  $url = valid($conn, $url);
  $normalizedUrl = valid($conn, $normalizedUrl);

  $query = "SELECT p.`$action` AS allowed
    FROM permissions p
    JOIN user_sub_menu usm ON p.id_sub_menu=usm.id_sub_menu
    WHERE p.id_role='$id_role'
      AND (usm.url='$url' OR usm.url='$normalizedUrl')
    LIMIT 1";
  $result = mysqli_query($conn, $query);

  if (!$result || mysqli_num_rows($result) == 0) {
    return false;
  }

  $permission = mysqli_fetch_assoc($result);
  return $permission['allowed'] == 1;
}

if (!isset($_SESSION["project_ksu_st_karolus"]["users"])) {
  function register($conn, $data, $action)
  {
    require_once(__DIR__ . '/pendaftaran-anggota.php');
    try {
      $account = buatAkunAnggota($conn, $data);
    } catch (InvalidArgumentException $exception) {
      alert($exception->getMessage(), 'danger'); return false;
    } catch (Throwable $exception) {
      error_log('Pendaftaran anggota: '.$exception->getMessage());
      alert('Pendaftaran gagal disimpan. Periksa data atau hubungi pengurus.', 'danger'); return false;
    }
    $_SESSION['data_auth'] = ['en_user'=>$account['en_user']];
    // The account stays inactive until verified. No duplicate account if mail fails.
    try {
      smtp_mail($account['email'], 'Verifikasi Akun KSU St Karolus',
        '<p>Kode verifikasi akun KSU St. Karolus Anda:</p><p><strong>'.htmlspecialchars($account['token']).'</strong></p><p>Berlaku selama 5 menit.</p>', '', '', 0, 0, true);
    } catch (Throwable $exception) {
      error_log('Email verifikasi gagal: '.$exception->getMessage());
      alert('Akun tersimpan. Jika email belum diterima, gunakan kirim ulang kode pada halaman verifikasi.', 'warning');
    }
    return 1;
  }

  function re_verifikasi($conn, $data, $action)
  {
    if ($action == "update") {
      $checkEN = "SELECT * FROM users WHERE en_user='$data[en_user]'";
      $checkEN = mysqli_query($conn, $checkEN);
      if (mysqli_num_rows($checkEN) == 0) {
        $message = "Maaf, sepertinya ada kesalahan saat mendaftar.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      } else if (mysqli_num_rows($checkEN) > 0) {
        $row = mysqli_fetch_assoc($checkEN);
        $name = $row['name'];
        $email = $row['email'];
        $token = generateToken();
        $reen_user = password_hash($token, PASSWORD_DEFAULT);
        $reen_user = str_replace("$", "", $reen_user);
        $reen_user = str_replace("/", "", $reen_user);
        $reen_user = str_replace(".", "", $reen_user);
        $to       = $email;
        $subject  = "Account Verification - KSU St Karolus";
        $message  = "<!doctype html>
        <html>
          <head>
              <meta name='viewport' content='width=device-width'>
              <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
              <title>Account Verification</title>
              <style>
                  @media only screen and (max-width: 620px) {
                      table[class='body'] h1 {
                          font-size: 28px !important;
                          margin-bottom: 10px !important;}
                      table[class='body'] p,
                      table[class='body'] ul,
                      table[class='body'] ol,
                      table[class='body'] td,
                      table[class='body'] span,
                      table[class='body'] a {
                          font-size: 16px !important;}
                      table[class='body'] .wrapper,
                      table[class='body'] .article {
                          padding: 10px !important;}
                      table[class='body'] .content {
                          padding: 0 !important;}
                      table[class='body'] .container {
                          padding: 0 !important;
                          width: 100% !important;}
                      table[class='body'] .main {
                          border-left-width: 0 !important;
                          border-radius: 0 !important;
                          border-right-width: 0 !important;}
                      table[class='body'] .btn table {
                          width: 100% !important;}
                      table[class='body'] .btn a {
                          width: 100% !important;}
                      table[class='body'] .img-responsive {
                          height: auto !important;
                          max-width: 100% !important;
                          width: auto !important;}}
                  @media all {
                      .ExternalClass {
                          width: 100%;}
                      .ExternalClass,
                      .ExternalClass p,
                      .ExternalClass span,
                      .ExternalClass font,
                      .ExternalClass td,
                      .ExternalClass div {
                          line-height: 100%;}
                      .apple-link a {
                          color: inherit !important;
                          font-family: inherit !important;
                          font-size: inherit !important;
                          font-weight: inherit !important;
                          line-height: inherit !important;
                          text-decoration: none !important;
                      .btn-primary table td:hover {
                          background-color: #d5075d !important;}
                      .btn-primary a:hover {
                          background-color: #000 !important;
                          border-color: #000 !important;
                          color: #fff !important;}}
              </style>
          </head>
          <body class style='background-color: #e1e3e5; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;'>
              <table role='presentation' border='0' cellpadding='0' cellspacing='0' class='body' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; background-color: #e1e3e5; width: 100%;' width='100%' bgcolor='#e1e3e5'>
              <tr>
                  <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;' valign='top'>&nbsp;</td>
                  <td class='container' style='font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto;' width='580' valign='top'>
                  <div class='content' style='box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;'>
          
                      <!-- START CENTERED WHITE CONTAINER -->
                      <table role='presentation' class='main' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; background: #ffffff; border-radius: 3px; width: 100%;' width='100%'>
          
                      <!-- START MAIN CONTENT AREA -->
                      <tr>
                          <td class='wrapper' style='font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;' valign='top'>
                          <table role='presentation' border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; width: 100%;' width='100%'>
                              <tr>
                              <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;' valign='top'>
                                  <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;'>Hi " . $name . ",</p>
                                  <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;'>Selamat akun kamu sudah terdaftar, tinggal satu langkah lagi kamu sudah bisa menggunakan akun. Silakan salin kode token dibawah ini untuk memverifikasi akun kamu.</p>
                                  <table role='presentation' border='0' cellpadding='0' cellspacing='0' class='btn btn-primary' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; box-sizing: border-box; min-width: 100%; width: 100%;' width='100%'>
                                  <tbody>
                                      <tr>
                                      <td align='left' style='font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;' valign='top'>
                                          <table role='presentation' border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: auto; width: auto;'>
                                          <tbody>
                                              <tr>
                                              <td style='font-family: sans-serif; font-size: 14px; vertical-align: top; background-color: #ffffff; border-radius: 5px; text-align: center; font-weight: bold;' valign='top' bgcolor='#ffffff' align='center'>" . $token . "</td>
                                              </tr>
                                          </tbody>
                                          </table>
                                      </td>
                                      </tr>
                                  </tbody>
                                  </table>
                                  <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;'>Terima kasih telah mendaftar di KSU St Karolus.</p>
                                  <small>Peringatan! Ini adalah pesan otomatis sehingga Anda tidak dapat membalas pesan ini.</small>
                              </td>
                              </tr>
                          </table>
                          </td>
                      </tr>
          
                      <!-- END MAIN CONTENT AREA -->
                      </table>
                      
                      <!-- START FOOTER -->
                      <div class='footer' style='clear: both; margin-top: 10px; text-align: center; width: 100%;'>
                      <table role='presentation' border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; width: 100%;' width='100%'>
                          <tr>
                          <td class='content-block' style='font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; color: #9a9ea6; font-size: 12px; text-align: center;' valign='top' align='center'>
                              <span class='apple-link' style='color: #9a9ea6; font-size: 12px; text-align: center;'>Workarea Jln. S. K. Lerik, Kota Baru, Kupang, NTT, Indonesia. (0380) 8438423</span>
                          </td>
                          </tr>
                          <tr>
                          <td class='content-block powered-by' style='font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; color: #9a9ea6; font-size: 12px; text-align: center;' valign='top' align='center'>
                              Powered by <a href='https://www.netmedia-framecode.com' style='color: #9a9ea6; font-size: 12px; text-align: center; text-decoration: none;'>Netmedia Framecode</a>.
                          </td>
                          </tr>
                      </table>
                      </div>
                      <!-- END FOOTER -->
          
                  <!-- END CENTERED WHITE CONTAINER -->
                  </div>
                  </td>
                  <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;' valign='top'>&nbsp;</td>
              </tr>
              </table>
          </body>
        </html>";
        smtp_mail($to, $subject, $message, "", "", 0, 0, true);
        $_SESSION['data_auth'] = ['en_user' => $reen_user];
        $sql = "UPDATE users SET en_user='$reen_user', token='$token', updated_at=current_timestamp WHERE en_user='$data[en_user]'";
      }
    }

    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }

  function verifikasi($conn, $data, $action)
  {
    if ($action == "update") {
      $checkEN = "SELECT * FROM users WHERE en_user='$data[en_user]'";
      $checkEN = mysqli_query($conn, $checkEN);
      if (mysqli_num_rows($checkEN) == 0) {
        $message = "Maaf, sepertinya ada kesalahan saat mendaftar.";
        $message_type = "warning";
        alert($message, $message_type);
        return false;
      } else if (mysqli_num_rows($checkEN) > 0) {
        $row = mysqli_fetch_assoc($checkEN);
        $token_primary = $row['token'];
        $updated_at = strtotime($row['updated_at']);
        $current_time = time();
        if (($current_time - $updated_at) > (5 * 60)) {
          $message = "Maaf, waktu untuk verifikasi telah habis.";
          $message_type = "warning";
          alert($message, $message_type);
          $_SESSION["project_ksu_st_karolus"] = [
            "message-warning" => "Maaf, waktu untuk verifikasi telah habis.",
            "time-message" => time()
          ];
          return false;
        }
        if ($data['token'] !== $token_primary) {
          $message = "Maaf, kode token yang anda masukan masih salah.";
          $message_type = "warning";
          alert($message, $message_type);
          return false;
        }
        $sql = "UPDATE users SET id_active='1', updated_at=current_timestamp WHERE en_user='$data[en_user]'";
      }
    }

    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }

  function forgot_password($conn, $data, $action, $baseURL)
  {
    if ($action == "update") {
      $checkEmail = "SELECT * FROM users WHERE email='$data[email]'";
      $checkEmail = mysqli_query($conn, $checkEmail);
      if (mysqli_num_rows($checkEmail) === 0) {
        $message = "Maaf, email yang anda masukan belum terdaftar.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      } else {
        $row = mysqli_fetch_assoc($checkEmail);
        $name = valid($conn, $row['name']);
        $token = generateToken();
        $en_user = password_hash($token, PASSWORD_DEFAULT);
        $en_user = str_replace("$", "", $en_user);
        $en_user = str_replace("/", "", $en_user);
        $en_user = str_replace(".", "", $en_user);
        $to       = $data['email'];
        $subject  = "Reset password - KSU St Karolus";
        $message  = "<!doctype html>
        <html>
          <head>
              <meta name='viewport' content='width=device-width'>
              <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
              <title>Reset password</title>
              <style>
                  @media only screen and (max-width: 620px) {
                      table[class='body'] h1 {
                          font-size: 28px !important;
                          margin-bottom: 10px !important;}
                      table[class='body'] p,
                      table[class='body'] ul,
                      table[class='body'] ol,
                      table[class='body'] td,
                      table[class='body'] span,
                      table[class='body'] a {
                          font-size: 16px !important;}
                      table[class='body'] .wrapper,
                      table[class='body'] .article {
                          padding: 10px !important;}
                      table[class='body'] .content {
                          padding: 0 !important;}
                      table[class='body'] .container {
                          padding: 0 !important;
                          width: 100% !important;}
                      table[class='body'] .main {
                          border-left-width: 0 !important;
                          border-radius: 0 !important;
                          border-right-width: 0 !important;}
                      table[class='body'] .btn table {
                          width: 100% !important;}
                      table[class='body'] .btn a {
                          width: 100% !important;}
                      table[class='body'] .img-responsive {
                          height: auto !important;
                          max-width: 100% !important;
                          width: auto !important;}}
                  @media all {
                      .ExternalClass {
                          width: 100%;}
                      .ExternalClass,
                      .ExternalClass p,
                      .ExternalClass span,
                      .ExternalClass font,
                      .ExternalClass td,
                      .ExternalClass div {
                          line-height: 100%;}
                      .apple-link a {
                          color: inherit !important;
                          font-family: inherit !important;
                          font-size: inherit !important;
                          font-weight: inherit !important;
                          line-height: inherit !important;
                          text-decoration: none !important;
                      .btn-primary table td:hover {
                          background-color: #d5075d !important;}
                      .btn-primary a:hover {
                          background-color: #000 !important;
                          border-color: #000 !important;
                          color: #fff !important;}}
              </style>
          </head>
          <body class style='background-color: #e1e3e5; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;'>
              <table role='presentation' border='0' cellpadding='0' cellspacing='0' class='body' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; background-color: #e1e3e5; width: 100%;' width='100%' bgcolor='#e1e3e5'>
              <tr>
                  <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;' valign='top'>&nbsp;</td>
                  <td class='container' style='font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto;' width='580' valign='top'>
                  <div class='content' style='box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;'>
          
                      <!-- START CENTERED WHITE CONTAINER -->
                      <table role='presentation' class='main' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; background: #ffffff; border-radius: 3px; width: 100%;' width='100%'>
          
                      <!-- START MAIN CONTENT AREA -->
                      <tr>
                          <td class='wrapper' style='font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;' valign='top'>
                          <table role='presentation' border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; width: 100%;' width='100%'>
                              <tr>
                              <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;' valign='top'>
                                  <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;'>Hi " . $name . ",</p>
                                  <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;'>Pesan ini secara otomatis dikirimkan kepada anda karena anda meminta untuk mereset kata sandi. Jika anda tidak sama sekali ingin mereset atau bukan anda yang ingin mereset abaikan saja. Klik tombol reset berikut untuk melanjutkan:</p>
                                  <table role='presentation' border='0' cellpadding='0' cellspacing='0' class='btn btn-primary' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; box-sizing: border-box; min-width: 100%; width: 100%;' width='100%'>
                                  <tbody>
                                      <tr>
                                      <td align='left' style='font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;' valign='top'>
                                          <table role='presentation' border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: auto; width: auto;'>
                                          <tbody>
                                              <tr>
                                                <td style='font-family: sans-serif; font-size: 14px; vertical-align: top; background-color: #ffffff; border-radius: 5px; text-align: center;' valign='top' bgcolor='#ffffff' align='center'>
                                                  <a href='" . $baseURL . "auth/new-password?en=" . $en_user . "' target='_blank' style='background-color: #ffffff; border: solid 1px #000; border-radius: 5px; box-sizing: border-box; cursor: pointer; display: inline-block; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 25px; text-decoration: none; text-transform: capitalize; border-color: #000; color: #000;'>Atur Ulang Kata Sandi</a> 
                                                </td>
                                              </tr>
                                          </tbody>
                                          </table>
                                      </td>
                                      </tr>
                                  </tbody>
                                  </table>
                                  <small>Peringatan! Ini adalah pesan otomatis sehingga Anda tidak dapat membalas pesan ini.</small>
                              </td>
                              </tr>
                          </table>
                          </td>
                      </tr>
          
                      <!-- END MAIN CONTENT AREA -->
                      </table>
                      
                      <!-- START FOOTER -->
                      <div class='footer' style='clear: both; margin-top: 10px; text-align: center; width: 100%;'>
                      <table role='presentation' border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; width: 100%;' width='100%'>
                          <tr>
                          <td class='content-block' style='font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; color: #9a9ea6; font-size: 12px; text-align: center;' valign='top' align='center'>
                              <span class='apple-link' style='color: #9a9ea6; font-size: 12px; text-align: center;'>Workarea Jln. S. K. Lerik, Kota Baru, Kupang, NTT, Indonesia. (0380) 8438423</span>
                          </td>
                          </tr>
                          <tr>
                          <td class='content-block powered-by' style='font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; color: #9a9ea6; font-size: 12px; text-align: center;' valign='top' align='center'>
                              Powered by <a href='https://www.netmedia-framecode.com' style='color: #9a9ea6; font-size: 12px; text-align: center; text-decoration: none;'>Netmedia Framecode</a>.
                          </td>
                          </tr>
                      </table>
                      </div>
                      <!-- END FOOTER -->
          
                  <!-- END CENTERED WHITE CONTAINER -->
                  </div>
                  </td>
                  <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;' valign='top'>&nbsp;</td>
              </tr>
              </table>
          </body>
        </html>";
        smtp_mail($to, $subject, $message, "", "", 0, 0, true);
        $sql = "UPDATE users SET en_user='$en_user', token='$token', updated_at=current_timestamp WHERE email='$data[email]'";
      }
    }

    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }

  function new_password($conn, $data, $action)
  {
    if ($action == "update") {
      $lenght = strlen($data['password']);
      if ($lenght < 8) {
        $message = "Maaf, password yang anda masukan harus 8 digit atau lebih.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      } else if ($data['password'] !== $data['re_password']) {
        $message = "Maaf, konfirmasi password yang anda masukan belum sama.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      } else {
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password='$password' WHERE email='$data[email]'";
      }
    }

    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }

  function login($conn, $data)
  {
    // check account
    $checkAccount = mysqli_query($conn, "SELECT * FROM users JOIN user_role ON users.id_role=user_role.id_role WHERE users.email='$data[email]'");
    if (mysqli_num_rows($checkAccount) == 0) {
      $message = "Maaf, akun yang anda masukan belum terdaftar.";
      $message_type = "danger";
      alert($message, $message_type);
      return false;
    } else if (mysqli_num_rows($checkAccount) > 0) {
      $row = mysqli_fetch_assoc($checkAccount);
      if (password_verify($data['password'], $row["password"])) {
        $_SESSION["project_ksu_st_karolus"]["users"] = [
          "id" => $row["id_user"],
          "id_role" => $row["id_role"],
          "role" => $row["role"],
          "email" => $row["email"],
          "name" => $row["name"],
          "image" => $row["image"]
        ];
        return mysqli_affected_rows($conn);
      } else {
        $message = "Maaf, kata sandi yang anda masukan salah.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      }
    }
  }
}

if (isset($_SESSION["project_ksu_st_karolus"]["users"])) {

  function profil($conn, $data, $action, $id_user)
  {
    if ($action == "update") {
      $path = "../assets/img/profil/";
      if (!empty($_FILES['image']["name"])) {
        $fileName = basename($_FILES["image"]["name"]);
        $fileName = str_replace(" ", "-", $fileName);
        $fileName_encrypt = crc32($fileName);
        $ekstensiGambar = explode('.', $fileName);
        $ekstensiGambar = strtolower(end($ekstensiGambar));
        $imageUploadPath = $path . $fileName_encrypt . "." . $ekstensiGambar;
        $fileType = pathinfo($imageUploadPath, PATHINFO_EXTENSION);
        $allowTypes = array('jpg', 'png', 'jpeg');
        if (in_array($fileType, $allowTypes)) {
          $imageTemp = $_FILES["image"]["tmp_name"];
          compressImage($imageTemp, $imageUploadPath, 75);
          $image = $fileName_encrypt . "." . $ekstensiGambar;
        } else {
          $message = "Maaf, hanya file gambar JPG, JPEG, dan PNG yang diizinkan.";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        }
      }
      if (!empty($_FILES['image']["name"])) {
        $unwanted_characters = "../assets/img/profil/";
        $remove_image = str_replace($unwanted_characters, "", $data['imageOld']);
        if ($remove_image != "default.svg") {
          unlink($path . $remove_image);
        }
      } else if (empty($_FILE['image']["name"])) {
        $image = $data['imageOld'];
      }
      if (!empty($data['password'])) {
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET name='$data[name]', image='$image', password='$password' WHERE id_user='$id_user'";
      } else {
        $sql = "UPDATE users SET name='$data[name]', image='$image' WHERE id_user='$id_user'";
      }
    }

    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }

  function setting($conn, $data, $action)
  {

    if ($action == "update") {
      $path = "../assets/img/auth/";
      if (!empty($_FILES['image']["name"])) {
        $fileName = basename($_FILES["image"]["name"]);
        $fileName = str_replace(" ", "-", $fileName);
        $fileName_encrypt = crc32($fileName);
        $ekstensiGambar = explode('.', $fileName);
        $ekstensiGambar = strtolower(end($ekstensiGambar));
        $imageUploadPath = $path . $fileName_encrypt . "." . $ekstensiGambar;
        $fileType = pathinfo($imageUploadPath, PATHINFO_EXTENSION);
        $allowTypes = array('jpg', 'png', 'jpeg');
        if (in_array($fileType, $allowTypes)) {
          $imageTemp = $_FILES["image"]["tmp_name"];
          move_uploaded_file($imageTemp, $imageUploadPath);
          $image = $fileName_encrypt . "." . $ekstensiGambar;
        } else {
          $message = "Maaf, hanya file gambar JPG, JPEG, dan PNG yang diizinkan.";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        }
      }
      if (!empty($_FILES['image']["name"])) {
        $unwanted_characters = "../assets/img/auth/";
        $remove_image = str_replace($unwanted_characters, "", $data['imageOld']);
        unlink($path . $remove_image);
      } else if (empty($_FILE['image']["name"])) {
        $image = $data['imageOld'];
      }
      $sql = "UPDATE auth SET image='$image', bg='$data[bg]', model='$data[model]'";
    }

    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }

  function utilities($conn, $data, $action)
  {

    if ($action == "update") {
      $path = "../assets/img/";
      if (!empty($_FILES['logo']["name"])) {
        $fileName = basename($_FILES["logo"]["name"]);
        $fileName = str_replace(" ", "-", $fileName);
        $fileName_encrypt = crc32($fileName);
        $ekstensiGambar = explode('.', $fileName);
        $ekstensiGambar = strtolower(end($ekstensiGambar));
        $imageUploadPath = $path . $fileName_encrypt . "." . $ekstensiGambar;
        $fileType = pathinfo($imageUploadPath, PATHINFO_EXTENSION);
        $allowTypes = array('jpg', 'png', 'jpeg');
        if (in_array($fileType, $allowTypes)) {
          $imageTemp = $_FILES["logo"]["tmp_name"];
          move_uploaded_file($imageTemp, $imageUploadPath);
          $logo = $fileName_encrypt . "." . $ekstensiGambar;
        } else {
          $message = "Maaf, hanya file gambar JPG, JPEG, dan PNG yang diizinkan.";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        }
      }
      if (!empty($_FILES['logo']["name"])) {
        $unwanted_characters = "../assets/img/";
        $remove_image = str_replace($unwanted_characters, "", $data['logoOld']);
        unlink($path . $remove_image);
      } else if (empty($_FILE['logo']["name"])) {
        $logo = $data['logoOld'];
      }
      $sql = "UPDATE utilities SET logo='$logo', name_web='$data[name_web]', keyword='$data[keyword]', description='$data[description]', author='$data[author]'";
    }

    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }

  function users($conn, $data, $action)
  {

    if ($action == "update") {
      $sql = "UPDATE users SET id_role='$data[id_role]', id_active='$data[id_active]' WHERE id_user='$data[id_user]'";
    }

    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }

  function pengumuman($conn, $data, $action)
  {
    if ($action == "insert") {
      $sql = "INSERT INTO pengumuman(judul, isi_pengumuman, tgl_posting, id_admin) VALUES('$data[judul]', '$data[isi_pengumuman]', '$data[tgl_posting]', '$data[id_admin]')";
    }

    if ($action == "update") {
      $sql = "UPDATE pengumuman SET judul='$data[judul]', isi_pengumuman='$data[isi_pengumuman]', tgl_posting='$data[tgl_posting]', id_admin='$data[id_admin]' WHERE id_pengumuman='$data[id_pengumuman]'";
    }

    if ($action == "delete") {
      $sql = "DELETE FROM pengumuman WHERE id_pengumuman='$data[id_pengumuman]'";
    }

    $query = mysqli_query($conn, $sql);
    if (!$query) {
      return false;
    }

    return $action == "update" ? 1 : mysqli_affected_rows($conn);
  }

  function kebijakan($conn, $data, $action)
  {
    if ($action == "insert") {
      $sql = "INSERT INTO kebijakan_koperasi(judul, isi_kebijakan, tgl_ubah, id_admin) VALUES('$data[judul]', '$data[isi_kebijakan]', '$data[tgl_ubah]', '$data[id_admin]')";
    }

    if ($action == "update") {
      $sql = "UPDATE kebijakan_koperasi SET judul='$data[judul]', isi_kebijakan='$data[isi_kebijakan]', tgl_ubah='$data[tgl_ubah]', id_admin='$data[id_admin]' WHERE id_kebijakan='$data[id_kebijakan]'";
    }

    if ($action == "delete") {
      $sql = "DELETE FROM kebijakan_koperasi WHERE id_kebijakan='$data[id_kebijakan]'";
    }

    $query = mysqli_query($conn, $sql);
    if (!$query) {
      return false;
    }

    return $action == "update" ? 1 : mysqli_affected_rows($conn);
  }

  function simpanan($conn, $data, $action)
  {
    if ($action == "insert") {
      $sql = "INSERT INTO simpanan(id_anggota, id_penerima, jenis_simpanan, jumlah, tgl_simpanan, status_simpanan, id_rat) VALUES('$data[id_anggota]', NULL, '$data[jenis_simpanan]', '$data[jumlah]', '$data[tgl_simpanan]', 'Pengajuan', '$data[id_rat]')";
    }

    if ($action == "update") {
      $sql = "UPDATE simpanan SET id_anggota='$data[id_anggota]', id_penerima=NULL, jenis_simpanan='$data[jenis_simpanan]', jumlah='$data[jumlah]', tgl_simpanan='$data[tgl_simpanan]' WHERE id_simpanan='$data[id_simpanan]' AND status_simpanan='Pengajuan'";
    }

    if ($action == "delete") {
      $sql = "DELETE FROM simpanan WHERE id_simpanan='$data[id_simpanan]' AND status_simpanan='Pengajuan'";
    }

    $query = mysqli_query($conn, $sql);
    if (!$query) {
      return false;
    }

    return $action == "update" ? 1 : mysqli_affected_rows($conn);
  }

  function pinjaman($conn, $data, $action)
  {
    $id_validator = !empty($data['id_validator']) ? "'$data[id_validator]'" : "NULL";
    $tanggal_disetujui = !empty($data['tanggal_disetujui']) ? "'$data[tanggal_disetujui]'" : "NULL";

    if ($action == "insert") {
      $sql = "INSERT INTO pinjaman(id_anggota, id_validator, jumlah_pinjaman, tenor_bulan, bunga_persen, status_pinjam, tanggal_pengajuan, tanggal_disetujui, id_rat, basis_bunga) VALUES('$data[id_anggota]', $id_validator, '$data[jumlah_pinjaman]', '$data[tenor_bulan]', '$data[bunga_persen]', '$data[status_pinjam]', '$data[tanggal_pengajuan]', $tanggal_disetujui, '$data[id_rat]', 'total')";
    }

    if ($action == "update") {
      $sql = "UPDATE pinjaman SET id_anggota='$data[id_anggota]', id_validator=$id_validator, jumlah_pinjaman='$data[jumlah_pinjaman]', tenor_bulan='$data[tenor_bulan]', bunga_persen='$data[bunga_persen]', status_pinjam='$data[status_pinjam]', tanggal_pengajuan='$data[tanggal_pengajuan]', tanggal_disetujui=$tanggal_disetujui WHERE id_pinjaman='$data[id_pinjaman]' AND status_pinjam='Pengajuan'";
    }

    if ($action == "delete") {
      $sql = "DELETE FROM pinjaman WHERE id_pinjaman='$data[id_pinjaman]' AND status_pinjam='Pengajuan'";
    }

    $query = mysqli_query($conn, $sql);
    if (!$query) {
      return false;
    }

    return mysqli_affected_rows($conn);
  }

  function angsuran($conn, $data, $action)
  {
    if ($action == "insert" || $action == "update") {
      $id_pinjaman = $data['id_pinjaman'];
      $exclude_angsuran = $action == "update" ? " AND id_angsuran != '$data[id_angsuran]'" : "";
      $pinjaman_query = mysqli_query($conn, "SELECT jumlah_pinjaman, tenor_bulan, bunga_persen, basis_bunga FROM pinjaman WHERE id_pinjaman='$id_pinjaman'");
      $pinjaman_data = mysqli_fetch_assoc($pinjaman_query);
      $jumlah_pinjaman = $pinjaman_data ? (float) $pinjaman_data['jumlah_pinjaman'] : 0;
      $tenor = $pinjaman_data ? (int) $pinjaman_data['tenor_bulan'] : 0;
      $bunga_persen = $pinjaman_data ? (float) $pinjaman_data['bunga_persen'] : 0;
      $angsuran_bulanan = $tenor > 0 ? totalTagihanPinjaman($jumlah_pinjaman, $bunga_persen, $tenor, $pinjaman_data['basis_bunga']) / $tenor : 0;
      $total_pinjaman = $angsuran_bulanan * $tenor;
      $angsuran_query = mysqli_query($conn, "SELECT COALESCE(SUM(jumlah_bayar), 0) AS total_bayar FROM angsuran WHERE id_pinjaman='$id_pinjaman'$exclude_angsuran");
      $angsuran_data = mysqli_fetch_assoc($angsuran_query);
      $total_bayar = $angsuran_data ? (float) $angsuran_data['total_bayar'] : 0;
      $sisa_sebelum_bayar = max($total_pinjaman - $total_bayar, 0);
      $angsuran_ke = (int) $data['angsuran_ke'];

      if (isset($data['auto_hitung_angsuran'])) {
        $angsuran_ke_query = mysqli_query($conn, "SELECT COALESCE(MAX(angsuran_ke), 0) AS angsuran_terakhir FROM angsuran WHERE id_pinjaman='$id_pinjaman'");
        $angsuran_ke_data = mysqli_fetch_assoc($angsuran_ke_query);
        $angsuran_terakhir = $angsuran_ke_data ? (int) $angsuran_ke_data['angsuran_terakhir'] : 0;
        if ($action == "update") {
          $angsuran_ke_query = mysqli_query($conn, "SELECT COALESCE(MAX(angsuran_ke), 0) AS angsuran_terakhir FROM angsuran WHERE id_pinjaman='$id_pinjaman' AND id_angsuran != '$data[id_angsuran]'");
          $angsuran_ke_data = mysqli_fetch_assoc($angsuran_ke_query);
          $angsuran_terakhir = $angsuran_ke_data ? (int) $angsuran_ke_data['angsuran_terakhir'] : 0;
        }
        $angsuran_ke = max($angsuran_ke, $angsuran_terakhir + 1);
        $angsuran_ke = $tenor > 0 ? min($angsuran_ke, $tenor) : $angsuran_ke;
        $jumlah_bulan = max($angsuran_ke - $angsuran_terakhir, 1);
        $jumlah_bayar = $angsuran_ke >= $tenor ? $sisa_sebelum_bayar : min(round($angsuran_bulanan, 2) * $jumlah_bulan, $sisa_sebelum_bayar);
      } else {
        $jumlah_bayar = (float) $data['jumlah_bayar'];
      }

      $sisa_pinjaman = max($total_pinjaman - ($total_bayar + $jumlah_bayar), 0);
    }

    if ($action == "insert") {
      $sql = "INSERT INTO angsuran(id_pinjaman, id_penerima, angsuran_ke, jumlah_bayar, sisa_pinjaman, tgl_bayar) VALUES('$data[id_pinjaman]', '$data[id_penerima]', '$angsuran_ke', '$jumlah_bayar', '$sisa_pinjaman', '$data[tgl_bayar]')";
    }

    if ($action == "update") {
      $sql = "UPDATE angsuran SET id_pinjaman='$data[id_pinjaman]', id_penerima='$data[id_penerima]', angsuran_ke='$angsuran_ke', jumlah_bayar='$jumlah_bayar', sisa_pinjaman='$sisa_pinjaman', tgl_bayar='$data[tgl_bayar]' WHERE id_angsuran='$data[id_angsuran]'";
    }

    if ($action == "delete") {
      $sql = "DELETE FROM angsuran WHERE id_angsuran='$data[id_angsuran]'";
    }

    $query = mysqli_query($conn, $sql);
    if (!$query) {
      return false;
    }

    return $action == "update" ? 1 : mysqli_affected_rows($conn);
  }

  function anggota($conn, $data, $action)
  {
    if ($action == "insert") {
      $checkEmail = mysqli_query($conn, "SELECT id_user FROM users WHERE email='$data[email]'");
      if (mysqli_num_rows($checkEmail) > 0) {
        $message = "Maaf, email yang anda masukan sudah terdaftar.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      }

      $checkNik = mysqli_query($conn, "SELECT id_anggota FROM anggota WHERE nik='$data[nik]'");
      if (mysqli_num_rows($checkNik) > 0) {
        $message = "Maaf, NIK yang anda masukan sudah terdaftar.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      }

      if ($data['password'] !== $data['re_password']) {
        $message = "Maaf, konfirmasi password yang anda masukan belum sama.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      }

      $password = password_hash($data['password'], PASSWORD_DEFAULT);
      $token = generateToken();
      $en_user = password_hash($token, PASSWORD_DEFAULT);
      $en_user = str_replace("$", "", $en_user);
      $en_user = str_replace("/", "", $en_user);
      $en_user = str_replace(".", "", $en_user);

      mysqli_begin_transaction($conn);

      $insertUser = "INSERT INTO users(id_role, id_active, en_user, token, name, email, password) VALUES('$data[id_role]', '$data[id_active]', '$en_user', '$token', '$data[name]', '$data[email]', '$password')";
      $queryUser = mysqli_query($conn, $insertUser);

      if (!$queryUser) {
        mysqli_rollback($conn);
        $message = "Data anggota gagal ditambahkan.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      }

      $id_user = mysqli_insert_id($conn);
      $insertAnggota = "INSERT INTO anggota(id_user, nik, nama_lengkap, jenis_anggota, no_telp, alamat, tgl_gabung) VALUES('$id_user', '$data[nik]', '$data[nama_lengkap]', '$data[jenis_anggota]', '$data[no_telp]', '$data[alamat]', '$data[tgl_gabung]')";
      $queryAnggota = mysqli_query($conn, $insertAnggota);

      if (!$queryAnggota) {
        mysqli_rollback($conn);
        $message = "Data anggota gagal ditambahkan.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      }

      mysqli_commit($conn);
      return 1;
    }

    if ($action == "update") {
      $checkEmail = mysqli_query($conn, "SELECT id_user FROM users WHERE email='$data[email]' AND id_user != '$data[id_user]'");
      if (mysqli_num_rows($checkEmail) > 0) {
        $message = "Maaf, email yang anda masukan sudah terdaftar.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      }

      $checkNik = mysqli_query($conn, "SELECT id_anggota FROM anggota WHERE nik='$data[nik]' AND id_anggota != '$data[id_anggota]'");
      if (mysqli_num_rows($checkNik) > 0) {
        $message = "Maaf, NIK yang anda masukan sudah terdaftar.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      }

      if (!empty($data['password']) || !empty($data['re_password'])) {
        if ($data['password'] !== $data['re_password']) {
          $message = "Maaf, konfirmasi password yang anda masukan belum sama.";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        }

        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $password_sql = ", password='$password'";
      } else {
        $password_sql = "";
      }

      mysqli_begin_transaction($conn);

      $updateUser = "UPDATE users SET id_role='$data[id_role]', id_active='$data[id_active]', name='$data[name]', email='$data[email]'$password_sql WHERE id_user='$data[id_user]'";
      $queryUser = mysqli_query($conn, $updateUser);

      if (!$queryUser) {
        mysqli_rollback($conn);
        $message = "Data anggota gagal diubah.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      }

      $memberId = (int)$data['id_anggota'];
      $hasApplication = mysqli_query($conn, "SELECT 1 FROM simpanan WHERE id_anggota=$memberId UNION ALL SELECT 1 FROM pinjaman WHERE id_anggota=$memberId LIMIT 1");
      if (mysqli_num_rows($hasApplication) > 0) $data['jenis_anggota'] = 'Tetap';
      $updateAnggota = "UPDATE anggota SET nik='$data[nik]', nama_lengkap='$data[nama_lengkap]', jenis_anggota='$data[jenis_anggota]', no_telp='$data[no_telp]', alamat='$data[alamat]', tgl_gabung='$data[tgl_gabung]' WHERE id_anggota='$data[id_anggota]'";
      $queryAnggota = mysqli_query($conn, $updateAnggota);

      if (!$queryAnggota) {
        mysqli_rollback($conn);
        $message = "Data anggota gagal diubah.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      }

      mysqli_commit($conn);
      return 1;
    }

    if ($action == "delete") {
      mysqli_begin_transaction($conn);

      $deleteAnggota = "DELETE FROM anggota WHERE id_anggota='$data[id_anggota]'";
      $queryAnggota = mysqli_query($conn, $deleteAnggota);

      if (!$queryAnggota) {
        mysqli_rollback($conn);
        $message = "Data anggota gagal dihapus.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      }

      $deleteUser = "DELETE FROM users WHERE id_user='$data[id_user]'";
      $queryUser = mysqli_query($conn, $deleteUser);

      if (!$queryUser) {
        mysqli_rollback($conn);
        $message = "Data anggota gagal dihapus.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      }

      mysqli_commit($conn);
      return 1;
    }
  }

  function role($conn, $data, $action)
  {
    if ($action == "insert") {
      $checkRole = "SELECT * FROM user_role WHERE role LIKE '%$data[role]%'";
      $checkRole = mysqli_query($conn, $checkRole);
      if (mysqli_num_rows($checkRole) > 0) {
        $message = "Maaf, role yang anda masukan sudah ada.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      } else {
        $sql = "INSERT INTO user_role(role) VALUES('$data[role]')";
      }
    }

    if ($action == "update") {
      if ($data['role'] !== $data['roleOld']) {
        $checkRole = "SELECT * FROM user_role WHERE role LIKE '%$data[role]%'";
        $checkRole = mysqli_query($conn, $checkRole);
        if (mysqli_num_rows($checkRole) > 0) {
          $message = "Maaf, role yang anda masukan sudah ada.";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        }
      }
      $sql = "UPDATE user_role SET role='$data[role]' WHERE id_role='$data[id_role]'";
    }

    if ($action == "delete") {
      $sql = "DELETE FROM user_role WHERE id_role='$data[id_role]'";
    }

    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }

  function menu($conn, $data, $action)
  {
    if ($action == "insert") {
      $namaFolder = strtolower($data['menu']);
      $namaFolder = str_replace(" ", "-", $namaFolder);
      $checkMenu = "SELECT * FROM user_menu WHERE menu='$data[menu]'";
      $checkMenu = mysqli_query($conn, $checkMenu);
      if (mysqli_num_rows($checkMenu) > 0) {
        $message = "Maaf, menu yang anda masukan sudah ada.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      } else {
        $pathFolder = __DIR__ . '/../views/' . $namaFolder;
        if (!is_dir($pathFolder)) {
          mkdir($pathFolder, 0777, true);
          $file = fopen($pathFolder . '/redirect.php', "w");
          fwrite($file, '<?php if (!isset($_SESSION["project_ksu_st_karolus"]["users"])) {
            header("Location: ../../auth/");
            exit;
          }
          ');
          fclose($file);

          $file_controller = fopen("../controller/" . $namaFolder . ".php", "w");
          fwrite($file_controller, '<?php
  
          require_once("../../config/Base.php");
          require_once("../../config/Auth.php");
          require_once("../../config/Alert.php");
          require_once("../../views/' . $namaFolder . '/redirect.php");
          ');
          fclose($file_controller);
        } else {
          $message = "Folder $namaFolder sudah ada!";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        }
        $sql = "INSERT INTO user_menu(icon,menu) VALUES('$data[icon]','$data[menu]')";
      }
    }

    if ($action == "update") {
      $menu_baru = strtolower(str_replace(' ', '-', $data['menu']));
      $menu_lama = strtolower(str_replace(' ', '-', $data['menuOld']));
      if ($menu_baru !== $menu_lama) {
        $checkMenu = "SELECT * FROM user_menu WHERE menu='$data[menu]'";
        $checkMenu = mysqli_query($conn, $checkMenu);
        if (mysqli_num_rows($checkMenu) > 0) {
          $message = "Maaf, menu yang anda masukan sudah ada.";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        }
        $folder_lama = __DIR__ . '/../views/' . $menu_lama;
        $folder_baru = __DIR__ . '/../views/' . $menu_baru;
        if (is_dir($folder_lama)) {
          if ($menu_baru !== $menu_lama) {
            if (rename($folder_lama, $folder_baru)) {
            } else {
              $message = "Gagal mengubah nama folder.";
              $message_type = "danger";
              alert($message, $message_type);
              return false;
            }
          }
        } else {
          $message = "Folder lama tidak ditemukan.";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        }
      }
      $sql = "UPDATE user_menu SET icon='$data[icon]', menu='$data[menu]' WHERE id_menu='$data[id_menu]'";
    }

    if ($action == "delete") {
      $menu = strtolower(str_replace(' ', '-', $data['menu']));
      $pathFolder = __DIR__ . '/../views/' . $menu;
      unlink("../controller/" . $menu . ".php");
      hapusFolderRecursively($pathFolder);
      $sql = "DELETE FROM user_menu WHERE id_menu='$data[id_menu]'";
    }

    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }

  function sub_menu($conn, $data, $action, $baseURL)
  {
    $url = strtolower($data['title']);
    $url = str_replace(" ", "-", $url);

    if ($action == "insert") {
      $checkSubMenu = "SELECT * FROM user_sub_menu WHERE title='$data[title]'";
      $checkSubMenu = mysqli_query($conn, $checkSubMenu);
      if (mysqli_num_rows($checkSubMenu) > 0) {
        $message = "Maaf, nama sub menu yang anda masukan sudah ada.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      } else {
        $menu = "SELECT * FROM user_menu WHERE id_menu = '$data[id_menu]'";
        $view_menu = mysqli_query($conn, $menu);
        $data_menu = mysqli_fetch_assoc($view_menu);
        $menu = strtolower($data_menu['menu']);
        $menu = str_replace(" ", "-", $menu);

        $file_views = fopen("../views/" . $menu . "/" . $url . ".php", "w");
        fwrite($file_views, '<?php require_once("../../controller/' . $menu . '.php");
        $_SESSION["project_ksu_st_karolus"]["name_page"] = "' . $data['title'] . '";
        require_once("../../templates/views_top.php"); ?>

        <div class="nxl-content">

          <!-- [ page-header ] start -->
          <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
              <div class="page-header-title">
                <h5 class="m-b-10"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] ?></h5>
              </div>
              <ul class="breadcrumb">
                <li class="breadcrumb-item">' . $data['title'] . '</li>
                <li class="breadcrumb-item"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] ?></li>
              </ul>
            </div>
            <div class="page-header-right ms-auto">
              <div class="page-header-right-items">
                <div class="d-flex d-md-none">
                  <a href="javascript:void(0)" class="page-header-right-close-toggle">
                    <i class="feather-arrow-left me-2"></i>
                    <span>Back</span>
                  </a>
                </div>
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                  <a href="add-' . $url . '" class="btn btn-primary">
                    <i class="feather-plus me-2"></i>
                    <span>Tambah</span>
                  </a>
                </div>
              </div>
              <div class="d-md-none d-flex align-items-center">
                <a href="javascript:void(0)" class="page-header-right-open-toggle">
                  <i class="feather-align-right fs-20"></i>
                </a>
              </div>
            </div>
          </div>
          <!-- [ page-header ] end -->

          <!-- [ Main Content ] start -->
          <div class="main-content">
          </div>
          <!-- [ Main Content ] end -->

        </div>

        <?php require_once("../../templates/views_bottom.php") ?>
        ');
        fclose($file_views);

        $file_views_add = fopen("../views/" . $menu . "/add-" . $url . ".php", "w");
        fwrite($file_views_add, '<?php require_once("../../controller/' . $menu . '.php");
        $_SESSION["project_ksu_st_karolus"]["name_page"] = "Tambah ' . $data['title'] . '";
        require_once("../../templates/views_top.php"); ?>

        <div class="nxl-content">

          <!-- [ page-header ] start -->
          <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
              <div class="page-header-title">
                <h5 class="m-b-10"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] ?></h5>
              </div>
              <ul class="breadcrumb">
                <li class="breadcrumb-item">' . $data['title'] . '</li>
                <li class="breadcrumb-item"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] ?></li>
              </ul>
            </div>
          </div>
          <!-- [ page-header ] end -->

          <!-- [ Main Content ] start -->
          <div class="main-content">
          </div>
          <!-- [ Main Content ] end -->

        </div>

        <?php require_once("../../templates/views_bottom.php") ?>
        ');
        fclose($file_views_add);

        $petik = "'";
        $file_views_edit = fopen("../views/" . $menu . "/edit-" . $url . ".php", "w");
        fwrite($file_views_edit, '<?php require_once("../../controller/' . $menu . '.php");
        if(!isset($_GET["p"])){
          header("Location: menu");
          exit();
        }else{
          $id = valid($conn, $_GET["p"]); 
          $pull_data = "SELECT * FROM  WHERE  = ' . $petik . '$id' . $petik . '";
          $store_data = mysqli_query($conn, $pull_data);
          $view_data = mysqli_fetch_assoc($store_data);
        $_SESSION["project_ksu_st_karolus"]["name_page"] = "Ubah ' . $data['title'] . '";
        require_once("../../templates/views_top.php"); ?>

        <div class="nxl-content">

          <!-- [ page-header ] start -->
          <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
              <div class="page-header-title">
                <h5 class="m-b-10"><?= $_SESSION["project_ksu_st_karolus"]["name_page"] ?></h5>
              </div>
              <ul class="breadcrumb">
                <li class="breadcrumb-item">' . $data['title'] . '</li>
                <li class="breadcrumb-item"><?= $_SESSION["project_ksu_st_karolus"]["name_page"].' . $petik . ' ' . $petik . '.$view_data[""]  ?></li>
              </ul>
            </div>
          </div>
          <!-- [ page-header ] end -->

          <!-- [ Main Content ] start -->
          <div class="main-content">
          </div>
          <!-- [ Main Content ] end -->

        </div>

        <?php }
        require_once("../../templates/views_bottom.php") ?>
        ');
        fclose($file_views_edit);

        $url_sub = $menu . "/" . $url;
        $sql = "INSERT INTO user_sub_menu(id_menu,id_active,title,url) VALUES('$data[id_menu]','$data[id_active]','$data[title]','$url_sub')";
      }
    }

    if ($action == "update") {
      if ($data['title'] !== $data['titleOld']) {
        $checkSubMenu = "SELECT * FROM user_sub_menu WHERE title='$data[title]'";
        $checkSubMenu = mysqli_query($conn, $checkSubMenu);
        if (mysqli_num_rows($checkSubMenu) > 0) {
          $message = "Maaf, nama sub menu yang anda masukan sudah ada.";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        }
      }
      $menu = "SELECT * FROM user_menu WHERE id_menu = '$data[id_menu]'";
      $view_menu = mysqli_query($conn, $menu);
      $data_menu = mysqli_fetch_assoc($view_menu);
      $menu = strtolower($data_menu['menu']);
      $menu = str_replace(" ", "-", $menu);
      rename($menu . '/' . $data['urlOld'] . '.php', $menu . '/' . $url . '.php');
      rename($menu . '/' . "add-" . $data['urlOld'] . '.php', $menu . '/' . "add-" . $url . '.php');
      rename($menu . '/' . "edit-" . $data['urlOld'] . '.php', $menu . '/' . "edit-" . $url . '.php');
      $sql = "UPDATE user_sub_menu SET id_menu='$data[id_menu]', id_active='$data[id_active]', title='$data[title]', url='$url' WHERE id_sub_menu='$data[id_sub_menu]'";
    }

    if ($action == "delete") {
      unlink("../views/" . $data['menu'] . "/" . $url . ".php");
      unlink("../views/" . $data['menu'] . "/" . "add-" . $url . ".php");
      unlink("../views/" . $data['menu'] . "/" . "edit-" . $url . ".php");
      $sql = "DELETE FROM user_sub_menu WHERE id_sub_menu='$data[id_sub_menu]'";
    }

    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }

  function role_permission($conn, $data, $action)
  {
    if ($action == "update") {
      $id_role = valid($conn, $data['id_role']);
      $affected_rows = 0;
      $reset_sql = "UPDATE permissions SET `view`=0, `create`=0, `edit`=0, `delete`=0 WHERE id_role='$id_role'";
      mysqli_query($conn, $reset_sql);
      if (isset($data['access']) && is_array($data['access'])) {
        foreach ($data['access'] as $id_sub_menu => $actions) {
          $view = isset($actions['view']) ? 1 : 0;
          $create = isset($actions['create']) ? 1 : 0;
          $edit = isset($actions['edit']) ? 1 : 0;
          $delete = isset($actions['delete']) ? 1 : 0;
          $check_sql = "SELECT id_permission FROM permissions WHERE id_role='$id_role' AND id_sub_menu='$id_sub_menu'";
          $check_query = mysqli_query($conn, $check_sql);
          if (mysqli_num_rows($check_query) > 0) {
            $update_sql = "UPDATE permissions SET `view`='$view', `create`='$create', `edit`='$edit', `delete`='$delete' 
                         WHERE id_role='$id_role' AND id_sub_menu='$id_sub_menu'";
            mysqli_query($conn, $update_sql);
            $affected_rows++;
          } else {
            $insert_sql = "INSERT INTO permissions (id_role, id_sub_menu, `view`, `create`, `edit`, `delete`) 
                         VALUES ('$id_role', '$id_sub_menu', '$view', '$create', '$edit', '$delete')";
            mysqli_query($conn, $insert_sql);
            $affected_rows++;
          }
        }
      }
      return $affected_rows > 0 ? $affected_rows : 1;
    }
    return false;
  }

  function __name($conn, $data, $action)
  {
    if ($action == "insert") {
    }

    if ($action == "update") {
    }

    if ($action == "delete") {
    }

    // mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }
}
