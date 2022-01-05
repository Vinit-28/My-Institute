<?php

    session_start();

    if( isset($_SESSION["isUserLogedIn"]) && isset($_SESSION["userId"]) && isset($_SESSION["sessionId"]) && $_SESSION["userId"] != "" && $_SESSION["sessionId"]!="" ){

        header('Location: ./UserIndex.php');
    }
    else{
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/Login.css">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>

    <title>Document</title>
</head>

<body>

    <div id="mainContainer">

        <h1 id="heading1"><i class='bx bxs-graduation bx-tada'></i>&nbsp;My Institute !</h1>

        <div id="formContainer">
            <div style="position: relative; width:80%">
                <form action="" id="form">
                    <input class="formInput" type="text" placeholder="User Id" id="userId" autocomplete="off">
                    <br id="break1">
                    <input class="formInput" type="password" placeholder="Password" id="password">
                    <br>
                    <div id="btnContainer">
                        <button type="submit" id="login">Login</button>
                    </div>
                    <div id="anchorContainer">
                        <a id="forgetAnchor" href="">Forget Password ?</a>
                        <a id="askAnchor" href="./SignUp.php">Don't have account ? Sign Up !</a>
                    </div>
                </form>
            </div>

            <div id="formSvg">
                <img src="../Images/LoginImage.svg" alt="">
            </div>
        </div>
    </div>

    <script src="../JS/jquery.js"></script>

    <script>

        function authenticateUser(e){
           
            e.preventDefault();
            
            // Creating the XHR Object //
            let xhrObject = new XMLHttpRequest();
            xhrObject.open("POST", "../Server/server.php");
            xhrObject.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            
            // Creating Some Variables //
            let credentials = {
                "task" :"login", 
                "userId" : document.getElementById("userId").value, 
                "password" : document.getElementById("password").value
            };


            // After getting the Response from the Server this Function will be executed //
            xhrObject.onload = function(){
                
                if( this.status != 200 ){
                    alert("Something Went Wrong!");
                }
                else{
                    let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
                    
                    if(responseText.includes("Failed") || responseText.includes("Success")){
                        let response = JSON.parse(responseText);
                        if(response.result == "Failed"){
                            alert(response.message);
                        }
                        else{
                            window.location = "./UserIndex.php";
                        }
                    }
                    else{
                        alert("Something Went Wrong!");
                    }
                }
            };

            // Making the Request //
            xhrObject.send("request="+JSON.stringify(credentials));
        }
        
        document.getElementById('login').addEventListener('click', authenticateUser);

    </script>

    
    <script>
        $(document).ready(function(){

            $("body").height($(window).height() - ($(window).height()*10/100));

            $('input,textarea').focus(function () {
                $(this).data('placeholder', $(this).attr('placeholder'))
                    .attr('placeholder', '');
            }).blur(function () {
                $(this).attr('placeholder', $(this).data('placeholder'));
            });

        })
    </script>

</body>

</html>

<?php
    }
?>