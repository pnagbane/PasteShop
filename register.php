<?php
$pagetitle = "Create an Account";
require_once('assets/header.php');

// Variable Declaration
$imageError = $firstnameError = $middlenameError = $surnameError = $phoneError = $emailError = $passwordError = $confirmPasswordError = $genderError = $roleError = "";

// Capturing form data
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image = $_FILES['image'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Validating the firstname
    if(empty($firstname)) {
        $firstnameError = "Firstname is required";
    }

    // Validating the surname
    if(empty($surname)) {
        $surnameError = "Surname is required";
    }

    // Validating the email
    if(empty($email)) {
        $emailError = "E-Mail Address is required";
    } else {
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $query = "SELECT * FROM users WHERE email = ? LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows > 0) {
                $emailError = "$email already exist";
            }
        } else {
            $emailError = "Invalid Email Address";
        }
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
                    <img src="https://flowbite.com/docs/images/logo.svg" id="preview" alt="Upload Image" class="h-auto max-w-full  rounded-lg"/>
        
                    <figcaption class="mt-2 text-sm text-center text-red-500 dark:text-red-400"><?= $imageError ?></figcaption>
                </figure>

                <label for="image" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Upload Image</label>
                <input type="file" id="upload" name="image" class="block w-full text-sm font-medium text-gray-900 dark:text-gray-400 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" 
                accept="image/png, image/jpeg, image/jpg" 
                />
            </div>

            <!-- Firstname -->
            <div>
                <label for="firstname" class="block text-sm font-medium text-gray-700 mb-1">Firstname</label>
                <input type="text" name="firstname" class="w-full px-4 py-3 rounded-lg border border-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" />
                <span class="text-red-500"><?= $firstnameError ?></span>
            </div>

            <!-- Middlename -->
            <div>
                <label for="middlename" class="block text-sm font-medium text-gray-700 mb-1">Middlename</label>
                <input type="text" name="middlename" class="w-full px-4 py-3 rounded-lg border border-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" />
                <span class="text-red-500"><?= $middlenameError ?></span>
            </div>

            <!-- Surname -->
            <div>
                <label for="surname" class="block text-sm font-medium text-gray-700 mb-1">Surname</label>
                <input type="text" name="surname" class="w-full px-4 py-3 rounded-lg border border-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" />
                <span class="text-red-500"><?= $surnameError ?></span>
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" class="w-full px-4 py-3 rounded-lg border border-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" />
                <span class="text-red-500"><?= $emailError ?></span>
            </div>


            <!-- Phone Number -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="tel" name="phone" class="w-full px-4 py-3 rounded-lg border border-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" />
                <span class="text-red-500"><?= $phoneError ?></span>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" class="w-full px-4 py-3 rounded-lg border border-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" />
                <span class="text-red-500"><?= $passwordError ?></span>
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input type="password" name="confirm_password" class="w-full px-4 py-3 rounded-lg border border-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" />
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
        if(file['size'] <= maxsize) {
            if(file['type'] == 'image/png' || file['type'] == 'image/jpeg' || file['type'] == 'image/jpg') {
                preview.src = URL.createObjectURL(file);
                console.log(file);
            } else {
                alert(file['type'] + " not supported")
            }
        } else {
            alert("file size is too large");
        }


    });
</script>