<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Biểu đồ thống kê</title>
</head>
<style>
    .container {
        display: flex;
        justify-content: space-between;
        align-items: center
    }
    @media (max-width: 980px) {
        .container {
            flex-direction: column;
        }
    }
</style>
<body>
    <div class="container">
        <div>
            <canvas id="myChart" width="400" height="400"></canvas>
        </div>
        <div>
            <canvas id="myBarChart" width="400" height="400"></canvas>
        </div>
    </div>
    <script>
        $(function () {
            var ctx = document.getElementById("myChart").getContext('2d');
            var chartPieData = @json($chartPieData);
            var myChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: chartPieData.labels,
                    datasets: [{
                        label: '# of Votes',
                        data: chartPieData.data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255,99,132,1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    }
                }
            });
            var chartLineData = @json($chartLineData);
            var ctxBar = document.getElementById("myBarChart").getContext('2d');
            var barChartData = {
                labels: chartLineData.labels,
                datasets: [{
                    label: '# of Votes',
                    data: chartLineData.data,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            };
            var myBarChart = new Chart(ctxBar, {
                type: 'bar',
                data: barChartData,
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        });
    </script>
</body>
</html>
