

$(".tagname").on("input", function () {
    if (($(this).prop("value")).length > 0)
        $($(this).next()).prop("disabled", false);
    else {
        $($(this).next()).prop("value", "");
        $($(this).next()).prop("disabled", true);
    }
});

$(".clickButton").click(function (e) {
    e.preventDefault();
})

var skills = []
function insertSkill() {
    obj = document.getElementById("skillSelect");
    newSkill = document.getElementById("skillInsert").value;

    if (skills.length < 10) {
        if (skills.includes(newSkill))
            alert("Skill Repeated");
        else {
            var temp = document.createElement('option');
            temp.value = newSkill;
            temp.innerHTML = newSkill;
            obj.appendChild(temp);

            document.getElementById("skillInsert").value = "";
            skills.push(newSkill)
        }

    }
    else
        alert("Enough Skills Taken !")

}

function deleteSkill() {

    obj = document.getElementById("skillSelect");
    for (i = 0; i < obj.length; i++) {
        if (obj[i].value == obj.value) {
            skills.splice(
                skills.indexOf(obj.value), 1
            )
            obj.removeChild(obj[i]);
            break;
        }
    }
}



var compentensies = ["", "Fast Learner", "Leadership", "Communication", "Focus", "Integrity", "Responsibility"
    , "Punctual", "Teamwork"]

var checkboxes = [];

function count(value) {

    console.log("before => ", checkboxes);
    if (checkboxes.length < 4) {
        if (checkboxes.includes(value) == false) {
            checkboxes.push(value);
        }
        else {
            $($("input[type=checkbox]").children()['prevObject'][value - 1]).prop("checked", false);
            checkboxes.splice(checkboxes.indexOf(value), 1);
        }

    }
    else {
        if (checkboxes.includes(value) == true) {
            $($("input[type=checkbox]").children()['prevObject'][value - 1]).prop("checked", false);
            checkboxes.splice(checkboxes.indexOf(value), 1);
        }
        else {
            alert("Not more values are allowed");
            $($("input[type=checkbox]").children()['prevObject'][value - 1]).prop("checked", false);
        }
    }
    // console.log(checkboxes , value);
    console.log("after => ", checkboxes);
}

// document.addEventListener('visibilitychange', function(){
//    document.title = document.visibilityState;
//    console.log(document.visibilityState);
// });


function createResume(event) {

    if (
        document.getElementById('name').value == '' ||
        document.getElementById('tag').value == '' ||
        document.getElementById('email').value == '' ||
        document.getElementById('phone').value == '' ||
        document.getElementById('address').value == '' ||
        document.getElementById('city').value == '' ||
        document.getElementById('pincode').value == '' ||
        document.getElementById('state').value == '' ||
        document.getElementById('professionalsummary').value == '' ||

        document.getElementById('class12board').value == '' ||
        document.getElementById('class12subject').value == '' ||
        document.getElementById('class12name').value == '' ||
        document.getElementById('class12percent').value == '' ||
        document.getElementById('from12').value == '' ||
        document.getElementById('to12').value == '' ||

        document.getElementById('class10board').value == '' ||
        document.getElementById('class10name').value == '' ||
        document.getElementById('class10percent').value == '' ||
        document.getElementById('from10').value == '' ||
        document.getElementById('to10').value == ''



    ) {
        alert('Fill the necessary Details');
        event.preventDefault();
    }
    else {
        localStorage.setItem('name', document.getElementById('name').value);
        localStorage.setItem('image', document.getElementById('image').value);
        localStorage.setItem('tag', document.getElementById('tag').value);
        localStorage.setItem('phone', document.getElementById('phone').value);
        localStorage.setItem('email', document.getElementById('email').value);
        if (document.getElementById('github').value != "") {
            localStorage.setItem('github', document.getElementById('github').value);
            localStorage.setItem('githuburl', document.getElementById('githuburl').value);
        }
        if (document.getElementById('linkedin').value != "") {
            localStorage.setItem('linkedin', document.getElementById('linkedin').value);
            localStorage.setItem('linkedinurl', document.getElementById('linkedinurl').value);
        }

        localStorage.setItem('summary', document.getElementById('professionalsummary').value);

        localStorage.setItem('class10board', document.getElementById('class10board').value);
        localStorage.setItem('class10school', document.getElementById('class10name').value);
        localStorage.setItem('class10percent', document.getElementById('class10percent').value+"%");
        localStorage.setItem('class10time', document.getElementById('from10').value + " - " + document.getElementById('to10').value);

        localStorage.setItem('class12type', document.getElementById('class12subject').value + " "+document.getElementById('class12board').value);
        localStorage.setItem('class12school', document.getElementById('class12name').value);
        localStorage.setItem('class12percent', document.getElementById('class12percent').value+"%");
        localStorage.setItem('class12time', document.getElementById('from12').value + " - " + document.getElementById('to12').value);

        
        localStorage.setItem('skills', skills);
        
        if (checkboxes.length != 0) {
            var core = [];
            for (i = 0; i < checkboxes.length; i++) {
                core.push(compentensies[checkboxes[i]])
            }
            localStorage.setItem('coreCompentencies', core);
        }


        if(document.getElementById('project1name').value != "" || document.getElementById('project1name').value != undefined )
        {
            localStorage.setItem('project1name', document.getElementById('project1name').value);
            localStorage.setItem('project1summary', document.getElementById('project1summary').value);
        }
        
        if(document.getElementById('project2name').value != "" || document.getElementById('project2name').value != undefined )
        {
            localStorage.setItem('project2name', document.getElementById('project2name').value);
            localStorage.setItem('project2summary', document.getElementById('project2summary').value);
        }
        
        if(document.getElementById('project3name').value != "" || document.getElementById('project3name').value != undefined ){
            localStorage.setItem('project3name', document.getElementById('project3name').value);
            localStorage.setItem('project3summary', document.getElementById('project3summary').value);
        }

        window.open('./resume.html', "_blank");
    }

}






