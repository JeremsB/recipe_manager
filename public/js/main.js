/**
 * List Ingredient class
 */

class ListIngredient {
    constructor(trash) {
        this.trash = trash;

        if(this.trash) {
            this.listenOnClickTrash();
        }
    }

    listenOnClickTrash() {
        this.trash.map((trash) => {
            trash.addEventListener('click', event => {
                event.preventDefault();

                if (confirm("Voulez vous supprimer l'ingrédient ?")) {
                    // Delete on list
                    var item = document.querySelector(`.ingredient[data-id="${trash.dataset.id}"]`);
                    item.remove();

                    // Delete on database
                    const url = `/ingredient/delete/${trash.dataset.id}`;

                    var xhr = new XMLHttpRequest();
                    xhr.open('GET', url);
                    xhr.send();
                }
            })
        })
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const trash = [].slice.call(document.querySelectorAll('.ingredient-trash'));

    if(trash) {
        new ListIngredient(trash);
    }
})