

// Declaring Some Global Varibales //
let relatedPersons = {};
let instituteClasses = {};


// Function to make a AJAX request to the Server ( Utility Function ) //
function makeAJAXRequest(requesType, serverUrl, data, onLoadFunction, async=true){

    // Encoding the Data //
    for(let key in data){
        if( typeof(data[key]) == 'string' )
            data[key] = encodeURIComponent(data[key]);
    }

    // Creating the XHR Object //
    let xhrObject = new XMLHttpRequest();
    xhrObject.open(requesType, serverUrl, async);
    xhrObject.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    // After getting the Response from the Server this Function will be executed //
    xhrObject.onload = onLoadFunction;

    // Making the Request //
    xhrObject.send("request="+JSON.stringify(data));
}



// Function to Convert string to integer if possible //
function isStringConvertableToNumber(stringValue){

    let num = Number(stringValue);

    if( isNaN(num) ){
       return [false, 0];
    }   
    return [true, num];
}


// ------------------------------- Add Person ------------------------------- // 

// Fucntion to make a AJAX request to the server and Will add a new Person in the Database //
function addPersonInTheDatabase(e){

    e.preventDefault();
    
    // Creating Some Variables //
    let designation = document.getElementById("add-designation");
    let gender = document.getElementById("add-gender");
    let Class = document.getElementById("add-class");
    
    
    // If the Person's Gender is not defined //
    if( gender.options[gender.selectedIndex].value.toLowerCase() == "gender"){
        alert("Please Select the Gender of the Person !!!");
        return;
    }
    // If the Person's Designation is not defined //
    if( designation.options[designation.selectedIndex].value.toLowerCase() == "designation" ){
        alert("Please Select the Designation of the Person !!!");
        return;
    }
    // If the Student Class is not defined //
    if( designation.options[designation.selectedIndex].value.toLowerCase() != "teacher" && Class.options[Class.selectedIndex].value.toLowerCase() == "class" ){
        alert("Please Select a Class Or Make a Class !!!");
        return;
    }

    let personData = {
        "task" : "Add Person", 
        "loggedInUser" : document.getElementById("userId").textContent, 
        "instituteId" : document.getElementById("instituteId").textContent, 
        "sessionId" : document.getElementById("sessionId").textContent,
        "authority" : document.getElementById("authority").textContent,
        "userId" : document.getElementById("add-personId").value,
        "name" : document.getElementById("add-name").value,
        "password" : document.getElementById("add-password").value,
        "email" : document.getElementById("add-email").value,
        "gender" : gender.options[gender.selectedIndex].value,
        "designation" : designation.options[designation.selectedIndex].value,
        "class" : Class.options[Class.selectedIndex].value,
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



// Function to upload Add Person Excel File on the Server //
function uploadAddPersonFile(e){

    e.preventDefault();

    // Creating Some Variables //
    let data = {
        "task" : "Add Persons", 
        "loggedInUser" : document.getElementById("userId").textContent, 
        "instituteId" : document.getElementById("instituteId").textContent, 
        "authority" : document.getElementById("authority").textContent,
        "sessionId" : document.getElementById("sessionId").textContent,
    };

    // Encoding the Data //
    for(let key in data){
        data[key] = encodeURIComponent(data[key]);
    }

    let xhr = new XMLHttpRequest();
    let formData = new FormData();
    let addPersonsFile = document.getElementById("addPersonsFile").files[0];      
    
    formData.append("request", JSON.stringify(data));   
    formData.append("addPersonsFile", addPersonsFile);
    
    xhr.timeout = 10000;
    xhr.open("POST", '../../Server/Utilities/InstituteSpecificUtilities.php'); 
    
    // Function to be executed When the request has made and got the response from the server //
    xhr.onload = function(){

        // if( this.status != 200 ){
        //     alert("Something Went Wrong!");
        // }
        // else{
        //     let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
        //     if( responseText.includes("Success") || responseText.includes("Failed") ){
        //         let response = JSON.parse(responseText);
        //         let message = (response.result == "Success")? (response.message + "\nNumber of Persons Added = " + response.successfullInsertions) : response.message
        //         alert(message);
        //     }
        //     else{
        //         alert(responseText);
        //     }
        // }
        alert(this.responseText);
    }
    xhr.send(formData);
}



// Function to open Add Person Excel File Pattern //
function addPersonFilePattern(e){

    e.preventDefault();
    window.open('http://localhost/Server/UserRelatedDocs/sampleFile.xls', '_blank');
}



// Binding the Function addPersonInTheDatabase to the button submitAddPersonForm //
document.getElementById("submitAddPersonForm").addEventListener("click", addPersonInTheDatabase);
document.getElementById("uploadAddPersonFile").addEventListener("click", uploadAddPersonFile);
document.getElementById("addPersonFilePattern").addEventListener("click", addPersonFilePattern);




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

    if( selectedPerson.designation.toLowerCase() == "student" && updatedData.fees != selectedPerson.fees) return true;
    
    return (updatedData.name != selectedPerson.name || updatedData.phoneNo != selectedPerson.phoneNo || updatedData.address != selectedPerson.address || updatedData.city != selectedPerson.city || updatedData.pinCode != selectedPerson.pinCode || updatedData.state != selectedPerson.state || updatedData.adharCardNo != selectedPerson.adharCardNo || updatedData.gender != selectedPerson.gender || updatedData.designation != selectedPerson.designation || updatedData.class != selectedPerson.class);
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
function openModalForSelectedPerson(){
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


    // If the Selected Person is a Student //
    if( selectedPersonProfile.designation.toLowerCase() == "student" ){
        depositedFees.style.display = remainingFees.style.display = "block";
        depositedFees.value = (selectedPersonProfile.feeSubmitted + " ( Deposited Fees ) " );
        let totalFee = getTotalFees(selectedPersonProfile.class);
        remainingFees.value = (totalFee-selectedPersonProfile.feeSubmitted + " ( Remaining Fees ) " );
        updateClass.options[0].innerText = "Class";

        // Adding Some Event Listeners to the Deposited Fees Input Field //
        depositedFees.addEventListener("focusin",()=>{depositedFees.value = depositedFees.value.replace(" ( Deposited Fees ) ", "");});

        depositedFees.addEventListener("focusout",()=>{
            depositedFees.value = depositedFees.value.replace(" ( Deposited Fees ) ", "");
            depositedFees.value += " ( Deposited Fees ) ";
        });

        depositedFees.addEventListener("input",()=>{

            let lst = isStringConvertableToNumber(depositedFees.value);
            let isPossible = lst[0], depositedFeesValue = lst[1];

            if( isPossible == false ){
                alert("Deposited Fees should always be in number !!!");
                depositedFees.value = "0";
            }
            else if( depositedFeesValue > totalFee ){
                alert("Deposited Fees can't be greater than the Total Fees !!!");
                depositedFees.value = "0";
            }
            else if( depositedFeesValue < 0 ){
                alert("Deposited Fees can't be lesser than the 0 !!!");
                depositedFees.value = "0";
            }
            
            // If deeposited Values is not able to be converted in integer then it will be always set to 0 //
            remainingFees.value = (totalFee-depositedFees.value + " ( Remaining Fees ) " );
        });
        
    }
    // If the Selected Person is a Teacher //
    else{
        depositedFees.style.display = "none";
        remainingFees.style.display = "none";
        updateClass.options[0].innerText = "Class Teacher Of";
        if( document.getElementById("authority").textContent.toLowerCase() == "teacher" ){
            updateDetailsButton.disabled = true;
        }
    }

    // Function to update details of the selected person //
    function updateDetailsOfSelectedPerson(){
        
        // Creating the Updated data variable //
        let updatedData = {
            "task" : "Update Person Details", 
            "loggedInUser" : document.getElementById("userId").textContent, 
            "instituteId" : document.getElementById("instituteId").textContent, 
            "authority" : document.getElementById("authority").textContent,
            "sessionId" : document.getElementById("sessionId").textContent,
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
        
        // If Person's Class is not Selected //
        if( selectedPersonProfile.designation.toLowerCase() == "student" ){
            updatedData["fees"] = document.getElementById("depositedFees").value.replace(" ( Deposited Fees ) ","");
            
            if( updateClass.selectedIndex == 0 ){
                alert("Please Select a Class");
                return;
            }
        }

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


// ------------------------------- Updating Classes of the Institute ------------------------------- // 


// function to handle Add Class Functionality //
function openAddClassForm(){

    // Getting the ClassContainer //
    let ClassContainer = document.getElementById("ClassContainer");

    // Creating the Tags //
    let form = document.createElement("form");
    let inputClassName = document.createElement("input");
    let inputFees = document.createElement("input");
    let button = document.createElement("button");
    
    // Giving values to their Attributes //
    ClassContainer.innerHTML = "";
    form.id = "addClassForm";
    form.classList.add("forms");
    form.method = "POST";
    inputClassName.id = "className-add";
    inputFees.id = "fees-add";
    inputFees.placeholder = "Class Fees";
    inputClassName.placeholder = "Class Name";
    button.id = "addClass-save";
    button.type = "submit";
    button.innerText = "Create Class";
    button.classList.add("addClassButton");

    // Wrapping up the Tags //
    form.appendChild(inputClassName);
    form.appendChild(inputFees);
    form.appendChild(button);
    ClassContainer.appendChild(form);


    function addClassInTheDatabase(e){

        e.preventDefault();
        let data = {
            "task" : "Update Classes", 
            "subtask" : "Add Class", 
            "loggedInUser" : document.getElementById("userId").textContent, 
            "instituteId" : document.getElementById("instituteId").textContent, 
            "authority" : document.getElementById("authority").textContent,
            "sessionId" : document.getElementById("sessionId").textContent,
            "className" : document.getElementById("className-add").value,
            "fees" : document.getElementById("fees-add").value,
        };

        let onLoadFunction = function(){

            if( this.status != 200 ){
                alert("Something Went Wrong!");
            }
            else{
                let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
                if( responseText.includes("Success") || responseText.includes("Failed") ){
                    let response = JSON.parse(responseText);
                    alert(response.message);
                    if( response.result == "Success" ){

                        // document.getElementById("className-add").value = "";
                        // document.getElementById("fees-add").value = "";
                        showClasses();
                    }
                }
                else{
                    alert(responseText);
                }
            }
        }

        // Making the AJAX Request //
        makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", data, onLoadFunction)
    }

    // Binding the Function searchPersonInTheDatabase to the button searchPerson //
    document.getElementById("addClass-save").addEventListener("click", addClassInTheDatabase);
}



// function to get a Class Card //
function getClassCard(classData){

    // Creating tags //
    let containerItem = document.createElement("div");
    let inputCheckBox = document.createElement("input");
    let className = document.createElement("p");

    // Giving values to their Attributes //
    containerItem.classList.add("containerItem");
    inputCheckBox.type = "checkbox";
    inputCheckBox.name = "showClassesCard";
    // inputCheckBox.id = classData.className;
    inputCheckBox.value = classData.className;
    className.innerText = classData.className + " ( " + classData.fees + " )";


    // Wrapping the Tag //
    containerItem.appendChild(inputCheckBox);
    containerItem.appendChild(className);
    return containerItem;
}



// function to handle Show Classes Functionality //
function showClasses(){

    // Getting the ClassContainer //
    let ClassContainer = document.getElementById("ClassContainer")
    ClassContainer.innerHTML = "";

    let data = {
        "task" : "Update Classes", 
        "subtask" : "Show Classes", 
        "loggedInUser" : document.getElementById("userId").textContent, 
        "authority" : document.getElementById("authority").textContent,
        "instituteId" : document.getElementById("instituteId").textContent, 
        "sessionId" : document.getElementById("sessionId").textContent,
    };

    let onLoadFunction = function(){

        if( this.status != 200 ){
            alert("Something Went Wrong!");
        }
        else{
            let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
            if( responseText.includes("Success") || responseText.includes("Failed") ){
                let response = JSON.parse(responseText);
                if( response.result == "Success" ){
                    
                    // Creating the Form Tag for (Checkboxes) //
                    let showClassForm = document.createElement("form");
                    showClassForm.id = "showClassForm";
                    instituteClasses = response.classes;
                    for(let key in response.classes){
                        // Appending the new Class in the Form tag as Checkbox //
                        showClassForm.appendChild(getClassCard(response.classes[key]));
                    }
                    // Appending the Form in the ClassContainer //
                    ClassContainer.appendChild(showClassForm);
                }
            }
            else{
                alert(responseText);
            }
        }
    }

    // Making the AJAX Request //
    makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", data, onLoadFunction)
}


// Function to get the Selected Classes from the Form Menu (Checkboxes) //
function getSelectedClasses(){

    let checkboxes = document.getElementsByName('showClassesCard');
    let selectedClasses= [];
    for (var checkbox of checkboxes)
    {
        if (checkbox.checked) {
            selectedClasses.push(checkbox.value);
        }
    }
    return selectedClasses;
}



// Function to delete the Selected Classes //
function deleteClass(){
    
    let selectedClasses = getSelectedClasses();

    // If a User has Selected the Classes //
    if( selectedClasses.length ){

        let data = {
            "task" : "Update Classes", 
            "subtask" : "Delete Classes", 
            "loggedInUser" : document.getElementById("userId").textContent, 
            "instituteId" : document.getElementById("instituteId").textContent, 
            "authority" : document.getElementById("authority").textContent,
            "sessionId" : document.getElementById("sessionId").textContent,
            "selectedClasses" : selectedClasses
        };
    
        let onLoadFunction = function(){
    
            if( this.status != 200 ){
                alert("Something Went Wrong!");
            }
            else{
                let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
                if( responseText.includes("Success") || responseText.includes("Failed") ){
                    let response = JSON.parse(responseText);
                    alert(response.message);
                    showClasses();
                }
                else{
                    alert(responseText);
                }
            }
        }
    
        // Making the AJAX Request //
        makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", data, onLoadFunction)

    }
    else{
        alert("Please select atleast one class !!!");
    }
}



// Function to get the Class Details using its Name //
function getClassDetails(className){

    for(let classDetails in instituteClasses){
        if( instituteClasses[classDetails].className == className )
            return instituteClasses[classDetails];
    }
}



// Function to Update the Class Details(Fees) //
function updateClass(){
    
    let selectedClasses = getSelectedClasses();

    // If the user has selected only one Class then it will only be updated //
    if( selectedClasses.length == 1 ){

        // Getting the Selected Class //
        let selectedClass = getClassDetails(selectedClasses[0]);

        // Getting the ClassContainer //
        let ClassContainer = document.getElementById("ClassContainer");

        // Creating the Tags //
        let form = document.createElement("form");
        let inputClassName = document.createElement("input");
        let inputClassFess = document.createElement("input");
        let updateButton = document.createElement("button");
        

        // Giving values to their Attributes //
        form.id = "updateClassForm";
        form.classList.add("forms");
        form.method = "POST";
        inputClassName.id = "className-update";
        inputClassFess.id = "fees-update";
        inputClassName.placeholder = "Updated Class Name";
        inputClassFess.placeholder = "Updated Class Fees";
        inputClassName.placeholder = "Class Name";
        updateButton.id = "saveUpdatedClassInfo";
        updateButton.type = "submit";
        updateButton.innerText = "Save Changes";
        updateButton.classList.add("saveUpdatedClassInfo-button");
        
        ClassContainer.innerHTML = "";
        inputClassName.value = selectedClass.className;
        inputClassFess.value = selectedClass.fees;
        

        // Wrapping up the Tags //
        form.appendChild(inputClassName);
        form.appendChild(inputClassFess);
        form.appendChild(updateButton);
        ClassContainer.appendChild(form);


        // Function to be executed when the user has enetered the updated Details //
        function updateClassInfoInTheDatabase(e){
            e.preventDefault();

            let updatedClassName = document.getElementById("className-update").value;
            let updatedClassFees = document.getElementById("fees-update").value;

            // If nothing has to be updated //
            if( selectedClass.className == updatedClassName && selectedClass.fees == updatedClassFees ){
                alert("Please Update Something !!!");
                return;
            }

            let data = {
                "task" : "Update Classes", 
                "subtask" : "Update Class", 
                "loggedInUser" : document.getElementById("userId").textContent, 
                "instituteId" : document.getElementById("instituteId").textContent, 
                "authority" : document.getElementById("authority").textContent,
                "sessionId" : document.getElementById("sessionId").textContent,
                "updatedClassInfo" : {"updatedClassName" : updatedClassName, "updatedFees" : updatedClassFees, "className" : selectedClass.className}
            };
            
            let onLoadFunction = function(e){
                e.preventDefault();
                if( this.status != 200 ){
                    alert("Something Went Wrong!");
                }
                else{
                    let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
                    if( responseText.includes("Success") || responseText.includes("Failed") ){
                        let response = JSON.parse(responseText);
                        alert(response.message);
                        showClasses();
                    }
                    else{
                        alert(responseText);
                    }
                }
            }

            // Making the AJAX Request //
            makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", data, onLoadFunction)
        }
        // Binding the Save Changes Button with the updateClassInfoInTheDatabase Function //
        document.getElementById("saveUpdatedClassInfo").addEventListener("click", updateClassInfoInTheDatabase);
    }
    else{
        // If user has Selected more than 1 class //
        if( selectedClasses.length > 1 )
            alert("Please select only one class !!!");
        // If user has not even selected a single class // 
        else 
            alert("Please select a class !!!");
    }
}



// Binding the Update Classes Function  //
document.getElementById("addClass").addEventListener("click", openAddClassForm);
document.getElementById("showClass").addEventListener("click", showClasses);
document.getElementById("deleteClass").addEventListener("click", deleteClass);
document.getElementById("updateClass").addEventListener("click", updateClass);








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


    let data = {
        "task" : "Update Classes", 
        "subtask" : "Show Classes", 
        "loggedInUser" : document.getElementById("userId").textContent, 
        "instituteId" : document.getElementById("instituteId").textContent, 
        "sessionId" : document.getElementById("sessionId").textContent,
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
                if(response.result == "Success"){
                    for(let key in response.classes){    
                        // Creating an option tag With the value of ClassName //
                        let option = document.createElement("option");
                        option.value = option.innerText = response.classes[key].className;
                        option.classList.add("options");
                        // Appending the option in the Class Dropdown Menu //
                        classDropdownMenu.appendChild(option);
                    }
                    // Calling the callback function if any //
                    if( callback != null )
                        callback();
                }
            }
            else{
                alert(responseText);
            }
        }
    }

    // Making the AJAX Request //
    makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", data, onLoadFunction);
}


// Function to make Class Dropdown Menu disabled/enabled according to the designation Selected //
function changeDesignation(designationId, classId){

    let designation = document.getElementById(designationId);
    let designationValue = designation.options[designation.selectedIndex].value;
    let Class = document.getElementById(classId);
    Class.selectedIndex = 0;
    if( designationValue.toLowerCase() == "teacher" ){
        Class.options[0].innerText = "Class Teacher Of";
    }
    else{
        Class.options[0].innerText = "Class";
    }
}







// Making ready the institute Classes List //
appendClassDropdownMenu("add-class");