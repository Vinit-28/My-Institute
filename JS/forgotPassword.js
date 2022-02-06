


// Function to make a AJAX request to the Server //
function makeAJAXRequest(requesType, serverUrl, data, onLoadFunction, async=true){

    // Creating the XHR Object //
    let xhrObject = new XMLHttpRequest();
    xhrObject.open(requesType, serverUrl, async);
    xhrObject.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    // After getting the Response from the Server this Function will be executed //
    xhrObject.onload = onLoadFunction;

    // Making the Request //
    xhrObject.send("request="+JSON.stringify(data));
}


// Function to switch from Submit-OTP to Reset-Password Form //
function switchToSubmitResetPassword(){
    
    let container2 = document.getElementById("container2");
    let container3 = document.getElementById("container3");

    // Code to flip Second Card of Enter OTP
    container2.style.transform = "rotateY(90deg)";
    setTimeout( ()=>{
        container2.style.display = "none";
        container3.style.display = "flex";
        setTimeout( ()=>{
            container3.style.transform = "rotateY(0deg)";
        }, 50);
    }, 350);
}


// Function to get the New password from the User and Request for password Updation in the Server //
function getAndSubmitNewPassword(userId, postVerifyingOTPID){

    // Switching to Password-Reset Form //
    switchToSubmitResetPassword();

    // Getting required tag elements //
    let changePassword = document.getElementById("changePassword");
    let newPassword = document.getElementById("newPassword");
    let confirmPassword = document.getElementById("confirmPassword");


    // Binding the button with its handler //
    changePassword.addEventListener("click", (e)=>{

        e.preventDefault();

        // If new password and confirm password are not same //
        if( newPassword.value != confirmPassword.value ){
            alert("New Password and Confirm New Password Should be Same !!!");
            return;
        }
        
        // Creating some request variables and response handler //
        let data = {
            "task" : "Change Password",
            "userId" : userId,
            "newPassword" : newPassword.value,
            "postVerifyingOTPID" : postVerifyingOTPID,
        };
        
        let onLoadFunction = function(){
            
            if( this.status != 200 ){
                alert("Something Went Wrong !!!");
            }
            else{
                
                let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
                if( responseText.includes("Success") ){
                    let response = JSON.parse(responseText);
                    // Displaying the Request Response Message // 
                    alert(response.message);
                    window.close();
                }
                else{
                    alert(responseText);
                }
            }
        }
        // Making the AJAX Request //
        makeAJAXRequest("POST", "../../../Server/server.php", data, onLoadFunction, false);
    });
}


// Function to send the request for OTP verification to the Server //
function verifyOTP(userId, OTP){

    // Creating some request variables and response handler //
    let data = {
        "task" : "Verify OTP",
        "userId" : userId,
        "OTP" : OTP
    };

    let onLoadFunction = function(){
        
        if( this.status != 200 ){
            alert("Something Went Wrong !!!");
        }
        else{
            
            let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
            if( responseText.includes("Success") ){
                let response = JSON.parse(responseText);
                
                // Displaying the Request Response Message // 
                alert(response.message);

                // If OTP is not verified //
                if( response.isOTPVerified == "false" ){
                    window.close();
                }
                // If OTP is verified //
                else{
                    // Calling the function to get the new password from the user and send it to the Server //
                    getAndSubmitNewPassword(userId, response.postVerifyingOTPID);
                }
            }
            else{
                alert(responseText);
            }
        }
    }
    // Making the AJAX Request //
    makeAJAXRequest("POST", "../../../Server/server.php", data, onLoadFunction, false);
}


// Function to flip the Get-UserId-Card to Get-OTP-Card // 
function switchToSubmitOTPForm(){

    let container = document.getElementById("container");
    let container2 = document.getElementById("container2");
    container.classList.add('Y');

    // Code to flip First Card of Enter User ID
    setTimeout( ()=>{
        container.style.display = "none";
        container2.style.display = "flex";
        setTimeout( ()=>{
            container2.style.transform = "rotateY(0deg)";
        }, 50);

    }, 350);
}


// Function to get the value of OTP entered by the user //
function getValueOfOTP(){

    let elements = document.getElementById("otpContainer").children;
    let otp = "";
    for(let index=0 ; index<elements.length ; index++){
        otp += elements[index].value;
    }

    return otp;
}


// Function to get the OTP from the User and submit it to the Server //
function getAndSubmitOTP(userId){

    // Switching to the OTP Form //
    switchToSubmitOTPForm();
    let submitOTP = document.getElementById("submitOTP");

    // Binfing the button with its handler //
    submitOTP.addEventListener("click", (e)=>{
        e.preventDefault();

        // Calling the function to verify the OTP from the Server //
        verifyOTP(userId, getValueOfOTP());
    });
}


// Function to submit the userId to the server and request for OTP for password reset //
function submitUserId(e){

    e.preventDefault();
    userId = document.getElementById("userId").value;

    // If userId is empty(Invalid) //
    if( userId == "" ){
        alert("Please Enter Your UserId !!!");
        return;
    }

    // Creating some request variables and resposne handler //
    let data = {
        "task" : "Password Reset Request", 
        "userId" : userId
    };

    let onLoadFunction = function(){
        
        if( this.status != 200 ){
            alert("Something Went Wrong !!!");
        }
        else{
            
            let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
            if( responseText.includes("Success") ){
                let response = JSON.parse(responseText);
                // Displaying the Request Response Message //
                alert(response.message);

                // If the password reset request is failed to be created //
                if( response.isRequestMade == "false" ){
                    window.close();
                }
                // If the password reset request is successfully created //
                else{
                    console.log("OTP => " + response.OTP);
                    getAndSubmitOTP(userId);
                }
            }
            else{
                alert(responseText);
            }
        }
    }

    // Making the AJAX Request //
    makeAJAXRequest("POST", "../../../Server/server.php", data, onLoadFunction, false);
}


// Binding the Button to its respective handler //
document.getElementById("submitUserId").addEventListener("click", submitUserId)