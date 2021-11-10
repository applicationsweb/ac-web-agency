var search_bar = document.getElementById('search_bar');
var keywords = '';

// Ecoute le clavier de l'utilisateur sur la barre de recherche
if(search_bar) {
    search_bar.addEventListener('keydown', (e) => {
        if (e.defaultPrevented) {
            return; // Ne devrait rien faire si l'événement de la touche était déjà consommé.
        }

        switch (e.key) {
            case "ArrowDown":
                break;
            case "CapsLock":
                break;
            case "Delete":
                break;
            case "Shift":
                break;
            case "ArrowUp":
            break;
            case "ArrowLeft":
                break;
            case "ArrowRight":
                break;
            case "Enter":
                break;
            case "Escape":
                break;
            case "Backspace":
                // Si on sélectionne du texte avec la souris et qu'on efface
                if(!window.getSelection().toString()) {
                    // Efface la dernière lettre lorsque la touche "Backspace" est pressée
                    keywords = keywords.slice(0, -1);
                } else {
                    keywords = keywords.replace(window.getSelection().toString(),'');
                }
                research(keywords);
                return;
            default:
                // Incrémente la chaîne du mot clé
                keywords += e.key;
                research();
                return;
        }

        // Annuler l'action par défaut pour éviter qu'elle ne soit traitée deux fois.
        e.preventDefault();    
    });
}

// Effectue la recherche avec les mots clés tapés par l'utilisateur
function research() {
    var list = document.getElementById('list_movies').rows;
    for (let i = 0; i < list.length; i++) { // Pour chaque TD du tableau
        if(i != 0) {
            let id = list[i].cells[0].innerText;
            let name = list[i].cells[1].innerText;
            let kind = list[i].cells[2].innerText;
            var splittedKind = kind.split(" - "); 
            let existName = existKing = false; 
            let target = document.getElementById(id);

            // Si le nom du film match avec la recherche
            if(name.toLowerCase().includes(keywords.toLowerCase())) {
                existName = true;
            }

            // Si les genres du film matchs avec la recherche
            for (let k of splittedKind) {
                if(k.toLowerCase().includes(keywords.toLowerCase())) {
                   existKing = true; 
                }
            }

            // Si résultat match ou que aucune recherche on affiche les films
            if(keywords == '' || existName || existKing) {
                target.classList.remove('d-none');
            } else {
                target.classList.add('d-none');
            }
        }
    }
}