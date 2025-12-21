<?php

require_once '../connect.php';
require_once '../_base.php';
require_once '../admin/layout.php';

auth('Admin');

if (is_get()) {
    $stm = $_db->prepare('SELECT * FROM user WHERE user_id = 1');
    $stm->execute();
    $u = $stm->fetch();

    if (!$u) {
        redirect('/');
    }

    extract((array)$u);
    $_SESSION['photo'] = $u->photo;
}

if (is_post()) { 
    $username   = 'admin';
    $gender = req('gender');
    $age = req('age');
    $email = req('email'); 
    $photo = $_SESSION['photo'];
    $f = get_file('photo');
    $password   = req('password');

    //username validation
    if ($username !== 'admin'){
        $_err['username'] = 'Please enter your username';
    }

    //gender validation
    if ($gender == '') {
        $_err['gender'] = 'Required';
    }

    //age validation
    if($age == ''){
        $_err['age'] = 'Required';
    }

    // email query
    $stm = $_db->prepare('
            SELECT COUNT(*) FROM user
            WHERE email = ? AND user_id = 1
        ');
        $stm->execute([$email]);

    //email validation
    if ($stm->fetchColumn() == 0) {
        if($email == ''){
            $_err['email'] = 'Please enter your email';
        }else if(!is_email($email)){
            $_err['email'] = 'Invalid email address. Please use a Gmail address.';
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
                unlink("../admin/photo/$photo");
        
            $photo = save_photo($f, '../admin/photo');
            
        }
        
        // (2) Update user (email, name, photo)
        // TODO
        $stm = $_db->prepare('
            UPDATE user
            SET gender = ?, age = ?, email = ?, photo = ?
            WHERE user_id = 1
        ');

        
        $stm->execute([$gender, $age, $email, $photo]);

        temp('info', 'Record updated');
        redirect('/admin/admin.php');
    }

}

?>
<link rel="stylesheet" href="../css/admin.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="../../javascript/app.js"></script>


<main>
<div class="profile">
    <form method="post" class="form" enctype="multipart/form-data">
        <label for="username">Username:</label>
        <?= html_text_disabled('username') ?>
        <?= err('username') ?><br>
        
        <label>Gender:</label>
        <?= html_radios('gender', $_genders) ?>
        <?= err('gender') ?>
        
        <label for="age">Age:</label>
        <?= html_text('age') ?>
        <?= err('age') ?><br>
        
        <label for="email">Email:</label>
        <?= html_text('email') ?>
        <?= err('email') ?><br>
        
        <label for="photo">Photo:</label>
        <label class="upload" tabindex="0">
            <?= html_file('photo', 'image/*', 'hidden') ?>     
            <img id="preview" src="/admin/photo/<?= $photo ?>">               
        </label>
        <?= err('photo') ?><br>

        <section>
            <button method="POST" >Submit</button>
            <button type="reset">Reset</button>
            <button class="reset_password"><a href="/reset.php" style="text-decoration: none;">Reset Password</a></button>
            <button class="logout" ><a href="../../page/user/logout.php" style="text-decoration: none;" data-confirm="Are you sure you want to Log out?">Log Out</a></button>
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