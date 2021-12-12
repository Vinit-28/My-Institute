

// Declaring Some Global Varibales //
let relatedPersons = {};
let instituteClasses = {};


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


// Function to get the Classes from the Institute's Database //
function getClassesOfTheInstitute(){

    let data = {
        "task" : "Update Classes", 
        "subtask" : "Show Classes", 
        "loggedInUser" : document.getElementById("userId").textContent, 
        "instituteId" : document.getElementById("instituteId").textContent,  
        "sessionId" : document.getElementById("sessionId").textContent,
        "authority" : document.getElementById("authority").textContent,
    };

    let onLoadFunction = function(){

        if( this.status != 200 ){
            alert("Something Went Wrong!");
        }
        else{
            let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
            if( responseText.includes("Success") || responseText.includes("Failed") ){
                let response = JSON.parse(responseText);
                instituteClasses = response.classes;
            }
            else{
                alert(responseText);
            }
        }
    }

    // Making the AJAX Request //
    makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", data, onLoadFunction, false);
}


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
    suggestedPersonDept.innerHTML = personDetails['designation'];

    if( personDetails['designation'].toLowerCase() == "student" ){
        suggestedPersonDept.innerHTML += " ( " + personDetails['class'] +" )";
    }


    // Wrapping the tags of the Person's Card //
    suggestedPersonProfile.appendChild(profile);
    suggestedPersonDetails.appendChild(suggestedPersonID);
    suggestedPersonDetails.appendChild(suggestedPersonName);
    suggestedPersonDetails.appendChild(suggestedPersonDept);
    suggestedPerson.appendChild(suggestedPersonProfile);
    suggestedPerson.appendChild(suggestedPersonDetails);
    
    suggestedPerson.id = key;
    suggestedPerson.onclick = openModalForSelectedPerson;
    return suggestedPerson;
}


