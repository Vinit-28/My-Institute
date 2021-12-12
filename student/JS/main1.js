


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







// ---------------------------------- Update Personal Details ---------------------------------- //


// Function to get the Isntitute Details From the Database //
function getStudentDetails(){

    let studentData = {};

    let data = {
        "task" : "Get Student Data", 
        "loggedInUser" : document.getElementById("userId").textContent, 
        "instituteId" : document.getElementById("instituteId").textContent, 
        "userId" : document.getElementById("userId").textContent, 
        "sessionId" : document.getElementById("sessionId").textContent,
        "authority" : document.getElementById("authority").textContent,
    };

    let onLoadFunction = function(){
        if( this.status != 200 ){
            alert("Something Went Wrong!");
        }
        else{
            let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
            if( responseText.includes("Success") ){
                let response = JSON.parse(responseText);
                studentData = response.studentData[0];
            }
            else{
                alert(responseText);
            }
        }
    }

    // Making the AJAX Request //
    makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", data, onLoadFunction, false);
    return studentData;
}


// Function to fill up the Self Profile Section //
function fillUpPersonalDetails(){
    
    // Getting the Institute Data //
    let studentData = getStudentDetails();

    // Getting the tag Elements //
    let studentProfileDiv = document.getElementById("studentProfileDiv");
    let studentId = document.getElementById("personalPersonId");
    let studentName = document.getElementById("personalName");
    let studentEmail = document.getElementById("personalEmail");
    let studentPhoneNo = document.getElementById("personalPhoneNo");
    let studentAddress = document.getElementById("personalAddress");
    let studentCity = document.getElementById("personalCity");
    let studentState = document.getElementById("personalState");
    let studentPinCode = document.getElementById("personalPinCode");


    // Assigining Values to their attributes //
    studentProfileDiv.style.display = "block";
    studentId.value = studentData.userId;
    studentId.disabled = true;
    studentName.value = studentData.name;
    studentEmail.value = studentData.email;
    studentPhoneNo.value = studentData.phoneNo;
    studentAddress.value = studentData.address;
    studentCity.value = studentData.city;
    studentState.value = studentData.state;
    studentPinCode.value = studentData.pinCode;
}


// Function to update the Self Profile Details in the Database //
function updatePersonalDetails(e){

    e.preventDefault();

    // Creating Some Variables //
    let data = {
        "task" : "Update My Profile", 
        "loggedInUser" : document.getElementById("userId").textContent, 
        "instituteId" : document.getElementById("instituteId").textContent, 
        "authority" : document.getElementById("authority").textContent,
        "sessionId" : document.getElementById("sessionId").textContent,
        "updatedStudentName" : document.getElementById("personalName").value,
        "updatedStudentEmail" : document.getElementById("personalEmail").value,
        "updatedStudentPhoneNumber" : document.getElementById("personalPhoneNo").value,
        "updatedStudentAddress" : document.getElementById("personalAddress").value,
        "updatedStudentCity" : document.getElementById("personalCity").value,
        "updatedStudentState" : document.getElementById("personalState").value,
        "updatedStudentPinCode" : document.getElementById("personalPinCode").value,
    };

    
    let xhr = new XMLHttpRequest();
    let formData = new FormData();
    let profileImg = document.getElementById("studentProfileImg").src;  
    let Image = document.getElementById("newProfile");
    
    formData.append("request", JSON.stringify(data));   
    
    // If image is Selected || Profile Picture is updated //
    if( Image.files.length > 0 ){
        let profileImg = Image.files[0];      
        formData.append("profileImg", profileImg);
    }    
    

    xhr.timeout = 10000;
    xhr.open("POST", '../../Server/Utilities/InstituteSpecificUtilities.php'); 
    
    // Function to be executed When the request has made and got the response from the server //
    xhr.onload = function(){

        if( this.status != 200 ){
            alert("Something Went Wrong!");
        }
        else{
            let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
            if( responseText.includes("Success") ){
                let response = JSON.parse(responseText);
                alert(response.message);
                // Reloading the Page to see the Updated changes //
                window.location.reload();
            }
            else{
                alert(responseText);
            }
        }
    }

    xhr.send(formData);
}


// Binding the updatePersonalDetails button with its handler //
document.getElementById("updatePersonalDetails").addEventListener("click", updatePersonalDetails);

