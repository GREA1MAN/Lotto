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