// Function to search a person in the database //
function searchPersonInTheDatabase(e){
   
    if( e!=undefined && e!=null ) e.preventDefault();
    
    // Creating Some Variables //
    let personData = {
        "task" : "Search Person", 
        "loggedInUser" : document.getElementById("userId").textContent, 
        "instituteId" : document.getElementById("instituteId").textContent, 
        "authority" : document.getElementById("authority").textContent,
        "sessionId" : document.getElementById("sessionId").textContent,
        "searchKey" : document.getElementById("searchKey").value,
    };

    let onLoadFunction = function(){
        
        if( this.status != 200 ){
            alert("Something Went Wrong!");
        }
        else{
            let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");

            if( responseText.includes("Success") || responseText.includes("Failed") ){
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
        }
    };

    // Making the Request to the Server //
    makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", personData, onLoadFunction);
}


// Binding the Function searchPersonInTheDatabase to the button searchPerson //
document.getElementById("searchPerson").addEventListener("click", searchPersonInTheDatabase);




// ------------------------------- Update Person Details ------------------------------- // 

// Function to return the index value of search key found in the Listitems //
function getIndexOfValue(listItems, find){
    for(let i=0;i<listItems.length;i++){
        if( find == listItems[i].value ) return i;   
    } 
    return 0;
}  


// Function to get the total fees of a Class //
function getTotalFees(className){
    for(let key in instituteClasses){
        if( instituteClasses[key].className == className ) return instituteClasses[key].fees;
    }
}


// Function to check whether the details of the selected person is Modified or not //
function isDataModified(updatedData, selectedPerson){

    if( selectedPerson.designation.toLowerCase() == "student" && updatedData.class != selectedPerson.class) return true;
    
    return (updatedData.name != selectedPerson.name || updatedData.phoneNo != selectedPerson.phoneNo || updatedData.address != selectedPerson.address || updatedData.city != selectedPerson.city || updatedData.pinCode != selectedPerson.pinCode || updatedData.state != selectedPerson.state || updatedData.adharCardNo != selectedPerson.adharCardNo || updatedData.gender != selectedPerson.gender || updatedData.designation != selectedPerson.designation);
}


// Function to close the modal //
function closeModal(){

    let modal = document.getElementById("selectedPersonProfileContainer");
    let updateDetailsButton = document.getElementById("updateDetailsButton");
    let closeModalButton = document.getElementById("closeModalButton");

    updateDetailsButton.remove();
    closeModalButton.remove();
    modal.style.display = "none";
}


// Function to open the Modal to display the Selected Person's Details and Root/Teacher can modify it too //
function openModalForSelectedPerson(selectedPerson){
    // Getting the information of the Selected Person //
    let selectedPersonProfile =  relatedPersons[this.id];
    // Getting Tags //
    let modal = document.getElementById("selectedPersonProfileContainer");
    let modalForm = document.getElementById("modalForm");
    let selectedImg = document.getElementById("selectedImg");
    let updatePersonId = document.getElementById("update-personId");
    let updateName = document.getElementById("update-name");
    let updateEmail = document.getElementById("update-email");
    let updateGender = document.getElementById("update-gender");
    let updateDesignation = document.getElementById("update-designation");
    let updateClass = document.getElementById("update-class");
    let updatePhoneNo = document.getElementById("update-phoneNo");
    let updateAdharCardNo = document.getElementById("update-adharCardNo");
    let updateAddress = document.getElementById("update-address");
    let updateCity = document.getElementById("update-city");
    let updateState = document.getElementById("update-state");
    let updatePinCode = document.getElementById("update-pinCode");
    let depositedFees = document.getElementById("depositedFees");
    let remainingFees = document.getElementById("remainingFees");
    let updateDetailsButton = document.createElement("button");
    let closeModalButton = document.createElement("button");


    
    // Assigning data to their Attributes //
    updateDetailsButton.type = closeModalButton.type =  "button";
    updateDetailsButton.innerText = "Update Details";
    updateDetailsButton.id = "updateDetailsButton";
    closeModalButton.id = "closeModalButton";
    closeModalButton.innerText = "Go Back";
    appendClassDropdownMenu("update-class", ()=>{
        updateClass.selectedIndex =  getIndexOfValue(updateClass.options, selectedPersonProfile.class);
    });
    modal.style.display = "flex";
    selectedImg.src = selectedPersonProfile.profilePath;
    updatePersonId.value = selectedPersonProfile.userId;
    updatePersonId.disabled = true;
    updateEmail.disabled = true;
    updateName.value = selectedPersonProfile.name;
    updateEmail.value = selectedPersonProfile.email;
    updateName.value = selectedPersonProfile.name;
    updateGender.selectedIndex = getIndexOfValue(updateGender.options, selectedPersonProfile.gender);
    updateDesignation.selectedIndex = getIndexOfValue(updateDesignation.options, selectedPersonProfile.designation);
    updateDesignation.disabled =  true;
    updatePhoneNo.value = selectedPersonProfile.phoneNo;
    updateAdharCardNo.value = selectedPersonProfile.adharCardNo;
    updateAddress.value = selectedPersonProfile.address;
    updateCity.value = selectedPersonProfile.city;
    updateState.value = selectedPersonProfile.state;
    updatePinCode.value = selectedPersonProfile.pinCode;
    modalForm.appendChild(closeModalButton);
    modalForm.appendChild(updateDetailsButton);


    // If the Selected Person is a student //
    if( selectedPersonProfile.designation.toLowerCase() == "student" ){
        depositedFees.style.display = remainingFees.style.display = "block";
        depositedFees.value = (selectedPersonProfile.feeSubmitted + " ( Deposited Fees ) " );
        let totalFee = getTotalFees(selectedPersonProfile.class);
        remainingFees.value = (totalFee-selectedPersonProfile.feeSubmitted + " ( Remaining Fees ) " );
    }
    // Otherwise class dropdown menu will be disabled //
    else{
        depositedFees.style.display = "none";
        remainingFees.style.display = "none";
        updateClass.disabled = true;
    }

    // Function to update details of the selected person //
    function updateDetailsOfSelectedPerson(){
        
        // Creating the Updated data variable //
        let updatedData = {
            "task" : "Update Person Details", 
            "loggedInUser" : document.getElementById("userId").textContent, 
            "instituteId" : document.getElementById("instituteId").textContent,  
            "sessionId" : document.getElementById("sessionId").textContent,
            "authority" : document.getElementById("authority").textContent,
            "userId" : updatePersonId.value,
            "name" : updateName.value,
            "email" : updateEmail.value,
            "gender" : updateGender.options[updateGender.selectedIndex].value,
            "designation" : updateDesignation.options[updateDesignation.selectedIndex].value,
            "class" : updateClass.options[updateClass.selectedIndex].value,
            "phoneNo" : updatePhoneNo.value,
            "adharCardNo" : updateAdharCardNo.value,
            "address" : updateAddress.value,
            "state" : updateState.value,
            "city" : updateCity.value,
            "pinCode" : updatePinCode.value,
        };
        
        // If data is modified //
        if( isDataModified(updatedData, selectedPersonProfile) ){
            
            // Onload Function to be executed When request is made and got the response //
            let onLoadFunction = function(){
                
                if( this.status != 200 ){
                    alert("Something Went Wrong!");
                }
                else{
                    let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");           
                    if( responseText.includes("Success") ){
                        let response = JSON.parse(responseText);
                        alert(response.message);
                        document.getElementById("selectedPersonProfileContainer").style.display = "none";
                        updateDetailsButton.remove();
                        closeModalButton.remove();
                        searchPersonInTheDatabase();
                    }
                    else{
                        alert(responseText);
                    }
                }
            }

            // Making the Request to the Server //
            makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", updatedData, onLoadFunction);
        }
        // If Data is not Modified //
        else{
            alert("Nothing to update !!!");
        }
    }

        
    updateDetailsButton.addEventListener("click", updateDetailsOfSelectedPerson);
    closeModalButton.addEventListener("click", closeModal);
}





// Function to Create or Append or Edit the Class Dropdown Menu int the Add Person Pannel //
function appendClassDropdownMenu(classDropdownMenuId, callback=null){
    
    // Getting the ClassContainer //
    let classDropdownMenu = document.getElementById(classDropdownMenuId)
    classDropdownMenu.innerHTML = "";

    // Default Menu Option //
    {
        // Creating an option tag With the value of ClassName //
        let option = document.createElement("option");
        option.value = option.innerText = "Class";
        option.classList.add("options");
        // Appending the option in the Class Dropdown Menu //
        classDropdownMenu.appendChild(option);
    }

    getClassesOfTheInstitute();
    
    for(let key in instituteClasses){    
        // Creating an option tag With the value of ClassName //
        let option = document.createElement("option");
        option.value = option.innerText = instituteClasses[key].className;
        option.classList.add("options");
        // Appending the option in the Class Dropdown Menu //
        classDropdownMenu.appendChild(option);
    }
    
    // Calling the callback function if any //
    if( callback != null )
        callback();
}





// Getting the Classes of the Institute //
getClassesOfTheInstitute();