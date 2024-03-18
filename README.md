# Quadro Societário

🏢 Esta API foi desenvolvida com o intuito de possibilitar a criação e persistência de dados de empresas e seus sócios.

Foi desenvolvida com as tecnologias e ferramentas:
1. Symfony 6.4 LTS - Framework PHP ![Symfony](https://img.shields.io/badge/Symfony-6.4_LTS-blue?logo=symfony)
2. DoctrineORM ![DoctrineORM](https://img.shields.io/badge/DoctrineORM-2.x-green?logo=doctrine)
3. PostgreSQL - para persistência dos dados ![PostgreSQL](https://img.shields.io/badge/PostgreSQL-latest-blue?logo=postgresql)
4. Docker Compose para utilizar a imagem do PostgreSQL ![Docker](https://img.shields.io/badge/Docker_Compose-latest-blue?logo=docker)
5. VS Code ![VSCode](https://img.shields.io/badge/VS_Code-latest-blue?logo=visual-studio-code)

Requisitos desta API para testes em ambiente de dev:
1. PHP 8 - Versão compatível com o Symfony 6.4 LTS ![PHP](https://img.shields.io/badge/PHP-8-blue?logo=php)
2. Symfony v6+ ![Symfony](https://img.shields.io/badge/Symfony-v6+-blue?logo=symfony)
3. Composer ![Composer](https://img.shields.io/badge/Composer-latest-blue?logo=composer)
4. Docker ![Docker](https://img.shields.io/badge/Docker-latest-blue?logo=docker)

Para rodar esse projeto, você precisa seguir os passos abaixo:
1. Clonar este repositório
2. Fazer download das dependências com o comando 'composer install'
3. Disponibilizar o serviço de banco de dados por meio do Docker com o comando: docker-compose up -d
4. Disponibilizar o PHP Web Server via Symfony com o comando: symfony serve -d --no-tls
5. O endpoint estará disponível em: [http://127.0.0.1:8000/api](http://127.0.0.1:8000/api)

Das Entidades contidas no Projeto:
- Corporation -> Empresas que serão cadastradas
- Partner -> Sócios das empresas
Existe uma relação OneToMany entre empresas e Sócios e também a forma reversa

Dos Endpoints:
**Corporation:**
- Cadastro: [http://127.0.0.1:8000/api/corporations](http://127.0.0.1:8000/api/corporations)
- Listagem: [http://127.0.0.1:8000/api/corporations](http://127.0.0.1:8000/api/corporations)
- Listagem por ID: [http://127.0.0.1:8000/api/corporations/:id](http://127.0.0.1:8000/api/corporations/:id)
- Listagem por CNPJ: [http://127.0.0.1:8000/api/corporations/cnpj/:cnpj](http://127.0.0.1:8000/api/corporations/cnpj/:cnpj)
- Delete: [http://127.0.0.1:8000/api/corporations/:id](http://127.0.0.1:8000/api/corporations/:id)
- Update: [http://127.0.0.1:8000/api/corporations/:id](http://127.0.0.1:8000/api/corporations/:id)

**Partner:**
- Cadastro: [http://127.0.0.1:8000/api/partners](http://127.0.0.1:8000/api/partners)
- Listagem: [http://127.0.0.1:8000/api/partners](http://127.0.0.1:8000/api/partners)
- Listagem por ID: [http://127.0.0.1:8000/api/partners/:id](http://127.0.0.1:8000/api/partners/:id)
- Listagem por CPF: [http://127.0.0.1:8000/api/partners/cpf/:cpf](http://127.0.0.1:8000/api/partners/cpf/:cpf)
- Delete: [http://127.0.0.1:8000/api/partners/:id](http://127.0.0.1:8000/api/partners/:id)
- Delete por CPF: [http://127.0.0.1:8000/api/partners/cpf/:cpf](http://127.0.0.1:8000/api/partners/cpf/:cpf)
- Update: [http://127.0.0.1:8000/api/partners/:cpf](http://127.0.0.1:8000/api/partners/:cpf)
