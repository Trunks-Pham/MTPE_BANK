<style>
body {
    font-family: Arial, sans-serif;
}

h2 {
    margin-top: 20px;
    color: #333;
}

.filter-container {
    margin: 20px 0;
}

#filter-form {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
}

#filter-form label {
    margin-right: 10px;
}

#filter-form select, 
#filter-form input[type="submit"] {
    margin: 5px;
}

#transaction_table,
#jar_table {
    margin-top: 20px;
    overflow-x: auto; 
}

#transaction_table table,
#jar_table table {
    width: 100%;
    border-collapse: collapse;
    white-space: nowrap; /* Ngăn chặn việc quấn dòng */
    overflow-wrap: break-word; /* Phá vỡ từ khi cần thiết */
}

#transaction_table th, 
#transaction_table td,
#jar_table th, 
#jar_table td {
    padding: 10px;
    text-align: left;
}

#transaction_table tr:nth-child(even),
#jar_table tr:nth-child(even) {
    background-color: #f2f2f2;
}

#chartContainerChar {
    display: flex;
    flex-wrap: wrap;
}

.chart-column {
    flex-grow: 1;
    flex-basis: 50%;
    box-sizing: border-box; 
    padding: 10px; 
    max-width: 100%;
}

#chartContainer {
    margin-left: 50px;
    margin-right: 50px;
}

#chartContainerChar .chart-column p {
    margin-left: 150px;
}


@media screen and (max-width: 600px) {
    #filter-form {
        flex-direction: column;
    }

    #filter-form select, 
    #filter-form input[type="submit"] {
        width: 100%;
        margin: 5px 0;
    }
    #chartContainer {
        margin-left: 1px;
        margin-right: 1px;
    }
    #chartContainerChar {
        margin-left: 15px;
        margin-right: 15px;
    }#chartContainerChar .chart-column p {
    margin-left: 5px;
}

}

#xem_them_button,#an_bot_button{
    margin-top: 20px;
    margin-bottom: 20px;
    border: #f2f2f2;
    background-color: white;
    font-size: 16px; 
}
#xem_them_button:hover, #an_bot_button:hover{ 
    color: blue;
    font-size: 20px;
}
 
</style>

<?php
include 'connect.php'; 
session_start(); 

