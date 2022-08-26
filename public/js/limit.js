// Const declarations.
const amountField = document.querySelector('#amount');
const dateField = document.querySelector('#date');
const categoryField = document.querySelector('#category');

const limitInfoBox = document.querySelector('#limit_info_box');
const limitValueBox = document.querySelector('#limit_value_box');
const limitLeftBox = document.querySelector('#limit_left_box');

const limitInfo = document.createElement('p');
const limitValue = document.createElement('p');
const limitLeft = document.createElement('p');

// Rendering alert boxes.
const renderInfoBox = (field, limit) => {
    if (!!limit) {
        limitInfo.innerText = `You set the limit ${limit.toFixed(2)} PLN monthly for that category.`;      
    } else {
        limitInfo.innerText = `The limit for this category has not been set.`;
    }

    field.appendChild(limitInfo);
}

const renderValueBox = (field, monthlyExpenses) => {
    if (!!monthlyExpenses) {
        limitValue.innerText = `You spent ${monthlyExpenses} PLN this month for that category.`;   
    } else {
        limitValue.innerText = `You did not spend any money for this category this month!`;
    }

    field.appendChild(limitValue);
}

const renderLeftBox = (field, limitInfoData, monthlyExpenses, amount) => {
    limitLeft.innerText = `Limit balance after operation: ${(limitInfoData - monthlyExpenses - amount).toFixed(2)} PLN`;   

    field.appendChild(limitLeft);
}

// Async fetch funtcions.
const getLimitForCategory = async (category) => {
    try {
        const res = await fetch(`../api/limit/${category}`);
        const data = await res.json();
        return data;
    } catch (e) {
        console.log('ERROR', e);
    }
}

const getMonthlyExpenses = async (category, date) => {
    try {
        const res = await fetch(`../api/limitSum/${category}/${date}`);
        const data = await res.json();
        return data;
    } catch (e) {
        console.log('ERROR', e);
    }
}

// Events logic.
const eventsAction = async (category, date, amount) => {
    if (!!category) {
        const limitInfoData = await getLimitForCategory(category);
        renderInfoBox(limitInfoBox, limitInfoData);
        limitInfoBox.classList.remove('hidden');

        if (!!date) {
            const monthlyExpenses = await getMonthlyExpenses(category, date);
            renderValueBox(limitValueBox, monthlyExpenses);
            limitValueBox.classList.remove('hidden');

            if (!!amount) {
                renderLeftBox(limitLeftBox, limitInfoData, monthlyExpenses, amount);
                limitLeftBox.classList.remove('hidden');
            } else {
                limitLeftBox.classList.add('hidden');
            }
        } else {
            limitValueBox.classList.add('hidden');
            limitLeftBox.classList.add('hidden');
        }
    } else {
        limitInfoBox.classList.add('hidden');
        limitValueBox.classList.add('hidden');
        limitLeftBox.classList.add('hidden');
    }
}

// Event listeners.
categoryField.addEventListener('change', async () => {
    const category = categoryField.options[categoryField.selectedIndex].value;
    const date = dateField.value;
    const amount = amountField.value;

    eventsAction(category, date, amount);
})

dateField.addEventListener('change', async () => {
    const category = categoryField.options[categoryField.selectedIndex].value;
    const date = dateField.value;
    const amount = amountField.value;

    eventsAction(category, date, amount);
})

amountField.addEventListener('input', async () => {
    const category = categoryField.options[categoryField.selectedIndex].value;
    const date = dateField.value;
    const amount = amountField.value;

    eventsAction(category, date, amount);
})