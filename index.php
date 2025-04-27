<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "kalkulator"; #nama database

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$hasil = "";
$ekspresi = "";
$error = "";

if (isset($_POST['hapus_history'])) {
    mysqli_query($conn, "DELETE FROM history");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['input'])) {
    $ekspresi = $_POST['input'];
    $ekspresi = preg_replace('/(\d+)\s*%\s*(\d+)/', '($1/100)*$2', $ekspresi);
    $ekspresi = preg_replace('/(\d+)\s*%/', '($1/100)', $ekspresi);
    $ekspresi = preg_replace('/(\d+)√(\d+)/', '$1*sqrt($2)', $ekspresi);
    $ekspresi = preg_replace('/√(\d+)/', 'sqrt($1)', $ekspresi);

    try {
        if (preg_match('/\/\s*0(\.0*)?$/', $ekspresi)) {
            $error = "Tidak bisa dibagi dengan 0";
        } elseif (preg_match('/[\+\-\*\/]$/', $ekspresi)) {
            $error = "Ekspresi tidak lengkap";
        } else {
            $hasil = @eval("return $ekspresi;");
            if ($hasil !== false && $hasil !== null) {
                // Hanya INSERT ke database jika tidak ada error
                $sql = "INSERT INTO history (operasi, hasil) VALUES ('" . mysqli_real_escape_string($conn, $_POST['input']) . "', '$hasil')";
                mysqli_query($conn, $sql);
                $ekspresi = $hasil;
            } else {
                $error = "Error dalam perhitungan";
            }
        }
    } catch (Exception $e) {
        $error = "Error!";
    }
}

$history = mysqli_query($conn, "SELECT * FROM history ORDER BY id DESC LIMIT 10");
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kalkulator Sederhana</title>
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px;
        }

        .container {
            display: flex;
            gap: 30px;
        }

        .kalkulator {
            background-color: #222;
            padding: 20px;
            border-radius: 30px;
            width: 270px;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.05);
        }

        .display {
            height: 50px;
            font-size: 18px;
            text-align: right;
            padding: 15px;
            border-radius: 15px;
            background-color: #000;
            color: #fff;
            margin-bottom: 10px;
            border: none;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            box-sizing: border-box;
        }

        .error {
            background-color: #333;
            padding: 10px;
            border-radius: 15px;
            color: orange;
            font-size: 16px;
            text-align: center;
            margin-bottom: 15px;
        }

        .buttons {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        button {
            height: 60px;
            font-size: 24px;
            font-weight: bold;
            border-radius: 50%;
            border: none;
            background-color: #2f2f2f;
            color: #ffffff;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }

        button.orange {
            background-color: #ff9500;
            color: #000000;
        }

        button:hover {
            background-color: #3d3d3d;
            transform: scale(1.05);
        }

        button.orange:hover {
            background-color: #ffaa33;
        }

        .btn-equal {
            border-radius: 20px;
        }

        .history {
            background-color: #222;
            border-radius: 30px;
            padding: 20px;
            width: 250px;
            max-height: 500px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .history h3 {
            margin-top: 0;
        }

        .history div {
            padding: 12px 14px;
            background-color: #111;
            margin-bottom: 12px;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
        }

        .clear {
            background-color: #ff3b3b !important;
            color: white;
        }

        .hapus-history {
            margin-top: 10px;
            display: flex;
            justify-content: center;
        }

        .hapus-history button {
            padding: 8px 14px;
            font-size: 14px;
            border-radius: 10px;
            background-color: #ff3b3b;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .hapus-history button:hover {
            background-color: #e02b2b;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
            color: white;
        }
    </style>
    <script>
        let isResultDisplayed = false;

        function append(val) {
            let input = document.getElementById("input");
            let display = document.getElementById("display");

            if (isResultDisplayed) {
                if (['+', '-', '*', '/'].includes(val)) {
                    input.value = display.innerText + val;
                } else {
                    input.value = val;
                }
                isResultDisplayed = false;
            } else {
                input.value += val;
            }

            display.innerText = input.value;
        }

        function akar() {
            let input = document.getElementById("input");
            let display = document.getElementById("display");
            let value = input.value;
            let lastChar = value.slice(-1);

            // Tetap langsung nambah √ tanpa *
            input.value += '√';
            display.innerText = input.value;
            isResultDisplayed = false;
        }




        function clearInput() {
            document.getElementById("input").value = "";
            document.getElementById("display").innerText = "";
            isResultDisplayed = false;
        }

        function backspace() {
            let input = document.getElementById("input");
            input.value = input.value.slice(0, -1);
            document.getElementById("display").innerText = input.value;
        }

        window.onload = function() {
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $hasil !== ""): ?>
                isResultDisplayed = true;
            <?php endif; ?>
        }
    </script>
</head>

<body>
    <div class="container">
        <form class="kalkulator" method="post">
            <h2>Kalkulator</h2>
            <?php if ($error): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>
            <div class="display" id="display">
                <?= ($_SERVER["REQUEST_METHOD"] == "POST" && $hasil !== "") ? $hasil : htmlspecialchars($ekspresi); ?>
            </div>
            <input type="hidden" name="input" id="input" value="<?= htmlspecialchars($ekspresi) ?>">
            <div class="buttons">
                <button type="button" class="clear" onclick="clearInput()">C</button>
                <button type="button" onclick="backspace()">⌫</button>
                <button type="button" onclick="append('%')">%</button>
                <button type="button" class="orange" onclick="append('/')">/</button>

                <button type="button" onclick="append('7')">7</button>
                <button type="button" onclick="append('8')">8</button>
                <button type="button" onclick="append('9')">9</button>
                <button type="button" class="orange" onclick="append('*')">*</button>

                <button type="button" onclick="append('4')">4</button>
                <button type="button" onclick="append('5')">5</button>
                <button type="button" onclick="append('6')">6</button>
                <button type="button" class="orange" onclick="append('-')">-</button>

                <button type="button" onclick="append('1')">1</button>
                <button type="button" onclick="append('2')">2</button>
                <button type="button" onclick="append('3')">3</button>
                <button type="button" class="orange" onclick="append('+')">+</button>

                <button type="button" onclick="akar()">√</button>
                <button type="button" onclick="append('0')">0</button>
                <button type="button" onclick="append('.')">.</button>
                <button type="submit" class="orange btn-equal">=</button>
            </div>

        </form>

        <div class="history">
            <h3>History</h3>
            <div>
                <?php while ($row = mysqli_fetch_assoc($history)) : ?>
                    <div><?= htmlspecialchars($row['operasi']) ?> = <?= $row['hasil'] ?></div>
                <?php endwhile; ?>
            </div>
            <form method="post" class="hapus-history">
                <button type="submit" name="hapus_history" onclick="return confirm('Yakin ingin hapus semua history?')">🗑️ Hapus History</button>
            </form>
        </div>
    </div>
</body>

</html>
