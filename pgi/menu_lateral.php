<!-- Barra lateral -->
<div class="sidebar" id="sidebar">
    <button class="toggle-btn" onclick="toggleSidebar()">
        <i class="fi fi-br-bars-sort" id="toggle-icon" style="color: #697891; margin-right: 5px;"></i>
        <span class="menu-text" id="sidebar-title" style="display: none;">NEXUS</span>
    </button>

    <!-- Página inicial -->
    <i id="menu-pagina-inicial" class="fa-solid fa-house page-link" onclick="window.location.href='menu.php'">
        <span class="menu-text">Página inicial</span>
    </i>

    <!-- Item com dropdown: Backoffice -->
    <div id="menu-backoffice" class="menu-item dropdown">
        <i class="fa-solid fa-phone" onclick="toggleDropdown();">
            <span class="menu-text" style="display: none;">Backoffice</span>
        </i>
        <!-- Submenu -->
        <div class="submenu" id="submenu">
            <a href="paineltfp.php" class="page-link">Painel TFP</a>
            <a href="#" class="page-link">Pós venda</a>
            <a href="fila.php" class="page-link">Fila de vendas</a>
        </div>
    </div>

    <!-- Dashboards -->
    <i id="menu-dashboards" class="fa-solid fa-shapes">
        <span class="menu-text">Dashboards</span>
    </i>

    <!-- Comercial -->
    <i id="menu-comercial" class="fa-solid fa-pen-to-square">
        <span class="menu-text">Comercial</span>
    </i>

    <!-- Item com dropdown: Recursos Humanos -->
    <div id="menu-recursos-humanos" class="menu-item dropdown-rh">
        <i class="fa-solid fa-user-group" onclick="toggleDropdownRH()">
            <span class="menu-text" style="display: none;">Recursos Humanos</span>
        </i>
        <!-- Submenu -->
        <div class="submenu" id="submenurh">
            <a href="painelrh.php" class="page-link">Cadastrar Colaborador</a>
            <a href="colaboradores_rh.php" class="page-link">Ver Colaboradores</a>
            <a href="carga-lotecolaboradores_.php" class="page-link">Opções de cadastro</a>
        </div>
    </div>

    <!-- Configurações -->
    <i id="menu-configuracoes" class="fas fa-cog page-link" onclick="window.location.href='configuracoes.php'">
        <span class="menu-text"><a href="configuracoes.php" class="page-link">Configurações</a></span>
    </i>

    <!-- Texto de rodapé -->
    <div class="footer">
        <p>&copy; 2024 VIGGO - Todos os direitos reservados.</p>
        <p>Versão: 1.0.0</p>
    </div>
</div>
