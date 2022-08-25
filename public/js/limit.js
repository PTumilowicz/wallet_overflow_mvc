const amountField = document.querySelector('#amount');
const dateField = document.querySelector('#date');
const categoryField = document.querySelector('#category');
const limitBox = document.querySelector('#limit_box');

const limitInfo = document.createElement('span');
const limitValue = document.createElement('span');
const limitLeft = document.createElement('span');

const renderLimitInfo = (field, limit = '') => {
    if (!!limit) {
        limitInfo.innerText = `You set the limit ${limit} PLN monthly for that category.`;
    } else {
        limitInfo.innerText = `The limit for this category has not been set.`;
    }

    field.appendChild(limitInfo);

    if (limitBox.classList.contains('hidden')) limitBox.classList.toggle('hidden');
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

const getLimitForCategory = async () => {
    const category = categoryField.options[categoryField.selectedIndex].value;

    if (!!category) {
        try {
            const res = await fetch(`../api/limit/${category}`);
            const data = await res.json();
            return data;
        } catch (e) {
            console.log('ERROR', e);
        }
    } else {
        limitBox.classList.add('hidden');
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
            if (data != 0) {
                cashLeft = data - amount;
                renderWarningOnDOM(amountField, cashLeft);
            }
        })
    } else {
        limitValue.remove();
    }
}

categoryField.addEventListener('change', async () => {
    const limitInfoData = await getLimitForCategory();
    renderLimitInfo(limitBox, limitInfoData);
})

amountField.addEventListener('input', () => {
    getWarning();
})