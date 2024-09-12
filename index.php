<?php
session_start(); 

function generateLotteryNumbers($num) {
    $numbers = [];
    while (count($numbers) < $num) {
        $rand = sprintf('%03d', rand(0, 999)); //สุ่มเลข3หลัก ถ้าไม่ครบ3ตัวใส่ 0 ไว้เข้างหน้า
        if (!in_array($rand, $numbers)) {
            $numbers[] = $rand;
        }
    }
    return $numbers;
}

function getRandomPrizes() {
    $prizes = [];
    $prizes['1st'] = generateLotteryNumbers(1); //รางวัลที่1 1ตัว
    $prizes['2nd'] = generateLotteryNumbers(3); //รางวัลที่2 3ตัว

    $firstPrize = $prizes['1st'][0]; //รางวัลใกล้เคียงหมายเลขที่1 +- ไมเกิน10เลข
    $prizes['nearby'] = [];
    while (count($prizes['nearby']) < 2) {
        $rand = sprintf('%03d', $firstPrize + rand(-10, 10));
        if ($rand !== $firstPrize && !in_array($rand, $prizes['nearby']) && $rand >= '000' && $rand <= '999') {
            $prizes['nearby'][] = $rand;
        }
    }

    $prizes['last2'] = sprintf('%02d', rand(0, 99)); //รางวัล2ตัวท้าย
    return $prizes;
}


if (isset($_POST['generate_prizes'])) {
    $_SESSION['prizes'] = getRandomPrizes();
}
$prizes = $_SESSION['prizes'] ?? [];
?>

<?php include 'random.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lottery System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">ล๊อตโต้สุ่มตัวเลข</h1>
        <form method="post">
            <button type="submit" name="generate_prizes" class="btn btn-primary mb-3">สุ่มตัวเลขรางวัล</button>
        </form>

        <?php if ($prizes): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ชนิดของรางวัล</th>
                        <th>เลขที่ถูก</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>รางวัลที่ 1</td>
                        <td><?php echo implode(', ', $prizes['1st']); ?></td>
                    </tr>
                    <tr>
                        <td>รางวัลที่ 2</td>
                        <td><?php echo implode(', ', $prizes['2nd']); ?></td>
                    </tr>
                    <tr>
                        <td>รางวัลใกล้เคียง</td>
                        <td><?php echo implode(', ', $prizes['nearby']); ?></td>
                    </tr>
                    <tr>
                        <td>เลขท้าย 2 ตัว</td>
                        <td><?php echo $prizes['last2']; ?></td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>


        <form method="post" class="mt-4">
            <div class="mb-3">
                <label for="lottery_number" class="form-label">เลขล๊อตเตอรี่</label>
                <input type="text" class="form-control" id="lottery_number" name="lottery_number" pattern="\d{2,3}" title="Two or three digit number" required>
            </div>
            <button type="submit" name="check_prize" class="btn btn-success">ตรวจรางวัล</button>
        </form>

        <?php if (isset($_POST['check_prize'])): ?>
            <?php
            $lottery_number = $_POST['lottery_number']; 
            $results = [];


            // ตรวจรางวัล หากถูก3หลัก
            if (strlen($lottery_number) == 3) {
                if (in_array($lottery_number, $prizes['1st'] ?? [])) {
                    $results[] = 'ถูกรางวัลที่ 1 !!!';
                }
                if (in_array($lottery_number, $prizes['2nd'] ?? [])) {
                    $results[] = 'ถูกรางวัลที่ 2 !!';
                }
                if (in_array($lottery_number, $prizes['nearby'] ?? [])) {
                    $results[] = 'ถูกรางวัลใกล้เคียง !';
                }
                if (substr($lottery_number, -2) === $prizes['last2']) {
                    $results[] = 'ถูกรางวัล 2 ตัวสุดท้าย !';
                }
            }

            // ถูก2หลักด้วย
            elseif (strlen($lottery_number) == 2) {
                if (substr('0' . $lottery_number, -2) === $prizes['last2']) {
                    $results[] = 'ถูกรางวัล 2 ตัวสุดท้าย !';
                }
            }

            $resultText = $results ? implode(' และ ', $results) : 'ไม่ถูกรางวัล';
            ?>
            <div class="alert alert-info mt-3">
                หมายเลขของคุณ <?php echo htmlspecialchars($lottery_number); ?> <?php echo htmlspecialchars($resultText); ?>.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>


