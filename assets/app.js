import './styles/app.css';
import './styles/backend.css';
import './styles/fixedFees.css';
import './styles/dashboard.css';

// start the Stimulus application
import './bootstrap';
import './formWizard';

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







