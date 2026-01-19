<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Ongkir - RajaOngkir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-4 text-center">Cek Ongkir</h2>

            <form id="ongkirForm">
                <div class="mb-3">
                    <label class="form-label">Kota Asal</label>
                    <select class="form-select" id="origin_city" name="origin_city" required>
                        <option value="">Memuat kota...</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kota Tujuan</label>
                    <select class="form-select" id="destination_city" name="destination_city" required>
                        <option value="">Memuat kota...</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Berat (gram)</label>
                    <input type="number" class="form-control" name="weight" min="1" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kurir</label>
                    <select class="form-select" name="courier" required>
                        <option value="">Pilih Kurir</option>
                        <option value="jne">JNE</option>
                        <option value="tiki">TIKI</option>
                        <option value="pos">POS Indonesia</option>
                    </select>
                </div>

                <button class="btn btn-primary w-100" type="submit">Cek Ongkir</button>
            </form>

            <div id="results" class="mt-4"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    fetch("kota.php")
        .then(res => res.text())
        .then(data => {
            document.getElementById("origin_city").innerHTML =
                '<option value="">Pilih Kota</option>' + data;
            document.getElementById("destination_city").innerHTML =
                '<option value="">Pilih Kota</option>' + data;
        })
        .catch(() => {
            document.getElementById("origin_city").innerHTML =
                '<option value="">Gagal load kota</option>';
            document.getElementById("destination_city").innerHTML =
                '<option value="">Gagal load kota</option>';
        });
});

document.getElementById("ongkirForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const results = document.getElementById("results");
    results.innerHTML = '<div class="text-info">Memuat...</div>';

    fetch("cek_ongkir.php", {
        method: "POST",
        body: new FormData(this)
    })
    .then(res => res.text())
    .then(data => results.innerHTML = data)
    .catch(() => results.innerHTML = '<div class="text-danger">Gagal cek ongkir</div>');
});
</script>

</body>
</html>
