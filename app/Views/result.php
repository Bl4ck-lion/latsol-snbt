<?php
use app\Core\Auth;

$isPremium = Auth::isPremium($user);
$totalQuestions = count($results);
$correctAnswers = 0;
foreach ($results as $result) {
    if ($result['is_correct']) {
        $correctAnswers++;
    }
}
$score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Kuis - Latsol SNBT Harian</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        .result-summary {
            background: var(--glass-background);
            padding: 2rem;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
            text-align: center;
        }
        .question-analysis {
            background: var(--glass-background);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
        }
        .choice {
            display: block;
            padding: 1rem;
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            margin-bottom: 0.5rem;
        }
        .choice.correct {
            background-color: rgba(29, 233, 182, 0.2);
            border-color: var(--accent-color);
        }
        .choice.incorrect {
            background-color: rgba(255, 82, 82, 0.2);
            border-color: #ff5252;
        }
        .explanation {
            margin-top: 1rem;
            padding: 1rem;
            background: rgba(0,0,0,0.2);
            border-radius: var(--border-radius);
        }
        .premium-blocker {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="background-animation"></div>
    <div class="container">
        <header class="main-header">
            <h1>Hasil Kuis</h1>
        </header>

        <main>
            <div class="result-summary glass-card">
                <h2>Skor Akhir: <?= $score ?></h2>
                <p>Kamu menjawab <?= $correctAnswers ?> dari <?= $totalQuestions ?> soal dengan benar.</p>
                <a href="/dashboard" class="button-logout" style="text-decoration:none;">Kembali ke Dashboard</a>
            </div>

            <?php foreach ($results as $index => $result): ?>
                <div class="question-analysis glass-card">
                    <h4>Pertanyaan #<?= $result['number'] ?></h4>
                    <p><?= nl2br(htmlspecialchars($result['stem'])) ?></p>

                    <div class="choices">
                        <?php
                        $choices = $result['choices'] ?? [];
                        foreach ($choices as $label => $content):
                            $class = 'choice';
                            if ($label == $result['correct_choice']) {
                                $class .= ' correct';
                            } elseif ($label == $result['user_choice'] && !$result['is_correct']) {
                                $class .= ' incorrect';
                            }
                        ?>
                            <span class="<?= $class ?>">
                                <strong><?= $label ?>.</strong> <?= htmlspecialchars($content) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>

                    <div class="explanation">
                        <h5>Pembahasan</h5>
                        <?php if ($isPremium): ?>
                            <p><?= nl2br(htmlspecialchars($result['explanation'])) ?></p>
                        <?php else:
                            $explanation = $result['explanation'];
                            $words = explode(' ', $explanation);
                            $preview = implode(' ', array_slice($words, 0, 5));
                        ?>
                            <p><?= nl2br(htmlspecialchars($preview)) ?>...</p>
                            <div class="premium-blocker">
                                <h5>Ingin lihat pembahasan lengkap?</h5>
                                <p>Hubungi admin untuk upgrade ke akun premium dan buka semua fitur!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </main>
    </div>
</body>
</html>
