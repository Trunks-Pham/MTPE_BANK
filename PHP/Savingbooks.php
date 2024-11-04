<style> 
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}

h2 {
    margin-top: 20px;
    text-align: center;
}

h3 {
    font-size: 25px;
    text-align: center;
}

.container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center; 
}


.info-container {
    margin: 10px;
    width: 100%;
    border: 1px solid #ccc;
    padding: 10px;
}

.info-container table {
    width: 100%;
    border-collapse: collapse;
}

.info-container th,
.info-container td {
    border: 1px solid #ccc;
    padding: 8px;
    text-align: center;
}

.info-container th {
    background-color: #f2f2f2;
}

.info-container tr:nth-child(even) {
    background-color: #f2f2f2;
}

.info-container {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px;
        }

        .create_savings{
            margin-left: 250px;
            margin-right: 250px;
        }

.shadow-table {
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    width: 100%;
}

td {
    padding: 20px;
}
#withdrawForm{
    margin-left: 250px;
    margin-right: 250px;
}
@media only screen and (max-width: 1000px) {
    .info-container {
        width: 100%;
        padding: 5px;
        margin: 5px;
        margin-top: 20px;
    }        
    .create_savings{
        margin-left: 5px;
        margin-right: 5px;
    }#withdrawForm{
    margin-left: 5px;
    margin-right: 5px;
    }
}

.buttons-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

@media only screen and (max-width: 600px) {
    .buttons-container {
        flex-direction: column;
    }
}

.buttons-container button {
    flex-grow: 1; 
    background-color: #0066FF; 
    color: white; 
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin: 5px;
}

.buttons-container button:hover {
    background-color: #0033FF; 
} 

input[type="number"] {
    height: 40px;
    width: 100%;
    }
</style>

<?php
session_start();
include 'connect.php';

if (isset($_SESSION['loggedInLoginName'])) {
    $loggedInLoginName = $_SESSION['loggedInLoginName'];

    $sql_savingsBook = "SELECT sb.savingsBookID, sb.savingsName, sb.savingsTerm, sb.savingsAmount, sb.interestRate, sb.interestAmountPerTerm, sb.savingsDate
                        FROM savingsBook sb
                        INNER JOIN customerAccount ca ON sb.accountNumber = ca.accountNumber
                        INNER JOIN customer c ON ca.id = c.id
                        WHERE c.loginName = ?";
                        
    $stmt = $conn->prepare($sql_savingsBook);
    $stmt->bind_param("s", $loggedInLoginName);
    $stmt->execute();
    $result_savingsBook = $stmt->get_result();

    $sql_interestRate = "SELECT month, interestRate
                        FROM interestRateByTerm";
    $result_interestRate = $conn->query($sql_interestRate);
}
?>

<body>
    <h2><b>Tiết Kiệm Trực Tuyến</b></h2>
    <div class="container">
        <div class="info-container">
            <h3><b>Thông Tin Lãi Suất Tiết Kiệm Quý 2 Năm 2024</b></h3>
            <table>
                <tr>
                    <th>Kỳ Hạn Tiết Kiệm</th>
                    <th>Lãi Suất %/Năm</th>
                </tr>
                <?php
                    if ($result_interestRate->num_rows > 0) {
                    while ($row_interestRate = $result_interestRate->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row_interestRate["month"] . " Tháng</td>";
                    echo "<td>" . number_format($row_interestRate["interestRate"] * 100, 1) . " %/Năm</td>";
                    echo "</tr>";
                    }
                    } else {
                        echo "<tr><td colspan='2'>Không có dữ liệu</td></tr>";
                    }
                ?>
            </table>
        </div>


<div class="buttons-container">
    <button onclick="toggleSection('createSavingsSection')">Tạo Sổ Tiết Kiệm</button>
    <button onclick="toggleSection('mySavingsSection')">Sổ Tiết Kiệm Của Tôi</button>
    <button onclick="toggleSection('withdrawSavingsSection')">Rút Tiền Từ Sổ Tiết Kiệm</button>
    <button onclick="toggleSection('interestCalculationSection')">Công Cụ Tính Lãi Suất</button>
