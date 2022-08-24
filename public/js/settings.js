const categoryField = document.querySelector('#expense_category_select');
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

    if (!!category) {
        fetch(`../api/limit/${category}`)
        .then(response => response.json())
        .then(data => {
            renderOnDOM(categoryField, data);
        });
    } else {
        limitInfo.remove();
    }
}

categoryField.addEventListener('change', () => {
    getLimitForCategory();
})