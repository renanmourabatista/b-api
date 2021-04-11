## Bank Api

* docker-compose up
* localhost:88/api

### Comandos do docker

#### Executar as migrations
- `docker exec -it bank.api php artisan migrate`
#### Popular o banco  
- `docker exec -it bank.api php artisan db:seed`

#### Comando para verificar qualidade do código

 - `docker run -it --rm -v $(pwd):/project -w /project jakzal/phpqa:1.55.0-php7.4 phpmd app text cleancode,codesize,controversial,design,naming,unusedcode`

#### Gerar a ApiDoc

- `docker exec -it bank.api apidoc -i app/Presentation -o doc/`

* A documentação da API é gerada dentro da da pasta `doc` na raiz do projeto

### Comandos do projeto

#### Autorizar as transações

- `docker exec -it bank.api php artisan authorize:transfers`

#### Notificar as transferencias dos Lojistas

- `docker exec -it bank.api php artisan notify:transfers`

### Rotas do projeto

 A documentação completa das rotas é gerada com a **apidoc** 

* `localhost:88/api/wallets/transfers/:id/revert` [POST]
* `localhost:88/api/wallets/transfers` [PUT]

### Usuários para teste em ambiente de desenvolvimento

#### Usuário lojista
* **email:** teste@lojista.com
* **senha:** password

#### Usuário comum 1
* **email:** teste@comum.com
* **senha:** password

#### Usuário comum 2
* **email:** teste@comum1.com
* **senha:** password