<?php
// templates/footer.php
if (!defined('ROOT_PATH')) exit;
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <h3 class="footer-title text-primary">Instituto Atleta Para Sempre</h3>
                <p class="text-muted">Promovendo o esporte e a inclusão social através da Lei de Incentivo ao Esporte no Brasil. Transformando vidas por meio da educação e cidadania.</p>
                <p class="text-muted mt-2"><strong>CNPJ:</strong> 00.000.000/0001-00</p>
                <p class="text-muted"><strong>Endereço:</strong> Rua do Esporte, 123 - Centro, Cidade - UF</p>
                
                <div class="social-links">
                    <a href="#" class="social-link" aria-label="Facebook">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                    </a>
                    <a href="#" class="social-link" aria-label="Instagram">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                    </a>
                    <a href="#" class="social-link" aria-label="YouTube">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33 2.78 2.78 0 0 0 1.94 2c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.33 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>
                    </a>
                </div>
            </div>
            
            <div>
                <h4 class="footer-title">Acesso Rápido</h4>
                <ul class="footer-links">
                    <li><a href="/quem-somos" class="footer-link">Quem Somos</a></li>
                    <li><a href="/projetos" class="footer-link">Projetos</a></li>
                    <li><a href="/noticias" class="footer-link">Notícias</a></li>
                    <li><a href="/trabalhe-conosco" class="footer-link">Trabalhe Conosco</a></li>
                    <li><a href="/contato" class="footer-link">Contato</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="footer-title">Transparência</h4>
                <ul class="footer-links">
                    <li><a href="/transparencia/estatuto" class="footer-link">Estatuto Social</a></li>
                    <li><a href="/transparencia/dirigentes" class="footer-link">Dirigentes</a></li>
                    <li><a href="/transparencia/financeiro" class="footer-link">Relatórios Financeiros</a></li>
                    <li><a href="/transparencia/termos" class="footer-link">Termos de Fomento</a></li>
                    <li><a href="/transparencia/painel" class="footer-link">Painel de Transferências</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Instituto Atleta Para Sempre. Todos os direitos reservados.</p>
            <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="btn btn-ghost btn-sm" aria-label="Voltar ao topo">
                Voltar ao topo
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 15l-6-6-6 6"/></svg>
            </button>
        </div>
    </div>
</footer>
