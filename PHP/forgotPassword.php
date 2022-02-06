<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/reset.css">
    <link rel="stylesheet" href="../CSS/forget.css">
    <link rel="stylesheet" href="../CSS/newPassword.css">
    <title>Forgot Password</title>
</head>
<body id="body">
    
    <div id="container">

        <div class="heading">Enter User-ID</div>

        <div class="image">
            <img src="../Images/sad.jpg" alt="">
        </div>

        <div class="content">


            
            <div class="subHeading">Enter your Log-In ID</div>

            <form action="">

                <div class="input">
                    <input type="text" placeholder="Log-In ID here..." id="userId">
                </div>

                <button id="submitUserId">Next</button>

            </form>



        </div>

    </div>




    <div id="container2">

        <div class="heading">Enter OTP !</div>

        <div class="image">
            <img src="../Images/happy.jpg" alt="">
        </div>

        <div class="content">


            
            <div class="subHeading">Enter OTP sent to your E-mail</div>

            <form action="" >

                <div class="otpContainer" id="otpContainer">
                    <input required type="text" autocomplete="off" class="otp" >
                    <input required type="text" autocomplete="off" class="otp">
                    <input required type="text" autocomplete="off" class="otp">
                    <input required type="text" autocomplete="off" class="otp">
                    <input required type="text" autocomplete="off" class="otp">
                    <input required type="text" autocomplete="off" class="otp">
                </div>

                <button id="submitOTP">Submit</button>

            </form>



        </div>

    </div>

    <div id="container3">

        <div class="heading">Reset Password !</div>

        <div class="image">
            <img src="../Images/happy.jpg" alt="">
        </div>

        <div class="content">


            
            <div class="subHeading">Set up New Password</div>

            <form action="" >

                <div style="display:flex; row-gap:0.9rem; flex-direction: column; margin-top: 1rem; align-items: center;">
                    <div class="input">
                        <input id="newPassword" class="resetInput" type="password" placeholder="New Password">
                    </div>
                    <div class="input">
                        <input id="confirmPassword" class="resetInput" type="password" placeholder="Confirm Password">
                    </div>
                    
                </div>

                <button id="changePassword">Submit</button>

            </form>



        </div>

    </div>

</body>

</html>
<script src="../JS/jquery.js"></script>
<script src="../JS/forgotPassword.js"></script>

<script>

    $("#body").height($(window).height())
    $(window).resize(()=>{
        window.location.reload(true)
    })



    // Code to flip First Card of Enter User ID
    // $("#submitButton").click((e)=>{
    //     e.preventDefault();
    //     $("#container").addClass('Y')
    //     setTimeout(()=>{
    //         $("#container").css({"display" : "none"})
    //         $("#container2").css({"display" : "flex"})
    //         setTimeout(()=>{
    //             $("#container2").css({"transform" : "rotateY(0deg)"})
                
    //         },50)
    //     } , 350)
    // })
    
    
    
    // Code to flip Second Card of Enter OTP
    $("#btn").click((e)=>{
        e.preventDefault();
        $("#container2").css({"transform" : "rotateY(90deg)"})
        setTimeout(()=>{
            $("#container2").css({"display" : "none"})
            $("#container3").css({"display" : "flex"})
            setTimeout(()=>{
                $("#container3").css({"transform" : "rotateY(0deg)"})
                
            },50)
        } , 350)
    })

    

    // OTP Code
    $(".otp").on('input' , (e)=>{

        let value = e.target.value[(e.target.value).length-1]
        e.target.value = value
        
        $(e.target).next().focus();
        
    })



    // Here you will get the entered OTP by User !
    document.getElementById('btn').addEventListener('click' , (e)=>{
        e.preventDefault();
        let elements = $(".otpContainer").children();
        let otp = "";
        for(let index=0 ; index<elements.length ; index++)
        {
            otp += elements[index].value;
        }
        console.log(otp)
    })

</script>