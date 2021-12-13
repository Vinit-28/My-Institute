var phone = document.getElementById('phone');
phone.href = "tel: +91" + localStorage['phone'];
phone.innerText = localStorage['phone']

var email = document.getElementById('email');
email.href = "mailto:" + localStorage['email'];
email.innerText = localStorage['email'];

if (localStorage['github'] != "") {
    var github = document.getElementById('github');
    github.innerText = localStorage['github']
    if (localStorage['githuburl'] != "")
        github.href = localStorage['githuburl']
}
else {
    document.getElementById('moreLink1').style.display = "none";
}

if (localStorage['linkedin'] != "") {
    var linkedin = document.getElementById('linkedin');
    linkedin.innerText = localStorage['linkedin']
    if (localStorage['linkedinurl'] != "")
        linkedin.href = localStorage['linkedinurl']
}
else {
    document.getElementById('moreLink2').style.display = "none";
}


document.getElementById('name').innerText = localStorage['name']
document.getElementById('tag').innerText = localStorage['tag']


document.getElementById('summary').innerText = localStorage['summary'];

document.getElementById('class12type').innerText = localStorage['class12type']
document.getElementById('class12name').innerText = localStorage['class12school']
document.getElementById('class12percent').innerText = localStorage['class12percent']
document.getElementById('class12time').innerText = localStorage['class12time']

document.getElementById('class10board').innerText = localStorage['class10board']
document.getElementById('class10name').innerText = localStorage['class10school']
document.getElementById('class10percent').innerText = localStorage['class10percent']
document.getElementById('class10time').innerText = localStorage['class10time']



// Adding Skills
if (localStorage['skills'].length != 0) {
    var skillsContainer = document.getElementById('skillsContainer');
    var skillshave = localStorage['skills'].split(',');
    for (var i = 0; i < skillshave.length; i++) {
        var div = document.createElement('div');
        div.classList.add('skill');
        div.innerText = skillshave[i];

        skillsContainer.appendChild(div);
    }
}
else
    document.getElementById('skillblock').style.display = "none";


// Adding Compentencies
if (localStorage['coreCompentencies'].length != 0) {
    var skillsContainer = document.getElementById('coreContainer');
    var corehave = localStorage['coreCompentencies'].split(',');
    for (var i = 0; i < corehave.length; i++) {
        var div = document.createElement('div');
        div.classList.add('skill');
        div.innerText = corehave[i];

        skillsContainer.appendChild(div);
    }
}
else
    document.getElementById('coreblock').style.display = "none";


var count = 0
if (localStorage['project1name'] != "") {
    document.getElementById('project1name').innerText = localStorage['project1name']
    document.getElementById('project1summary').innerText = localStorage['project1summary']
    count = 1
}
if (localStorage['project2name'] != "") {
    document.getElementById('project2name').innerText = localStorage['project2name']
    document.getElementById('project2summary').innerText = localStorage['project2summary']
    count = 1
}
if (localStorage['project3name'] != "") {
    document.getElementById('project3name').innerText = localStorage['project3name']
    document.getElementById('project3summary').innerText = localStorage['project3summary']
    count = 1
}


if (count == 0)
    document.getElementById('projectblock').style.display = "none";

alert('Wait for 1 minute, in case if your image is not loaded !\n Ant the click \'OK\'')
setTimeout(function () {
    print()
}, 2000)