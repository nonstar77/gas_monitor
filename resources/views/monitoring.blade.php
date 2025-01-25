<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Sensor</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e0f7fa;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #00796b;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            background-color: #ffffff;
        }
        th, td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #00796b;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f1f1f1;
        }
        tr:hover {
            background-color: #b2dfdb;
            transition: background-color 0.3s ease;
        }
    </style>
</head>
<body>
    <h1>Monitoring Data Sensor</h1>
    <table id="sensor-table" border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Gas Value</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data akan dimasukkan secara dinamis menggunakan JavaScript -->
        </tbody>
    </table>

    <script>
        // Fungsi untuk mengambil data sensor dari server
        function fetchSensorData() {
            fetch('http://192.168.18.45:8000/api/sensor')  // Ganti dengan URL API Laravel Anda
                .then(response => response.json())
                .then(data => {
                    // Ambil elemen tabel dan reset isinya
                    const tableBody = document.querySelector('#sensor-table tbody');
                    tableBody.innerHTML = ''; // Kosongkan isi tabel sebelum memasukkan data baru

                    // Masukkan data ke dalam tabel
                    data.forEach(item => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${item.id}</td>
                            <td>${item.gas_value}</td>
                            <td>${item.created_at}</td>
                        `;
                        tableBody.appendChild(row);
                    });
                })
                .catch(error => {
                    console.error('Error fetching sensor data:', error);
                });
        }

        // Panggil fungsi fetchSensorData saat pertama kali halaman dimuat
        fetchSensorData();

        // Perbarui data setiap 1 detik
        setInterval(fetchSensorData, 1000);
    </script>
</body>
</html>
