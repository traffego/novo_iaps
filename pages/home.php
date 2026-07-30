<?php
/**
 * Home — Instituto Atleta Para Sempre
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Início';
$page_description = 'Instituto Atleta Para Sempre — promovendo inclusão social e desenvolvimento humano através do esporte de alto rendimento.';

// Projetos em destaque
$projetos_destaque = db_fetch_all(
    'SELECT p.*, ps.projetos_status as status_nome
     FROM tab_projetos p
     LEFT JOIN tab_projetos_status ps ON p.projeto_status = ps.id
     WHERE p.mostra_inicial = 1 AND p.ativo = 1
     ORDER BY p.id DESC LIMIT 6'
);

// Números de impacto
$total_projetos  = db_count('SELECT COUNT(*) FROM tab_projetos WHERE ativo = 1');
$total_curriculos = db_count('SELECT COUNT(*) FROM tab_curriculos');
$total_docs      = db_count('SELECT COUNT(*) FROM tab_projetos_documentos');

// Mapa de cores por status
$status_class = [
    'Concluído'   => 'badge-success',
    'Em Execução' => 'badge-primary',
    'Em Prestação de Contas' => 'badge-warning',
    'Assinado'    => 'badge-info',
    'Proposta Aprovada e Plano de Trabalho Complementado em Análise' => 'badge-secondary',
];

ob_start();
?>
<!-- HERO -->
<section class="hero" id="hero">
    <div class="hero-bg"></div>
    <div class="container hero-content fade-in-up">
        <span class="hero-badge">Esporte que transforma</span>
        <h1 class="hero-title">Instituto Atleta<br><span class="text-gradient">Para Sempre</span></h1>
        <p class="hero-subtitle">
            Promovemos inclusão social, desenvolvimento humano e cidadania<br>
            através do esporte de alto rendimento e da Lei de Incentivo ao Esporte.
        </p>
        <div class="hero-actions">
            <a href="/trabalhe-conosco" class="btn btn-primary btn-lg">Trabalhe Conosco</a>
            <a href="/editais" class="btn btn-outline btn-lg">Ver Editais</a>
        </div>
    </div>
    <div class="hero-scroll-hint" aria-hidden="true">
        <span></span>
    </div>
</section>

<!-- NÚMEROS DE IMPACTO -->
<section class="section section-stats" id="impacto">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card fade-in-up">
                <div class="stat-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                </div>
                <div class="stat-number">15+</div>
                <div class="stat-label">Projetos ativos</div>
            </div>
            <div class="stat-card fade-in-up" style="animation-delay:.1s">
                <div class="stat-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="stat-number" style="font-size: 2rem;">mais de 50</div>
                <div class="stat-label">Carreiras encaminhadas</div>
            </div>
            <div class="stat-card fade-in-up" style="animation-delay:.2s">
                <div class="stat-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div class="stat-number" data-counter data-target="<?= $total_docs ?>"><?= $total_docs ?></div>
                <div class="stat-label">Documentos Publicados</div>
            </div>
            <div class="stat-card fade-in-up" style="animation-delay:.3s">
                <div class="stat-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                </div>
                <div class="stat-number" data-counter data-target="100">100</div>
                <div class="stat-label">% Transparência</div>
            </div>
        </div>
    </div>
</section>

<!-- PROJETOS EM DESTAQUE -->
<?php if (!empty($projetos_destaque)): ?>
<section class="section" id="projetos">
    <div class="container">
        <div class="section-header fade-in-up">
            <span class="section-tag">Nossos Projetos</span>
            <h2 class="section-title">Projetos em Destaque</h2>
            <p class="section-subtitle">Conheça as iniciativas que estamos desenvolvendo para transformar vidas através do esporte.</p>
        </div>
        <div class="projects-grid">
            <?php foreach ($projetos_destaque as $p): ?>
            <article class="project-card fade-in-up">
                <div class="project-card-header">
                    <span class="badge <?= e($status_class[$p['status_nome']] ?? 'badge-secondary') ?>">
                        <?= e($p['status_nome'] ?? 'Em andamento') ?>
                    </span>
                    <?php if ($p['valor']): ?>
                    <span class="project-value"><?= e($p['valor']) ?></span>
                    <?php endif; ?>
                </div>
                <h3 class="project-card-title"><?= e($p['nome_projeto']) ?></h3>
                <?php if ($p['num_proposta']): ?>
                <p class="project-proposta">Proposta nº <?= e($p['num_proposta']) ?></p>
                <?php endif; ?>
                <?php if ($p['objeto']): ?>
                <p class="project-card-desc"><?= e(truncate($p['objeto'], 150)) ?></p>
                <?php endif; ?>
                <a href="/editais" class="project-card-link">Ver documentos →</a>
            </article>
            <?php endforeach; ?>
        </div>
        <div class="text-center" style="margin-top:2rem">
            <a href="/editais" class="btn btn-outline">Ver todos os editais</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- MISSÃO INSTITUCIONAL -->
<section class="section section-mission fade-in-up" id="missao">
    <div class="container">
        <div class="mission-grid">
            <div class="mission-content">
                <span class="section-tag">Nossa Missão</span>
                <h2 class="section-title">Esporte como<br>ferramenta de transformação</h2>
                <p>O Instituto Atleta Para Sempre é uma organização do terceiro setor dedicada ao fomento do esporte brasileiro por meio da Lei Federal de Incentivo ao Esporte (Lei 11.438/2006).</p>
                <p>Atuamos captando e gerenciando recursos para projetos esportivos que promovem inclusão social, desenvolvimento humano e formação de atletas de alto rendimento em todo o Brasil.</p>
                <div class="mission-links">
                    <a href="/quem-somos" class="btn btn-primary">Conheça o Instituto</a>
                    <a href="/transparencia/declaracao" class="btn btn-ghost">Transparência</a>
                </div>
            </div>
            <div class="mission-values">
                <div class="value-card">
                    <div class="value-icon">🏆</div>
                    <h4>Excelência</h4>
                    <p>Comprometidos com a qualidade em cada projeto e iniciativa esportiva.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🤝</div>
                    <h4>Inclusão</h4>
                    <p>O esporte como ponte para a igualdade e oportunidades para todos.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">📋</div>
                    <h4>Transparência</h4>
                    <p>Prestação de contas pública e total transparência na gestão dos recursos.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">💪</div>
                    <h4>Compromisso</h4>
                    <p>Dedicação plena ao desenvolvimento humano através do esporte.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA TRABALHE CONOSCO -->
<section class="section section-cta fade-in-up" id="cta">
    <div class="container">
        <div class="cta-card">
            <div class="cta-content">
                <h2>Faça parte do nosso time</h2>
                <p>Estamos sempre em busca de profissionais apaixonados pelo esporte e pelo impacto social. Envie seu currículo e junte-se a nós!</p>
            </div>
            <div class="cta-actions">
                <a href="/trabalhe-conosco" class="btn btn-primary btn-lg">Enviar Currículo</a>
                <a href="/contato" class="btn btn-outline-white btn-lg">Fale Conosco</a>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
