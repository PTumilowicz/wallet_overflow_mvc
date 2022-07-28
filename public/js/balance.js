const rangeChooser = document.querySelector('select[name="period"]');
const startDate = document.querySelector('input[name="start_date"]');
const startDateLabel = document.querySelector('label[for="start_date"]');
const endDate = document.querySelector('input[name="end_date"]');
const endDateLabel = document.querySelector('label[for="end_date"]');

rangeChooser.addEventListener("change", function(e) {
    if (rangeChooser.value === 'custom_period') {
        startDate.classList.remove('hidden');
        startDate.required = true;
        startDateLabel.classList.remove('hidden');
        endDate.classList.remove('hidden');
        endDate.required = true;
        endDateLabel.classList.remove('hidden');
    } else {
        startDate.classList.add('hidden');
        startDate.required = false;
        startDateLabel.classList.add('hidden');
        endDate.classList.add('hidden');
        endDate.required = false;
        endDateLabel.classList.add('hidden');
    }
});