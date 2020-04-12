/**
 * List Ingredient class
 */

class ListIngredient {
    constructor(list, edit, trash, add, cancelAdding, addAdding, updateEdit, updateTrash) {
        this.list = list;
        
        if(this.list) {
            this.edit = edit,
            this.trash = trash;
            this.add = add;

            if(this.edit) {
                this.listenOnClickEdit();

                this.updateEdit = updateEdit;
                this.updateTrash = updateTrash;

                if(this.updateEdit && this.updateTrash) {
                    this.listenOnClickUpdateEdit();
                    this.listenOnClickUpdateTrash();
                }
            }

            if(this.trash) {
                this.listenOnClickTrash();
            }

            if(this.add) {
                this.listenOnCLickAdd();
                this.cancelAdding = cancelAdding;
                this.addAdding = addAdding;

                if(this.cancelAdding && this.addAdding) {
                    this.listenOnClickCancelAdding();
                    this.listenOnClickAddAdding();
                }
            }
        }  
    }

    listenOnClickEdit() {
        this.edit.map((edit) => {
            edit.addEventListener('click', event => {
                event.preventDefault();

                var ingredient = document.querySelector(`div.ingredient[data-id="${edit.dataset.id}"]`);
                var ingredientActions = document.querySelector(`.ingredient-actions[data-id="${edit.dataset.id}"]`);
                var ingredientUpdateFields = document.querySelector(`.ingredient-update-fields[data-id="${edit.dataset.id}"]`);
                var ingredientUpdateActions = document.querySelector(`.ingredient-update-actions[data-id="${edit.dataset.id}"]`);

                ingredient.classList.add('active');
                ingredientActions.classList.add('active');
                ingredientUpdateFields.classList.remove('active');
                ingredientUpdateActions.classList.remove('active');
            })
        })
    }

    listenOnClickUpdateEdit() {
        this.updateEdit.map((updateEdit) => {
            updateEdit.addEventListener('click', event => {
                event.preventDefault();

                var name = document.querySelector(`.ingredient-update-name[data-id="${updateEdit.dataset.id}"]`).value;
                var price = document.querySelector(`.ingredient-update-price[data-id="${updateEdit.dataset.id}"]`).value;

                if (confirm("Voulez vous vraiment modifier l'ingrédient ?")) {
                    var ingredient = document.querySelector(`div.ingredient[data-id="${updateEdit.dataset.id}"]`);
                    var ingredientActions = document.querySelector(`.ingredient-actions[data-id="${updateEdit.dataset.id}"]`);
                    var ingredientUpdateFields = document.querySelector(`.ingredient-update-fields[data-id="${updateEdit.dataset.id}"]`);
                    var ingredientUpdateActions = document.querySelector(`.ingredient-update-actions[data-id="${updateEdit.dataset.id}"]`);

                    ingredient.classList.remove('active');
                    ingredientActions.classList.remove('active');
                    ingredientUpdateFields.classList.add('active');
                    ingredientUpdateActions.classList.add('active');

                    ingredient.innerHTML = `${name} au prix de ${price}`;

                    // Encode
                    var encodedName = encodeURIComponent(window.btoa(name));
                    var encodedPrice = encodeURIComponent(window.btoa(price));

                    // Update on database
                    const url = `/ingredient/update/${updateEdit.dataset.id}/${encodedName}/${encodedPrice}`;

                    var xhr = new XMLHttpRequest();
                    xhr.open('GET', url);
                    xhr.send();
                }else {
                    document.querySelector(`.ingredient-update-name[data-id="${updateEdit.dataset.id}"]`).value = updateEdit.dataset.name;
                    document.querySelector(`.ingredient-update-price[data-id="${updateEdit.dataset.id}"]`).value = updateEdit.dataset.price;

                    var ingredient = document.querySelector(`div.ingredient[data-id="${updateEdit.dataset.id}"]`);
                    var ingredientActions = document.querySelector(`.ingredient-actions[data-id="${updateEdit.dataset.id}"]`);
                    var ingredientUpdateFields = document.querySelector(`.ingredient-update-fields[data-id="${updateEdit.dataset.id}"]`);
                    var ingredientUpdateActions = document.querySelector(`.ingredient-update-actions[data-id="${updateEdit.dataset.id}"]`);

                    ingredient.classList.remove('active');
                    ingredientActions.classList.remove('active');
                    ingredientUpdateFields.classList.add('active');
                    ingredientUpdateActions.classList.add('active');
                }
            })
        })
    }

