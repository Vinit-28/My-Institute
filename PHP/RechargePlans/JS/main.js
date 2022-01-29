
// Declaring some Global Variables //
let scrollAllow = false;
let rechargePlans = {};




// --------------------------- Make Payment Section --------------------------- //

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




// Make Payment for Recharge Plans //
function buyNow(planId){

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
            "amount": (rechargePlans[planId]['planAmount']*100), 
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
                        "planId" : rechargePlans[planId]['planId'],
                        "planAmount" : rechargePlans[planId]['planAmount'],
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



// Function to get the recharge plans from the database //
function getRechargePlans(){

    // Craeting Some Request & Handler Variables //
    let data = {
        "task" : "Get Recharge Plans", 
    };

    let onLoadFunction = function(){
        
        if( this.status != 200 ){
            alert("Something Went Wrong !!!");
        }
        else{
            
            let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
            
            if( responseText.includes("Success") ){
                let response = JSON.parse(responseText);
                rechargePlans = response['rechargePlans'];
            }
            else{
                alert(responseText);
            }
        }
    }

    // Making the AJAX Request //
    makeAJAXRequest("POST", "../../../Server/server.php", data, onLoadFunction, false);
}


// Function to make and return a plan Card //
function getRechargePlanCard(planDetails){

    // Getting the list of Plan Description //
    let planDescription = planDetails['planDescription'].split("\r\n");
    
    // Creating Tags for a Plan Card //
    let plan = document.createElement("div");
    let planSlot1 = document.createElement("div");
    let planName = document.createElement("div");
    let planPrice = document.createElement("div");
    let currency = document.createElement("span");
    let monthly = document.createElement("span");
    let planSlot2 = document.createElement("div");
    let planTag = document.createElement("div");
    let planPoints = document.createElement("ul");
    let planButton = document.createElement("div");
    let price = document.createElement("p");


    // Adding Classes to the Tags //
    plan.classList.add("plan");
    planSlot1.classList.add("planSlot1");
    planName.classList.add("planName");
    planPrice.classList.add("planPrice");
    currency.classList.add("currency");
    monthly.classList.add("monthly");
    planSlot2.classList.add("planSlot2");
    planTag.classList.add("planTag");
    planPoints.classList.add("planPoints");
    planButton.classList.add("planButton");

    
    // Assigning values to their attributes //
    planName.innerText = planDetails['planName'];
    currency.innerText = "Rs";
    price.innerText = planDetails['planAmount'];
    monthly.innerText = "/month";
    planTag.innerText = planDetails['planTagline'];
    planButton.innerText = "Buy Plan";
    planButton.addEventListener("click", ()=>{buyNow(planDetails['planId']);});

    // Creating Description Lines for the plan //
    for(let i=0; i<planDescription.length; i++){

        // Creating Tags //
        let liTag = document.createElement("li");
        let iTag = document.createElement("i");
        
        // Adding Classes and wrapping up the tags //
        iTag.classList.add("bx");
        iTag.classList.add("bx-check");
        liTag.appendChild(iTag);
        liTag.innerText = planDescription[i];
        planPoints.appendChild(liTag);
    }


    // Wrapping up the Tags //
    planPrice.appendChild(currency);
    planPrice.appendChild(price);
    planPrice.appendChild(monthly);

    planSlot1.appendChild(planName);
    planSlot1.appendChild(planPrice);

    planSlot2.appendChild(planTag);
    planSlot2.appendChild(planPoints);

    plan.appendChild(planSlot1);
    plan.appendChild(planSlot2);
    plan.appendChild(planButton);

    return plan;
}


// Function to show the recharge plans // 
function showRechargePlans(){

    // Getting recharge plans from the database //
    getRechargePlans();

    // Getting the planContainer Tag //
    let plansContainer = document.getElementById("plansContainer");
    plansContainer.innerHTML = "";

    // Iterating through the Plans //
    for(let key in rechargePlans){

        // Appending the recharge plans in the plansContainer //
        plansContainer.appendChild(getRechargePlanCard(rechargePlans[key]));
        plansContainer.appendChild(getRechargePlanCard(rechargePlans[key]));
        plansContainer.appendChild(getRechargePlanCard(rechargePlans[key]));
        plansContainer.appendChild(getRechargePlanCard(rechargePlans[key]));
        plansContainer.appendChild(getRechargePlanCard(rechargePlans[key]));
        plansContainer.appendChild(getRechargePlanCard(rechargePlans[key]));
        plansContainer.appendChild(getRechargePlanCard(rechargePlans[key]));
        plansContainer.appendChild(getRechargePlanCard(rechargePlans[key]));
        plansContainer.appendChild(getRechargePlanCard(rechargePlans[key]));
        plansContainer.appendChild(getRechargePlanCard(rechargePlans[key]));
        plansContainer.appendChild(getRechargePlanCard(rechargePlans[key]));
    }
}


// Calling the Function to show the recharge plans //
showRechargePlans();