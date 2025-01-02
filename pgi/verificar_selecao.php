<?php
require 'conexao_admpgi.php';

$query = "SELECT ID FROM dados_tfp WHERE is_selected = 1";
$result = $conn->query($query);

$selected_clients = [];
while ($row = $result->fetch_assoc()) {
    $selected_clients[] = (int) $row['ID'];
}

echo json_encode($selected_clients);
$conn->close();
?>
