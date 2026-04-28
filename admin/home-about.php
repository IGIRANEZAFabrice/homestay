<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] !== true) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';

$msg     = '';
$msgType = 'success';

// ── Ensure at least one row exists ──────────────────────────────────────────
$check = $conn->query("SELECT COUNT(*) AS cnt FROM home_about");
$row   = $check->fetch_assoc();
if ((int)$row['cnt'] === 0) {
    $conn->query("INSERT INTO home_about (label, heading, body, badge_1, badge_2, badge_3,
        metric_1_num, metric_1_suffix, metric_1_label,
        metric_2_num, metric_2_suffix, metric_2_label,
        metric_3_num, metric_3_suffix, metric_3_label)
        VALUES (
            'Welcome to Virunga Homestay',
            'Your Musanze basecamp for volcano sunrises, slow-evening fires, and effortless guided days.',
            'Live inside a warm local home, wake to mountain air, and lean on accredited bilingual specialists for every trek, transfer, and taste of Rwanda, Uganda, or DRC. We blend heartfelt hosting with pro-level trip support so you can explore boldly and unwind completely.',
            'Family-run', 'Tourist Info Centre', 'Volcano & gorilla ready',
            12, '+', 'Years hosting',
            840, '', 'Stays curated',
            98, '%', 'Guests recommend'
        )");
}

// ── FORM SUBMIT ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $label   = $conn->real_escape_string(trim($_POST['label']   ?? ''));
    $heading = $conn->real_escape_string(trim($_POST['heading'] ?? ''));
    $body    = $conn->real_escape_string(trim($_POST['body']    ?? ''));

    $badge1 = $conn->real_escape_string(trim($_POST['badge_1'] ?? ''));
    $badge2 = $conn->real_escape_string(trim($_POST['badge_2'] ?? ''));
    $badge3 = $conn->real_escape_string(trim($_POST['badge_3'] ?? ''));

    $m1_num = (int)($_POST['metric_1_num'] ?? 0);
    $m1_suf = $conn->real_escape_string(trim($_POST['metric_1_suffix'] ?? ''));
    $m1_lbl = $conn->real_escape_string(trim($_POST['metric_1_label']  ?? ''));

    $m2_num = (int)($_POST['metric_2_num'] ?? 0);
    $m2_suf = $conn->real_escape_string(trim($_POST['metric_2_suffix'] ?? ''));
    $m2_lbl = $conn->real_escape_string(trim($_POST['metric_2_label']  ?? ''));

    $m3_num = (int)($_POST['metric_3_num'] ?? 0);
    $m3_suf = $conn->real_escape_string(trim($_POST['metric_3_suffix'] ?? ''));
    $m3_lbl = $conn->real_escape_string(trim($_POST['metric_3_label']  ?? ''));

    $id = (int)($_POST['id'] ?? 0);

    $sql = "UPDATE home_about SET
        label='$label', heading='$heading', body='$body',
        badge_1='$badge1', badge_2='$badge2', badge_3='$badge3',
        metric_1_num=$m1_num, metric_1_suffix='$m1_suf', metric_1_label='$m1_lbl',
        metric_2_num=$m2_num, metric_2_suffix='$m2_suf', metric_2_label='$m2_lbl',
        metric_3_num=$m3_num, metric_3_suffix='$m3_suf', metric_3_label='$m3_lbl'
        WHERE id=$id";

    if ($conn->query($sql)) {
        $msg = 'Home About section updated successfully.';
    } else {
        $msg     = 'Error updating: ' . $conn->error;
        $msgType = 'error';
    }
}

// ── Load current data ───────────────────────────────────────────────────────
$res = $conn->query("SELECT * FROM home_about LIMIT 1");
$r   = $res->fetch_assoc();

