<?php
/**
 * Quem Somos — Instituto Atleta Para Sempre
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Quem Somos';
$page_description = 'Conheça o Instituto Atleta Para Sempre, nossa história, missão e o impacto gerado por meio do esporte e da cidadania.';

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
        <p class="page-hero-sub">Promovendo a cidadania e o desenvolvimento humano integral através da prática esportiva.</p>
    </div>
</section>

<section class="section" id="sobre">
    <div class="container">
        <div class="about-grid fade-in-up">
            <div class="about-text">
                <span class="section-tag">Nossa História & Propósito</span>
                <h2 class="section-title">Instituto Atleta para Sempre</h2>
                
                <p>O <strong>Instituto Atleta para Sempre</strong> é uma Organização da Sociedade Civil, de caráter social e beneficente, instituída em 2012, com sede em Recife-PE. Atuamos na promoção da cidadania e no fortalecimento do desenvolvimento humano por meio da prática esportiva, especialmente em comunidades em situação de vulnerabilidade social. Nossas atividades são orientadas pelos princípios da Assistência Social e alinhadas ao interesse público, em conformidade com a Lei nº 13.019/2014.</p>
                
                <p>Temos como patrono o Capitão do Tetra, <strong>Ricardo Rocha</strong>, cuja experiência profissional e metodologia de gestão esportiva fundamentam as ações desenvolvidas pelo Instituto. Sua prática, consolidada ao longo de trabalhos realizados em diferentes regiões do Estado, contribuiu para a formação esportiva e social de mais de 2.000 crianças e adolescentes.</p>
                
                <p>O <strong>Instituto Atleta para Sempre</strong> atua de forma integrada com parceiros públicos e privados, ampliando o acesso ao esporte, ao lazer e à formação cidadã. Nosso propósito é oferecer alternativas qualificadas para o contraturno escolar, promovendo habilidades técnicas, motoras e sociais, além de incentivar valores como respeito, honestidade e responsabilidade.</p>
                
                <p>Comprometemo-nos com a construção de oportunidades e com a transformação social, contribuindo para que crianças, jovens e adultos desenvolvam trajetórias mais seguras, saudáveis e alinhadas ao exercício pleno da cidadania.</p>
            </div>

            <div class="about-visual">
                <div class="card patrono-card">
                    <div class="patrono-image-wrapper">
                        <img src="/assets/img/ricardo_rocha.jpg" alt="Ricardo Rocha - Patrono do Instituto Atleta para Sempre" class="patrono-img">
                        <div class="patrono-badge">
                            <span>🏆 Patrono Institucional</span>
                        </div>
                    </div>
                    <div class="patrono-info">
                        <h3>Ricardo Rocha</h3>
                        <p class="patrono-subtitle">Capitão do Tetra & Fundador da Metodologia Esportiva</p>
                        <div class="patrono-stats">
                            <div class="p-stat">
                                <span class="p-stat-num">+2.000</span>
                                <span class="p-stat-lbl">Jovens Impactados</span>
                            </div>
                            <div class="p-stat">
                                <span class="p-stat-num">2012</span>
                                <span class="p-stat-lbl">Ano de Fundação</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MISSÃO, VISÃO E VALORES -->
<section class="section section-mission-detail" id="missao">
    <div class="container">
        <div class="section-header fade-in-up text-center mb-8">
            <span class="section-tag">Nossos Pilares</span>
            <h2 class="section-title">Missão, Visão e Valores</h2>
            <p class="section-subtitle">Os princípios fundamentais que guiam nossa atuação social e desportiva.</p>
        </div>

        <div class="mvv-grid">
            <div class="mvv-card fade-in-up">
                <div class="mvv-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/></svg>
                </div>
                <h3>Missão</h3>
                <p>Promover a inclusão social, a cidadania e o desenvolvimento humano integral por meio do esporte e da educação em comunidades de vulnerabilidade.</p>
            </div>
            
            <div class="mvv-card fade-in-up" style="animation-delay:.1s">
                <div class="mvv-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </div>
                <h3>Visão</h3>
                <p>Ser referência em transformação social e contraturno escolar esportivo em Pernambuco e no Brasil, integrando esporte, lazer e formação cidadã.</p>
            </div>
            
            <div class="mvv-card fade-in-up" style="animation-delay:.2s">
                <div class="mvv-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <h3>Valores</h3>
                <ul class="mvv-list">
                    <li>Respeito e Dignidade</li>
                    <li>Honestidade e Ética</li>
                    <li>Responsabilidade Social</li>
                    <li>Igualdade de Oportunidades</li>
                    <li>Transparência Pública</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