if (isset($_SESSION['loggedInLoginName'])) {
    $loggedInLoginName = $_SESSION['loggedInLoginName'];

    // Truy vấn tổng hợp giao dịch
    $sql = "SELECT t.transactionType, SUM(t.transactionAmount) as totalAmount 
            FROM transaction t
            INNER JOIN customerAccount ca ON t.accountNumber = ca.accountNumber
            INNER JOIN customer c ON ca.id = c.id
            WHERE c.loginName = '$loggedInLoginName'
            GROUP BY t.transactionType";
    $result = $conn->query($sql);

    // Lấy dữ liệu cho biểu đồ
    $transactionTypes = [];
    $transactionAmounts = [];
    while ($row = $result->fetch_assoc()) {
        $transactionTypes[] = $row['transactionType'];
        $transactionAmounts[] = $row['totalAmount'];
    }

    // Truy vấn giao dịch hủ
    $sql_jar = "SELECT jt.jarTransactionName, SUM(jt.jarTransactionAmount) as totalAmount 
                FROM jarTransaction jt
                INNER JOIN transaction t ON jt.transactionID = t.transactionID
                INNER JOIN customerAccount ca ON t.accountNumber = ca.accountNumber
                INNER JOIN customer c ON ca.id = c.id
                WHERE c.loginName = '$loggedInLoginName'
                GROUP BY jt.jarTransactionName";
    $result_jar = $conn->query($sql_jar);

    // Lấy dữ liệu cho báo cáo giao dịch hủ
    $jarTransactions = [];
    while ($row = $result_jar->fetch_assoc()) {
        $jarTransactions[] = $row;
    }

    
    // Truy vấn giao dịch tiết kiệm
    $sql_savings = "SELECT sb.savingsName, SUM(sb.savingsAmount) as totalAmount 
                    FROM savingsBook sb
                    INNER JOIN customerAccount ca ON sb.accountNumber = ca.accountNumber
                    INNER JOIN customer c ON ca.id = c.id
                    WHERE c.loginName = '$loggedInLoginName'
                    GROUP BY sb.savingsName";
    $result_savings = $conn->query($sql_savings);

    // Lấy dữ liệu cho báo cáo giao dịch tiết kiệm
    $savingsTransactions = [];
    while ($row = $result_savings->fetch_assoc()) {
        $savingsTransactions[] = $row;
    }

    // Truy vấn giao dịch chi tiết
    $sql_transactionDetail = "SELECT t.accountNumber, t.transactionDate, t.transactionType, t.transactionAmount, t.transactionContent
                            FROM transaction t
                            INNER JOIN customerAccount ca ON t.accountNumber = ca.accountNumber
                            INNER JOIN customer c ON ca.id = c.id
                            WHERE c.loginName = '$loggedInLoginName'
                            ORDER BY t.transactionDate DESC"; // Sắp xếp theo ngày giao dịch giảm dần
    $result_transactionDetail = $conn->query($sql_transactionDetail);    

    if ($result_transactionDetail->num_rows > 0) {
        echo "<h2 style='text-align: center;'><b>Báo Cáo Thu Chi Tổng Hợp Năm [2024]</b></h2>
        
        <div id='chartContainerChar'>
    
            <div class='chart-column'>
            <canvas id='myChart' width='50' height='50' style='width: 50px !important; height: 50px !important;'></canvas>
            <p style=''>Biểu đồ tròn thể hiện tổng thu - chi trong năm 2024</p>
            </div>
    
            <div class='chart-column'>
                <canvas id='myLine' width='auto' height='150'></canvas>
                <p style=''>Biểu đồ cột thể hiện mức giao dịch các tháng trong năm 2024</p>
            </div>
    
        </div>
        
        <div id='chartContainer'>
            <div class='filter-container'>
                <form method='POST' action='export_pdf.php' id='filter-form'  target='_blank'>
                    <label for='filter'>Bộ lọc:</label>
                    <select name='filter' id='filter'>
                        <option value='all'>Tất cả</option>
                        <option value='today'>Hôm nay</option>
                        <option value='this_week'>Tuần này</option>
                        <option value='this_month'>Tháng này</option>
                    </select> 

                    <input type='submit' name='export_pdf' value='In Sao Kê'>
                </form>

                
            </div>
        




        <div id='transaction_table' class='page'>
            <div style='display: flex; justify-content: center;'>
                <table border='1'>
                    <tr>
                        <th style='text-align: center;'>Số Thứ Tự</th> 
                        <th style='text-align: center;'>Thời Gian Giao Dịch</th>
                        <th style='text-align: center;'>Phân Loại Thu Chi</th>
                        <th style='text-align: center;'>Số Tiền</th>
                        <th style='text-align: center;'>Nội Dung</th>
                    </tr>";
    
                $count = 1;
                $transaction_count = 0;
                while ($row = $result_transactionDetail->fetch_assoc()) {
                    if ($transaction_count < 10) {
                        echo "<tr>
                                <td style='text-align: center;'>" . $count++ . "</td> 
                                <td style='text-align: center;'>" . date("H:i:s d/m/Y", strtotime($row["transactionDate"])) . "</td>
                                <td style='text-align: center;'>" . $row["transactionType"] . "</td>
                                <td style='text-align: right;'>"  . number_format($row["transactionAmount"], 0, ',', '.') . " VNĐ" . "</td>
                                <td>" . $row["transactionContent"] . "</td>
                            </tr>";
                        $transaction_count++;
                    } else {
                        break;
                    }
                }
                echo "</table>";
            echo "</div>";


        // Nút "Xem thêm"  "Ẩn bớt"
        if ($result_transactionDetail->num_rows > 10) {
            echo "<div style='text-align: center;'>
                    <button id='xem_them_button' onclick='xemThem()'>Xem thêm</button>  <button id='an_bot_button' onclick='anBot()'>Ẩn bớt</button> 
                </div>
             
            ";
        }
 
        echo "</div></div></div>";
    }
    
    


        //LẤY DỮ LIỆU CHO BIỂU ĐỒ CỘT
        $year = 2024;

        // Lấy dữ liệu từ cơ sở dữ liệu
        $sql_customer = "SELECT MONTH(t.transactionDate) AS month, COUNT(*) AS transactionCount
        FROM transaction t
        INNER JOIN customerAccount ca ON t.accountNumber = ca.accountNumber
        INNER JOIN customer c ON ca.id = c.id
        WHERE c.loginName = '$loggedInLoginName'
        AND YEAR(t.transactionDate) >= $year
        GROUP BY MONTH(t.transactionDate)";
        $result_customer = $conn->query($sql_customer);
        
        $transactionCounts = array_fill(1, 12, 0); 
        while ($row = $result_customer->fetch_assoc()) {
            $month = $row['month'];
            $transactionCounts[$month] = $row['transactionCount'];
        }

        
    }
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
    document.getElementById('filter').addEventListener('change', function() {
        var filterValue = this.value;
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'transaction_table.php?filter=' + filterValue, true);
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 400) {
                document.getElementById('transaction_table').innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    });

