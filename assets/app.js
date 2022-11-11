import './styles/app.css';
import './styles/backend.css';

// start the Stimulus application
import './bootstrap';

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
};

document
.querySelectorAll('.add_item_fixed, .add_item_variable')
.forEach(btn => {
    btn.addEventListener("click", addFormToCollection)
});