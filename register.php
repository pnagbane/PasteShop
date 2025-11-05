<?php
$pagetitle = "Create an Account";
require_once('assets/header.php');
require_once('assets/mailer.php');

// Variable Declaration
$imageError = $firstnameError = $middlenameError = $surnameError = $phoneError = $emailError = $passwordError = $confirmPasswordError = $genderError = $roleError = "";

// 
$firstname = $middlename = $surname = $email = $password = $confirmPassword = $phone = "";

// Capturing form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image = $_FILES['image'];
    $firstname = trim(htmlspecialchars($_POST['firstname']));
    $middlename = htmlspecialchars($_POST['middlename']);
    $surname = trim(htmlspecialchars($_POST['surname']));
    $email = htmlspecialchars($_POST['email']);
    $gender = htmlspecialchars($_POST['gender']);
    $phone = htmlspecialchars($_POST['phone']);
    $password = htmlspecialchars($_POST['password']);
    $confirmPassword = htmlspecialchars($_POST['confirm_password']);
    $role = htmlspecialchars($_POST['role']);

    // Validating the firstname
    if (empty($firstname)) {
        $firstnameError = "Firstname is required";
    }

    // Validating the surname
    if (empty($surname)) {
        $surnameError = "Surname is required";
    }

    // Validating the email
    if (empty($email)) {
        $emailError = "E-Mail Address is required";
    } else {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $query = "SELECT * FROM users WHERE email = ? LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $emailError = "$email already exist";
            }
        } else {
            $emailError = "Invalid Email Address";
        }
    }

    // Validating the phone number 
    if (empty($phone)) {
        $phoneError = "Phone number is required";
    } else {
        if (preg_match('/^(0|\+234)[789][01]\d{8}$/', $phone)) {
            $query = "SELECT * FROM users where phone = ? LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('s', $phone);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $phoneError = "$phone already exist";
            }
        } else {
            $phoneError = "Invalid phone number";
        }
    }

    // Password Validation
    if ($password != $confirmPassword) {
        $passwordError = $confirmPasswordError = "Password don't match";
    } else {
        if (preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
        } else {
            $passwordError = $confirmPasswordError = "Invalid Password Format";
        }
    }

    // Validate Image
    if ($image['error'] == 0) {
        if ($image['type'] == 'image/jpeg' || $image['type'] == 'image/jpg' || $image['type'] == 'image/png') {
            $fileSize = 3 * 1024 * 1024;
            if ($image['size'] <= $fileSize) {
                // $storage = 'profilepictures/';
                $filename = uniqid($firstname . $surname . "_") . "." . pathinfo($image['name'], PATHINFO_EXTENSION);
                $fileLocation = 'profilepictures/' . $filename;
            } else {
                $imageError = "Image size is too large";
            }
        } else {
            $imageError = $image['type'] . " not supported";
        }
    } else {
        $imageError = "File is corrupted";
    }

    if ($firstnameError == "" && $surnameError == "" && $emailError == "" && $phoneError == "" && $passwordError == "" && $confirmPasswordError == "" && $imageError == "") {
        $verification_code = bin2hex(random_bytes(32));
        $link = "http://localhost/pasteshop/verify.php?code=$verification_code";
        $name = $firstname . " " . $surname;
        $mail->setFrom('pasteshop@roncloud.com.ng', 'PasteShop');
        $mail->addAddress($email, $name);
        $mail->isHTML(true);
        $mail->Subject = 'Account Verification';
        $mail->Body = "<h1>Hello $name</h1> <p>Below is a link to verify your account with us at PasteShop Limited</p> <p>Please click <a href='$link'>here</a> to verify</p> <p>$link</p> <h3>Thank you</h3> ";
        if ($mail->send()) {
            $query = "INSERT INTO users (`firstname`, `middlename`, `surname`, `email`, `password`, `phone`, `gender`, `profile_picture`, `user_role`, `verification_code`) VALUES (?,?,?,?,?,?,?,?,?,?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ssssssssss', $firstname, $middlename, $surname, $email, $hash, $phone, $gender, $fileLocation, $role, $verification_code);
            $stmt->execute();
            move_uploaded_file($image['tmp_name'], $fileLocation);
            echo "<h1>Registered Successfully";
        } else {
            echo "<h1>Registration Failed";
        }
    } else {
        echo "<h1>Registration Failed</h1>";
    }

    // echo $firstname; 
    // var_dump($image);
}

