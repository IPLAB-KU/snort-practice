<?php
// DB 연결 파일 포함
require_once __DIR__ . '/db_connect.php';

// SQL 테스트 (users 테이블 확인)
$sql = $sql = "SELECT id, name, email, phone, address FROM users WHERE id = 1 LIMIT 1";

$result = $mysqli->query($sql);

if (!$result) {
    echo "<h3>❌ SQL 실행 오류:</h3><pre>" . htmlspecialchars($mysqli->error) . "</pre>";
    exit;
}

// 결과 출력
echo "<h2>✅ DB 연결 성공 — 사용자 정보 테스트</h2>";
echo "<table border='1' cellspacing='0' cellpadding='6' style='border-collapse: collapse;'>
        <tr style='background:#f3f3f3;'>
            <th>ID</th>
            <th>이름</th>
            <th>이메일</th>
            <th>전화번호</th>
            <th>주소</th>
            <th>포인트</th>
            <th>상태</th>
            <th>가입일</th>
        </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
    echo "<td>" . htmlspecialchars($row['phone'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['address'] ?? '') . "</td>";
    echo "<td>" . number_format((int)$row['points']) . "</td>";
    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
    echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
    echo "</tr>";
}

echo "</table>";

$result->free();
$mysqli->close();
?>