</div>


    <div class="info-container"> 
        <div class="create_savings" id="createSavingsSection" style="display: none;">
            <h3><b>Tạo Sổ Tiết Kiệm</b></h3>
            <form action="create_savings.php" method="post">
                <label for="savingsName">Tên Sổ Tiết Kiệm:</label><br>
                <input type="text" id="savingsName" name="savingsName"><br>

                <label for="savingsAmount">Số Tiền Tiết Kiệm:</label><br>
                <input dir="rtl" type="number" id="savingsAmount" name="savingsAmount"><br>

                <label for="interestRate">Lãi Suất Kỳ Hạn:</label><br>
                <select id="interestRate" name="interestRate">
            <?php
            include 'connect.php';
            $interestRateQuery = "SELECT * FROM interestRateByTerm";
            $result = mysqli_query($conn, $interestRateQuery);
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['interestRate']. "'>" . $row['interestRate']*100  . " %/Năm - " . $row['month'] . " tháng" . "</option>";
                }
            }
            ?>
            </select><br><br>


                <input type="submit" value="Tạo Sổ Tiết Kiệm">
            </form>
            </div>
    </div>

    <?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['loggedInLoginName'])) {
        echo "Bạn cần đăng nhập để truy cập trang này.";
        exit; 
    }

    include 'connect.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $savingsName = $_POST['savingsName'];
        $savingsTerm = $_POST['savingsTerm'];
        $savingsAmount = $_POST['savingsAmount'];
        $interestRate = $_POST['interestRate'];

        $loggedInLoginName = $_SESSION['loggedInLoginName'];
        $accountNumberQuery = "SELECT ca.accountNumber 
                            FROM customerAccount ca 
                            INNER JOIN customer c ON ca.id = c.id 
                            WHERE c.loginName = '$loggedInLoginName'";
        $result = mysqli_query($conn, $accountNumberQuery);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $accountNumber = $row['accountNumber'];

            $query = "INSERT INTO savingsBook (accountNumber, savingsName, savingsTerm, savingsAmount, interestRate) 
                    VALUES ('$accountNumber', '$savingsName', '$savingsTerm', '$savingsAmount', '$interestRate')";

            if (mysqli_query($conn, $query)) {
                echo "Sổ tiết kiệm đã được tạo thành công!";
            } else {
                echo "Error: " . $query . "<br>" . mysqli_error($conn);
            }
        } else {
            echo "Không thể lấy thông tin số tài khoản của người dùng.";
        }
    }
    ?>




<div class="info-container" id="mySavingsSection" style="display: none;">
    <h3><b>Sổ Tiết Kiệm Của Tôi</b></h3>
    <table>
        <tr>
            <th>Số Thứ Tự Sổ</th>
            <th>Ngày Tạo Sổ Tiết Kiệm</th>
            <th>Tên Sổ Tiết Kiệm</th>
            <th>Kỳ Hạn</th>
            <th>Số Tiền Tiết Kiệm</th>
            <th>Lãi Suất %/Năm</th>
            <th>Số Tiền Lãi/Kỳ Hạn</th>
        </tr>

        <?php 
        if ($result_savingsBook) {
            if ($result_savingsBook->num_rows > 0) {
                $count = 1;
                while ($row_savingsBook = $result_savingsBook->fetch_assoc()) {
                echo "<tr>";
                    echo "<td>" . $count . "</td>";
                    echo "<td>" . date('H:i:s d/m/Y', strtotime($row_savingsBook["savingsDate"])) . "</td>";
                    echo "<td>" . $row_savingsBook["savingsName"] . "</td>";
                    echo "<td>" . $row_savingsBook["savingsTerm"] . " Tháng</td>";
                    echo "<td>" . number_format($row_savingsBook["savingsAmount"], 0, ',', '.') . " VNĐ</td>";
                    echo "<td>" . number_format($row_savingsBook["interestRate"] * 100, 1) . " %/Năm</td>";;
                    echo "<td>" . number_format($row_savingsBook["interestAmountPerTerm"], 0, ',', '.') . " VNĐ</td>";
                echo "</tr>";
                $count++;
                }
            } else {
                echo "<tr><td colspan='7'>Tạo sổ tiết kiệm đii ạ !!! Tiết kiệm là con đường giúp mình giàu từ từ =)))</td></tr>";
            }
        } else {
            echo " Nghèo quá chưa mở sổ được hẻ =)))) " . $conn->error;
        }
    ?>
    </table>
