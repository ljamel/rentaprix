var currentTab = 0; // Current tab is set to be the first tab (0)
var valid = false;
showTab(currentTab); // Display the current tab

function showTab(n) {
    var x = document.getElementsByClassName("tab");
    x[n].style.display = "block";

    if (n == 0) {
        document.getElementById("prevBtn").style.display = "none";
    } else {
        document.getElementById("prevBtn").style.display = "inline";
    }
    if (n == (x.length - 1)) {
        document.getElementById("nextBtn").innerHTML = "Valider";
        document.getElementById("nextBtn").style.backgroundColor = "#04AA6D";
    } else {
        document.getElementById("nextBtn").innerHTML = "Suivant <i class='fa-solid fa-angles-right'></i>";
    }
    // ... and run a function that displays the correct step indicator:
    fixStepIndicator(n)
}
function nextPrev(n) {
    validateForm(n);
}
function validateForm(n) {
    var x, y, i;
    const allForm = document.querySelector('#regForm');
    x = document.getElementsByClassName("tab");
    y = x[currentTab].getElementsByClassName("input");

    const form = new FormData(allForm);
    form.append('tab', currentTab);

    fetch(allForm.action, {
        headers: {
            "X-Requested-With": "XMLHttpRequest"
        },
        method: 'POST',
        body: form,
    })
        .then(response => response.json())
        .then(json => {
            return handleResponse(json,n);
        });
}
function fixStepIndicator(n) {
    var i, x = document.getElementsByClassName("step");

    for (i = 0; i < x.length; i++) {
        x[i].className = x[i].className.replace(" active", "");
    }

    x[n].className += " active";
}
const handleResponse = function (response, n) {
    removeErrors();
    if(response.code === 'CALCUL_INVALID_FORM') {
        handleErrors(response.errors);
    } else {
        var x = document.getElementsByClassName("tab");

        x[currentTab].querySelectorAll('input').forEach(e => {
            e.style.border = "#ccc 1px solid";
        })
        x[currentTab].style.display = "none";
        currentTab = currentTab + n;
        // if you have reached the end of the form... :
        if (currentTab >= x.length) {
            window.location.href="/dashboard/calcul/redirect";
        }

        showTab(currentTab);
    }
}
const removeErrors = function() {
    const invalidFeedbackElements = document.querySelectorAll('.error-message');
    invalidFeedbackElements.forEach(errorElement => errorElement.remove());
}
const handleErrors = function(errors) {
    if (errors.length === 0) return true;
    for (const key in errors) {
        let element = document.querySelector(`#calcul_${key}`);
        let div = document.createElement('div');
        div.classList.add('error-message');
        div.innerText = errors[key];
        element.style.border= "solid 1px #aa3a3c";
        element.after(div);
    }
    return false;
}
const searchFees = (idInput, idTable) =>{
    let input, filter, table, tr, td, i, txtValue;
    input = document.getElementById(idInput);
    filter = input.value.toUpperCase();
    table = document.getElementById(idTable);
    tr = table.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[0];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}
const deleteThisLink = (blockId) =>{
    let block = document.querySelector(blockId);
    if(confirm("êtes vous sûr de vouloir supprimer cet élement?")){
        block.remove();
    }
}

window.searchFees = searchFees;
window.deleteThisLink = deleteThisLink;
window.nextPrev = nextPrev;