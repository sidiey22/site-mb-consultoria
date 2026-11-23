# Site MB Consultoria e Assessoria Política

- `src/index.src.html`: código-fonte do site (um único arquivo, com ícones em `{{i:nome}}`).
- `build.py`: gera `site/index.html` (versão estática).
- `build_php.py`: gera `php/` (site + painel `/admin`, PHP 8 + MySQL, para HostGator).
- `site/`: versão estática para aprovação. `php/`: pacote de produção.

Nunca coloque a senha real do banco no `php/inc/config.php` dentro do GitHub: preencha só no servidor.

## Publicação automática
Cada envio para a branch `main` que altera `php/` publica o site na HostGator (GitHub Actions, via FTP).
Segredos necessários no repositório: `FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD`.
O `inc/config.php` real fica só no servidor (use `inc/config.sample.php` como modelo).
