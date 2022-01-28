<?php
    session_start();
    require "./UserAuthentication.php";

    // If the user is registered and his institute's plan is not expired then redirect to UserIndex.php //
    if( isUserAuthenticated() && $_SESSION['userPlanDetails']['isPlanExpired'] == "No" ){

        header('Location: ../../UserIndex.php');
    }
    else{
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/payment.css">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>

    <title>Document</title>
</head>
<body>

    <div id="paymentDiv">

        <div id="firstHeading">
            Pricing
        </div>

        <div id="firstSubHeading">
            Get you account for a Digital Institute !
        </div>


        <div id="wholeContainer">

            <div id="leftButton" class="arrow"><i class='bx bx-chevron-left'></i></div>

            <!-- Plans Container Start -->
            <div id="plansContainer">

                <!-- Plan Card Start -->
                <div class="plan">
                    <div class="planSlot1">
                        <div class="planName">Plan Name</div>
                        <div class="planPrice"><span class="currency">Rs</span><p>1000</p><span class="monthly">/month</span></div>
                    </div>
                    
                    <div class="planSlot2">
                        <div class="planTag">Plan TagLine</div>
                        <ul class="planPoints">
                            <li><i class='bx bx-check'></i>Live Classes</li>
                        </ul>
                    </div>

                    <div class="planButton" onclick="buyNow(planId);">Buy Plan</div>
                </div>
                <!-- Plan Card END -->

            </div>
            <!-- Plans Container END -->

            <div id="rightButton" class="arrow"><i class='bx bx-chevron-right'></i></div>

        </div>

    </div>

    <?php

        if( isUserAuthenticated() ){
            echo "<p id='userId' style='display: none;'>" . $_SESSION['userId'] . "</p>";
            echo "<p id='sessionId' style='display: none;'>" . $_SESSION['sessionId'] . "</p>";
            echo "<p id='instituteId' style='display: none;'>" . $_SESSION['instituteId'] . "</p>";
            echo "<p id='authority' style='display: none;'>" . $_SESSION['authority'] . "</p>";
            echo "<p id='authenticity' style='display: none;'>" . "Authenticated" . "</p>";
        }
        else{
            echo "<p id='authenticity' style='display: none;'>" . "Unauthenticated" . "</p>";
        }
    ?>
</body>
</html>


<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="../JS/jquery.js"></script>
<script src="../JS/main.js"></script>

<?php
    }
?>