</div>






<div class="info-container" id="withdrawSavingsSection" style="display: none;">
    <h3><b>Rút Tiền Từ Sổ Tiết Kiệm</b></h3>
    <form id="withdrawForm" action="withdraw_savings.php" method="post">
        <label for="savingsID">Chọn Sổ Tiết Kiệm:</label><br>
        <select id="savingsID" name="savingsID">
            <?php
            include 'connect.php';
            $loggedInLoginName = $_SESSION['loggedInLoginName'];
            $savingsBookQuery = "SELECT savingsBookID, savingsName FROM savingsBook WHERE accountNumber = (SELECT accountNumber FROM customerAccount WHERE id = (SELECT id FROM customer WHERE loginName = '$loggedInLoginName'))";
            $result = mysqli_query($conn, $savingsBookQuery);
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['savingsBookID'] . "'>" . $row['savingsName'] . "</option>";
                }
            }
            ?>
        </select><br><br>

        <label for="withdrawAmount">Số Tiền Muốn Rút:</label><br>
        <input dir="rtl" pattern="[0-9]+" title="Số Tiền Tối Thiểu 1.000 VNĐ" type="number" id="withdrawAmount" name="withdrawAmount" placeholder="NẾU ĐÁO HẠN THÌ NHẬP CHÍNH XÁC SỐ TIỀN !!!" required><br><br>

        <input type="submit" name="withdraw" value="Rút Tiền">
        <input type="submit" name="mature" value="Đáo Hạn">
    </form>
</div>







   <div class="info-container" id="interestCalculationSection" style="display: none;">
        <h3><b>Công Cụ Tính Lãi Suất</b></h3>
        <table class="shadow-table">
            <tr class="interest-table">
                <td class="formCell">
                    <form id="interestForm">
                        <label for="principal"><b>Số Tiền Tiết Kiệm Dự Tính:</b></label>
                        <input dir="rtl" type="number" id="principal" name="principal" required ><br><br>

                        <label for="term"><b>Kỳ Hạn Tiết Kiệm Dự Định:</b></label>
                        <select id="term" name="term" required>
                            <option value="1">1 tháng</option>
                            <option value="3">3 tháng</option>
                            <option value="6">6 tháng</option>
                            <option value="12">12 tháng</option>
                            <option value="18">18 tháng</option>
                        </select><br><br>

                        <input type="submit" name="calculate" value="Tính Lãi Suất" >
                    </form>
                </td>
                <td class="formCell">
                    <p id="output" style="text-align: left;">
                        Số tiền lãi nhận được: 0 ₫<br>
                        Tổng số tiền nhận được khi đáo hạn: 0 ₫
                    </p>
                </td>
            </tr> 
    </div> 

</table>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $("#interestForm").on('submit', function(event){
        event.preventDefault();

        $.ajax({
            url: 'calculate_interest.php',
            type: 'post',
            data: $(this).serialize(),
            success: function(result){
                $("#output").html(result);
            }
        });
    });
});





function validateAmount(inputId, errorId) {
            var input = document.getElementById(inputId);
            var error = document.getElementById(errorId);
            var value = parseFloat(input.value.replace(/,/g, ''));
            if (isNaN(value) || value <= 1000) {
                error.textContent = "Số tiền phải lớn hơn 1000.";
                return false;
            } else {
                error.textContent = "";
                return true;
            }
        }

        $("#createSavingsForm").on('submit', function(event){
            if (!validateAmount('savingsAmount', 'savingsAmountError')) {
                event.preventDefault();
            }
        });

        $("#withdrawForm").on('submit', function(event){
            if (!validateAmount('withdrawAmount', 'withdrawAmountError')) {
                event.preventDefault();
            }
        });

        $("#interestForm").on('submit', function(event){
            if (!validateAmount('principal', 'principalError')) {
                event.preventDefault();
            }
        });

function toggleSection(sectionId) {
    var section = document.getElementById(sectionId);
    if (section.style.display === "none") {
        section.style.display = "block";
    } else {
        section.style.display = "none";
    }
}
</script> 
