


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



// Function to set the allignment //
function setAlignment()
{
    var number = $('#plansContainer').children().length;
    var width = $($('#plansContainer').children()[0]).width()
    var colgap = ($('#plansContainer').css("column-gap").split("px"))[0]
    var totalWidth = width * number + ((number+1)*colgap);
    var parentwidth = $('#plansContainer').width()


    if(totalWidth >= parentwidth)
    {
        $("#plansContainer").css({"justify-content" : "start"})
    }
}

$(window).resize( ()=>{
    console.log('yes')
    setAlignment()
})
setAlignment();




// Make Payment for Recharge Plans //
function buyNow(planId, planAmount){

    let authenticity = document.getElementById("authenticity").innerText;
    
    // Checking the user authenticity (Logged in or Not) //
    if( authenticity == "Authenticated" ){

        // If the user is not the root //
        if( document.getElementById("instituteId").innerText != document.getElementById("userId").innerText ){
            alert("You are not an Institute !!!");
            return;
        }
        
        // Creating Payment Creadentails and Success Handler //
        let paymentCredentials = {
            "key": "rzp_test_jx2TOpgplWSuNP", // My Razorpay Key Id //
            "amount": (planAmount*100), 
            "currency": "INR",
            "name": "My-Institute",
            "description": "Institute Recharge Transaction",
            "handler": function (response){
                
                // If transaction is Successfull //
                if( response.razorpay_payment_id != undefined ){
                    
                    // console.log(response.razorpay_payment_id);
                    // Creating some variables to update transaction and recharge details in the database // 
                    let data = {
                        "task" : "Update Institute Recharge Details", 
                        "loggedInUser" : document.getElementById("userId").textContent, 
                        "instituteId" : document.getElementById("instituteId").textContent,  
                        "sessionId" : document.getElementById("sessionId").textContent,
                        "authority" : document.getElementById("authority").textContent,
                        "paymentId" : response.razorpay_payment_id,
                        "planId" : planId,
                        "planAmount" : planAmount,
                    };
                
                    let onLoadFunction = function(){
                        
                        // If case of failure of Updation in the Database //
                        if( this.status != 200 ){
                            alert("Something Went Wrong !!!");
                            alert("In case the money is deducted from your please reach to our customer support !!!\nPlease Note your Transaction/Payment Id = " + response.razorpay_payment_id);
                        }
                        else{
                            
                            let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
                            
                            // If transaction and recharge updation is successfull //
                            if( responseText.includes("Success") ){
                                let response = JSON.parse(responseText);

                                // Prompting the Success Message //
                                let anything = confirm("Recharge was Successfull !!!\nPlan will expire on " + response.planExpiryDate);

                                // Redirecting to the Home Page //
                                if( anything == true || anything == false ){
                                    window.open("../../UserIndex.php", "_self");
                                }
                            }
                            // If case of any Exception Occurs while Updation in the Database //
                            else{
                                alert(responseText);
                                alert("In case the money is deducted from your please reach to our customer support !!!\nPlease Note your Transaction/Payment Id = " + response.razorpay_payment_id);
                            }
                        }
                    }
                
                    // Making the AJAX Request //
                    makeAJAXRequest("POST", "../../../Server/Utilities/InstituteSpecificUtilities.php", data, onLoadFunction, false);
                }
            }
        };

        // Creating the RazorPay object //
        let razorPay = new Razorpay(paymentCredentials);
        razorPay.open();
    }
    // If the user is not Logged in or not Authenticated then redirect him/her to the UserIndex Page(Login Page -- Indirectly)
    else{
        window.open("../../UserIndex.php", "_self");
    }
}