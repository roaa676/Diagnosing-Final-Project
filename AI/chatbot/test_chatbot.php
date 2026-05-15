<?php
/**
 * ─────────────────────────────────────────────────────────────────────────────
 * اختبار مستقل لمساعد بوصلة — لا يحتاج Laravel أو OpenAI API
 * يختبر: SkillRulesRepository + SafetyFilter + Fallback Response
 * ─────────────────────────────────────────────────────────────────────────────
 *
 * شغّله بـ: php test_chatbot.php
 */

declare(strict_types=1);

// ─── تعريف base_path mock بدل Laravel ───────────────────────────────────────
function base_path(string $path = ''): string
{
    $base = __DIR__ . '/../../backend';
    return $base . ($path ? '/' . ltrim($path, '/') : '');
}

// ─── تحميل الملفات مباشرة (بدون autoloader) ────────────────────────────────
require_once __DIR__ . '/services/SkillRulesRepository.php';
require_once __DIR__ . '/services/SafetyFilter.php';

use App\Services\Chatbot\SkillRulesRepository;
use App\Services\Chatbot\SafetyFilter;

// ─── ألوان الـ terminal ───────────────────────────────────────────────────────
$GREEN  = "\033[0;32m";
$YELLOW = "\033[1;33m";
$RED    = "\033[0;31m";
$BLUE   = "\033[0;34m";
$CYAN   = "\033[0;36m";
$BOLD   = "\033[1m";
$RESET  = "\033[0m";

function section(string $title): void
{
    global $BOLD, $CYAN, $RESET;
    echo "\n{$BOLD}{$CYAN}══════════════════════════════════════════════════{$RESET}\n";
    echo "{$BOLD}{$CYAN}  {$title}{$RESET}\n";
    echo "{$BOLD}{$CYAN}══════════════════════════════════════════════════{$RESET}\n";
}

function pass(string $msg): void { global $GREEN, $RESET; echo "  {$GREEN}✓ {$msg}{$RESET}\n"; }
function fail(string $msg): void { global $RED, $RESET;   echo "  {$RED}✗ {$msg}{$RESET}\n"; }
function info(string $msg): void { global $YELLOW, $RESET; echo "  {$YELLOW}→ {$msg}{$RESET}\n"; }
function box(string $label, string $content): void
{
    global $BLUE, $RESET, $BOLD;
    echo "\n  {$BOLD}{$BLUE}[{$label}]{$RESET}\n";
    $lines = explode("\n", $content);
    foreach ($lines as $line) {
        echo "  {$line}\n";
    }
}

// ─────────────────────────────────────────────────────────────────────────────
section("1. تحميل SkillRulesRepository");
// ─────────────────────────────────────────────────────────────────────────────

try {
    $repo = new SkillRulesRepository();
    pass("تم تحميل SkillRulesRepository بنجاح");
} catch (\Throwable $e) {
    fail("فشل التحميل: " . $e->getMessage());
    exit(1);
}

// ─────────────────────────────────────────────────────────────────────────────
section("2. اختبار قراءة المهارات");
// ─────────────────────────────────────────────────────────────────────────────

$skills = [
    'dyslexia_mirroring',
    'dyslexia_visual_discrimination',
    'dyslexia_structural_similarity',
    'dyscalculia_quantity_comparison',
    'dyscalculia_number_sequence',
    'dyscalculia_number_reversal',
];

foreach ($skills as $skillId) {
    $name = $repo->getSkillArabicName($skillId);
    if ($name && $name !== $skillId) {
        pass("{$skillId} → {$name}");
    } else {
        fail("{$skillId} → لم يُوجَد");
    }
}

// ─────────────────────────────────────────────────────────────────────────────
section("3. اختبار تفسير الدرجات");
// ─────────────────────────────────────────────────────────────────────────────

$testScores = [
    ['score' => 30,  'expected_severity' => 'high'],
    ['score' => 42,  'expected_severity' => 'high'],
    ['score' => 55,  'expected_severity' => 'moderate'],
    ['score' => 65,  'expected_severity' => 'moderate'],
    ['score' => 75,  'expected_severity' => 'low'],
    ['score' => 90,  'expected_severity' => 'low'],
];

foreach ($testScores as $test) {
    $band  = $repo->getScoreBand($test['score']);
    $label = $repo->getScoreArabicLabel($test['score']);
    $level = $repo->getRecommendedTrainingLevel($test['score']);

    $actualSeverity = $band['severity'];
    $ok = ($actualSeverity === $test['expected_severity']);

    if ($ok) {
        pass("درجة {$test['score']} → {$label} (مستوى تدريب: {$level})");
    } else {
        fail("درجة {$test['score']} → خطأ في التصنيف: {$actualSeverity} بدلاً من {$test['expected_severity']}");
    }
}

// ─────────────────────────────────────────────────────────────────────────────
section("4. اختبار توصيات التمارين");
// ─────────────────────────────────────────────────────────────────────────────

$testExercises = [
    ['skill' => 'dyslexia_mirroring',           'level' => 1],
    ['skill' => 'dyscalculia_quantity_comparison', 'level' => 2],
    ['skill' => 'dyslexia_visual_discrimination', 'level' => 3],
];

