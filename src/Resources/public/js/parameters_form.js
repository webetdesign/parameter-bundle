function refreshValue(collection) {
    const inputs = collection.querySelectorAll('input.parameter-list-value');
    const inputParameterValue = collection.querySelector('.parameter-value');

    inputParameterValue.value = Array.from(inputs, e => e.value).join(';');
}

function onParameterChange(event) {
    refreshValue(event.target.closest('.parameter-collection'));
}

function addItem(event) {
    const target = event.target;
    const collection = target.closest('.parameter-collection');
    const list = collection.querySelector('.parameter-collection-row');
    const template = collection.querySelector('template');

    const item = template.content.cloneNode(true);
    item.querySelector('.parameter-collection-delete').addEventListener('click', removeItem);
    item.querySelector('input.parameter-list-value').addEventListener('keyup', onParameterChange);

    list.appendChild(item);
}

function removeItem(event) {
    const target = event.target;
    const item = target.closest('.row');
    const collection = item.closest('.parameter-collection');
    item.remove();

    refreshValue(collection);
}

function initListParameters(element) {
    const deleteItems = element.querySelectorAll('.parameter-collection-delete');
    for (let item of deleteItems) {
        item.addEventListener('click', removeItem);
    }

    const inputs = element.querySelectorAll('input.parameter-list-value');
    for (let item of inputs) {
        item.addEventListener('keyup', onParameterChange);
    }

    const add = element.querySelector('.parameter-collection-add');
    add.addEventListener('click', addItem);
}

document.addEventListener("DOMContentLoaded", () => {
    const list = document.getElementsByClassName('parameter-collection');
    for (let item of list) {
        initListParameters(item);
    }
});
