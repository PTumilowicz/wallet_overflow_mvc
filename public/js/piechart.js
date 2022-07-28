google.charts.load('current', {'packages':['corechart']});

google.charts.setOnLoadCallback(drawFirstChart);
google.charts.setOnLoadCallback(drawSecondPiechart);

function drawFirstChart() {
    const data = google.visualization.arrayToDataTable([
        ['Category', 'Income Amount'],
        // <?php
        //     foreach ($incomeBalance as $income) {
        //         echo "['".$income['name']."', ".number_format($income['incomeSum'], 2, '.', '')."],";
        //     }
        // ?>
    ]);

    const options = {
        title: 'Income Balance',
        is3D: true,
        backgroundColor: 'transparent',
        colors: ['#344E41', '#3A5A40', '#588157', '#A3B18A', '#DAD7CD'],
    };

    const chart = new google.visualization.PieChart(document.getElementById('incomePiechart'));
    chart.draw(data, options);
}

function drawSecondPiechart() {
    const data = google.visualization.arrayToDataTable([
        ['Category', 'Income Amount'],
        // <?php
        //     foreach ($expenseBalance as $expense) {
        //         echo "['".$expense['name']."', ".number_format($expense['expenseSum'], 2, '.', '')."],";
        //     }
        // ?>
    ]);

    const options = {
        title: 'Expense Balance',
        is3D: true,
        backgroundColor: 'transparent',
        colors: ['#F25C54', '#F27059', '#F4845F', '#F79D65', '#F7B267'],
    };

    const chart = new google.visualization.PieChart(document.getElementById('expensePiechart'));
    chart.draw(data, options);
}