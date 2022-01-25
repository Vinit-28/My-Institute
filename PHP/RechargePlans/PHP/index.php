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

        <div id="plansContainer">

            
            <div class="plan">
                <div class="planSlot1">
                    <div class="planName">Aman Plan</div>
                    <div class="planPrice">
                        <span class="dollar">$</span>
                        13.55
                        <span class="monthly">/month</span>
                    </div>
                </div>
                
                <div class="planSlot2">
                    <div class="planTag">
                        A Basic plan with 1 week Free !
                    </div>
                    <ul class="planPoints">
                        <li><i class='bx bx-check'></i>Live Classes</li>
                        <li><i class='bx bx-check'></i>Take Assignments</li>
                        <li><i class='bx bx-check'></i>Individual Account</li>
                        <li><i class='bx bx-check'></i>Conduct Tests</li>
                    </ul>
                </div>

                <div class="planButton" onclick="buyNow(2, 1000);">Buy Plan</div>
            </div>
            
            <div class="plan">
                <div class="planSlot1">
                    <div class="planName">Basic Plan</div>
                    <div class="planPrice">
                        <span class="dollar">$</span>
                        13.55
                        <span class="monthly">/month</span>
                    </div>
                </div>
                
                <div class="planSlot2">
                    <div class="planTag">
                        A Basic plan with 1 week Free !
                    </div>
                    <ul class="planPoints">
                        <li><i class='bx bx-check'></i>Live Classes</li>
                        <li><i class='bx bx-check'></i>Take Assignments</li>
                        <li><i class='bx bx-check'></i>Individual Account</li>
                        <li><i class='bx bx-check'></i>Conduct Tests</li>
                    </ul>
                </div>

                <div class="planButton">Buy Plan</div>
            </div>
            
            
        </div>
    </div>

    <p id="userId" style="display: none;"><?php echo $_SESSION['userId']; ?></p>
    <p id="sessionId" style="display: none;"><?php echo $_SESSION['sessionId']; ?></p>
    <p id="instituteId" style="display: none;"><?php echo $_SESSION['instituteId']; ?></p>
    <p id="authority" style="display: none;"><?php echo $_SESSION['authority']; ?></p>

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