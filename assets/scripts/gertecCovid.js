const extraSymptoms = document.getElementsByClassName("extra");
const unityDropdown = document.getElementsByClassName("unity");
const sectorDropdown = document.getElementsByClassName("sector");

const extraSelected = (symptom) => {
    if (document.querySelector(`input[name='${symptom}']`).checked) {
        document.getElementById("noneAbove").checked = false;
    }
}

const noneSelected = () => {
    for (let i=0; i<extraSymptoms.length; i++) {
        extraSymptoms[i].checked = false;
    }
}

// const unityChange = (unity) => {   
//     var xmlhttp = new XMLHttpRequest();
//     xmlhttp.onreadystatechange = function() {
//       if (this.readyState == 4 && this.status == 200) {
//         const sectors = JSON.parse(this.responseText.split("<")[0]);
//         while (sectorDropdown[0].firstChild) {
//             sectorDropdown[0].removeChild(sectorDropdown[0].firstChild);
//         }
//         for (let i of sectors) {
//             // console.log(i);
//             var opt = document.createElement('option');
//             opt.value = i[1];
//             opt.innerHTML = i[0];
//             sectorDropdown[0].appendChild(opt);
//         }
//       }
//     };
//     xmlhttp.open("GET", "gertecCovid.php?getSectors=" + unity, true);
//     xmlhttp.send();
// }

