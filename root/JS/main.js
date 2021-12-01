

// Declaring Some Global Varibales //
let relatedPersons = {};
let selectedProfile = undefined;


// Function to make a AJAX request to the Server //
function makeAJAXRequest(requesType, serverUrl, data, onLoadFunction){

    // Creating the XHR Object //
    let xhrObject = new XMLHttpRequest();
    xhrObject.open(requesType, serverUrl);
    xhrObject.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    // After getting the Response from the Server this Function will be executed //
    xhrObject.onload = onLoadFunction;

    // Making the Request //
    xhrObject.send("request="+JSON.stringify(data));
}



// ------------------------------- Add Person ------------------------------- // 

// Fucntion to make a AJAX request to the server and Will add a new Person in the Database //
function addPersonInTheDatabase(e){

    e.preventDefault();
    
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
    
    let onLoadFunction = function(){
        
        if( this.status != 200 ){
            alert("Something Went Wrong!");
        }
        else{
            let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");           
            if( responseText.includes("Failed") || responseText.includes("Success") ){
                let response = JSON.parse(responseText);
                if(response.result.includes("Failed")){
                    alert(response.message);
                }
                else{
                    alert(response.message);
                    document.getElementById("addstudentform").reset();
                }
            }
            else{
                alert(responseText);
            }
        }
    };
    
    // Making the Request to the Server //
    makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", personData, onLoadFunction);
}

// Binding the Function addPersonInTheDatabase to the button submitAddPersonForm //
document.getElementById("submitAddPersonForm").addEventListener("click", addPersonInTheDatabase);




// ------------------------------- Search Person ------------------------------- // 


// Function that will make and return a card conatining Searched Person Information //
function getPersonCard(personDetails, key){


    // Creating Tags //
    let suggestedPerson = document.createElement("div");
    let suggestedPersonProfile = document.createElement("div");
    let profile = document.createElement("img");
    let suggestedPersonDetails = document.createElement("div");
    let suggestedPersonID = document.createElement("span");
    let suggestedPersonName = document.createElement("span");
    let suggestedPersonDept = document.createElement("span");

    // Adding Classes //
    suggestedPerson.classList.add("suggestedPerson");
    suggestedPersonProfile.classList.add("suggestedPersonProfile");
    suggestedPersonDetails.classList.add("suggestedPersonDetails");
    suggestedPersonDetails.classList.add("suggestedPersonDetails");
    suggestedPersonID.classList.add("suggestedPersonID");
    suggestedPersonName.classList.add("suggestedPersonName");
    suggestedPersonDept.classList.add("suggestedPersonDept");


    // Adding content to the tags //
    profile.src = personDetails['profilePath'];
    suggestedPersonID.innerHTML = personDetails['userId'];
    suggestedPersonName.innerHTML = personDetails['name'];
    suggestedPersonDept.innerHTML = personDetails['designation'] + " ( Department )";

    console.log(personDetails['profilePath']);

    // Wrapping the tags of the Person's Card //
    suggestedPersonProfile.appendChild(profile);
    suggestedPersonDetails.appendChild(suggestedPersonID);
    suggestedPersonDetails.appendChild(suggestedPersonName);
    suggestedPersonDetails.appendChild(suggestedPersonDept);
    suggestedPerson.appendChild(suggestedPersonProfile);
    suggestedPerson.appendChild(suggestedPersonDetails);
    
    suggestedPerson.id = key;
    suggestedPerson.onclick = showClickedCarProfile;
    return suggestedPerson;
}


// Function to search a person in the database //
function searchPersonInTheDatabase(e){
   
    e.preventDefault();
    
    // Creating Some Variables //
    let personData = {
        "task" : "Search Person", 
        "instituteId" : document.getElementById("userId").textContent, 
        "sessionId" : document.getElementById("sessionId").textContent,
        "searchKey" : document.getElementById("searchKey").value,
    };

    let onLoadFunction = function(){
        
        if( this.status != 200 ){
            alert("Something Went Wrong!");
        }
        else{
            let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");

            if( responseText.includes("Success") ){
                let response = JSON.parse(responseText);
                if(response.result.includes("Failed")){
                    alert(response.message);
                }
                else{
                    let totalPersons = 0;
                    let searchResult = document.getElementById("searchResults");
                    searchResult.innerHTML = "";
                    searchResult.style.display = "block";
                    relatedPersons = response.relatedPersons;

                    for(let key in response.relatedPersons){
                       
                        // Make a Person Card //
                        searchResult.appendChild(getPersonCard(response.relatedPersons[key], key));
                        totalPersons+=1;
                    }
                    
                    // If No One Found //
                    if( totalPersons <= 0 ){
                        let notFound = document.createElement("div");
                        notFound.classList.add("notFound");
                        notFound.innerText = "Couldn't find anything ! (Try again using relevant keywords.)";
                        notFound.style.color = "red";
                        searchResult.appendChild(notFound);
                    }
                }
            }
            else{
                alert(responseText);
            }
            console.log(this.responseText);
        }
    };

    // Making the Request to the Server //
    makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", personData, onLoadFunction);
}


// Function to be executed When a Person's Card is Clicked //
function showClickedCarProfile(){
    console.log(relatedPersons[this.id]);
    selectedProfile =  this.id;
}


// Binding the Function searchPersonInTheDatabase to the button searchPerson //
document.getElementById("searchPerson").addEventListener("click", searchPersonInTheDatabase);




// ------------------------------- Update Person Details ------------------------------- // 


// Function to check whether the Details of a Person is Modified or not //
function areTheDetailsModified(){

    return true;
}



// Function to upadte the details of a person in the Institute Database //
function updatePersonDetails(e){
    
    e.preventDefault();
    
    if( areTheDetailsModified() ){

        // Creating Some Variables //
        let designation = document.getElementById("update-designation")
        let gender = document.getElementById("update-gender")

        let personData = {
            "task" : "Update Person Details", 
            "instituteId" : document.getElementById("userId").textContent, 
            "sessionId" : document.getElementById("sessionId").textContent,
            "userId" : relatedPersons[selectedProfile].userId,
            "authority" : relatedPersons[selectedProfile].authority,
            "name" : document.getElementById("update-name").value,
            "gender" : gender.options[gender.selectedIndex].value,
            "designation" : designation.options[designation.selectedIndex].value,
            "phoneNo" : document.getElementById("update-phoneNo").value,
            "adharCardNo" : document.getElementById("update-adharCardNo").value,
            "address" : document.getElementById("update-address").value,
            "state" : document.getElementById("update-state").value,
            "city" : document.getElementById("update-city").value,
            "pinCode" : document.getElementById("update-pinCode").value,
        };

        let onLoadFunction = function(){
            
            if( this.status != 200 ){
                alert("Something Went Wrong!");
            }
            else{
                let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
                if( responseText.includes("Success") ){
                    alert("Person Details Upadted Successfully !!!");
                }
                else{
                    alert(responseText);
                }
            }
        };

        // Making the Request to the Server //
         makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", personData, onLoadFunction);
    }
    else{
        alert("Nothing to be Updated !!!");
    }
}



// Binding the Function searchPersonInTheDatabase to the button searchPerson //
// document.getElementById("updatePersonDetails").addEventListener("click", updatePersonDetails);




// ------------------------------- Updating Classes of the Institute ------------------------------- // 



// Binding the Function searchPersonInTheDatabase to the button searchPerson //
// document.getElementById("addClass-save").addEventListener("click", addClass);