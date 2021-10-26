$(document).ready(function () {
    const pathnameArray = window.location.pathname.split('/');
    const sortieSelectionnee = pathnameArray[pathnameArray.length-1];
    const villeSelectionne = $('#sortie_villeAccueil').find(":selected").val();
    if (sortieSelectionnee !== 'create') {
        fetch(window.location.origin + '/select/sortie/' + sortieSelectionnee)
            .then(response => response.json())
            .then((data) => { // Get Ville ID et Site ID
                console.log("data:");
                console.log(data)
                updateLieux(data[0],data[1]);
            });
    } else if (villeSelectionne){
        updateLieux(villeSelectionne, null);
    } else {
        $('#sortie_site').attr('disabled', true);
    }
    $('#sortie_villeAccueil').change((e) => {
        updateLieux(e.currentTarget.value, null);
        $('#sortie_site').attr('disabled', false);
    });
});

function updateLieux(villeSelectionnee, siteSelectionne) {
    console.log('update');
    fetch(window.location.origin + '/select/ville/' + villeSelectionnee)
        .then(response => response.json())
        .then((sites) => {
            $('#sortie_site').empty();
            sites.forEach((element) => {
                // Selectionne l'element que s'il est selectionné dans l'objet
                const isSelectedElement = siteSelectionne === element[0];
                createSelectOption(element, isSelectedElement);
            });
        });
}

function createSelectOption(option, isSelected) {
    let site = document.createElement("option");
    site.value = option[0];
    site.innerText = option[1];
    $('#sortie_site').append(site);
    if(isSelected) {
        site.setAttribute('selected', "true");
    }
}