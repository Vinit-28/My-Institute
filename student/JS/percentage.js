// var fees = document.getElementById('feesProgress');
// var feesCounter = 0
// var feesID = setInterval(function () {
//     if (fees.innerHTML != "50%") {
//         fees.innerHTML = feesCounter + "%"
//         feesCounter += 1
//     }
//     else {
//         clearInterval(feesID);
//     }

// }, 40);


function update(targetID, number) {

    var target = document.getElementById(targetID);
    var counter = 0
    var ID = setInterval(function () {
        if (target.innerHTML != number+"%") {
            target.innerHTML = counter + "%"
            counter += 1
        }
        else {
            clearInterval(ID);
        }

    }, 40);
}


update('feesProgress' , 50)
update('attendanceProgress' , 90)