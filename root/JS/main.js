

// ------------------------------- Add Person ------------------------------- // 

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




// ------------------------------- Search Person ------------------------------- // 



function getPersonCard(personDetails){


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
    profile.src = "../IMAGES/profile.jpg";
    suggestedPersonID.innerHTML = personDetails['userId'];
    suggestedPersonName.innerHTML = personDetails['name'];
    suggestedPersonDept.innerHTML = "Department";

    // Wrapping the tags of the Person's Card //
    suggestedPersonProfile.appendChild(profile);
    suggestedPersonDetails.appendChild(suggestedPersonID);
    suggestedPersonDetails.appendChild(suggestedPersonName);
    suggestedPersonDetails.appendChild(suggestedPersonDept);
    
    suggestedPerson.appendChild(suggestedPersonProfile);
    suggestedPerson.appendChild(suggestedPersonDetails);
    
    return suggestedPerson;
}


function searchPersonInTheDatabase(e){
    e.preventDefault();
    // Creating the XHR Object //
    let xhrObject = new XMLHttpRequest();
    xhrObject.open("POST", "../../Server/Utilities/InstituteSpecificUtilities.php");
    xhrObject.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    // Creating Some Variables //
    let personData = {
        "task" : "Search Person", 
        "instituteId" : document.getElementById("userId").textContent, 
        "sessionId" : document.getElementById("sessionId").textContent,
        "searchKey" : document.getElementById("searchKey").value,
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
                    let totalPersons = 0;
                    let searchResult = document.getElementById("searchResults");
                    searchResult.innerHTML = "";
                    searchResult.style.display = "block";
                    for(let index in response.relatedPersons){
                        // Make a Person Card //
                        searchResult.appendChild(getPersonCard(response.relatedPersons[index]));
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
        }
    };

    // Making the Request //
    xhrObject.send("request="+JSON.stringify(personData));

}





// Binding the Function searchPersonInTheDatabase to the button searchPerson //
document.getElementById("searchPerson").addEventListener("click", searchPersonInTheDatabase);