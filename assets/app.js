import './styles/app.css';
import './styles/backend.css';
import './styles/fixedFees.css';

// start the Stimulus application
import './bootstrap';

const addTagFormDeleteLink = (item) => {
    let firstItem = item;

    item = item.querySelector('.prototype');
    
    if(item === null) {
        item = firstItem;
    }
    
    const container = document.createElement('div');
    container.setAttribute('class', 'delete-input');

    const removeFormButton = document.createElement('a');
    
    const deleteImage = document.createElement('i');
    deleteImage.setAttribute('class', 'fa-solid fa-circle-minus moins');

    removeFormButton.appendChild(deleteImage);
    container.appendChild(removeFormButton);

    item.append(container);

    removeFormButton.addEventListener('click', (e) => {
        e.preventDefault();
        item.remove();
    });
}

const addFormToCollection = (e) => {
    const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);

    const item = document.createElement('div');

    item.innerHTML = collectionHolder
        .dataset
        .prototype
        .replace(
        /__name__/g,
        collectionHolder.dataset.index
        );

    collectionHolder.appendChild(item);

    collectionHolder.dataset.index++;

    addTagFormDeleteLink(item);
};

document
.querySelectorAll('.add_item_fixed, .add_item_variable, .add_item_salary')
.forEach(btn => {
    btn.addEventListener("click", addFormToCollection)
});

document
    .querySelectorAll('.prototype')
    .forEach((item) => {
        addTagFormDeleteLink(item)
    })

// code for collapsible menu
var coll = document.getElementsByClassName("collapsible");
var i;

for (i = 0; i < coll.length; i++) {
    coll[i].addEventListener("click", function() {
        this.classList.toggle("active-link");
        var content = this.nextElementSibling;
        if (content.style.display === "block") {
        content.style.display = "none";
        } else {
        content.style.display = "block";
        }
    });
}