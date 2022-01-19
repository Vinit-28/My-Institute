// Script to handel display of attendance modal

document.getElementById('attendanceProgress').addEventListener('click' , ()=>{
    document.getElementById('studentAttendanceModal').style.display = "flex";
})
document.getElementById('closeAttendanceModalButton').addEventListener('click' , ()=>{
    document.getElementById('studentAttendanceModal').style.display = "none";
})

// Script to handel display of attendance modal end
