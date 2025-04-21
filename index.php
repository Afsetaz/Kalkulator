<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Kalkulator Sederhana</title>
    <style>
        body {
            background: #000;
            color: #fff;
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .calculator {
            display: flex;
            background: #1e1e1e;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
        }

        .left {
            padding: 20px;
        }

        .screen {
            width: 250px;
            height: 60px;
            background: #000;
            color: #fff;
            text-align: right;
            padding: 10px;
            font-size: 2em;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .buttons {
            display: grid;
            grid-template-columns: repeat(4, 60px);
            /* Menjaga tombol dalam kolom yang teratur */
            grid-gap: 10px;
        }

        .btn {
            height: 60px;
            width: 60px;
            font-size: 1.2em;
            border: none;
            border-radius: 50%;
            background: #333;
            color: #fff;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn:hover {
            background: #444;
        }

        /* Tombol operator dengan warna oranye */
        .btn.operator {
            background: orange;
            color: #000;
        }

        .btn.double {
            grid-column: span 2;
            width: 130px;
        }

        .right {
            background: #2b2b2b;
            padding: 20px;
            width: 200px;
            overflow-y: auto;
            border-left: 1px solid #444;
            display: flex;
            flex-direction: column;
        }

        .history {
            flex-grow: 1;
            overflow-y: auto;
        }

        .btn-zero {
            border-radius: 30px;
        }
    </style>
</head>

<body>

    <div class="calculator">
        <div class="left">
            <div class="screen" id="result">0</div>
            <div class="buttons">
                <button class="btn" data-action="clear">C</button>
                <button class="btn" data-action="open-paren">(</button>
                <button class="btn" data-action="close-paren">)</button>
                <button class="btn operator" data-action="divide">/</button>

                <button class="btn" data-action="seven">7</button>
                <button class="btn" data-action="eight">8</button>
                <button class="btn" data-action="nine">9</button>
                <button class="btn operator" data-action="multiply">*</button>

                <button class="btn" data-action="four">4</button>
                <button class="btn" data-action="five">5</button>
                <button class="btn" data-action="six">6</button>
                <button class="btn operator" data-action="subtract">-</button>

                <button class="btn" data-action="one">1</button>
                <button class="btn" data-action="two">2</button>
                <button class="btn" data-action="three">3</button>
                <button class="btn operator" data-action="add">+</button>

                <button class="btn" data-action="percent">%</button>
                <button class="btn" data-action="zero">0</button>
                <button class="btn" data-action="decimal">.</button>
                <button class="btn operator" data-action="equal">=</button>
            </div>
        </div>

        <div class="right">
            <h3>History</h3>
            <div class="history" id="history">
                <!-- History -->
            </div>
        </div>
    </div>

    <script>
        let expression = '';
        let historyBox = document.getElementById("history");

        document.querySelectorAll(".btn").forEach(btn => {
            btn.onclick = () => {
                const action = btn.getAttribute('data-action');
                const value = btn.textContent;

                if (action === 'clear') {
                    clearResult();
                } else if (action === 'equal') {
                    calculateResult();
                } else if (action === 'percent') {
                    handlePercent(); // Menambahkan penanganan untuk persen
                } else {
                    expression += value;
                    document.getElementById("result").textContent = expression;
                }
            }
        });

        function clearResult() {
            expression = '';
            document.getElementById("result").textContent = '0';
        }

        function calculateResult() {
            try {
                let result = eval(expression);

                if (!isFinite(result)) {
                    document.getElementById("result").textContent = 'Tidak bisa dibagi dengan 0';
                    historyBox.innerHTML += `<div>${expression} = ERROR</div>`;
                    expression = '';
                    return;
                }

                historyBox.innerHTML += `<div>${expression} = ${result}</div>`;
                document.getElementById("result").textContent = result;
                expression = result.toString();

                historyBox.scrollTop = historyBox.scrollHeight;

            } catch {
                document.getElementById("result").textContent = 'Error';
                expression = '';
            }
        }

        // Fungsi untuk menangani tombol persen
        function handlePercent() {
            if (expression) {
                // Mengubah angka dalam ekspresi menjadi persen (misalnya 50 => 0.5)
                let percentValue = (parseFloat(expression) / 100).toString();
                historyBox.innerHTML += `<div>${expression} = ${percentValue}</div>`; // Menambahkan ke history
                document.getElementById("result").textContent = percentValue;
                expression = percentValue; // Memperbarui ekspresi
                historyBox.scrollTop = historyBox.scrollHeight;
            }
        }
    </script>
</body>

</html>
