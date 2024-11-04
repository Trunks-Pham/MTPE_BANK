    <main class="container">
        <h2 style="text-align: center;">Kiến Thức Tài Chính</h2>
        <main class="news">
            <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "content news mtpe bank";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Lỗi Kết Nối: " . $conn->connect_error);
            }

            $sql = "SELECT id, poster, image, title FROM posts";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='news-post'>";
                    echo "<a href='detailnews.php?id=" . $row["id"] . "'>";
                    echo "<img src='" . $row["poster"] . "' alt='Poster'>";
                    echo "<div class='title'>" . $row["title"] . "</div>";
                    echo "</a>";
                    echo "</div>";
                }
            } else {
                echo "có gì đâu mà coi =)))";
            }
            $conn->close();
            ?>
            <?php include 'Chatbot.php'; ?>
        </main>
    </main>