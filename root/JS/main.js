

// Fucntion to make a AJAX request to the server and Will add a new Person in the Database //
function addPersonInTheDatabase(e){

    e.preventDefault();

    // Creating the XHR Object //
    let xhrObject = new XMLHttpRequest();
    xhrObject.open("POST", "../../Server/Utilities/InstituteSpecificUtilities.php");
    xhrObject.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    // Creating Some Variables //
    let designation = document.getElementById("add-designation")
    let gender = document.getElementById("add-gender")

    let personData = {
        "task" : "Add Teacher", 
        "instituteId" : document.getElementById("userId").textContent, 
        "sessionId" : document.getElementById("sessionId").textContent,
        "userId" : document.getElementById("add-personId").value,
        "name" : document.getElementById("add-name").value,
        "password" : document.getElementById("add-password").value,
        "email" : document.getElementById("add-email").value,
        "gender" : gender.options[gender.selectedIndex].value,
        "designation" : designation.options[designation.selectedIndex].value,
        "phoneNo" : document.getElementById("add-phoneNo").value,
        "adharCardNo" : document.getElementById("add-adharCardNo").value,
        "address" : document.getElementById("add-address").value,
        "state" : document.getElementById("add-state").value,
        "city" : document.getElementById("add-city").value,
        "pinCode" : document.getElementById("add-pinCode").value,
    };

    // After getting the Response from the Server this Function will be executed //
    xhrObject.onload = function(){
        
        if( this.status != 200 ){
            alert("Something Went Wrong!");
        }
        else{
            this.responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");

            if(this.responseText.includes("SOME INTERNAL ERROR !!!")){
                alert(this.responseText);
            }
            else{
                let response = JSON.parse(this.responseText);
                if(response.result.includes("Failed")){
                    alert(response.message);
                }
                else{
                    alert(response.message);
                    document.getElementById("addstudentform").reset();
                }
            }
        }
    };

    // Making the Request //
    xhrObject.send("request="+JSON.stringify(personData));
}


// Binding the Function addPersonInTheDatabase to the button submitAddPersonForm //
document.getElementById("submitAddPersonForm").addEventListener("click", addPersonInTheDatabase);