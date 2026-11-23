<?php
/**
 * MB Consultoria — configuração do site.
 * Preencha os dados do banco MySQL criado no cPanel (veja o passo a passo).
 * Este arquivo fica dentro de /inc, que é bloqueada para acesso pela internet (.htaccess).
 */

// ---- Banco de dados MySQL (cPanel > Bancos de Dados MySQL) ----
define('DB_HOST', 'localhost');
define('DB_NAME', 'USUARIOCPANEL_mbsite');     // nome completo do banco, com o prefixo do cPanel
define('DB_USER', 'USUARIOCPANEL_mbuser');     // usuário completo do banco
define('DB_PASS', 'COLOQUE_A_SENHA_DO_BANCO');

// ---- Endereço do site (opcional) ----
// Deixe vazio para o sistema descobrir sozinho. Se quiser fixar, use sem barra no fim:
// define('SITE_URL', 'https://www.seudominio.com.br');
define('SITE_URL', '');

// ---- Fuso horário (Cuiabá) ----
define('TIMEZONE', 'America/Cuiaba');

// ---- Valores iniciais de e-mail (SMTP) ----
// Depois de instalar, tudo isso pode ser mudado dentro do painel: Configurações > E-mail.
define('SMTP_HOST', '');            // ex.: mail.seudominio.com.br
define('SMTP_PORT', 465);           // 465 (SSL) ou 587 (TLS)
define('SMTP_SECURE', 'ssl');       // ssl, tls ou none
define('SMTP_USER', '');            // e-mail completo, ex.: site@seudominio.com.br
define('SMTP_PASS', '');
define('SMTP_FROM', '');            // e-mail que aparece como remetente (normalmente o mesmo do usuário)
