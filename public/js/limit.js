const amountField = document.querySelector('#amount');
const dateField = document.querySelector('#date');
const categoryField = document.querySelector('#category');
const limitBox = document.querySelector('#limit_box');

const limitInfo = document.createElement('p');
const limitValue = document.createElement('p');
const limitLeft = document.createElement('p');

const renderLimitBox = (field, limit = '', monthlyExpenses) => {
    if (!!limit) {
        limitInfo.innerText = `You set the limit ${limit} PLN monthly for that category.`;

        if (limit - monthlyExpenses < 0) {
            limitValue.classList.add('error');
            limitValue.innerText = `You are ${monthlyExpenses - limit} PLN below the limit.`;
        } else {
            limitValue.classList.remove('error');
            limitValue.innerText = `You can spend ${limit - monthlyExpenses} PLN more this month.`;
        }

        
    } else {
        limitInfo.innerText = `The limit for this category has not been set.`;
        limitValue.innerText = '';
    }

    field.appendChild(limitInfo);
    field.appendChild(limitValue);
}

// const renderWarningOnDOM = (field, cashLeft) => {
//     if (cashLeft >= 0) {
//         limitValue.innerText = `After operation you will be ${cashLeft} PLN above the limit.`;

//         if (limitValue.classList.contains('error')) {
//             limitValue.classList.remove('error');
//         }

//         limitValue.classList.add('success');
//         field.insertAdjacentElement('afterend', limitValue);
//     } else {
//         limitValue.innerText = `After operation you will be ${-cashLeft} PLN below the limit.`;

//         if (limitValue.classList.contains('success')) {
//             limitValue.classList.remove('success');
//         }

//         limitValue.classList.add('error');
//         field.insertAdjacentElement('afterend', limitValue);
//     }
// }

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

const getMonthlyExpenses = async () => {
    const category = categoryField.options[categoryField.selectedIndex].value;
    const date = dateField.value;

    if (!!category && !!date) {
        try {
            const res = await fetch(`../api/limitSum/${category}/${date}`);
            const data = await res.json();
            return data;
        } catch (e) {
            console.log('ERROR', e);
        }
    }
}

// const getWarning = () => {
//     const category = categoryField.options[categoryField.selectedIndex].value;
//     const date = dateField.value;
//     const amount = amountField.value;

//     let cashLeft = 0;

//     if (!!category && !!date && !!amount) {
//         fetch(`../api/limitSum/${category}/${date}`)
//         .then(response => response.json())
//         .then(data => {
//             if (data != 0) {
//                 cashLeft = data - amount;
//                 renderWarningOnDOM(amountField, cashLeft);
//             }
//         })
//     } else {
//         limitValue.remove();
//     }
// }

categoryField.addEventListener('change', async () => {
    const limitInfoData = await getLimitForCategory();
    const monthlyExpenses = await getMonthlyExpenses();

    if (limitInfoData === undefined) {
        limitBox.classList.add('hidden');
    } else {
        renderLimitBox(limitBox, limitInfoData, monthlyExpenses);
        limitBox.classList.remove('hidden');
    }
})

dateField.addEventListener('change', async () => {
    const category = categoryField.options[categoryField.selectedIndex].value;

    if (!!category) {
        const limitInfoData = await getLimitForCategory();

        if (!!limitInfoData) {
            const monthlyExpenses = await getMonthlyExpenses();

            if (limitInfoData - monthlyExpenses < 0) {
                limitValue.classList.add('error');
                limitValue.innerText = `You are ${monthlyExpenses - limitInfoData} PLN below the limit.`;
            } else {
                limitValue.classList.remove('error');
                limitValue.innerText = `You can spend ${limitInfoData - monthlyExpenses} PLN more this month.`;
            }
        }
    }
})

// amountField.addEventListener('input', () => {
//     getWarning();
// })