    listenOnClickUpdateTrash() {
        this.updateTrash.map((updateTrash) => {
            updateTrash.addEventListener('click', event => {
                event.preventDefault();

                document.querySelector(`.ingredient-update-name[data-id="${updateTrash.dataset.id}"]`).value = updateTrash.dataset.name;
                document.querySelector(`.ingredient-update-price[data-id="${updateTrash.dataset.id}"]`).value = updateTrash.dataset.price;

                var ingredient = document.querySelector(`div.ingredient[data-id="${updateTrash.dataset.id}"]`);
                var ingredientActions = document.querySelector(`.ingredient-actions[data-id="${updateTrash.dataset.id}"]`);
                var ingredientUpdateFields = document.querySelector(`.ingredient-update-fields[data-id="${updateTrash.dataset.id}"]`);
                var ingredientUpdateActions = document.querySelector(`.ingredient-update-actions[data-id="${updateTrash.dataset.id}"]`);

                ingredient.classList.remove('active');
                ingredientActions.classList.remove('active');
                ingredientUpdateFields.classList.add('active');
                ingredientUpdateActions.classList.add('active');
            })
        })
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

            document.querySelector('.ingredient-adding-name').value = "";
            document.querySelector('.ingredient-adding-price').value = "";

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

                    // Div ingredient
                    var divIngredient = document.createElement('div');
                    divIngredient.classList.add('ingredient');
                    divIngredient.dataset.id = id;
                    divIngredient.innerHTML = response["name"] + " au prix de " + response["price"];

                    // Div actions
                    var divIngredientEdit = document.createElement('div');
                    divIngredientEdit.classList.add('ingredient-edit');
                    divIngredientEdit.dataset.id = id;
                    var spanIngredientEdit = document.createElement('span');
                    spanIngredientEdit.innerHTML = "Modifier";
                    divIngredientEdit.appendChild(spanIngredientEdit);

                    var divIngredientTrash = document.createElement('div');
                    divIngredientTrash.classList.add('ingredient-trash');
                    divIngredientTrash.dataset.id = id;
                    var spanIngredientTrash = document.createElement('span');
                    spanIngredientTrash.innerHTML = "Supprimer";
                    divIngredientTrash.appendChild(spanIngredientTrash);

                    var divIngredientActions = document.createElement('div');
                    divIngredientActions.dataset.id = id;
                    divIngredientActions.classList.add('ingredient-actions');

                    // Div ingredient update fields
                    var inputUpdateName = document.createElement('input');
                    inputUpdateName.type = "text";
                    inputUpdateName.classList.add('ingredient-update-name');
                    inputUpdateName.value = response["name"];
                    inputUpdateName.dataset.id = id;
                    inputUpdateName.placeholder = "Nom de l'ingrédient";

                    var inputUpdatePrice = document.createElement('input');
                    inputUpdatePrice.type = "text";
                    inputUpdatePrice.classList.add('ingredient-update-price');
                    inputUpdatePrice.value = response["price"];
                    inputUpdatePrice.dataset.id = id;
                    inputUpdatePrice.placeholder = "Prix de l'ingrédient";

                    var divUpdateFields = document.createElement('div');
                    divUpdateFields.classList.add('ingredient-update-fields');
                    divUpdateFields.classList.add('active');
                    divUpdateFields.dataset.id = id;

                    // Div ingredient update actions
                    var divUpdateEdit = document.createElement('div');
                    divUpdateEdit.classList.add('ingredient-update-edit');
                    divUpdateEdit.dataset.id = id;
                    divUpdateEdit.dataset.name = response["name"];
                    divUpdateEdit.dataset.price = response["price"];
                    var spanUpdateEdit = document.createElement('span');
                    spanUpdateEdit.innerHTML = "Modifier";
                    divUpdateEdit.appendChild(spanUpdateEdit);

                    var divUpdateTrash = document.createElement('div');
                    divUpdateTrash.classList.add('ingredient-update-trash');
                    divUpdateTrash.dataset.id = id;
                    divUpdateTrash.dataset.name = response["name"];
                    divUpdateTrash.dataset.price = response["price"];
                    var spanUpdateTrash = document.createElement('span');
                    spanUpdateTrash.innerHTML = "Annuler";
                    divUpdateTrash.appendChild(spanUpdateTrash);

                    var divUpdateActions = document.createElement('div');
                    divUpdateActions.classList.add('ingredient-update-actions');
                    divUpdateActions.classList.add('active');
                    divUpdateActions.dataset.id = id;

                    // Append children
                    divIngredientActions.appendChild(divIngredientEdit);
                    divIngredientActions.appendChild(divIngredientTrash);
                    divUpdateFields.appendChild(inputUpdateName);
                    divUpdateFields.appendChild(inputUpdatePrice);
                    divUpdateActions.appendChild(divUpdateEdit);
                    divUpdateActions.appendChild(divUpdateTrash);
                    li.appendChild(divIngredient);
                    li.appendChild(divIngredientActions);
                    li.appendChild(divUpdateFields);
                    li.appendChild(divUpdateActions);

                    var list = document.querySelector('.ingredients');
                    list.insertBefore(li, document.querySelector('li.ingredient:nth-child(3)'));

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

                    divIngredientEdit.addEventListener('click', event => {
                        event.preventDefault();
        
                        var ingredient = document.querySelector(`div.ingredient[data-id="${divIngredientEdit.dataset.id}"]`);
                        var ingredientActions = document.querySelector(`.ingredient-actions[data-id="${divIngredientEdit.dataset.id}"]`);
                        var ingredientUpdateFields = document.querySelector(`.ingredient-update-fields[data-id="${divIngredientEdit.dataset.id}"]`);
                        var ingredientUpdateActions = document.querySelector(`.ingredient-update-actions[data-id="${divIngredientEdit.dataset.id}"]`);
        
                        ingredient.classList.add('active');
                        ingredientActions.classList.add('active');
                        ingredientUpdateFields.classList.remove('active');
                        ingredientUpdateActions.classList.remove('active');
                    })

                    divUpdateEdit.addEventListener('click', event => {
                        event.preventDefault();
        
                        var name = document.querySelector(`.ingredient-update-name[data-id="${divUpdateEdit.dataset.id}"]`).value;
                        var price = document.querySelector(`.ingredient-update-price[data-id="${divUpdateEdit.dataset.id}"]`).value;
        
                        if (confirm("Voulez vous vraiment modifier l'ingrédient ?")) {
                            var ingredient = document.querySelector(`div.ingredient[data-id="${divUpdateEdit.dataset.id}"]`);
                            var ingredientActions = document.querySelector(`.ingredient-actions[data-id="${divUpdateEdit.dataset.id}"]`);
                            var ingredientUpdateFields = document.querySelector(`.ingredient-update-fields[data-id="${divUpdateEdit.dataset.id}"]`);
                            var ingredientUpdateActions = document.querySelector(`.ingredient-update-actions[data-id="${divUpdateEdit.dataset.id}"]`);
        
                            ingredient.classList.remove('active');
                            ingredientActions.classList.remove('active');
                            ingredientUpdateFields.classList.add('active');
                            ingredientUpdateActions.classList.add('active');
        
                            ingredient.innerHTML = `${name} au prix de ${price}`;
        
                            // Encode
                            var encodedName = encodeURIComponent(window.btoa(name));
                            var encodedPrice = encodeURIComponent(window.btoa(price));
        
                            // Update on database
                            const url = `/ingredient/update/${divUpdateEdit.dataset.id}/${encodedName}/${encodedPrice}`;
        
                            var xhr = new XMLHttpRequest();
                            xhr.open('GET', url);
                            xhr.send();
                        }else {
                            document.querySelector(`.ingredient-update-name[data-id="${divUpdateEdit.dataset.id}"]`).value = divUpdateEdit.dataset.name;
                            document.querySelector(`.ingredient-update-price[data-id="${divUpdateEdit.dataset.id}"]`).value = divUpdateEdit.dataset.price;
        
                            var ingredient = document.querySelector(`div.ingredient[data-id="${divUpdateEdit.dataset.id}"]`);
                            var ingredientActions = document.querySelector(`.ingredient-actions[data-id="${divUpdateEdit.dataset.id}"]`);
                            var ingredientUpdateFields = document.querySelector(`.ingredient-update-fields[data-id="${divUpdateEdit.dataset.id}"]`);
                            var ingredientUpdateActions = document.querySelector(`.ingredient-update-actions[data-id="${divUpdateEdit.dataset.id}"]`);
        
                            ingredient.classList.remove('active');
                            ingredientActions.classList.remove('active');
                            ingredientUpdateFields.classList.add('active');
                            ingredientUpdateActions.classList.add('active');
                        }
                    })

                    divUpdateTrash.addEventListener('click', event => {
                        event.preventDefault();
        
                        document.querySelector(`.ingredient-update-name[data-id="${divUpdateTrash.dataset.id}"]`).value = divUpdateTrash.dataset.name;
                        document.querySelector(`.ingredient-update-price[data-id="${divUpdateTrash.dataset.id}"]`).value = divUpdateTrash.dataset.price;
        
                        var ingredient = document.querySelector(`div.ingredient[data-id="${divUpdateTrash.dataset.id}"]`);
                        var ingredientActions = document.querySelector(`.ingredient-actions[data-id="${divUpdateTrash.dataset.id}"]`);
                        var ingredientUpdateFields = document.querySelector(`.ingredient-update-fields[data-id="${divUpdateTrash.dataset.id}"]`);
                        var ingredientUpdateActions = document.querySelector(`.ingredient-update-actions[data-id="${divUpdateTrash.dataset.id}"]`);
        
                        ingredient.classList.remove('active');
                        ingredientActions.classList.remove('active');
                        ingredientUpdateFields.classList.add('active');
                        ingredientUpdateActions.classList.add('active');
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

class Recipe {
    constructor(recipeDelete) {
        this.recipeDelete = recipeDelete;

        if(this.recipeDelete) {
            this.onClickRecipeDelete();
        }
    }

    onClickRecipeDelete() {
        this.recipeDelete.map((recipeDelete) => {
            recipeDelete.addEventListener('click', event => {
                event.preventDefault();
                
                if (confirm("Voulez vous supprimer la recette ?")) {
                    // Delete on list
                    var item = document.querySelector(`div.recipe.card[data-id="${recipeDelete.dataset.id}"]`);
                    item.remove();

                    // Delete on database
                    const url = `/recipe/delete/${recipeDelete.dataset.id}`;

                    console.log(recipeDelete.dataset.id);

                    var xhr = new XMLHttpRequest();
                    xhr.open('GET', url);
                    xhr.send();
                }
            })
        })
    }
}

class AddRecipe {
    constructor(addRecipeName, addRecipeIngredients, addRecipeInstructions, addRecipeTime, addRecipeDifficulty, addRecipeShared, addRecipePrice, addRecipeSubmit) {
        this.addRecipeName = addRecipeName;
        this.addRecipeIngredients = addRecipeIngredients;
        this.addRecipeInstructions = addRecipeInstructions;
        this.addRecipeTime = addRecipeTime;
        this.addRecipeDifficulty = addRecipeDifficulty;
        this.addRecipeShared = addRecipeShared;
        this.addRecipePrice = addRecipePrice;
        this.addRecipeSubmit = addRecipeSubmit;

        if(this.addRecipeIngredients) {
            this.listenOnChangeSelectIngredients();
        }

        if(this.addRecipeSubmit) {
            this.listenOnClickRecipeSubmit();
        }
    }

    listenOnChangeSelectIngredients() {
        this.addRecipeIngredients.addEventListener('change', event => {
            event.preventDefault();

            if(confirm("Ajouter cet ingrédient ?")) {
                var optionSelected = document.querySelectorAll('.option-ingredients')[this.addRecipeIngredients.value];
                optionSelected.disabled = "disabled";

                // Set price
                this.addRecipePrice.value = parseFloat(this.addRecipePrice.value) + parseFloat(optionSelected.dataset.price);

                // Construct li
                var div = document.createElement('div');
                div.innerHTML = optionSelected.dataset.name;

                var a = document.createElement('a');
                a.classList.add('btn');
                a.classList.add('btn-danger');
                a.classList.add('ml-3');
                a.href = "#";
                a.innerHTML = " X ";
                a.dataset.id = optionSelected.dataset.id;

                var divActions = document.createElement('div');
                divActions.appendChild(a);
                divActions.dataset.id = optionSelected.dataset.id;
                divActions.dataset.price = optionSelected.dataset.price;

                var li = document.createElement('li');
                li.classList.add('choosen-ingredient');
                li.dataset.id = optionSelected.dataset.id;
                li.dataset.name = optionSelected.dataset.name;
                li.dataset.price = optionSelected.dataset.price;
                li.appendChild(div);
                li.appendChild(divActions);

                var list = document.querySelector('ul.choosen-ingredients');
                list.appendChild(li);

                divActions.addEventListener('click', event => {
                    event.preventDefault();
                    
                    if (confirm("Voulez vous supprimer cet ingrédient ?")) {
                        // Remove from list
                        var li = document.querySelector(`.choosen-ingredient[data-id="${divActions.dataset.id}"]`);
                        li.remove();

                        // Set price
                        this.addRecipePrice.value = parseFloat(this.addRecipePrice.value) - parseFloat(divActions.dataset.price);

                        // Remove disabled
                        var optionSelected = document.querySelector(`.option-ingredients[data-id="${divActions.dataset.id}"]`);
                        console.log(optionSelected);
                        optionSelected.disabled = "";
                    }
                })
            }
        })
    }

    listenOnClickRecipeSubmit() {
        this.addRecipeSubmit.addEventListener('click', event => {
            event.preventDefault();
            
            // Get value of all fields
            var name = this.addRecipeName.value;
            var ingredients = [].slice.call(document.querySelectorAll('.choosen-ingredient'));
            var instructions = this.addRecipeInstructions.value;
            var time = this.addRecipeTime.value;
            var difficulty = this.addRecipeDifficulty.value;
            var shared = this.addRecipeShared.value === "checked" ? 1 : 0;
            var price = 0;
            ingredients.forEach(element => {
                price = price + parseFloat(element.dataset.price);
            })

            // Get all ID of selected ingrdients
            var ingredientsId = [];
            ingredients.forEach(element => {
                ingredientsId.push(element.dataset.id)
            })

            // Get all value of selected fields
            var data = {
                'name': name,
                'instructions': instructions,
                'time': time,
                'difficulty': difficulty,
                'shared': shared,
                'price': price
            }

            var jsonIngredientsId = JSON.stringify(ingredientsId);
            var jsonData = JSON.stringify(data);

            // Encode all data
            var encodedIngredientsId = encodeURIComponent(window.btoa(jsonIngredientsId));
            var encodedData = encodeURIComponent(window.btoa(jsonData));

            // Create recipe on database
            const url = `/recipe/create/${encodedIngredientsId}/${encodedData}`;

            var xhr = new XMLHttpRequest();
            xhr.open('GET', url);
            xhr.send();

            // Go to list of all recipes
            window.location.href = `/recipes`;
        })
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const list = document.querySelector('.ingredients');
    const edit = [].slice.call(document.querySelectorAll('.ingredient-edit'));
    const trash = [].slice.call(document.querySelectorAll('.ingredient-trash'));
    const add = document.querySelector('.ingredient-add');

    const cancelAdding = document.querySelector('.ingredient-adding-cancel');
    const addAdding = document.querySelector('.ingredient-adding-add');
    const updateEdit = [].slice.call(document.querySelectorAll('.ingredient-update-edit'));
    const updateTrash = [].slice.call(document.querySelectorAll('.ingredient-update-trash'));

    if(list && edit && trash && add && cancelAdding && addAdding && updateEdit && updateTrash) {
        new ListIngredient(list, edit, trash, add, cancelAdding, addAdding, updateEdit, updateTrash);
    }

    const recipeDelete = [].slice.call(document.querySelectorAll('.recipe-delete'));

    if(recipeDelete) {
        new Recipe(recipeDelete);
    }

    const addRecipeName = document.querySelector('.form-control.name');
    const addRecipeIngredients = document.querySelector('.form-control.recipe-ingredients');
    const addRecipeInstructions = document.querySelector('.form-control.instructions');
    const addRecipeTime = document.querySelector('.form-control.time');
    const addRecipeDifficulty = document.querySelector('.form-control.difficulty');
    const addRecipeShared = document.querySelector('.form-check-input.shared');
    const addRecipePrice = document.querySelector('.form-control.price');
    const addRecipeSubmit = document.querySelector('.form-control.submit');

    if(addRecipeName && addRecipeIngredients && addRecipeInstructions && addRecipeTime && addRecipeDifficulty && addRecipeShared && addRecipePrice && addRecipeSubmit) {
        new AddRecipe(addRecipeName, addRecipeIngredients, addRecipeInstructions, addRecipeTime, addRecipeDifficulty, addRecipeShared, addRecipePrice, addRecipeSubmit);
    }
})