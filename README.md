## Bank Api

### Ambiente

- Para subir a aplicação é necessário copiar conteúdo do arquivo `.env.example` para o arquivo `.env` dentro da pasta
`envs` na raiz do projeto
  
#### Host

- `localhost:88/api`

### Comandos do docker
#### Subir a aplicação
- `docker-compose up`
#### Executar as migrations
- `docker exec -it bank.api php artisan migrate`
#### Popular o banco  
- `docker exec -it bank.api php artisan db:seed`, utilize somente na primeira vez que rodar o projeto, pois irá gerar todos 
os dados necessários para o ambiente de desenvolvimento
#### Executar os testes 
- `docker exec -it bank.api php vendor/phpunit/phpunit/phpunit --configuration phpunit.xml /var/www/tests`

#### Comando para verificar qualidade do código

 - `docker run -it --rm -v $(pwd):/project -w /project jakzal/phpqa:1.55.0-php7.4 phpmd app text cleancode,codesize,controversial,design,naming,unusedcode`

#### Gerar a ApiDoc

- `docker exec -it bank.api apidoc -i app/Presentation -o doc/`

* A documentação da API é gerada dentro da da pasta `doc` na raiz do projeto

### Comandos do projeto

#### Autorizar as transferências

- `docker exec -it bank.api php artisan authorize:transfers`

#### Notificar as transferências dos Lojistas

- `docker exec -it bank.api php artisan notify:transfers`

### Rotas

* A documentação das rotas é gerada com a **apidoc**
* Para baixar também a collection do **Postman** com todas as rotas [clique aqui](https://www.getpostman.com/collections/961aa06f11fc06f78362)

### Usuários para teste em ambiente de desenvolvimento

#### Usuário lojista
* **email:** teste@lojista.com
* **senha:** password

#### Usuário comum 1
* **email:** teste@comum1.com
* **senha:** password

#### Usuário comum 2
* **email:** teste@comum2.com
* **senha:** password