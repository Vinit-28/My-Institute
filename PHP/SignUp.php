<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/SignUp.css">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <title>SignUp Page</title>
</head>

<body>

    <div id="mainContainer">

        <h1 id="heading1"><i class='bx bxs-graduation bx-tada'></i>&nbsp;Welcome To My Institute !</h1>
        <div id="formContainer">
            <div>
                <form action="" id="form">
                    <input class="formInput" id="instituteId" type="text" placeholder="Institute ID">
                    <input class="formInput" id="instituteName" type="text" placeholder="Institute Name">
                    <input class="formInput" id="instituteEmail" type="email" placeholder="Institute Email">
                    <input class="formInput" id="password" type="password" placeholder="Password">

                    <div id="btnContainer">
                        <button type="submit" id="signup">Sign-Up</button>
                        <span id="or">OR</span>
                        <a id="askAnchor" href="./Login.php">Already have an account ? Log In !</a>
                    </div>
                </form>
            </div>

            <div id="formSvg">
                <img src="../Images/SignUpImage.svg" alt="">
            </div>
        </div>


    </div>

</body>

</html>


<script src="../JS/jquery.js"></script>

<script>

    $(document).ready(function () {

        $("body").height($(window).height() - ($(window).height() * 5 / 100));

        $('input,textarea').focus(function () {
            $(this).data('placeholder', $(this).attr('placeholder'))
                .attr('placeholder', '');
        }).blur(function () {
            $(this).attr('placeholder', $(this).data('placeholder'));
        });
    });

</script>

<script>

    function signupUser(e){
        e.preventDefault();

        let instituteId = document.getElementById("instituteId").value;
        let instituteName = document.getElementById("instituteName").value;
        let instituteEmail = document.getElementById("instituteEmail").value;
        let password = document.getElementById("password").value;

        let credentials = {
            "task" :"signup", 
            "instituteId" : instituteId, 
            "instituteName" : instituteName, 
            "instituteEmail" : instituteEmail, 
            "password" : password
        };

        let xhrObject = new XMLHttpRequest();
        xhrObject.open("POST", "../Server/server.php");
        xhrObject.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhrObject.onload = function(){
                
            if( this.status != 200 ){
                alert("Something Went Wrong!");
            }
            else{
                let response = JSON.parse(this.responseText.replace(/(\r\n|\n|\r)/gm, ""));
                alert(response.message);
                
                if(response.result == "Success"){
                    window.location = "../index.php";
                }
            }
        };
        xhrObject.send("request="+JSON.stringify(credentials));
    }

    document.getElementById("signup").addEventListener("click", signupUser);

</script>