

// Declaring Some Global Varibales //
let relatedPersons = {};
let instituteClasses = {};
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
        "instituteId" : document.getElementById("userId").textContent, 
        "sessionId" : document.getElementById("sessionId").textContent,
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
    suggestedPersonDept.innerHTML = personDetails['designation'];

    if( personDetails['designation'].toLowerCase() == "student" ){
        suggestedPersonDept.innerHTML += " ( " + personDetails['class'] +" )";
    }

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
// function areTheDetailsModified(){

//     return true;
// }



// Function to upadte the details of a person in the Institute Database //
// function updatePersonDetails(e){
    
//     e.preventDefault();
    
//     if( areTheDetailsModified() ){

//         // Creating Some Variables //
//         let designation = document.getElementById("update-designation")
//         let gender = document.getElementById("update-gender")

//         let personData = {
//             "task" : "Update Person Details", 
//             "instituteId" : document.getElementById("userId").textContent, 
//             "sessionId" : document.getElementById("sessionId").textContent,
//             "userId" : relatedPersons[selectedProfile].userId,
//             "authority" : relatedPersons[selectedProfile].authority,
//             "name" : document.getElementById("update-name").value,
//             "gender" : gender.options[gender.selectedIndex].value,
//             "designation" : designation.options[designation.selectedIndex].value,
//             "phoneNo" : document.getElementById("update-phoneNo").value,
//             "adharCardNo" : document.getElementById("update-adharCardNo").value,
//             "address" : document.getElementById("update-address").value,
//             "state" : document.getElementById("update-state").value,
//             "city" : document.getElementById("update-city").value,
//             "pinCode" : document.getElementById("update-pinCode").value,
//         };

//         let onLoadFunction = function(){
            
//             if( this.status != 200 ){
//                 alert("Something Went Wrong!");
//             }
//             else{
//                 let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
//                 if( responseText.includes("Success") ){
//                     alert("Person Details Upadted Successfully !!!");
//                 }
//                 else{
//                     let response = JSON.parse(responseText);
//                     alert(responseText);
//                 }
//             }
//         };

//         // Making the Request to the Server //
//          makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", personData, onLoadFunction);
//     }
//     else{
//         alert("Nothing to be Updated !!!");
//     }
// }



// Binding the Function searchPersonInTheDatabase to the button searchPerson //
// document.getElementById("updatePersonDetails").addEventListener("click", updatePersonDetails);




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
            "instituteId" : document.getElementById("userId").textContent, 
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
        "instituteId" : document.getElementById("userId").textContent, 
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
            "instituteId" : document.getElementById("userId").textContent, 
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
        let upadteButton = document.createElement("button");
        

        // Giving values to their Attributes //
        form.id = "updateClassForm";
        form.method = "POST";
        inputClassName.id = "className-update";
        inputClassFess.id = "fees-update";
        inputClassName.placeholder = "Updated Class Name";
        inputClassFess.placeholder = "Updated Class Fees";
        inputClassName.placeholder = "Class Name";
        upadteButton.id = "saveUpdatedClassInfo";
        upadteButton.type = "submit";
        upadteButton.innerText = "Save Changes";
        upadteButton.classList.add("saveUpdatedClassInfo-button");
        
        ClassContainer.innerHTML = "";
        inputClassName.value = selectedClass.className;
        inputClassFess.value = selectedClass.fees;
        

        // Wrapping up the Tags //
        form.appendChild(inputClassName);
        form.appendChild(inputClassFess);
        form.appendChild(upadteButton);
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
                "instituteId" : document.getElementById("userId").textContent, 
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
                        console.log(response);
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
function appendClassDropdownMenu(){
        
    // Getting the ClassContainer //
    let classDropdownMenu = document.getElementById("add-class")
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
        "instituteId" : document.getElementById("userId").textContent, 
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


// Function to make Class Dropdown Menu disabled/enabled according to the designation Selected //
function changeDesignation(){

    let designation = document.getElementById("add-designation");
    let designationValue = designation.options[designation.selectedIndex].value;
    let Class = document.getElementById("add-class");
    Class.selectedIndex = 0;
    if( designationValue.toLowerCase() == "teacher" ){
        Class.disabled = true;
    }
    else{
        Class.disabled = false;
    }
}







// Making ready the institute Classes List //
appendClassDropdownMenu();