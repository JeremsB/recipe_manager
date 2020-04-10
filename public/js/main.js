/**
 * List Ingredient class
 */

class ListIngredient {
    constructor(list, trash, add, cancelAdding, addAdding) {
        this.list = list;
        
        if(this.list) {
            this.trash = trash;
            this.add = add;
            this.cancelAdding = cancelAdding;
            this.addAdding = addAdding;

            if(this.trash) {
                this.listenOnClickTrash();
            }

            if(this.add) {
                this.listenOnCLickAdd();

                if(this.cancelAdding && this.addAdding) {
                    this.listenOnClickCancelAdding();
                    this.listenOnClickAddAdding();
                }
            }
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

    listenOnCLickAdd() {
        this.add.addEventListener('click', event => {
            event.preventDefault();

            var liAdd = document.querySelector('.ingredient-add');
            var liAdding = document.querySelector('.ingredient-adding');

            liAdd.classList.add('active');
            liAdding.classList.remove('active');
        })
    }

    listenOnClickCancelAdding() {
        this.cancelAdding.addEventListener('click', event => {
            event.preventDefault();

            var liAdd = document.querySelector('.ingredient-add');
            var liAdding = document.querySelector('.ingredient-adding');

            liAdd.classList.remove('active');
            liAdding.classList.add('active');
        })
    }

    listenOnClickAddAdding() {
        this.addAdding.addEventListener('click', event => {
            event.preventDefault();

            var name = document.querySelector('.ingredient-adding-name').value;
            var price = document.querySelector('.ingredient-adding-price').value;

            if(this.isInt(price) || this.isFloat(price)) {
                // Create on database
                var encodedName = encodeURIComponent(window.btoa(name));
                var encodedPrice = encodeURIComponent(window.btoa(price));

                const url = `/ingredient/create/${encodedName}/${encodedPrice}`;

                var id = 0;
                var xhr = new XMLHttpRequest();
                xhr.open('GET', url);
                xhr.onload = function () {
                    var response = JSON.parse(xhr.response);
                    id = response["id"];

                    // Create new element
                    var li = document.createElement('li');
                    li.classList.add('ingredient');
                    li.dataset.id = id;

                    var divIngredient = document.createElement('div');
                    divIngredient.classList.add('ingredient');
                    divIngredient.innerHTML = response["name"] + " au prix de " + response["price"];

                    var divIngredientActions = document.createElement('div');
                    divIngredientActions.classList.add('ingredient-actions');

                    var divIngredientEdit = document.createElement('div');
                    divIngredientEdit.classList.add('ingredient-edit');
                    divIngredientEdit.dataset.id = id;
                    var spanIngredientEdit = document.createElement('span');
                    spanIngredientEdit.innerHTML = "Modifier";
                    divIngredientEdit.appendChild(spanIngredientEdit)

                    var divIngredientTrash = document.createElement('div');
                    divIngredientTrash.classList.add('ingredient-trash');
                    divIngredientTrash.dataset.id = id;
                    var spanIngredientTrash = document.createElement('span');
                    spanIngredientTrash.innerHTML = "Supprimer";
                    divIngredientTrash.appendChild(spanIngredientTrash)

                    divIngredientActions.appendChild(divIngredientEdit);
                    divIngredientActions.appendChild(divIngredientTrash);
                    li.appendChild(divIngredient);
                    li.appendChild(divIngredientActions);

                    var list = document.querySelector('.ingredients');
                    list.insertBefore(li, document.querySelector('li.ingredient:nth-child(4)'));

                    // Reset scrool
                    list.scrollTop = 0;

                    // Add event listener on new button
                    divIngredientTrash.addEventListener('click', event => {
                        event.preventDefault();
        
                        if (confirm("Voulez vous supprimer l'ingrédient ?")) {
                            // Delete on list
                            var item = document.querySelector(`.ingredient[data-id="${divIngredientTrash.dataset.id}"]`);
                            item.remove();
        
                            // Delete on database
                            const url = `/ingredient/delete/${divIngredientTrash.dataset.id}`;
        
                            var xhr = new XMLHttpRequest();
                            xhr.open('GET', url);
                            xhr.send();
                        }
                    })
                    
                    // Add class active on elements
                    var liAdd = document.querySelector('.ingredient-add');
                    var liAdding = document.querySelector('.ingredient-adding');

                    liAdd.classList.remove('active');
                    liAdding.classList.add('active');
                };
                xhr.send();
            }else {
                confirm("Veuillez renseigner un prix valable")
            }
        })
    }
    
    isInt(val) {
        var intRegex = /^-?\d+$/;
        if (!intRegex.test(val))
            return false;
    
        var intVal = parseInt(val, 10);
        return parseFloat(val) == intVal && !isNaN(intVal);
    }

    isFloat(val) {
        var floatRegex = /^-?\d+(?:[.,]\d*?)?$/;
        if (!floatRegex.test(val))
            return false;
    
        val = parseFloat(val);
        if (isNaN(val))
            return false;
        return true;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const list = document.querySelector('.ingredients');
    const trash = [].slice.call(document.querySelectorAll('.ingredient-trash'));
    const add = document.querySelector('.ingredient-add');

    const cancelAdding = document.querySelector('.ingredient-adding-cancel');
    const addAdding = document.querySelector('.ingredient-adding-add');

    if(list && trash && add && cancelAdding && addAdding) {
        new ListIngredient(list, trash, add, cancelAdding, addAdding);
    }
})