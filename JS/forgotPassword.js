


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


// Function to get the New password from the User and Request for password Updation in the Server //
function getAndSubmitNewPassword(userId, postVerifyingOTPID){

    // Getting and creating tag elements //
    let passwordReset = document.getElementById("passwordReset");
    let newPassword = document.createElement("input");
    let confirmPassword = document.createElement("input");
    let changePassword = document.createElement("button");

    // Assining values to tags attributes //
    passwordReset.innerHTML = "";
    newPassword.placeholder = "Enter New Password";
    confirmPassword.placeholder = "Confirm New Password";
    newPassword.type = confirmPassword.type = "password";
    changePassword.innerText = "Change Password";

    // Wrapping up the tags //
    passwordReset.appendChild(newPassword);
    passwordReset.appendChild(confirmPassword);
    passwordReset.appendChild(changePassword);


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
                    console.log(response);
                    console.log(response.postVerifyingOTPID);

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


// Function to get the OTP from the User and submit it to the Server //
function getAndSubmitOTP(userId){

    // Getting and creating tags //
    let passwordReset = document.getElementById("passwordReset");
    let h3 = document.createElement("h3");
    let OTP = document.createElement("input");
    let next = document.createElement("button");

    // Assining values to tags attributes //
    passwordReset.innerHTML = "";
    h3.innerText = "An OTP has sent to the E-Mail linked to the account " + userId
    OTP.type = "text";
    OTP.placeholder = "Enter OTP";
    next.innerText = "Verify OTP";

    // Wrapping up the tags //
    passwordReset.appendChild(h3);
    passwordReset.appendChild(OTP);
    passwordReset.appendChild(next);

    // Binfing the button with its handler //
    next.addEventListener("click", (e)=>{
        e.preventDefault();

        // Calling teh function to verify the OTP from the Server //
        verifyOTP(userId, OTP.value);
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
                    console.log(response);
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
document.getElementById("next").addEventListener("click", submitUserId)