?>


<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
    <form action="" class="w-full max-w-md bg-white/70 backdrop-blur-lg rounded-2xl shadow-xl p-8 border border-white/20" method="post" enctype="multipart/form-data">
        <div class="text-center space-y-2">
            <h2 class="text-3xl font-extrabold text-gray-900">Register Now</h2>
            <p class="text-gray-500 text-sm">Create new account</p>
        </div>

        <div class="space-y-4">
            <!-- Profile Picture Upload -->

            <div>
                <figure class="max-w-lg">
                    <img src="https://flowbite.com/docs/images/logo.svg" id="preview" alt="Upload Image" class="h-auto max-w-full  rounded-lg" />

                    <figcaption class="mt-2 text-sm text-center text-red-500 dark:text-red-400"><?= $imageError ?></figcaption>
                </figure>

                <label for="image" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Upload Image</label>
                <input type="file" id="upload" name="image" class="block w-full text-sm font-medium text-gray-900 dark:text-gray-400 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                    accept="image/png, image/jpeg, image/jpg" />
            </div>

            <!-- Firstname -->
            <div>
                <label for="firstname" class="block text-sm font-medium text-gray-700 mb-1">Firstname</label>
                <input type="text" name="firstname" class="w-full px-4 py-3 rounded-lg border border-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" value="<?= $firstname ?>" />
                <span class="text-red-500"><?= $firstnameError ?></span>
            </div>

            <!-- Middlename -->
            <div>
                <label for="middlename" class="block text-sm font-medium text-gray-700 mb-1">Middlename</label>
                <input type="text" name="middlename" class="w-full px-4 py-3 rounded-lg border border-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" value="<?= $middlename ?>" />
                <span class="text-red-500"><?= $middlenameError ?></span>
            </div>

            <!-- Surname -->
            <div>
                <label for="surname" class="block text-sm font-medium text-gray-700 mb-1">Surname</label>
                <input type="text" name="surname" class="w-full px-4 py-3 rounded-lg border border-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" value="<?= $surname ?>" />
                <span class="text-red-500"><?= $surnameError ?></span>
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" class="w-full px-4 py-3 rounded-lg border border-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" value="<?= $email ?>" />
                <span class="text-red-500"><?= $emailError ?></span>
            </div>


            <!-- Phone Number -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="tel" name="phone" class="w-full px-4 py-3 rounded-lg border border-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" value="<?= $phone ?>" />
                <span class="text-red-500"><?= $phoneError ?></span>
            </div>

            <!-- Password -->
            <div class="relative">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="pass1" class="w-full px-4 py-3 rounded-lg border border-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" value="<?= $password ?>" />
                <span class="h-5 w-5 absolute cursor-pointer" style="top: 40px; right:10px" id="check1"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                        <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                        <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" />
                    </svg>
                </span>
                <span class="text-red-500"><?= $passwordError ?></span>
            </div>

            <!-- Confirm Password -->
            <div class="relative">
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1 ">Confirm Password</label>
                <input type="password" name="confirm_password" id="pass2" class="w-full px-4 py-3 rounded-lg border border-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" value="<?= $confirmPassword ?>" />
                <span class="h-5 w-5 absolute cursor-pointer" style="top: 40px; right:10px" id="check2"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                        <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                        <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" />
                    </svg>
                </span>
                <span class="text-red-500"><?= $confirmPasswordError ?></span>
            </div>

            <!-- User Role -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">User Role</label>
                <select name="role" class="w-full px-4 py-3 rounded-lg border border-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200">
                    <option value="user">User</option>
                    <option value="vendor">Vendor</option>
                </select>
                <span class="text-red-500"><?= $roleError ?></span>
            </div>

            <!-- Gender -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                <select name="gender" class="w-full px-4 py-3 rounded-lg border border-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Others">Others</option>
                </select>
                <span class="text-red-500"><?= $genderError ?></span>
            </div>

            <!-- Submit -->
            <div>
                <input type="submit" value="Register" class="w-full bg-gradient-to-r from-blue-500 to-red-600 hover:from-blue-600 hover:to-red-700 text-white font-semibold py-3.5 rounded-lg duration-200 transform hover:scale-[1.01] shadow-md cursor-pointer" />
            </div>

        </div>
    </form>