foreach ($testExercises as $test) {
    $exercises = $repo->getExercisesForSkill($test['skill'], $test['level']);
    if (!empty($exercises)) {
        pass("{$test['skill']} مستوى {$test['level']} → " . count($exercises) . " تمارين");
        $first = $exercises[0];
        info("أول تمرين: " . ($first['name'] ?? 'بدون اسم'));
    } else {
        fail("{$test['skill']} مستوى {$test['level']} → لا توجد تمارين");
    }
}

// ─────────────────────────────────────────────────────────────────────────────
section("5. اختبار SafetyFilter — كشف التشخيصات المحظورة");
// ─────────────────────────────────────────────────────────────────────────────

$filter = new SafetyFilter();

$dangerousResponses = [
    "طفلك مصاب بعسر القراءة وتحتاج للعلاج الفوري.",
    "هذا تشخيص مؤكد لديسلكسيا.",
    "الحالة خطيرة وتستدعي تدخلاً فورياً.",
    "ننصح بإعطاء الطفل دواء ريتالين.",
];

foreach ($dangerousResponses as $response) {
    $result = $filter->check($response, false);
    if (!$result['safe'] && !empty($result['violations'])) {
        pass("تم اكتشاف: " . implode('، ', $result['violations']));
        info("الرد بعد التنقية: " . substr($result['response'], 0, 80) . "...");
    } else {
        fail("لم يُكتشف الخطر في: " . substr($response, 0, 50));
    }
}

// ─────────────────────────────────────────────────────────────────────────────
section("6. اختبار SafetyFilter — تأكيد إضافة Disclaimer");
// ─────────────────────────────────────────────────────────────────────────────

$responseWithoutDisclaimer = "الطفل يُظهر بعض مؤشرات مرتبطة بصعوبة الانعكاس المرآتي. ننصح بالتمارين.";
$result = $filter->check($responseWithoutDisclaimer, requireDisclaimer: true);

if (str_contains($result['response'], 'نتيجة مبدئية')) {
    pass("تم إضافة disclaimer تلقائياً");
    info(substr($result['response'], -100));
} else {
    fail("لم يُضف disclaimer");
}

// ─────────────────────────────────────────────────────────────────────────────
section("7. اختبار الرد الاحتياطي (Fallback Response)");
// ─────────────────────────────────────────────────────────────────────────────

// نموذج بيانات طفل (يحاكي ResultContextBuilder::buildContext())
$childContext = [
    'child' => [
        'age'          => 7,
        'name_alias'   => 'الطفل',
        'session_date' => date('Y-m-d'),
    ],
    'overall_summary' => [
        'main_indicator' => 'dyslexia_mirroring',
        'severity'       => 'high',
        'note'           => 'هذه نتيجة مبدئية مبنية على أداء الطفل داخل المنصة، وليست تشخيصًا طبيًا نهائيًا.',
    ],
    'skills' => [
        [
            'skill_id'                   => 'dyslexia_mirroring',
            'arabic_name'                => 'الانعكاس المرآتي للحروف والكلمات',
            'score'                      => 42,
            'level'                      => 'high_difficulty_indicator',
            'arabic_level_label'         => 'مؤشر صعوبة مرتفع',
            'recommended_training_level' => 1,
        ],
        [
            'skill_id'                   => 'dyscalculia_quantity_comparison',
            'arabic_name'                => 'إدراك الكميات ومقارنتها',
            'score'                      => 76,
            'level'                      => 'good_performance',
            'arabic_level_label'         => 'أداء جيد',
            'recommended_training_level' => 3,
        ],
        [
            'skill_id'                   => 'dyscalculia_number_sequence',
            'arabic_name'                => 'التسلسل الرقمي',
            'score'                      => 58,
            'level'                      => 'moderate_difficulty_indicator',
            'arabic_level_label'         => 'مؤشر صعوبة متوسط',
            'recommended_training_level' => 2,
        ],
    ],
];

$fallbackResponse = $filter->buildFallbackResponse($childContext, $repo);

if (!empty($fallbackResponse) && str_contains($fallbackResponse, 'نتيجة مبدئية')) {
    pass("تم بناء الرد الاحتياطي بنجاح");
} else {
    fail("الرد الاحتياطي فارغ أو ناقص");
}

box("الرد الاحتياطي الكامل (ما سيراه ولي الأمر بدون API)", $fallbackResponse);

// ─────────────────────────────────────────────────────────────────────────────
section("8. اختبار Disclaimer الإلزامي");
// ─────────────────────────────────────────────────────────────────────────────

$disclaimer = $repo->getMandatoryDisclaimer();
pass("Disclaimer: {$disclaimer}");

// ─────────────────────────────────────────────────────────────────────────────
section("9. اختبار تحويل risk_level إلى severity");
// ─────────────────────────────────────────────────────────────────────────────

$riskMappings = [
    'High Risk'     => 'high',
    'Moderate Risk' => 'moderate',
    'Normal'        => 'low',
    'No Risk'       => 'low',
    'No Norm Data'  => 'unknown',
];

foreach ($riskMappings as $riskLevel => $expectedSeverity) {
    $actual = $repo->mapRiskLevelToSeverity($riskLevel);
    if ($actual === $expectedSeverity) {
        pass("{$riskLevel} → {$actual}");
    } else {
        fail("{$riskLevel} → خطأ: {$actual} بدلاً من {$expectedSeverity}");
    }
}

// ─────────────────────────────────────────────────────────────────────────────
section("✅ انتهى الاختبار");
// ─────────────────────────────────────────────────────────────────────────────
echo "\n";