$pageTitle   = 'Home About — Virunga Homestay CMS';
$currentPage = 'home-about';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $pageTitle ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- Core Admin CSS -->
    <link rel="stylesheet" href="css/header.css" />
    <link rel="stylesheet" href="css/sidebar.css" />
    <link rel="stylesheet" href="css/dashboard.css" />

    <style>
      /* ── Form Styles ── */
      .crud-form {
        display: flex; flex-direction: column; gap: 22px;
        max-width: 780px;
        background: var(--surface-1);
        padding: 32px;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
      }
      .crud-form .form-group { display: flex; flex-direction: column; gap: 7px; }
      .crud-form label {
        font-size: 11px; font-weight: 600; text-transform: uppercase;
        letter-spacing: 0.6px; color: var(--text-2);
      }
      .crud-form input[type="text"],
      .crud-form input[type="number"],
      .crud-form textarea,
      .crud-form select {
        padding: 12px 14px;
        border: 1px solid var(--border);
        background: var(--surface-2);
        color: var(--text-1);
        border-radius: 8px;
        font-family: inherit;
        font-size: 14px;
        transition: border-color 0.2s, box-shadow 0.2s;
      }
      .crud-form input:focus,
      .crud-form textarea:focus,
      .crud-form select:focus {
        border-color: var(--amber);
        outline: none;
        box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
      }
      .crud-form textarea { resize: vertical; min-height: 110px; line-height: 1.6; }
      .crud-btn {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 13px 24px;
        border: none;
        background: var(--amber);
        color: #000;
        border-radius: 9px;
        cursor: pointer;
        font-weight: 700;
        font-size: 14px;
        transition: filter 0.2s, transform 0.1s;
      }
      .crud-btn:hover { filter: brightness(0.92); transform: translateY(-1px); }
      .crud-btn:active { transform: translateY(0); }

      /* ── Alert Banner ── */
      .alert {
        display: flex; align-items: center; gap: 12px;
        padding: 14px 20px;
        border-radius: 9px;
        margin-bottom: 22px;
        font-size: 14px; font-weight: 500;
        animation: slideIn 0.3s ease;
      }
      @keyframes slideIn { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
      .alert--success { background: var(--success); color: #fff; }
      .alert--error   { background: var(--danger);  color: #fff; }

      /* ── Section divider ── */
      .form-divider {
        display: flex; align-items: center; gap: 14px;
        margin: 8px 0 2px;
      }
      .form-divider__label {
        font-size: 13px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1px; color: var(--amber);
        white-space: nowrap;
      }
      .form-divider__line {
        flex: 1; height: 1px; background: var(--border);
      }

      /* ── Metrics row ── */
      .metric-row {
        display: grid;
        grid-template-columns: 1fr 100px 1fr;
        gap: 14px;
        align-items: end;
      }
      @media (max-width: 600px) {
        .metric-row {
          grid-template-columns: 1fr;
        }
      }

      /* ── Badges row ── */
      .badges-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
      }
      @media (max-width: 600px) {
        .badges-row {
          grid-template-columns: 1fr;
        }
      }

      /* ── Live preview card ── */
      .preview-panel {
        background: var(--surface-1);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 28px;
        box-shadow: var(--shadow-sm);
      }
      .preview-panel__title {
        font-size: 13px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1px; color: var(--amber); margin-bottom: 18px;
      }
      .preview-label {
        font-size: 12px; font-weight: 600; text-transform: uppercase;
        letter-spacing: 1.2px; color: var(--amber); margin-bottom: 6px;
      }
      .preview-heading {
        font-family: 'Cormorant Garamond', serif;
        font-size: 22px; font-weight: 600; color: var(--text-1);
        line-height: 1.3; margin-bottom: 10px;
      }
      .preview-body {
        font-size: 14px; color: var(--text-2); line-height: 1.65;
        margin-bottom: 14px;
      }
      .preview-badges {
        display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 18px;
      }
      .preview-badges span {
        display: inline-block;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 12px; font-weight: 600;
        background: rgba(212,175,55,0.12);
        color: var(--amber);
        border: 1px solid rgba(212,175,55,0.2);
      }
      .preview-metrics {
        display: flex; gap: 28px; flex-wrap: wrap;
      }
      .preview-metric {
        text-align: center;
      }
      .preview-metric__num {
        font-family: 'Cormorant Garamond', serif;
        font-size: 28px; font-weight: 700; color: var(--amber);
      }
      .preview-metric__label {
        font-size: 11px; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.5px;
      }

      /* ── Layout grid ── */
      .about-edit-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 28px;
        align-items: start;
      }
      @media (max-width: 960px) {
        .about-edit-grid {
          grid-template-columns: 1fr;
        }
      }
    </style>
  </head>
  <body>
    <?php include 'includes/header.php'; ?>

    <div class="dash-layout" id="dashLayout">
      <?php include 'includes/sidebar.php'; ?>

      <main class="dash-main">
        <!-- Page Header -->
        <div class="page-header">
          <div>
            <h1 class="page-header__title">Home About Section</h1>
            <p class="page-header__sub">
              Edit the welcome belt displayed on the homepage — tagline, description, badges, and counter metrics.
            </p>
          </div>
          <div class="page-header__actions">
            <a href="../home" target="_blank" class="btn btn-ghost">
              <i class="fa-solid fa-eye"></i> View Live
            </a>
          </div>
        </div>

        <div class="dash-grid gap-section" style="grid-template-columns: 1fr;">
          <div class="panel">
            <div class="panel__body">

              <!-- Alert Banner -->
              <?php if (!empty($msg)): ?>
                <div class="alert alert--<?= $msgType === 'error' ? 'error' : 'success' ?>">
                  <i class="fa-solid <?= $msgType === 'error' ? 'fa-circle-exclamation' : 'fa-circle-check' ?>"></i>
                  <?= htmlspecialchars($msg) ?>
                </div>
              <?php endif; ?>

              <div class="about-edit-grid">
                <!-- ══ FORM ════════════════════════════════════════════════ -->
                <form method="POST" action="home-about.php" class="crud-form" id="aboutForm">
                  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>" />

                  <!-- ── Text Content ─────────────────────────────────── -->
                  <div class="form-divider">
                    <span class="form-divider__label"><i class="fa-solid fa-pen-nib"></i> Text Content</span>
                    <span class="form-divider__line"></span>
                  </div>

                  <div class="form-group">
                    <label>Section Label *</label>
                    <input type="text" name="label" id="fLabel" required maxlength="120"
                           value="<?= htmlspecialchars($r['label']) ?>"
                           placeholder="e.g. Welcome to Virunga Homestay" />
                  </div>

                  <div class="form-group">
                    <label>Heading *</label>
                    <textarea name="heading" id="fHeading" required maxlength="500"
                              placeholder="Main heading text…"><?= htmlspecialchars($r['heading']) ?></textarea>
                  </div>

                  <div class="form-group">
                    <label>Body Paragraph *</label>
                    <textarea name="body" id="fBody" required maxlength="2000"
                              placeholder="Detailed description…" style="min-height:140px;"><?= htmlspecialchars($r['body']) ?></textarea>
                  </div>

                  <!-- ── Badges ───────────────────────────────────────── -->
                  <div class="form-divider">
                    <span class="form-divider__label"><i class="fa-solid fa-certificate"></i> Badges</span>
                    <span class="form-divider__line"></span>
                  </div>

                  <div class="badges-row">
                    <div class="form-group">
                      <label>Badge 1</label>
                      <input type="text" name="badge_1" id="fBadge1" maxlength="60"
                             value="<?= htmlspecialchars($r['badge_1']) ?>"
                             placeholder="e.g. Family-run" />
                    </div>
                    <div class="form-group">
                      <label>Badge 2</label>
                      <input type="text" name="badge_2" id="fBadge2" maxlength="60"
                             value="<?= htmlspecialchars($r['badge_2']) ?>"
                             placeholder="e.g. Tourist Info Centre" />
                    </div>
                    <div class="form-group">
                      <label>Badge 3</label>
                      <input type="text" name="badge_3" id="fBadge3" maxlength="60"
                             value="<?= htmlspecialchars($r['badge_3']) ?>"
                             placeholder="e.g. Volcano & gorilla ready" />
                    </div>
                  </div>

                  <!-- ── Metric 1 ─────────────────────────────────────── -->
                  <div class="form-divider">
                    <span class="form-divider__label"><i class="fa-solid fa-chart-simple"></i> Metric 1</span>
                    <span class="form-divider__line"></span>
                  </div>

                  <div class="metric-row">
                    <div class="form-group">
                      <label>Number</label>
                      <input type="number" name="metric_1_num" id="fM1Num" min="0"
                             value="<?= (int)$r['metric_1_num'] ?>" />
                    </div>
                    <div class="form-group">
                      <label>Suffix</label>
                      <input type="text" name="metric_1_suffix" id="fM1Suf" maxlength="10"
                             value="<?= htmlspecialchars($r['metric_1_suffix']) ?>"
                             placeholder="e.g. + or %" />
                    </div>
                    <div class="form-group">
                      <label>Label</label>
                      <input type="text" name="metric_1_label" id="fM1Lbl" maxlength="60"
                             value="<?= htmlspecialchars($r['metric_1_label']) ?>"
                             placeholder="e.g. Years hosting" />
                    </div>
                  </div>

                  <!-- ── Metric 2 ─────────────────────────────────────── -->
                  <div class="form-divider">
                    <span class="form-divider__label"><i class="fa-solid fa-chart-simple"></i> Metric 2</span>
                    <span class="form-divider__line"></span>
                  </div>

                  <div class="metric-row">
                    <div class="form-group">
                      <label>Number</label>
                      <input type="number" name="metric_2_num" id="fM2Num" min="0"
                             value="<?= (int)$r['metric_2_num'] ?>" />
                    </div>
                    <div class="form-group">
                      <label>Suffix</label>
                      <input type="text" name="metric_2_suffix" id="fM2Suf" maxlength="10"
                             value="<?= htmlspecialchars($r['metric_2_suffix']) ?>"
                             placeholder="e.g. + or %" />
                    </div>
                    <div class="form-group">
                      <label>Label</label>
                      <input type="text" name="metric_2_label" id="fM2Lbl" maxlength="60"
                             value="<?= htmlspecialchars($r['metric_2_label']) ?>"
                             placeholder="e.g. Stays curated" />
                    </div>
                  </div>

                  <!-- ── Metric 3 ─────────────────────────────────────── -->
                  <div class="form-divider">
                    <span class="form-divider__label"><i class="fa-solid fa-chart-simple"></i> Metric 3</span>
                    <span class="form-divider__line"></span>
                  </div>

                  <div class="metric-row">
                    <div class="form-group">
                      <label>Number</label>
                      <input type="number" name="metric_3_num" id="fM3Num" min="0"
                             value="<?= (int)$r['metric_3_num'] ?>" />
                    </div>
                    <div class="form-group">
                      <label>Suffix</label>
                      <input type="text" name="metric_3_suffix" id="fM3Suf" maxlength="10"
                             value="<?= htmlspecialchars($r['metric_3_suffix']) ?>"
                             placeholder="e.g. + or %" />
                    </div>
                    <div class="form-group">
                      <label>Label</label>
                      <input type="text" name="metric_3_label" id="fM3Lbl" maxlength="60"
                             value="<?= htmlspecialchars($r['metric_3_label']) ?>"
                             placeholder="e.g. Guests recommend" />
                    </div>
                  </div>

                  <!-- Submit -->
                  <div class="form-group" style="margin-top:10px;">
                    <button type="submit" class="crud-btn" id="submitBtn">
                      <i class="fa-solid fa-floppy-disk"></i>
                      Save Changes
                    </button>
                  </div>
                </form>

                <!-- ══ LIVE PREVIEW ════════════════════════════════════ -->
                <div class="preview-panel" id="previewPanel">
                  <div class="preview-panel__title">
                    <i class="fa-solid fa-eye"></i> Live Preview
                  </div>
                  <div class="preview-label" id="pvLabel"><?= htmlspecialchars($r['label']) ?></div>
                  <div class="preview-heading" id="pvHeading"><?= htmlspecialchars($r['heading']) ?></div>
                  <div class="preview-body" id="pvBody"><?= htmlspecialchars($r['body']) ?></div>
                  <div class="preview-badges" id="pvBadges">
                    <?php if (!empty($r['badge_1'])): ?><span><?= htmlspecialchars($r['badge_1']) ?></span><?php endif; ?>
                    <?php if (!empty($r['badge_2'])): ?><span><?= htmlspecialchars($r['badge_2']) ?></span><?php endif; ?>
                    <?php if (!empty($r['badge_3'])): ?><span><?= htmlspecialchars($r['badge_3']) ?></span><?php endif; ?>
                  </div>
                  <div class="preview-metrics">
                    <div class="preview-metric">
                      <div class="preview-metric__num" id="pvM1">
                        <?= (int)$r['metric_1_num'] ?><?= htmlspecialchars($r['metric_1_suffix']) ?>
                      </div>
                      <div class="preview-metric__label" id="pvM1L"><?= htmlspecialchars($r['metric_1_label']) ?></div>
                    </div>
                    <div class="preview-metric">
                      <div class="preview-metric__num" id="pvM2">
                        <?= (int)$r['metric_2_num'] ?><?= htmlspecialchars($r['metric_2_suffix']) ?>
                      </div>
                      <div class="preview-metric__label" id="pvM2L"><?= htmlspecialchars($r['metric_2_label']) ?></div>
                    </div>
                    <div class="preview-metric">
                      <div class="preview-metric__num" id="pvM3">
                        <?= (int)$r['metric_3_num'] ?><?= htmlspecialchars($r['metric_3_suffix']) ?>
                      </div>
                      <div class="preview-metric__label" id="pvM3L"><?= htmlspecialchars($r['metric_3_label']) ?></div>
                    </div>
                  </div>
                </div>
              </div><!-- /.about-edit-grid -->

            </div><!-- /.panel__body -->
          </div><!-- /.panel -->
        </div>
      </main>
    </div>

    <script src="js/index.js"></script>
    <script>
      // ── Live preview binding ──────────────────────────────────────────
      const bind = (inputId, previewId, transform) => {
        const input   = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (!input || !preview) return;
        input.addEventListener('input', () => {
          preview.textContent = transform ? transform(input.value) : input.value;
        });
      };

      bind('fLabel',   'pvLabel');
      bind('fHeading', 'pvHeading');
      bind('fBody',    'pvBody');

      // Badges — rebuild all three on any change
      const rebuildBadges = () => {
        const container = document.getElementById('pvBadges');
        container.innerHTML = '';
        ['fBadge1','fBadge2','fBadge3'].forEach(id => {
          const v = document.getElementById(id).value.trim();
          if (v) {
            const s = document.createElement('span');
            s.textContent = v;
            container.appendChild(s);
          }
        });
      };
      ['fBadge1','fBadge2','fBadge3'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', rebuildBadges);
      });

      // Metrics
      const updateMetric = (numId, sufId, pvNumId, lblId, pvLblId) => {
        const numEl = document.getElementById(numId);
        const sufEl = document.getElementById(sufId);
        const pvNum = document.getElementById(pvNumId);
        const lblEl = document.getElementById(lblId);
        const pvLbl = document.getElementById(pvLblId);
        const refresh = () => {
          pvNum.textContent = (numEl.value || '0') + (sufEl.value || '');
          pvLbl.textContent = lblEl.value;
        };
        numEl?.addEventListener('input', refresh);
        sufEl?.addEventListener('input', refresh);
        lblEl?.addEventListener('input', refresh);
      };
      updateMetric('fM1Num','fM1Suf','pvM1','fM1Lbl','pvM1L');
      updateMetric('fM2Num','fM2Suf','pvM2','fM2Lbl','pvM2L');
      updateMetric('fM3Num','fM3Suf','pvM3','fM3Lbl','pvM3L');

      // ── Auto-dismiss alert after 5s ─────────────────────────────────
      const alertEl = document.querySelector('.alert');
      if (alertEl) {
        setTimeout(() => {
          alertEl.style.transition = 'opacity 0.4s';
          alertEl.style.opacity = '0';
          setTimeout(() => alertEl.remove(), 400);
        }, 5000);
      }
    </script>
  </body>
</html>