</div>

<script>
    // Preview Image Upload
    const preview = document.querySelector('#preview');
    const upload = document.querySelector('#upload');

    upload.addEventListener('change', (e) => {
        file = e.target.files[0];
        maxsize = 3 * 1024 * 1024 // 3mb
        if (file['size'] <= maxsize) {
            if (file['type'] == 'image/png' || file['type'] == 'image/jpeg' || file['type'] == 'image/jpg') {
                preview.src = URL.createObjectURL(file);
                console.log(file);
            } else {
                alert(file['type'] + " not supported")
            }
        } else {
            alert("file size is too large");
        }
    });

    const check1 = document.querySelector('#check1');
    const pass1 = document.querySelector("#pass1");

    check1.addEventListener('click', (e) => {
        check1.classList.toggle('show');
        if (check1.classList.contains('show')) {
            pass1.type = 'text';
            check1.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6"><path d="M3.53 2.47a.75.75 0 0 0-1.06 1.06l18 18a.75.75 0 1 0 1.06-1.06l-18-18ZM22.676 12.553a11.249 11.249 0 0 1-2.631 4.31l-3.099-3.099a5.25 5.25 0 0 0-6.71-6.71L7.759 4.577a11.217 11.217 0 0 1 4.242-.827c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113Z" /><path d="M15.75 12c0 .18-.013.357-.037.53l-4.244-4.243A3.75 3.75 0 0 1 15.75 12ZM12.53 15.713l-4.243-4.244a3.75 3.75 0 0 0 4.244 4.243Z" /><path d="M6.75 12c0-.619.107-1.213.304-1.764l-3.1-3.1a11.25 11.25 0 0 0-2.63 4.31c-.12.362-.12.752 0 1.114 1.489 4.467 5.704 7.69 10.675 7.69 1.5 0 2.933-.294 4.242-.827l-2.477-2.477A5.25 5.25 0 0 1 6.75 12Z" /></svg>';
        } else {
            pass1.type = 'password';
            check1.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6"><path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" /><path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" /></svg>'
        }
    })

    const check2 = document.querySelector('#check2');
    const pass2 = document.querySelector("#pass2");

    check2.addEventListener('click', (e) => {
        check2.classList.toggle('show');
        if (check2.classList.contains('show')) {
            pass2.type = 'text';
            check2.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6"><path d="M3.53 2.47a.75.75 0 0 0-1.06 1.06l18 18a.75.75 0 1 0 1.06-1.06l-18-18ZM22.676 12.553a11.249 11.249 0 0 1-2.631 4.31l-3.099-3.099a5.25 5.25 0 0 0-6.71-6.71L7.759 4.577a11.217 11.217 0 0 1 4.242-.827c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113Z" /><path d="M15.75 12c0 .18-.013.357-.037.53l-4.244-4.243A3.75 3.75 0 0 1 15.75 12ZM12.53 15.713l-4.243-4.244a3.75 3.75 0 0 0 4.244 4.243Z" /><path d="M6.75 12c0-.619.107-1.213.304-1.764l-3.1-3.1a11.25 11.25 0 0 0-2.63 4.31c-.12.362-.12.752 0 1.114 1.489 4.467 5.704 7.69 10.675 7.69 1.5 0 2.933-.294 4.242-.827l-2.477-2.477A5.25 5.25 0 0 1 6.75 12Z" /></svg>';
        } else {
            pass2.type = 'password';
            check2.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6"><path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" /><path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" /></svg>'
        }
    })
</script>