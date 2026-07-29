<?php
/**
 * Quem Somos — Instituto Atleta Para Sempre
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Quem Somos';
$page_description = 'Conheça o Instituto Atleta Para Sempre, nossa missão, visão e valores no fomento ao esporte brasileiro.';

$org = db_fetch('SELECT * FROM tab_org WHERE cod_org = 10001 LIMIT 1');

ob_start();
?>
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a>
            <span aria-hidden="true">›</span>
            <span>Quem Somos</span>
        </nav>
        <h1 class="page-hero-title">Quem Somos</h1>
        <p class="page-hero-sub">Conheça nossa história, missão e o impacto que geramos através do esporte.</p>
    </div>
</section>

<section class="section" id="sobre">
    <div class="container">
        <div class="about-grid fade-in-up">
            <div class="about-text">
                <span class="section-tag">Nossa História</span>
                <h2 class="section-title">Instituto Atleta Para Sempre</h2>
                <p>O <strong>Instituto Atleta Para Sempre</strong> é uma organização do terceiro setor, sem fins lucrativos, dedicada ao desenvolvimento e fomento do esporte brasileiro por meio da Lei Federal de Incentivo ao Esporte (Lei nº 11.438, de 29 de dezembro de 2006).</p>
                <p>Atuamos como gestores de projetos esportivos captando recursos junto a patrocinadores e aplicando-os de forma transparente e eficiente em iniciativas que promovem:</p>
                <ul class="about-list">
                    <li>Inclusão social através do esporte de alto rendimento</li>
                    <li>Formação de atletas em diversas modalidades</li>
                    <li>Desenvolvimento humano e cidadania</li>
                    <li>Integração de pessoas com deficiência no paradesporto</li>
                    <li>Democratização do acesso ao esporte de qualidade</li>
                </ul>
                <?php if ($org && $org['site']): ?>
                <a href="<?= e($org['site']) ?>" class="btn btn-primary" target="_blank" rel="noopener">Visite nosso site</a>
                <?php endif; ?>
            </div>
            <div class="about-visual">
                <div class="about-card-stack">
                    <div class="about-highlight-card">
                        <div class="highlight-num">+15</div>
                        <div class="highlight-label">Anos de atuação</div>
                    </div>
                    <div class="about-highlight-card accent">
                        <div class="highlight-num">Lei</div>
                        <div class="highlight-label">11.438/2006<br>Incentivo ao Esporte</div>
                    </div>
                    <div class="about-highlight-card">
                        <div class="highlight-num">3º</div>
                        <div class="highlight-label">Setor — sem fins lucrativos</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MISSÃO, VISÃO E VALORES -->
<section class="section section-mission-detail" id="missao">
    <div class="container">
        <div class="section-header fade-in-up">
            <span class="section-tag">Nossa Essência</span>
            <h2 class="section-title">Missão, Visão e Valores</h2>
        </div>
        <div class="mvv-grid">
            <div class="mvv-card fade-in-up">
                <div class="mvv-icon">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/></svg>
                </div>
                <h3>Missão</h3>
                <p>Fomentar o esporte brasileiro por meio da captação e gestão transparente de recursos, promovendo inclusão social e desenvolvimento humano através de projetos esportivos de excelência.</p>
            </div>
            <div class="mvv-card fade-in-up" style="animation-delay:.1s">
                <div class="mvv-icon">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </div>
                <h3>Visão</h3>
                <p>Ser referência nacional na gestão de projetos esportivos, reconhecido pela transparência, impacto social e pela contribuição ao desenvolvimento do esporte de alto rendimento no Brasil.</p>
            </div>
            <div class="mvv-card fade-in-up" style="animation-delay:.2s">
                <div class="mvv-icon">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <h3>Valores</h3>
                <ul style="text-align:left; padding-left:1.2rem; margin:0">
                    <li>Transparência e ética</li>
                    <li>Excelência na gestão</li>
                    <li>Inclusão e diversidade</li>
                    <li>Comprometimento social</li>
                    <li>Respeito ao atleta</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- DADOS DA ORGANIZAÇÃO -->
<?php if ($org): ?>
<section class="section section-org-data" id="dados">
    <div class="container container-narrow">
        <div class="org-data-card fade-in-up">
            <h2 class="section-title" style="margin-bottom:1.5rem">Dados da Organização</h2>
            <div class="org-data-grid">
                <?php if ($org['nome_org']): ?>
                <div class="org-data-item">
                    <span class="org-data-label">Razão Social</span>
                    <span class="org-data-value"><?= e($org['nome_org']) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($org['telefone']): ?>
                <div class="org-data-item">
                    <span class="org-data-label">Telefone</span>
                    <span class="org-data-value"><?= e($org['telefone']) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($org['e_mail']): ?>
                <div class="org-data-item">
                    <span class="org-data-label">E-mail</span>
                    <span class="org-data-value"><a href="mailto:<?= e($org['e_mail']) ?>"><?= e($org['e_mail']) ?></a></span>
                </div>
                <?php endif; ?>
                <?php if ($org['endereco']): ?>
                <div class="org-data-item">
                    <span class="org-data-label">Endereço</span>
                    <span class="org-data-value"><?= e($org['endereco']) ?>, <?= e($org['bairro']) ?> — <?= e($org['cidade']) ?>/<?= e($org['estado']) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($org['cep']): ?>
                <div class="org-data-item">
                    <span class="org-data-label">CEP</span>
                    <span class="org-data-value"><?= e($org['cep']) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
