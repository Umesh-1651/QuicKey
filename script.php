<?php 

session_start();
include("connection.php");

$username = null;
$username_err = null;
$ok = null;
$pass = null;
$pass_err = null;

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    if(empty(trim($username))){
        $username_err = "Username cannot be empty";
    }
    if(empty(trim($password))){
        $pass_err = "Password cannot be empty";
    } else {
        $username_err = null;
        $pass_err = null;

        $username = $_POST['username'];
        $password = $_POST['password'];

        if(!empty($username) && !empty($password) && !is_numeric($username)){
            try {
                $query = "SELECT * FROM users WHERE uname = :username LIMIT 1";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

                if($user_data){
                    if(password_verify($password, $user_data['pass'])){
                        $_SESSION['uid'] = $user_data['uid'];
                        $updateQuery = "UPDATE users SET is_active = 1 WHERE uname = :uname";
                        $stmt = $pdo->prepare($updateQuery);
                        $stmt->bindParam(':uname', $username); // Bind the parameter
                        $stmt->execute();
                        header("Location: dashboard.php");
                        exit();
                    } else {
                        $pass_err = "Wrong Credentials, Please Enter Correct Details!";
                    }
                } else {
                    $pass_err = "No User in database, Please SignUp!";
                }
            } catch (PDOException $e) {
                $pass_err = "Database error occurred: " . $e->getMessage();
            }
        } else {
            $pass_err = "Wrong Credentials, Please Enter Correct Details!";
        }
    }
}

// session_start();
// include("connection.php");
//     $username=null;
//     $username_err=null;
//     $ok = null;
//     $pass=null;
//     $pass_err=null;

//     if(isset($_POST['login'])){
//         $username = $_POST['username'];
//         $password = $_POST['password'];
//         if(empty(trim(($username)))){
//             $username_err="Username cannot be empty";
//         }
//         if(empty(trim(($password)))){
//             $pass_err="Password cannot be empty";
//         }
//         else{
//             $username_err=null;
//             $pass_err=null;
//             $user_name = $_POST['username'];
//             $passwd = $_POST['password'];
//             if(!empty($user_name) && !empty($passwd) && !is_numeric($user_name)){
//                 $query = "select * from users where uname = '$user_name' limit 1";
//                 $result = mysqli_query($con,$query);
//                 if($result){
//                     if($result && mysqli_num_rows($result) >0){
//                         $user_data = mysqli_fetch_assoc($result);
//                         if(password_verify($passwd,$user_data['pass'])){
//                             $_SESSION['uid'] = $user_data['uid'];
//                             header("Location: dashboard.php");
//                             die;
                            
//                         }
//                         else{
//                             $pass_err = "Wrong Credentials, Please Enter Correct Details!";
//                         }
//                     }
//                     else{
                        
//                         $pass_err = "No User in database, Please SignIn!";
//                     }
//                 }
//                 else{
                    
//                     $pass_err = "Unable to Connect to Server!, Please try again.";
//                 }
//         }
//         else{
//             $pass_err = "Wrong Credentials, Please Enter Correct Details!";
//         }
//     }
// }
    // if($_SERVER['REQUEST_METHOD'] == "POST"){
    //         $user_name = $_POST['username'];
    //         $passwd = $_POST['password'];
    //         echo "step 0 passed";
    //         if(!empty($user_name) && !empty($passwd) && !is_numeric($user_name)){
                
    //             echo "Step 1 passed";
    //             $query = "select * from users where uname = '$user_name' limit 1";
    //             $result = mysqli_query($con,$query);
    //             echo "quey went";
    //             if($result){
    //                 echo "step 2 passed";
    //                 if($result && mysqli_num_rows($result) >0){
    //                     echo "step 3 passed";
    //                     $user_data = mysqli_fetch_assoc($result);
    //                     if($user_data['Passwd'] === $passwd){
    //                         $_SESSION['UserID'] = $user_data['UserID'];
    //                         header("Location: dashboard.html");
    //                         die;
    //                     }
    //                 }
    //             }
    //             echo "Wrong Username or Password!!!";
    //         }
    //         else{
    //             echo "Please Enter Valid Details!!!";
    //         }
    // }
?>
