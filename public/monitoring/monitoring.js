// Data untuk Chart dengan 3 subjek
let chartData = {
    labels: [], // Timestamp
    datasets: [
        {
            label: 'Gas Value MQ4',
            data: [],
            borderColor: '#1e90ff',
            backgroundColor: 'rgba(30, 144, 255, 0.5)',
            fill: true,
            tension: 0.5,
        },
        {
            label: 'Gas Value MQ6',
            data: [],
            borderColor: '#ab1111',
            backgroundColor: 'rgba(171, 17, 17, 0.5)',
            fill: true,
            tension: 0.5,
        },
        {
            label: 'Gas Value MQ8',
            data: [],
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
    type: 'line',
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
    fetch('http://127.0.0.1:8000/api/sensor')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(jsonData => {
            const tableBody = document.querySelector('#sensor-table tbody');
            tableBody.innerHTML = '';
            if (jsonData.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="5">No data available</td></tr>';
                return;
            }

            chartData.labels = [];
            chartData.datasets[0].data = [];
            chartData.datasets[1].data = [];
            chartData.datasets[2].data = [];

            jsonData.forEach(item => {
                const date = new Date(item.created_at);
                const formattedTimestamp = date.toLocaleString('en-US', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false,
                    timeZone: 'Asia/Jakarta',
                });

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.id}</td>
                    <td>${item.gas_value_mq4}</td>
                    <td>${item.gas_value_mq6}</td>
                    <td>${item.gas_value_mq8}</td>
                    <td>${formattedTimestamp}</td>
                `;
                tableBody.appendChild(row);

                chartData.labels.push(formattedTimestamp);
                chartData.datasets[0].data.push(item.gas_value_mq4);
                chartData.datasets[1].data.push(item.gas_value_mq6);
                chartData.datasets[2].data.push(item.gas_value_mq8);
            });

            gasChart.update();
        })
        .catch(error => console.error('Error fetching sensor data:', error));
}

// Ambil data saat halaman dimuat
fetchSensorData();

// Perbarui data setiap 1 detik
setInterval(fetchSensorData, 1000);