// Sơ đồ tròn
var ctx = document.getElementById('myChart').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($transactionTypes); ?>,
        datasets: [{
            data: <?php echo json_encode($transactionAmounts); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.5)',
                'rgba(54, 162, 235, 0.5)',
                'rgba(255, 206, 86, 0.5)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        plugins: {
            legend: {
                position: 'right'
            }
        },
        scales: {
            y: {
                ticks: {
                    color: 'rgba(0, 0, 0, 0.5)' // Màu chữ
                }
            },
            x: {
                ticks: {
                    color: 'rgba(0, 0, 0, 0.5)' // Màu chữ
                }
            }
        }
    }
});


//SƠ ĐỒ CỘT 
    // Năm được truyền từ PHP
    var year = <?php echo $year; ?>;

    // Sử dụng dữ liệu PHP để tạo biểu đồ bằng Chart.js
    var ctx = document.getElementById('myLine').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
            datasets: [{
                label: 'Số lượng giao dịch',
                data: <?php echo json_encode(array_values($transactionCounts)); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(255, 99, 132, 1)',
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
                        beginAtZero: true
                    }
                }]
            }
        }
    });
 
    //XEM THÊM
    var transaction_count = 11; // Số lượng giao dịch đã hiển thị

    function xemThem() {
        // Gửi yêu cầu AJAX đến máy chủ để lấy thêm dữ liệu
        $.ajax({
            url: 'get_more_transactions.php', // URL của tập tin PHP xử lý yêu cầu
            type: 'POST',
            data: {
                count: transaction_count // Gửi số lượng giao dịch hiện tại đến máy chủ
            },
            success: function(response) {
                // Thêm dữ liệu nhận được vào bảng
                $('#transaction_table table').append(response);

                // Tăng số lượng giao dịch đã hiển thị
                transaction_count += 10;
            }
        });
    }

     // Hàm ẩn bớt các giao dịch
    function anBot() {
        // Ẩn các dòng giao dịch vừa xuất hiện từ nút "Xem thêm"
        $('#transaction_table table tr:gt(' + (transaction_count - 10) + ')').hide();

        // Giảm số lượng giao dịch đã hiển thị
        transaction_count -= 10;

        // Ẩn nút "Ẩn bớt" nếu đã ẩn hết các giao dịch
        if (transaction_count <= 10) {
            $('#an_bot_button').hide();
        }
    }

</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>