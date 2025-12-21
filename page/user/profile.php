    <?php
require_once '../../connect.php';
require_once '../../_base.php';

auth('Member');

if (is_get()) {
    $stm = $_db->prepare('SELECT * FROM user WHERE user_id = ?');
    $stm->execute([$user->user_id]);
    $u = $stm->fetch();

    if (!$u) {
        redirect('/');
    }

    extract((array)$u);
    $_SESSION['photo'] = $u->photo;
}

if (is_post()) { 
        $username   = req('username');
        $gender = req('gender');
        $age = req('age');
        $email = req('email'); 
        $photo = $_SESSION['photo'];
        $f = get_file('photo');
        $password   = req('password');
        
        //username validation
        if ($username == ''){
            $_err['username'] = 'Please enter your username';
        } else {
            $stm = $_db->prepare('
                SELECT COUNT(*) FROM user
                WHERE username = ? AND user_id!= ?
            ');
            $stm->execute([$username, $user->user_id]);
    
            if ($stm->fetchColumn() > 0) {
                $_err['username'] = 'Username Duplicated';
            }
        }
    
        //gender validation
        if ($gender == '') {
            $_err['gender'] = 'Required';
        }
    
        //age validation
        if($age == ''){
            $_err['age'] = 'Required';
        }
    
        //email validation
        if($email == ''){
            $_err['email'] = 'Please enter your email';
        }else if(!is_email($email)){
            $_err['email'] = 'Invalid email address. Please use a Gmail address.';
        }else {
            $stm = $_db->prepare('
                SELECT COUNT(*) FROM user
                WHERE email = ? AND user_id!= ?
            ');
            $stm->execute([$email, $user->user_id]);
    
            if ($stm->fetchColumn() > 0) {
                $_err['email'] = 'email Duplicated';
            }
        }
    
        // Validate: photo (file)
        
        if ($f) {
            if (!str_starts_with($f->type, 'image/')) {
            $_err['photo'] = 'Must be image';
        }
        else if ($f->size > 3 * 1024 * 1024) {
            $_err['photo'] = 'Maximum 1MB';
        }

        }
    
    
        if (!$_err) {
    
            // (1) Delete and save photo --> optional
            if ($f) {
                    unlink("../../photos/$photo");
            
                $photo = save_photo($f, '../../photos');
                
            }
            
            // (2) Update user (email, name, photo)
            // TODO
            $stm = $_db->prepare('
                UPDATE user
                SET username = ?, gender = ?, age = ?, email = ?, photo = ?
                WHERE user_id = ?
            ');
            $stm->execute([$username, $gender, $age, $email, $photo, $user->user_id]);
    
            
            // (3) Update global user object
            $user->username = $username;
            $user->gender = $gender;
            $user->age = $age;
            $user->email = $email;
            $user->photo = $photo;

            temp('info', 'Record updated');
            header("Location: /index.php?user_id=$user->user_id");
        }
    
}

?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="../../javascript/app.js"></script>

<style>
    section button {
        font-size: larger;
    }
</style>

<head>
    <title>My Account</title>
    <link rel="shortcut icon" href="/image/teddy-bear.png">
    <link rel="stylesheet" href="/css/profile.css">
</head>

<body>
    <header>
        <nav>
            <div class="menu">
                <div class="menu_row">
                    <span class="back_btn" onclick="window.history.back()"><img src="/icon/back.jpg" alt="backbtn"></span>
                    <span><h1>My Account</h1></span>
                    <div class="reset_logout">
                        <span class="reset"><a href="./reset.php" style="text-decoration: none;"><h1>Reset Password</h1></a></span>
                        <span class="logout"><a href="./logout.php" style="text-decoration: none;" data-confirm="Are you sure you want to log out?"><h1>Logout</h1></a></span>
                    </div>  
                </div>            
            </div>
        </nav>
    </header>
</body>

<main>
<div class="profile">
            <form method="post" class="form" enctype="multipart/form-data">
                <label for="username">New Username &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
                <?= html_text('username') ?>
                <?= err('username') ?><br>
                
                <label>Gender&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
                <?= html_radios('gender', $_genders) ?>
                <?= err('gender') ?>
                
                <label for="age">Age&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
                <?= html_text('age') ?>
                <?= err('age') ?><br>
                
                <label for="email">Email&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
                <?= html_text('email') ?>
                <?= err('email') ?><br>
                
                <label for="photo">Photo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
                <label class="upload" tabindex="0">
                    <?= html_file('photo', 'image/*', 'hidden') ?>     
                    <img id="preview" src="../../photos/<?= $photo ?>">               
                </label>
                <?= err('photo') ?><br>
    
                <section>
                    <button data-confirm="Are you sure you wan to update this profile?">Submit</button>
                    <button type="reset">Reset</button>
                </section>
            </form>
</div>
</main>

<script>
document.getElementById('photo').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function (event) {
            document.getElementById('preview').src = event.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>

