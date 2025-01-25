<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Sensor</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #121212;
            color: #ffffff;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }
        .navbar {
            background-color: #ab1111;
            width: 100%;
            padding: 14px 0;
            text-align: center;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
        }
        .navbar a {
            color: white;
            padding: 14px 20px;
            text-decoration: none;
            text-transform: uppercase;
            font-size: 1.2rem;
            margin: 0 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .navbar a:hover {
            background-color: #b13737;
        }
        h1 {
            text-align: center;
            color: #b51d1d;
            margin-top: 80px;
            font-size: 2.5rem;
        }
        .table-container {
            width: 90%;
            max-width: 900px;
            margin: 20px auto;
            background: #1e1e1e;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.7);
            overflow: hidden;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #333333;
        }
        th {
            background-color: #ab1111;
            color: #ffffff;
        }
        .chart-container {
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="navbar">
        <a href="/">Monitoring</a>
        <a href="/">Settings</a>
        <a href="/">Help</a>
    </div>

    <h1>Monitoring Data Sensor</h1>
    <div class="chart-container">
        <canvas id="gasChart"></canvas>
    </div>
    <div class="table-container">
        <table id="sensor-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Gas Value MQ4</th>
                    <th>Gas Value MQ6</th>
                    <th>Gas Value MQ8</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="loading">Loading data...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        // Data untuk Chart dengan 3 subjek
        let chartData = {
            labels: [], // Timestamp
            datasets: [
                {
                    label: 'Gas Value MQ4',
                    data: [], // Nilai gas untuk MQ4
                    borderColor: '#1e90ff',
                    backgroundColor: 'rgba(30, 144, 255, 0.5)',
                    fill: true,
                    tension: 0.5,
                },
                {
                    label: 'Gas Value MQ6',
                    data: [], // Nilai gas untuk MQ6
                    borderColor: '#ab1111',
                    backgroundColor: 'rgba(171, 17, 17, 0.5)',
                    fill: true,
                    tension: 0.5,
                },
                {
                    label: 'Gas Value MQ8',
                    data: [], // Nilai gas untuk MQ8
                    borderColor: '#32cd32',
                    backgroundColor: 'rgba(50, 205, 50, 0.5)',
                    fill: true,
                    tension: 0.5,
                }
            ]
        };

        // Konfigurasi Chart.js
        const ctx = document.getElementById('gasChart').getContext('2d');
        const gasChart = new Chart(ctx, {
            type: 'line', // Tipe chart (line chart)
            data: chartData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        labels: { color: '#ffffff' }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: '#ffffff' }
                    },
                    y: {
                        ticks: { color: '#ffffff' },
                        beginAtZero: true
                    }
                }
            }
        });

        function fetchSensorData() {
            fetch('http://127.0.0.1:8000/api/sensor')  // Ganti dengan URL API Laravel Anda
                .then(response => {
                    console.log('Response status:', response.status);  // Log response status

                    // Check if the response is OK (status 200-299)
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.statusText);
                    }

                    return response.text();  // Get the response as text to inspect it first
                })
                .then(data => {
                    console.log('Raw response data:', data);  // Log the raw response

                    try {
                        const jsonData = JSON.parse(data);  // Parse the raw data into JSON
                        console.log('Parsed JSON data:', jsonData);

                        // Perbarui tabel
                        const tableBody = document.querySelector('#sensor-table tbody');
                        tableBody.innerHTML = '';
                        if (jsonData.length === 0) {
                            tableBody.innerHTML = '<tr><td colspan="5">No data available</td></tr>';
                            return;
                        }

                        // Perbarui data chart
                        chartData.labels = [];
                        chartData.datasets[0].data = [];
                        chartData.datasets[1].data = [];
                        chartData.datasets[2].data = [];

                        jsonData.forEach(item => {
                            // Format timestamp menjadi hanya tanggal dan waktu
                            const date = new Date(item.created_at);
                            const formattedTimestamp = date.toLocaleString('en-US', {
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit',
                                hour12: false, // Tampilkan dalam format 24 jam
                                timeZone: 'Asia/Jakarta',
                            });

                            // Tambahkan data ke tabel
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${item.id}</td>
                                <td>${item.gas_value_mq4}</td>
                                <td>${item.gas_value_mq6}</td>
                                <td>${item.gas_value_mq8}</td>
                                <td>${formattedTimestamp}</td>
                            `;
                            tableBody.appendChild(row);

                            // Tambahkan data ke chart untuk 3 subjek
                            chartData.labels.push(formattedTimestamp);
                            chartData.datasets[1].data.push(item.gas_value_mq4);  // MQ4
                            chartData.datasets[0].data.push(item.gas_value_mq6);  // MQ6
                            chartData.datasets[2].data.push(item.gas_value_mq8);  // MQ8
                        });

                        // Perbarui chart
                        gasChart.update();
                    } catch (error) {
                        console.error('Failed to parse JSON:', error);
                    }
                })
                .catch(error => {
                    console.error('Error fetching sensor data:', error);
                });
        }


        // Ambil data saat halaman dimuat
        fetchSensorData();

        // Perbarui data setiap 1 detik
        setInterval(fetchSensorData, 1000);
    </script>

</body>
</html>
