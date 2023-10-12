document.addEventListener('DOMContentLoaded', function () {
    var dateSelect = document.getElementById('dateSelect');
    var selectedDateInput = document.getElementById('selectedDate');
    var myForm = document.getElementById('myForm'); // Sélectionnez le formulaire par son ID
   

    dateSelect.addEventListener('change', function () {
        var selectedDate = dateSelect.value;
        selectedDateInput.value = selectedDate;
        // soumettre le formulaire 
        myForm.submit();

    });

});