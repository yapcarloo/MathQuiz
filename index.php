<?php
session_start();

if (!isset($_SESSION['level'], $_SESSION['operator'], $_SESSION['num_questions'])) {
    header("Location: settings.php");
    exit;
}

if (!isset($_SESSION['score'])) {
    $_SESSION['score'] = 0;
    $_SESSION['wrong'] = 0;
    $_SESSION['question_count'] = 0;
}

function generateQuestion($level, $operator, $custom_min = null, $custom_max = null) {
    if ($level === 'custom') {
        $min = $custom_min;
        $max = $custom_max;
    } else {
        $min = $level == 1 ? 1 : 11;
        $max = $level == 1 ? 10 : 100;
    }
    $num1 = rand($min, $max);
    $num2 = rand($min, $max);

    switch ($operator) {
        case 'addition':
            return ["$num1 + $num2", $num1 + $num2];
        case 'subtraction':
            return ["$num1 - $num2", $num1 - $num2];
        case 'multiplication':
            return ["$num1 * $num2", $num1 * $num2];
    }
}

function generateChoices($answer, $min, $max) {
    $choices = [$answer];
    while (count($choices) < 4) {
        $fake = rand($min, $max);
        if (!in_array($fake, $choices)) {
            $choices[] = $fake;
        }
    }
    shuffle($choices);
    return $choices;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['answer'] == $_SESSION['current_question'][1]) {
        $_SESSION['score']++;
    } else {
        $_SESSION['wrong']++;
    }
    $_SESSION['question_count']++;
    if ($_SESSION['question_count'] >= $_SESSION['num_questions']) {
        header("Location: result.php");
        exit;
    }
}

list($question, $answer) = generateQuestion(
    $_SESSION['level'],
    $_SESSION['operator'],
    $_SESSION['custom_min'] ?? null,
    $_SESSION['custom_max'] ?? null
);

$choices = generateChoices($answer, 1, 100);
$_SESSION['current_question'] = [$question, $answer];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Math Quiz</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Math Quiz</h1>
        <p>Question <?= $_SESSION['question_count'] + 1; ?>/<?= $_SESSION['num_questions']; ?></p>
        <p><?= $_SESSION['current_question'][0]; ?> = ?</p>
        <form method="post">
            <?php foreach ($choices as $choice): ?>
                <label>
                    <input type="radio" name="answer" value="<?= $choice; ?>" required>
                    <?= $choice; ?>
                </label>
            <?php endforeach; ?>
            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
