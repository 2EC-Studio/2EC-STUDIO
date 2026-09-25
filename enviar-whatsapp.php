<?php
// Permite que o frontend do seu site acesse este arquivo (Ajuste o domínio se necessário)
header("Access-Control-Allow-Origin: *"); 
header('Content-Type: application/json');

// 1. Segurança Básica: Só aceita requisições POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
    exit;
}

// 2. Recebe os dados enviados pelo JavaScript
$input = file_get_contents('php://input');
$dados = json_decode($input, true);
$mensagem = $dados['mensagem'] ?? '';

if (empty($mensagem)) {
    echo json_encode(['success' => false, 'message' => 'Mensagem vazia.']);
    exit;
}

// 3. SUAS CREDENCIAIS SECRETAS (Ficam escondidas aqui no servidor)
$numeroDestino = "5511999999999"; // Seu número com DDI e DDD
$apiKey = "COLE_SUA_CHAVE_SECRETA_AQUI"; // Chave do CallMeBot ou outra API

// 4. Monta a URL da API externa
$url = "https://api.callmebot.com/whatsapp.php?phone=" . urlencode($numeroDestino) . "&text=" . urlencode($mensagem) . "&apikey=" . urlencode($apiKey);

// 5. Envia a requisição via cURL (por baixo dos panos)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout de 10 segundos

$resposta = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 6. Retorna o resultado para o JavaScript
if ($httpCode == 200) {
    echo json_encode(['success' => true, 'message' => 'Enviado com sucesso!']);
} else {
    // Log de erro opcional para você verificar no servidor
    error_log("Erro WhatsApp API: " . $resposta);
    echo json_encode(['success' => false, 'message' => 'Falha ao contatar o WhatsApp.']);
}
?>