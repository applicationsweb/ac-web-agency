// Ouverture de la modal
document.querySelectorAll('.link_modal').forEach(item => {
    item.addEventListener('click', event => {
        // Récupère les informations du film
        let parent_row = event.currentTarget.parentElement.parentElement;
        let name = event.currentTarget.innerText;
        let author = parent_row.querySelector('.movie_author').innerText;
        let kinds = parent_row.querySelector('.movie_kind').innerText;
        let created = parent_row.querySelector('.movie_created').innerText;
        let image = parent_row.querySelector('.movie_image').innerText;

        // Affiche les informations du film
        document.querySelector('.modal-title').innerHTML = name;
        document.querySelector('.modal-body .modal-image').innerHTML = `<img src="${image}" alt="${name}" class="img-fluid">`;
        document.querySelector('.modal-body .modal-kinds').innerHTML = kinds;
        document.querySelector('.modal-body .modal-author').innerHTML = author;
        document.querySelector('.modal-body .modal-created').innerHTML = created;
    });
});
