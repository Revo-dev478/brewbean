<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test API RajaOngkir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="text-center mb-4">🔑 Test API Key RajaOngkir</h3>
                    
                    <form id="testForm">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Masukkan API Key Anda:</label>
                            <input type="text" class="form-control" id="apiKey" 
                                   placeholder="Contoh: a1b2c3d4e5f6g7h8..." 
                                   value="Yxf4tbUTdad1898a1e3a241eMe2F9wgY" required>
                            <small class="text-muted">Dapatkan di: https://rajaongkir.com/akun/panel</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Test API Key</button>
                    </form>

                    <div id="result" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('testForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const apiKey = document.getElementById('apiKey').value.trim();
    const resultDiv = document.getElementById('result');
    
    if (!apiKey) {
        resultDiv.innerHTML = '<div class="alert alert-warning">Masukkan API Key terlebih dahulu!</div>';
        return;
    }
    
    resultDiv.innerHTML = '<div class="alert alert-info">⏳ Testing API Key...</div>';
    
    try {
        const response = await fetch('test_api_backend.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'api_key=' + encodeURIComponent(apiKey)
        });
        
        const data = await response.text();
        resultDiv.innerHTML = data;
        
    } catch (error) {
        resultDiv.innerHTML = '<div class="alert alert-danger">❌ Error: ' + error.message + '</div>';
    }
});
</script>
</body>
</html>