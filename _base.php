<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// This ensures the session starts only if it's not already active.

date_default_timezone_set('Asia/Kuala_Lumpur');

// Is GET request?
function is_get() {
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

// Is POST request?
function is_post() {
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

// Encode HTML special characters
function encode($value) {
    return htmlentities($value);
}

// Obtain REQUEST (GET and POST) parameter
function req($key, $default = '') {
    if (!isset($_REQUEST[$key])) {
        return $default;
    }

    $value = $_REQUEST[$key];

    if (is_array($value)) {
        return array_map('trim', $value);
    }

    return trim((string)$value);
}


// Generate <input type='text'>
function html_text($key, $value = '', $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='text' id='$key' name='$key' value='$value' $attr>";
}


// Generate <input type='text' readonly>
function html_text_readonly($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='text' id='$key' name='$key' value='$value' readonly style='width: 1000px;'>";
}

// Generate <input type='text'>
function html_textarea($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<textarea id='$key' name='$key' value='$value' style='width: 460px; height: 150px; padding-top: 10px; vertical-align: top;' $attr>$value</textarea>";
}

// Redirect to URL
function redirect($url = null) {
    if (!$url) $url = $_SERVER['REQUEST_URI'];
    header("Location: $url");
    exit;
}

// Generate <input type='password'>
function html_password($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='password' id='$key' name='$key' value='$value' $attr>";
}

// Generate <input type='radio'> list
function html_radios($key, $items, $br = false) {
    $value = encode($GLOBALS[$key] ?? '');
    foreach ($items as $username => $text) {
        $state = $username == $value ? 'checked' : '';
        echo "<label><input type='radio' id='{$key}_$username' name='$key' value='$username' $state>$text</label>";
        
    }
}

// Generate <input type='search'>
function html_search($key, $text = '', $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='search' id='$key' name='$key' placeholder='$text' value='$value' $attr >";
}

// Generate <input type='file'>
function html_file($key, $accept = '', $attr = '' ) {
    echo "<input type='file'  name='$key' id='$key' accept='$accept' $attr>";
}

// Generate <input type='file'>
function html_file_disabled($key, $accept = '', $attr = '' ) {
    echo "<input type='file'  name='$key' id='$key' accept='$accept' disabled $attr>";
}

// Generate <input type='hidden'>
function html_hidden($name, $value = '') {
    return "<input type='hidden' name='$name' value='" . htmlspecialchars($value) . "'>";
}

// Generate <select>
function html_select($key, $items, $selected = '', $attr = '') {
    $selected = encode($selected);
    echo "<select id='$key' name='$key' $attr>";
    if ($selected === '') {
        echo "<option value=''>- Select One -</option>";
    }
    foreach ($items as $id => $text) {
        $state = ($id == $selected) ? 'selected' : '';
        echo "<option value='$id' $state>$text</option>";
    }
    echo '</select>';
}


// Generate text disabled
function html_text_disabled($key, $text = '', $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='text' id='$key' name='$key' value='$value' disabled $attr>";
}

// Obtain uploaded file --> cast to object
function get_file($key) {
    $f = $_FILES[$key] ?? null;
    
    if ($f && $f['error'] == 0) {
        return (object)$f;
    }

    return null;
}

// Crop, resize and save photo
function save_photo($f, $folder, $width = 200, $height = 200) {
    $photo = uniqid() . '.jpg';
    
    require_once 'lib/SimpleImage.php';
    $img = new SimpleImage();
    $img->fromFile($f->tmp_name)
        ->thumbnail($width, $height)
        ->toFile("$folder/$photo", 'image/jpeg');

    return $photo;
}

// Gender radio value
$_genders = [
    'M' => 'Male',
    'F' => 'Female',
];

// Status radio value 
$_status = [
    '1' => 'Active',
    '0' => 'Disabled',
];

// Global error array
$_err = [];


// Generate <span class='err'>
function err($key) {
    global $_err;
    if ($_err[$key] ?? false) {
        echo "<span class='err'>$_err[$key]</span>";
    }
    else {
        echo '<span></span>';
    }
}

// Set or get temporary session variable
function temp($key, $value = null) {
    if ($value !== null) {
        $_SESSION["temp_$key"] = $value; //set
    }
    else {
        $value = $_SESSION["temp_$key"] ?? null; //read
        unset($_SESSION["temp_$key"]);
        return $value;
    }
}



// Global user object
$user = $_SESSION['user'] ?? null;

// Authorization
function auth(...$roles) {
    global $user;
    if ($user) {
        if ($roles) {
            if (in_array($user->role, $roles)) {
                return; // OK
            }
        }
        else {
            redirect('/login.php');
        }
    }
    
}

// Login user
function login($user, $url = '/'){
    $_SESSION['user'] = $user;
    redirect($url);
}

// Is unique?
function is_unique($value, $table, $field) {
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() == 0;
}

// Is exists?
function is_exists($value, $table, $field) {
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() > 0;
}

// Is email?
function is_email($value) {
    return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
}

// ============================================================================
// Email Functions
// ============================================================================

// Demo Accounts:
// --------------
// AACS3173@gmail.com           npsg gzfd pnio aylm
// BAIT2173.email@gmail.com     ytwo bbon lrvw wclr
// liaw.casual@gmail.com        wtpa kjxr dfcb xkhg
// liawcv1@gmail.com            obyj shnv prpa kzvj

// Initialize and return mail object
function get_mail() {
    require_once 'lib/PHPMailer.php';
    require_once 'lib/SMTP.php';

    $m = new PHPMailer(true);
    $m->isSMTP();
    $m->SMTPAuth = true;
    $m->Host = 'smtp.gmail.com';
    $m->Port = 587;
    $m->Username = 'xiaoyu56c@gmail.com';
    $m->Password = 'xqxj ardr hlhe gipk';
    $m->CharSet = 'utf-8';
    $m->setFrom($m->Username, 'CTRL + EAT');

    return $m;
}

// Return base url (host + port)
function base($path = '') {
    return "http://$_SERVER[SERVER_NAME]:$_SERVER[SERVER_PORT]/$path";
}

// Get shopping cart
function get_cart() {
    return $_SESSION['cart'] ?? [];
}

// Set shopping cart
function set_cart($cart = []) {
    return $_SESSION['cart'] = $cart;
}

// Update shopping cart
function update_cart($product_id, $unit) {
    $cart = get_cart();

    if ($unit >= 1 && $unit <= 10 && is_exists($product_id, 'product', 'product_id')) {
        $cart[$product_id] = $unit;
    }
    else {
        unset($cart[$product_id]);
    }

    set_cart($cart);
}

// GlobaL range of units
$_units = array_combine(range(1, 10), range(1, 10));

// Global Address session
$address = $_SESSION['address'] ?? null;

// Global Payment Selection Option
$payment_method = [
    'online_transfer' => 'Online Transfer',
    'duitnow' => 'DuitNow',
];