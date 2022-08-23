const amountField = document.querySelector('#amount');
const dateField = document.querySelector('#date');
const categoryField = document.querySelector('#category');
const limitInfo = document.createElement('span');

const renderOnDOM = (field, limit) => {
    if (!!limit) {
        limitInfo.innerText = `You set the limit ${limit} PLN monthly for that category.`;
        field.insertAdjacentElement('afterend', limitInfo);
    } else {
        limitInfo.remove();
    } 
}

const getLimitForCategory = () => {
    const category = categoryField.options[categoryField.selectedIndex].value;

    fetch(`../api/limit/${category}`)
    .then(response => response.json())
    .then(data => {
        renderOnDOM(categoryField, data);
    });
}

const getWarning = () => {
    const category = categoryField.options[categoryField.selectedIndex].value;
    const date = dateField.value;

    if (!!category && !!date) {
        fetch(`../api/limitSum/${category}/${date}`)
        .then(response => response.json())
        .then(data => {
            console.log(data);
        })
    }
}

categoryField.addEventListener('change', () => {
    getLimitForCategory();
})

amountField.addEventListener('change', () => {
    getWarning();
})