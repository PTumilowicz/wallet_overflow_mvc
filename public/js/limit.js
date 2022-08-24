const amountField = document.querySelector('#amount');
const dateField = document.querySelector('#date');
const categoryField = document.querySelector('#category');
const limitInfo = document.createElement('span');
const limitValue = document.createElement('span');

const renderLimitOnDOM = (field, limit) => {
    if (!!limit) {
        limitInfo.innerText = `You set the limit ${limit} PLN monthly for that category.`;
        field.insertAdjacentElement('afterend', limitInfo);
    }
}

const renderWarningOnDOM = (field, cashLeft) => {
    if (cashLeft >= 0) {
        limitValue.innerText = `After operation you will be ${cashLeft} PLN above the limit.`;

        if (limitValue.classList.contains('error')) {
            limitValue.classList.remove('error');
        }

        limitValue.classList.add('success');
        field.insertAdjacentElement('afterend', limitValue);
    } else {
        limitValue.innerText = `After operation you will be ${-cashLeft} PLN below the limit.`;

        if (limitValue.classList.contains('success')) {
            limitValue.classList.remove('success');
        }

        limitValue.classList.add('error');
        field.insertAdjacentElement('afterend', limitValue);
    }
}

const getLimitForCategory = () => {
    const category = categoryField.options[categoryField.selectedIndex].value;

    if (!!category) {
        fetch(`../api/limit/${category}`)
        .then(response => response.json())
        .then(data => {
            renderLimitOnDOM(categoryField, data);
        });
    } else {
        limitInfo.remove();
    }
}

const getWarning = () => {
    const category = categoryField.options[categoryField.selectedIndex].value;
    const date = dateField.value;
    const amount = amountField.value;

    let cashLeft = 0;

    if (!!category && !!date && !!amount) {
        fetch(`../api/limitSum/${category}/${date}`)
        .then(response => response.json())
        .then(data => {
            cashLeft = data - amount;
            renderWarningOnDOM(amountField, cashLeft);
        })
    } else {
        limitValue.remove();
    }
}

categoryField.addEventListener('change', () => {
    getLimitForCategory();
})

amountField.addEventListener('input', () => {
    getWarning();
})