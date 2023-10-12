document.addEventListener('DOMContentLoaded', function () {
    var dateSelect = document.getElementById('dateSelect');
    var selectedDateInput = document.getElementById('selectedDate');
    var myForm = document.getElementById('myForm'); // Sélectionnez le formulaire par son ID
    var Table = document.getElementById('commandeTable'); // Sélectionnez le tableau par son ID
    var Table1 = document.getElementById('commandeTable1'); // Sélectionnez le tableau par son ID
    var Table2 = document.getElementById('commandeTable2'); // Sélectionnez le tableau par son ID

    console.log(selectedDateInput);
    dateSelect.addEventListener('change', function () {
        var selectedDate = dateSelect.value;
        console.log(selectedDate);
        selectedDateInput.value = selectedDate;
        console.log(selectedDateInput.value);

        // soumettre le formulaire 
        myForm.submit();

    });

    // Ajoutez un gestionnaire d'événements pour le formulaire
    myForm.addEventListener('submit', function (e) {
        e.preventDefault(); // Empêchez l'envoi du formulaire par défaut

        // Affichez le tableau
        commandeTable.style.display = 'table'; // ou 'table' pour afficher le tableau

        // Vous pouvez également soumettre le formulaire ici si nécessaire
        // myForm.submit();
    });
});