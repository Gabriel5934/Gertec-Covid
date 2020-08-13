const extraSymptoms = document.getElementsByClassName("extra");

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