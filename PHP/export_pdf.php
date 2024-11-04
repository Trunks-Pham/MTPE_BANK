<?php
ob_start();

require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');

// Tạo lớp kế thừa từ TCPDF để thiết lập header tùy chỉnh
class MYPDF extends TCPDF { 
    public function Header() { 
        $logo = '../IMAGES/Logo/Logo 4.jpg'; 
        if (file_exists($logo)) {
            $this->Image($logo, 10, 10, 20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        } 
        $this->SetFont('dejavusans', 'B', 12); 
        $this->Cell(0, 15, 'Ngân Hàng Đầu Tư Và Phát Triển Công Nghệ Số MTPE BANK', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }
}

// Tạo đối tượng PDF từ lớp kế thừa
$pdf = new MYPDF();

// Thiết lập thông tin tài liệu
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Author');
$pdf->SetTitle('Bảng Sao Kê Giao Dịch');
$pdf->SetSubject('Transactions');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// Thiết lập thông tin trang
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Thêm một trang
$pdf->AddPage();

// Thiết lập font chữ
$pdf->SetFont('dejavusans', '', 10);

// Truy vấn dữ liệu giao dịch
include 'connect.php';
session_start();

if (isset($_SESSION['loggedInLoginName'])) {
    $loggedInLoginName = $_SESSION['loggedInLoginName'];
    $filter = $_POST['filter'];

    $whereClause = '';
    if ($filter == 'today') {
        $whereClause = "AND DATE(t.transactionDate) = CURDATE()";
    } elseif ($filter == 'this_week') {
        $whereClause = "AND WEEK(t.transactionDate) = WEEK(CURDATE())";
    } elseif ($filter == 'this_month') {
        $whereClause = "AND MONTH(t.transactionDate) = MONTH(CURDATE())";
    }

    // Lấy thông tin khách hàng
    $sql_customerInfo = "SELECT c.name, ca.accountNumber
                         FROM customerAccount ca
                         INNER JOIN customer c ON ca.id = c.id
                         WHERE c.loginName = '$loggedInLoginName'";
    $result_customerInfo = $conn->query($sql_customerInfo);
    $customerInfo = $result_customerInfo->fetch_assoc();

    // Thiết lập múi giờ
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    // Lấy thời gian hiện tại với định dạng đầy đủ
    $datetimeNow = date('H:i:s d/m/Y');

    $sql_transactionDetail = "SELECT t.accountNumber, t.transactionDate, t.transactionType, t.transactionAmount, t.transactionContent
                              FROM transaction t
                              INNER JOIN customerAccount ca ON t.accountNumber = ca.accountNumber
                              INNER JOIN customer c ON ca.id = c.id
                              WHERE c.loginName = '$loggedInLoginName' $whereClause
                              ORDER BY t.transactionDate DESC";
    $result_transactionDetail = $conn->query($sql_transactionDetail);

    // Tạo nội dung bảng trong PDF
    $html = '<h1 style="text-align:center">Bảng Sao Kê Giao Dịch</h1>';
    $html .= '<p style="text-align: left">Khách hàng: <b>' . $customerInfo["name"] . '</b></p>';
    $html .= '<p style="text-align: left">Số Tài Khoản: <b>' . $customerInfo["accountNumber"] . '</b></p>';
    $html .= '<p style="text-align: left">Thời gian thực hiện sao kê: <b>' . $datetimeNow . '</b></p>';

    $html .= '<table border="1" cellpadding="5">';
    $html .= '<thead>
                 <tr style="text-align:center">
                     <th>Số Thứ Tự</th>
                     <th>Thời Gian Giao Dịch</th>
                     <th>Phân Loại Thu Chi</th>
                     <th>Số Tiền</th>
                     <th>Nội Dung</th>
                 </tr>
              </thead>
              <tbody>';

    $count = 1;
    while ($row = $result_transactionDetail->fetch_assoc()) {
        $transactionDate = date('H:i:s d/m/Y', strtotime($row["transactionDate"]));
        $html .= '<tr>
                     <td style="text-align:center">' . $count++ . '</td>
                     <td style="text-align:center">' . $transactionDate . '</td>
                     <td style="text-align:center">' . $row["transactionType"] . '</td>
                     <td style="text-align: right;">' . number_format($row["transactionAmount"], 0, ',', '.') . ' VNĐ</td>
                     <td>' . $row["transactionContent"] . '</td>
                  </tr>';
    }

    $html .= '</tbody></table>';

    // Thêm nội dung vào PDF
    $pdf->writeHTML($html, true, false, true, false, '');

    // Xóa bộ đệm trước khi xuất PDF
    ob_end_clean();

    // Xuất PDF
    $pdf->Output('transaction_report.pdf', 'I');
} else {
    // Xử lý khi không có phiên đăng nhập
    echo "Bạn cần đăng nhập để xem báo cáo.";
}
?>
